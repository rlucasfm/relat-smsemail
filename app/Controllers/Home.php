<?php

namespace App\Controllers;
use App\Models\SmsModel;
use App\Models\Usuarios;

class Home extends BaseController
{
	public function index()
	{
		$user = new Usuarios();
		try {
			echo "<pre>"; 
			var_dump($user->achar(1));
			echo "</pre>";
		} catch (\Exception $th) {
			echo $th->getMessage();
		}
	}
}
