<?php

namespace Presentation\DefaultAPI\Controllers;

use Core\Application\Logics\DefaultApp\{
    Commands\DeleteEntity,
    Commands\UpdateEntity,
    Commands\CreateEntity,
    Queries\GetEntites,
    Queries\GetEntity,
};
use Common\Libraries\Controller;
use Psr\Http\Message\ResponseInterface;

/**
 * ValuesController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */

class ValuesController extends Controller
{

    /**
     * The Method httpGet() called to handle a GET request
     * URI: GET: https://api.com/values
     * URI: GET: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id to the method
     */
    public function httpGet(?int $id): ResponseInterface
    {
        if (!is_null($id)) {
            $query = new GetEntity();
        } else {
            $query = new GetEntites();
        }

        return $this->mediator->process($query);
        // return $this->mediator->process();
    }

    /**
     * The Method httpPost() called to handle a POST request
     * This method requires a body(json) which is passed as the var string $content
     * URI: POST: https://api.com/values
     */
    public function httpPost(string $content): ResponseInterface
    {
        // code here
        $command = new CreateEntity();
        $command->data = json_decode($content);
        return $this->mediator->process($command);
    }

    /**
     * The Method httpPut() called to handle a PUT request
     * This method requires a body(json) which is passed as the var string $content and
     * An id as part of the uri.
     * URI: PUT: https://api.com/values/2 the number 2 in the uri is passed as int $id to the method
     */
    public function httpPut(string $content, int $id): ResponseInterface
    {

        // code here
        $command = new UpdateEntity();
        $command->data = json_decode($content);
        $command->id = $id;
        return $this->mediator->process($command);
    }

    /**
     * The Method httpDelete() called to handle a DELETE request
     * URI: DELETE: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id to the method
     */
    public function httpDelete(int $id): ResponseInterface
    {
        // code here
        $command = new DeleteEntity();
        $command->id = $id;
        return $this->mediator->process($command);
    }
}
