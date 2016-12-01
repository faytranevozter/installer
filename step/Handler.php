<?php 
/**
* 
*/
class Handler extends Core
{
	
	function first_landing()
	{
		redirect('first');
	}

	function index()
	{
		echo 'halaman tidak ditemukan';
	}
}