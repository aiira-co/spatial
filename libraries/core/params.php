<?php


class Params
{


	function __construct()
	{

		foreach($_REQUEST as $key => $value)
		{
			$this->$key = $value;
		}
	}

	function get($key)
	{
			return $this->$key ?? null;
	}

	// function set($key, $value){
	// 		$this->$key = $value;
	// }

}


?>
