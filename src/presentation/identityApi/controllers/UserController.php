<?php

namespace Presentation\IdentityApi\Controllers;

use Common\Libraries\Controller;
use Psr\Http\Message\ResponseInterface;
use Core\Application\Logics\Identity\{
    Commands\CreateUser,
    Commands\DeleteUser,
    Commands\UpdateUser,
    Queries\GetUser,
    Queries\GetUsers,
    Queries\ParamReserved,
};

/**
 * UserController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */
class UserController extends Controller
{
    /**
     * The Method httpGet() called to handle a GET request
     * URI: GET: https://api.com/values
     * URI: GET: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id
     * to the methodUndocumented function
     *
     * @param integer ...$id
     * @return ResponseInterface
     */
    public function httpGet(int $id): ResponseInterface
    {

        // return ['users hre'];
        // --- use this if you are connected to the Databases ---
        if (!is_null($id)) {
            // print_r($id);
            $query = new GetUser();
            $query->id = (int) $id[0];
        } else {
            $query = new GetUsers();
        }
        $query->params = $this->params;
        return (array) $this->mediator->process($query);

        // return ['data'=>$users,'totalCount'=>count($users)];
    }

    /**
     * The Method httpPost() called to handle a POST request
     * This method requires a body(json) which is passed as the var array $content
     * URI: POST: https://api.com/values
     *
     * @param string $content
     * @return ResponseInterface
     */
    public function httpPost(string $content): ResponseInterface
    {
        $content = (json_decode($content));

        $command = isset($content->field) ? new ParamReserved() : new CreateUser();
        $command->data = $content;
        return $this->mediator->process($command);
    }

    /**
     * The Method httpPut() called to handle a PUT request
     * This method requires a body(json) which is passed as the var array $content and
     * An id as part of the uri.
     * URI: PUT: https://api.com/values/2 the number 2 in
     * the uri is passed as int $id to the method
     *
     * @param string $content
     * @param integer $id
     * @return ResponseInterface
     */
    public function httpPut(string $content, int $id): ResponseInterface
    {
        $command = new UpdateUser();
        $command->id = $id;
        $command->data = json_decode($content);
        $command->params = $this->params;
        return $this->mediator->process($command);
    }

    /**
     * The Method httpDelete() called to handle a DELETE request
     * URI: DELETE: https://api.com/values/2 ,the number 2 in
     * the uri is passed as int ...$id to the method
     *
     * @param integer $id
     * @return array|null
     */
    public function httpDelete(int $id): ResponseInterface
    {
        $command = new DeleteUser();
        $command->id = $id;
        return $this->mediator->process($command);
    }
}
