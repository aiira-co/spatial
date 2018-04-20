<?php

    class Basket{


        function set($key, $value){
            $this->$key = $value;
        }



        function get($key){
          return $this->$key ?? null;
        }




    }

?>
