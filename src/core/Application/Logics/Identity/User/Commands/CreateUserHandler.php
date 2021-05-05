<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\User\Commands;

use Core\Application\Logics\Identity\Queries\ParamReserved;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Psr7\RequestHandler;

class CreateUserHandler extends RequestHandler
{
    /**
     * Handles Server Response
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        // Return response
        // check username & email again
        if ($this->_checkUsernameEmail($request->data->username, $request->data->email)) {
            $data = [
                'success' => false,
                'message' => 'Username/Email already exists',
            ];
        } else {

            $data = [
                'success' => true,
                'message' => 'Your account has been created',
                'userId' => $request->createUser(),
            ];
        }



        $payload = \json_encode($data ?? []);
        $this->response->getBody()->write($payload);
        return $this->response;
    }

    /**
     * Quickly check params again
     *
     * @param string $username
     * @param string $email
     * @return boolean
     */
    private function _checkUsernameEmail(string $username, string $email): bool
    {
        $checkParam = new ParamReserved();
        // check email
        $checkParam->data = [
            'field' => 'email',
            'value' => $email,
        ];
        $checkEmail = $checkParam->checkParam();
        // check username
        $checkParam->data = [
            'field' => 'username',
            'value' => $username,
        ];
        $checkUsername = $checkParam->checkParam();

        return $checkEmail && $checkUsername;
    }
}
