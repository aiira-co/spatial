<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\User\Queries;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Psr7\RequestHandler;

use function json_encode;

class GetUserHandler extends RequestHandler
{
    /**
     * Handles Server Response
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userInfo = [];
        // Return response
        if (!$request->userExist()) {
            $userInfo = [
                'success' => false,
                'message' => 'User was not found'
            ];
        }


        if ($request->params->categoryType === 'setting') {
            switch ($request->params->category) {
                case 'profile':
                    $userInfo = $request->getUserProfile();
                    break;
                case 'avatar':
                    $userInfo = $request->getUserAvatar();
                    break;
                case 'contact':
                    $userInfo = $request->getUserContact();
                    break;
                case 'notification':
                    $userInfo = $request->getUserNotification();
                    break;
                case 'apps':
                    $userInfo = $request->getUserAppClaims();
                    break;
                case 'account':
                    $userInfo = $request->getUserAccount();
                    break;

                default:
                    $userInfo = $request->getUser();
                    break;
            }
        } else {
            $userInfo = $request->getUser();
        }
        $data = $userInfo;
        $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
        $this->response->getBody()->write($payload);
        return $this->response;
    }
}
