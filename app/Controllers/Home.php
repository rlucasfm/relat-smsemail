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
		
		// Recupera cada id de cliente entre as datas
		$regs_clientes = $sms->buscarDatas('2021-03-01', '2021-03-09');
		$ids_arr = array();
		foreach($regs_clientes as $rc)
		{
			if($rc->clienteid != "Zenvia")
			{
				$ids_arr[] = $rc->clienteid;
			}
		}
		$ids_arr = array_unique($ids_arr);
		
		try {
			$resp = $evento->respondentes($ids_arr);
		} catch (\Exception $th) {
			echo $th->getMessage();
		}
		
	}
}
