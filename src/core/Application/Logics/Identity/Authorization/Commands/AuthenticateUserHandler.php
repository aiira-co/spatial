<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\Authorization\Commands;


use Core\Application\Logics\Identity\Claims\Commands\CreateAppClaim;
use Core\Application\Logics\Identity\Claims\Queries\GetAppClaim;
use Core\Application\Logics\Social\Person\Models\SocialPersonIds;
use Core\Domain\Identity\Person;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use Infrastructure\JWT\Token;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Core\App;
use Spatial\Psr7\RequestHandler;

use function json_encode;

class AuthenticateUserHandler extends RequestHandler
{
    /**
     * Handles Server Response
     *
     * GetUser -> isActive -> isLockDown -> Auth -> GenToken
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Return response
        // guzzle to get data

        // echo 'starting';

        $success = $request->getUserByEmail();
        // if does not user exists
        if (!$success) {
            // user not found

            $data = [
                'success' => false,
                'message' => 'The username and/or password is incorrect. Please try again.',
            ];
            $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
            $this->response->getBody()->write($payload);
            return $this->response;
        }


        // check isActivated
        $access = $request->isUserActivated();
        if (!$access['activated']) {
            $data = [
                'success' => $access['activated'],
                'message' => 'Your account is blocked!, Please contact Admin',
            ];

            $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
            $this->response->getBody()->write($payload);
            return $this->response;
        }


        //        check user device
        if (!$access['deviceAccess']) {
            $data = [
                'success' => $access['deviceAccess'],
                'message' => 'Your account is blocked for this device!, Please login from a different device',
            ];

            $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
            $this->response->getBody()->write($payload);
            return $this->response;
        }

        // return ['access' => $access, 'now' => (new \DateTime('now'))->getDate()];

        // check isLockDown
        if ($access['lockoutEnabled']) {
            // first check if lock down has ended & allow,
            $nowTime = new DateTime('now');
            if ($nowTime > $access['lockoutEnd']) {
                // zero out access fail
                $allowAccess = $request->updateAccessFailCount(true);
            } else {
                // report status
                $data = [
                    'success' => !$access['lockoutEnabled'],
                    'message' => 'Your account is on lock down!, Please wait and try again from ',
                    'time' => $access['lockoutEnd'],
                ];
                $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
                $this->response->getBody()->write($payload);
                return $this->response;
            }
        }

        // now authenticate found user
        // if could not be authenticated, increase fail count
        if (!$request->authUser()) {
            //log access fail
            // increase access fail count
            $data = [
                'success' => !$request->updateAccessFailCount(),
                'message' => 'The username and/or password is incorrect. Please try again!',
            ];
            $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
            $this->response->getBody()->write($payload);
            return $this->response;
        }

        // check app claim
        if (!$this->_appClaims($request->getUser(), (int)$request->data->device->app->id)) {
            $data = [
                'success' => false,
                'message' => 'Sorry, your account does not to have access to this App',
            ];
            $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
            $this->response->getBody()->write($payload);
            return $this->response;
        }

        // generate token

        // log user authenticated

        $data = $this->genToken($request->getVerifiedUser(), (int)$request->data->device->app->id);
        $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
        $this->response->getBody()->write($payload);
        return $this->response;
    }

    /**
     * appClaim
     *
     * @param Person $user
     * @param int $appId
     * @return bool
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function _appClaims(Person $user, int $appId): bool
    {
        $getClaim = new GetAppClaim();
        // check is appClaims exists
        if (!$getClaim->checkUserAppClaims($user->id, $appId)) {
            // create appClaim
            $createClaim = new CreateAppClaim();
            return $createClaim->createAppClaims($user->id, $appId);
            // create person for app @ genPersonId fn.

        }

        // check if its active,

        return $getClaim->isAppClaimActivated();
    }

    /**
     * @param array $user
     * @param int $appId
     * @return array|null
     * @throws \Exception
     */
    public function genToken(array $user, int $appId): ?array
    {
        //register new user to trial subscription.
        $permittedFor = false ? 'https://connect.aiira.co' : 'localhost:4401';
        $jwt = App::diContainer()->get(Token::class);
        //        try {
        $appIds = $this->_getAppsId($user, $appId);

        $prodMode = AppConfig['enableProdMode'];

        $permittedFor = match ($appId) {
            60 => $prodMode ? 'https://connect.aiira.co' : 'http://localhost:7200',
            64 => $prodMode ? 'https://business.aiira.co' : 'http://localhost:4700',
            70 => $prodMode ? 'https://play.aiira.co' : 'http://localhost:3200',
        };
        return [
            'unique' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'name' => $user['name'],
            'image' => $user['image'],
            'gid' => $appIds,
            'idToken' => $jwt->genToken($user['id'], $appIds, $permittedFor),
            'tokenType' => 'Bearer',
            'expiresAt' => getenv('JWT_TOKEN_TTL'),
            'deviceId' => $user['deviceId'],
            'requestConfirmation' => $user['requestConfirmation']
        ];
        //        } catch (Exception $e) {
        //            die($e->getMessage());
        //        }
    }

    /**
     * Get Apps Id
     *
     * @param array $user
     * @param int $appId
     * @return array|null
     * @throws ORMException
     * @throws \JsonException
     */
    private function _getAppsId(array $user, int $appId): ?array
    {
        // must have apps are
        // blog, forum, store, media

        // lyfeShot[58] => null
        // play[59] => blog
        // artist[60] => blog forum store media
        // store[61] => null
        // christ[62] => blog forum store media
        // messenger[63] => null
        // suite[64] => null
        $appModules = match ($appId) {
            60 => ['social'],
            // 62 => ['social', 'blog', 'event', 'forum', 'store', 'media'],
            // 59 => ['social', 'blog', 'event', 'play'],
            64 => ['suite'],
                //            65 => ['rent'],
            default => [],
        };

        //        print_r($appModules);

        $gIds = [];
        foreach ($appModules as $iValue) {
            $gIds[$iValue] = $this->_getAppPersonId($iValue, $user);
        }

        // if app contains social, update the gid
        if (isset($appModules[0]) && $appModules[0] === 'social') {
            // (new SocialPersonIds())->updatePersonGIDs($gIds);
        }

        //        print_r($gIds);

        return $gIds;
    }

    /**
     * Get Peron's Id in the app
     *
     * @param string $app
     * @param array $user
     * @return array
     * @throws \JsonException
     */
    private function _getAppPersonId(string $app, array $user): array
    {

        $host = '127.0.0.1:8080/' . $app . '-api/person/';
        //use cURL to fetch data
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,  $host);
        curl_setopt($ch, CURLOPT_POST,  1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($user));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //        curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.1');
        $response = curl_exec($ch);
        curl_close($ch);


        $result = json_decode((string)$response, false, 512, JSON_THROW_ON_ERROR);

        // print_r($result);

        if ($result->success) {
            return [
                'u' => $result->userId,
                'p' => $result->level
            ];
        }

        return [
            'u' => 0,
            'p' => 0
        ];
    }
}
