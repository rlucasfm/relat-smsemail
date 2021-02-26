<?php

namespace App\Controllers;

class API extends BaseController
{
	public function index()
	{
		$str_help = "API de Relatórios SMS/E-mail <br>1. /sms para o handle de GET SMS";
		echo($str_help);
	}

	public function sms()
	{
		$method = $this->request->getMethod(TRUE);
		if($method == "POST")
		{
			echo "<pre>";
			var_dump($this->request->getPost());
			echo "</pre>";

			$dir = realpath("/writable/logs/post.txt");
			file_put_contents($dir, $this->request->getPost());
		}
		else
		{
			echo "Apenas requisições POST são manipuladas";
		}		
	}
}
