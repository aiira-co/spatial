<?php

namespace Presentation\DefaultAPI\Controllers;

use Core\Application\Logics\DefaultApp\{
    Commands\DeleteEntity,
    Commands\UpdateEntity,
    Commands\CreateEntity,
    Queries\GetEntites,
    Queries\GetEntity,
};
use Core\Common\Libraries\Controller;
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
     * URI: POST: https://api.com/values
     * URI: POST: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id to the method
     */
    public function httpGet(?int ...$id): ResponseInterface
    {
        if (is_null($id[0])) {
            $query = new GetEntity();
        } else {
            $query = new GetEntites();
        }

        return $this->mediator->process($query);
        // return $this->mediator->process();
    }

    /**
     * The Method httpPost() called to handle a POST request
     * This method requires a body(json) which is passed as the var array $form
     * URI: POST: https://api.com/values
     */
    public function httpPost(array $form): ResponseInterface
    {
        // code here
        $command = new CreateEntity();
        $command->data = $form;
        return $this->mediator->process($command);
    }

    /**
     * The Method httpPut() called to handle a PUT request
     * This method requires a body(json) which is passed as the var array $form and
     * An id as part of the uri.
     * URI: POST: https://api.com/values/2 the number 2 in the uri is passed as int $id to the method
     */
    public function httpPut(array $form, int $id): ResponseInterface
    {

        // code here
        $command = new UpdateEntity();
        $command->data = $form;
        $command->id = $id;
        return $this->mediator->process($command);
    }

    /**
     * The Method httpDelete() called to handle a DELETE request
     * URI: POST: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id to the method
     */
    public function httpDelete(int $id): ResponseInterface
    {
        // code here
        $command = new DeleteEntity();
        $command->id = $id;
        return $this->mediator->process($command);
    }
}
