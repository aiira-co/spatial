<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\Commands;

use Core\Application\Logics\General\Commands\CreatePerson;
use Core\Domain\Identity\Person;
use Infrastructure\JWT\Token;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Mediator\Mediator;
use Spatial\Psr7\RequestHandler;

class AuthenticateUserHandler extends RequestHandler
{
    /**
     * Handles Server Response
     *
     * GetUser -> isActive -> isLockDown -> Auth -> GenToken
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Return response
        // guzzle to get data

        // echo 'sartgin';

        $success = $request->getUserByEmail();
        // if doesnot user exists
        if (!$success) {
            // user not found


            $data = [
                'sucess' => false,
                'message' => 'The username and/or password is incorrect. Please try again.',
            ];
            $payload = \json_encode($data ?? []);
            $this->response->getBody()->write($payload);
            return $this->response;
        }


        // check isActivated
        $access = $request->isUserActivated();
        if (!$access['activated']) {
            $data = [
                'sucess' => $access['activated'],
                'message' => 'Your account is blocked!, Please contact Admin',
            ];

            $payload = \json_encode($data ?? []);
            $this->response->getBody()->write($payload);
            return $this->response;
        }

        // return ['access' => $access, 'now' => (new \DateTime('now'))->getDate()];

        // check isLockDown
        if ($access['lockoutEnabled']) {
            // first check if lockdown has ended & allow,
            $nowTime = new \DateTime('now');
            if ($nowTime > $access['lockoutEnd']) {
                // zero out access fail
                $allowAccess = $request->upateAccessFailCount(true);
            } else {
                // report status
                $data = [
                    'sucess' => !$access['lockoutEnabled'],
                    'message' => 'Your account is on lockdown!, Please wait and try again from ',
                    'time' => $access['lockoutEnd'],
                ];
                $payload = \json_encode($data ?? []);
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
                'sucess' => !$request->upateAccessFailCount(),
                'message' => 'The username and/or password is incorrect. Please try again',
            ];
            $payload = \json_encode($data ?? []);
            $this->response->getBody()->write($payload);
            return $this->response;
        }


        // generate token

        // log user authtd

        $data = $this->genToken($request->getVerifiedUser(), (int) $request->params->gId);
        $payload = \json_encode($data ?? []);
        $this->response->getBody()->write($payload);
        return $this->response;
    }

    public function genToken(array $user, int $appId)
    {

        //register new user to trial subscription.

        return [
            'unique' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'name' => $user['name'],
            'image' => $user['image'],
            'idToken' => (new Token())
                ->genToken($user['id']),
            'tokenType' => 'Bearer',
            'expiresAt' => 3600,
        ];
    }
}
