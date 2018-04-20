<?php


class Params
{
    public function __construct()
    {
        foreach ($_REQUEST as $key => $value) {
            $this->$key = $value;
        }
    }

    public function get($key)
    {
        return $this->$key ?? null;
    }

    // function set($key, $value){
    // 		$this->$key = $value;
    // }
}
