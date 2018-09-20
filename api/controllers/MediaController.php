<?php
namespace Api\Controllers;

/**
 * MediaController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/mediaz
 *
 * @category Controller
 */

use Api\Models\PracticeModel;

class MediaController
{

    // method called to handle a GET request

    public function httpGet(int ...$id): ?array
    {
        $practiceModel = new PracticeModel;
        $users = $practiceModel->getItems('');

        return ['data'=>$users,'totalCount'=>count($users)];

        // return ['value1','value2'];
    }


    // method called to handle a POST request
    public function httpPost(array $form)
    {
        $postId=null;
        // code here
        return ['success'=>true,'alert'=>'We have it at post','id'=>$postId];
    }


    // method called to handle a PUT request
    public function httpPut(array $form, int $id)
    {

        // code here
        return ['success'=>true,'alert'=>'We have it at put'];
    }


    // method called to handle a DELETE request
    public function httpDelete(int $id)
    {
        // code here
        return ['id'=>2];
    }
}
