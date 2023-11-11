<?php

namespace Presentation\WebApi\Controllers;

use Core\Application\Logics\App\Person\{
    Commands\DeletePerson,
    Commands\UpdatePerson,
    Commands\CreatePerson,
    Queries\GetPersons,
    Queries\GetPerson,
};
use Infrastructure\Services\AuthUser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Spatial\Common\BindSourceAttributes\FromBody;
use Spatial\Common\HttpAttributes\HttpDelete;
use Spatial\Common\HttpAttributes\HttpGet;
use Spatial\Common\HttpAttributes\HttpPost;
use Spatial\Common\HttpAttributes\HttpPut;
use Spatial\Core\Attributes\ApiController;
use Spatial\Core\Attributes\Area;
use Spatial\Core\Attributes\Authorize;
use Spatial\Core\Attributes\Route;
use Spatial\Core\ControllerBase;
use Spatial\Mediator\Mediator;

/**
 * ValuesController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */
#[ApiController]
#[Area('web-api')]
#[Route('[area]/[controller]')]
class ValuesController extends ControllerBase
{


    /**
     * The Method httpGet() called to handle a GET request
     * URI: GET: https://api.com/values
     * URI: GET: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id to the method
     * @return ResponseInterface
     */
    #[HttpGet]
    #[Authorize(AuthUser::class)]
    public function getValues(): ResponseInterface
    {
        $query = new GetPersons();
        return $this->mediator->process($query);
        // return $this->mediator->process();
    }


    #[HttpGet('{id:int}')]
    public function httpGet(
        int $id
    ): ResponseInterface {
        $query = new GetPerson();
        return $this->mediator->process($query);
        // return $this->mediator->process();
    }

    /**
     * The Method httpPost() called to handle a POST request
     * This method requires a body(json) which is passed as the var string $content
     * URI: POST: https://api.com/values
     * @param string $content
     * @return ResponseInterface
     * @throws \JsonException
     */
    #[HttpPost]
    public function httpPost(
        #[FromBody] string $content
    ): ResponseInterface {
        // code here
        $command = new CreatePerson();
        $command->data = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
        return $this->mediator->process($command);
    }

    /**
     * The Method httpPut() called to handle a PUT request
     * This method requires a body(json) which is passed as the var string $content and
     * An id as part of the uri.
     * URI: PUT: https://api.com/values/2 the number 2 in the uri is passed as int $id to the method
     * @param string $content
     * @param int $id
     * @return ResponseInterface
     */
    #[HttpPut('{id:int}')]
    public function httpPut(
        #[FromBody] string $content,
        int $id
    ): ResponseInterface {
        // code here
        $command = new UpdatePerson();
        $command->data = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
        $command->id = $id;
        return $this->mediator->process($command);
    }

    /**
     * The Method httpDelete() called to handle a DELETE request
     * URI: DELETE: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id to the method
     * @param int $id
     * @return ResponseInterface
     */
    #[HttpDelete('{id:int}')]
    public function httpDelete(
        int $id
    ): ResponseInterface {
        // code here
        $command = new DeletePerson();
        $command->id = $id;
        return $this->mediator->process($command);
    }
}
