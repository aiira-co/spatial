<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\User\Commands;

use Core\Application\Logics\General\Commands\UpdatePerson;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Psr7\RequestHandler;

class UpdateUserHandler extends RequestHandler
{
    /**
     * Handles Server Response
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        if (!$request->userExist()) {
            $data = ['success' => false, 'message' => 'User was not found'];
            $payload = \json_encode($data ?? []);
            $this->response->getBody()->write($payload);
            return $this->response;
            // Then log user off
        }

        if (!is_null($request->params->categoryType) & $request->params->categoryType === 'setting') {
            switch ($request->params->category) {
                case 'profile':
                    $success = $request->updateUserProfile();
                    break;
                case 'avatar':
                    $success = $request->updateUserAvatar();
                    // update app persons
                    break;
                case 'cover':
                    $success = $request->updateUserCover();
                    break;
                case 'contact':
                    $success = $request->updateUserContact();
                    break;
                case 'api':
                    $success = $request->updateUserPassword();
                    break;
                case 'notification':
                    $success = $request->updateUserNotification();
                    break;
                case 'apps':
                    $success = $request->updateUserAppClaims();
                    break;
                case 'account':
                    $success = $request->updateUserAccount();
                    break;

                default:
                    $success = $request->updateUser();
                    break;
            }
        } else {
            $success = $request->updateUser();
        }

        if (!$success) {
            $data = [
                'success' => $success,
                'message' => 'Sorry, we could not update your profile. Please try again',
            ];
        } else {

            //send mail

            $data = [
                'success' => $success,
                'message' => 'Profile updated!, please verify your account from your email',
            ];
        }

        $payload = \json_encode($data ?? []);
        $this->response->getBody()->write($payload);
        return $this->response;
    }
}
