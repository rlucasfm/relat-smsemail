<?php

namespace App\Controllers;
use App\Models\Eventos;

class Home extends BaseController
{
	public function index()
	{
		$evento = new Eventos();
		try {
			echo "<pre>"; 
			var_dump($evento->achar());
			echo "</pre>";
		} catch (\Exception $th) {
			echo $th->getMessage();
		}
	}
}
