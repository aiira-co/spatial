<?php
namespace Api\Controllers;

/**
 * ValuesController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */

class ValuesController
{
    /**
    * Use constructor to Inject or instanciate dependecies
    */
    public function __construct()
    {
    }


    /**
    * The Method httpGet() called to handle a GET request
    * URI: POST: https://api.com/values
    * URI: POST: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id to the method
    */
    public function httpGet(int ...$id): ?array
    {
        // --- use this if you are connected to the Databases ---
        // if (count($id)) {
        //     $users = Lynq\Entity\EntityModel::table('user')
        //                     ->where('id', $id[0])
        //                     ->single();
        // } else {
        //     $users = Lynq\Entity\EntityModel::table('users')->get();
        // }

        // return ['data'=>$users,'totalCount'=>count($users)];

        return ['value1000','value2'];
    }


    /**
    * The Method httpPost() called to handle a POST request
    * This method requires a body(json) which is passed as the var array $form
    * URI: POST: https://api.com/values
    */
    public function httpPost(array $form)
    {
        $postId=null;
        // --- use this if you are connected to the Databases ---
        // if (Lynq\Entity\EntityModel::table('values')->add($form)) {
        //     $alert = 'Succesfully saved';
        //      $postId = Lynq\Entity\EntityModel::$postId;
        // } else {
        //     $alert = 'Could not be saved. Please try again';

        // }

        // code here
        return ['success'=>true,'alert'=>'We have it at post','id'=>$postId];
    }


    /**
    * The Method httpPut() called to handle a PUT request
    * This method requires a body(json) which is passed as the var array $form and
    * An id as part of the uri.
    * URI: POST: https://api.com/values/2 the number 2 in the uri is passed as int $id to the method
    */
    public function httpPut(array $form, int $id)
    {

        // --- use this if you are connected to the Databases ---
        // if (Lynq\Entity\EntityModel::table('values')->where('id',$id)->update($form)) {
        //     $alert = 'Succesfully updated';
        //      $success = true;
        // } else {
        //     $alert = 'Could not be saved. Please try again';
        //      $success = false;

        // }


        // code here
        return ['success'=>true,'alert'=>'We have it at put'];
    }


    /**
    * The Method httpDelete() called to handle a DELETE request
    * URI: POST: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id to the method
    */
    public function httpDelete(int $id)
    {
        // code here
        return ['id'=>$id];
    }
}
