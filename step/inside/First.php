<?php 

/**
* Step
*/
class First extends Core
{
	
	function __construct()
	{
		# code...
	}

	function index()
	{
		echo "nothing";
		// $this->load('step/ui/step1', array());
	}

	function defdef()
	{
		echo "halaman tidak ditemukan";
	}

	function signin($param='default param')
	{
		echo $param;
	}
}