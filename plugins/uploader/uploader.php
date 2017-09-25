<?php

  class Uploader{

    public $path;
    public $fileInfo = array('name' =>'' , 'type' =>'', 'ext' =>'', 'size' =>'', 'dimension' =>'');

    function GetImage(){
      echo 'Hello Uploader <br> Welcome here';
    }

    function MakeDirs($dirpath, $mode=0777) {
      return is_dir($dirpath) || mkdir($dirpath, $mode, true);
    }
  }


 ?>
