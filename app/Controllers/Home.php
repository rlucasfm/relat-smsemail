<?php

namespace App\Controllers;
use App\Models\Eventos;
use App\Models\SmsModel;

class Home extends BaseController
{
	public function index()
	{
		$sms 	= new SmsModel();
		$evento = new Eventos();
		
		try {
			$result = $evento->verificarID("48973", 9023);
			echo "<pre>";
			var_dump($result);
			echo "</pre>";
		} catch (\Exception $th) {
			throw $th;
		}

		// $rs = $evento->verificarEvento($ids, $code, $diabusca, $banco);							
		// if(gettype($rs) == "object")
		// {
		// 	$evtResp[$code][] = $rs;
		// }	
		// else
		// {
		// 	$evtResp[$code] = array_merge($evtResp[$code], $rs);
		// }	
		
		
	}
}
