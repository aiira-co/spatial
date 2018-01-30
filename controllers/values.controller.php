<?php
use CoreModel as DB;

class ValuesController
{

  

    function __construct()
    {
    }


    // method called to handle a GET request

    function httpGet(int ...$id): ?array
    {
        // --- use this if you are connected to the Databases ---
        // if (count($id)) {
        //     $users = DB::table('user')
        //                     ->where('id', $id[0])
        //                     ->single();
        // } else {
        //     $users = DB::table('users')->get();
        // }
        
        // return ['data'=>$users,'totalCount'=>count($users)];

        return ['value1','value2'];
    }


    // method called to handle a POST request
    function httpPost(array $form)
    {
        $postId=null;
      // --- use this if you are connected to the Databases ---
        // if (DB::table('values')->add($form)) {
        //     $alert = 'Succesfully saved';
        //      $postId = DB::$postId;
        // } else {
        //     $alert = 'Could not be saved. Please try again';
        
        // }

      // code here
        return ['success'=>true,'alert'=>'We have it at post','id'=>$postId];
    }


    // method called to handle a PUT request
    function httpPut(array $form, int $id)
    {

      // --- use this if you are connected to the Databases ---
        // if (DB::table('values')->where('id',$id)->update($form)) {
        //     $alert = 'Succesfully updated';
        //      $success = true;
        // } else {
        //     $alert = 'Could not be saved. Please try again';
        //      $success = false;
        
        // }


      // code here
        return ['success'=>true,'alert'=>'We have it at put'];
    }


    // method called to handle a DELETE request
    function httpDelete(int $id)
    {
      // code here
        return ['id'=>2];
    }
}
