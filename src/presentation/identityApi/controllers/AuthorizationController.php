<?php

namespace Presentation\IdentityApi\Controllers;

// use Api\Controller\BaseController;
use Common\Libraries\Controller;
use Psr\Http\Message\ResponseInterface;
use Core\Application\Logics\Identity\Commands\AuthenticateUser;

/**
 * AuthorizationController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */
class AuthorizationController extends Controller
{
    /**
     * Login Requests
     *
     * @param array $content
     * @return ResponseInterface
     */
    public function httpPost(string $content): ResponseInterface
    {
        $command = new AuthenticateUser();
        $command->data = json_decode($content);
        $command->params = $this->params;
        return $this->mediator->process($command);
    }
}
