<?php

namespace Presentation\IdentityApi\Controllers;

/**
 * UserController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */
class UserController extends BaseController
{
    private $table = 'User';
    // method called to handle a GET request

    public function httpGet(int ...$id): ?array
    {
        return ['data'=>'get user row by id'];
    }


    // method called to handle a POST request
    public function httpPost(array $form)
    {
        switch ($form['ResponseCode']) {
        case 0000:
          // code...
          break;

        default:
          // code...
          break;
      }
        // code here
        return ['id'=>2];
    }



    public function registerSubscription()
    {
    }

    // method called to handle a PUT request
    public function httpPut(array $form, int $id)
    {
        // code here
        return ['id'=>2];
    }
}
