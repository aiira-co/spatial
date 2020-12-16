<?php

namespace Presentation\IdentityApi\Controllers;

// use Api\Controller\BaseController;
use Common\Libraries\Controller;
use Psr\Http\Message\ResponseInterface;
use Core\Application\Logics\Identity\Commands\AuthenticateUser;
use Spatial\Common\BindSourceAttributes\FromBody;
use Spatial\Common\HttpAttributes\HttpPost;
use Spatial\Core\Attributes\ApiController;
use Spatial\Core\Attributes\Area;
use Spatial\Core\Attributes\Route;

/**
 * AuthorizationController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */
#[ApiController]
#[Area('identity-api')]
#[Route('[area]/[controller]')]
class AuthorizationController extends Controller
{
    /**
     * Login Requests
     *
     * @param string $content
     * @return ResponseInterface
     * @throws \JsonException
     */
    #[HttpPost]
    public function httpPost(
        #[FromBody] string $content
    ): ResponseInterface {
        $command = new AuthenticateUser();
        $command->data = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
        $command->params = $this->params;
        return $this->mediator->process($command);
    }
}
