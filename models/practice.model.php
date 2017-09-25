<?php

use CoreModel as DB;

class PracticeModel{

  private $table = 'persons';

  //this is responsible for quering the database
  function getItems($key){

      return DB::table($this->table)
                      ->where('name','LIKE','%'.$key.'%')
                      ->orderBy('id')
                      ->get();

  }


  function getItem(int $id){
      return DB::table($this->table)->where('id',$id)->single();

  }


  function countItems(){
    return DB::table($this->table)
                    ->count();
  }

  function addItem(array $data):bool{

      return DB::table($this->table)->add($data);

  }

  function updateItem(array $data, int $id):bool{

      return DB::Table($this->table)->where('id',$id)->update($data);
  }


  function deleteItem(int $n):bool{
      return DB::table($this->table)->where('id',$n)->delete();
  }

}



?>
