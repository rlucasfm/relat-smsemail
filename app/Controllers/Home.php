<?php

namespace App\Controllers;
use App\Models\Eventos;

class Home extends BaseController
{
	public function index()
	{
		$user = new Eventos();
		try {
			//echo "<pre>"; 
			var_dump($user->achar());
			//echo "</pre>";
		} catch (\Exception $th) {
			echo $th->getMessage();
		}
	}
}
