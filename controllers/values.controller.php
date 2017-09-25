<?php
use CoreModel as DB;

class ValuesController
{

  
    private $model;
  
    function __construct()
    {

        $this->model = CORE::getModel('practice');
        // $this->params =  CORE::getInstance('Params');
    }


    // method called to handle a GET request

    function httpGet(int $id = null):array
    {
        // return ['value1','value2'];
        $users = $this->model->getItems('');
        return $users;
    }


    // method called to handle a POST request
    function httpPost(array $form)
    {
      // code here
    }


    // method called to handle a PUT request
    function httpPut(int $id)
    {
      // code here
    }


    // method called to handle a DELETE request
    function httpDelete(int $id)
    {
      // code here
    }
}
