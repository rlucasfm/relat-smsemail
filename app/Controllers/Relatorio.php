<?php

namespace App\Controllers;

use App\Models\Eventos;
use App\Models\SmsModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Relatorio extends BaseController
{
	public function index()
	{
		$sms = new SmsModel();

		$data = [
			'titulo' => "Relatórios de envio"
		];

		echo view('relatorios', $data);
	}

	/**
	 * Recebe Requisição AJAX para retornar informações
	 * para alimentar os gráficos de relatório.
	 * 
	 * @var array
	 */
	public function buscarDatas()
	{
		$sms = new SmsModel();

		$id_banco = $this->request->getPost('id_banco') ?? "";
		$dataInicio = $this->request->getPost('dataInicio');
		
		$dataFim = $this->request->getPost('dataFim');
		$dataFim = new \DateTime($dataFim);
		$dataFim->modify('+1 day');
		$dataFim = $dataFim->format('Y-m-d');		

		try {
			$response = $sms->buscarDatas($dataInicio, $dataFim, $id_banco);
		} catch (\Exception $err) {
			echo $err;
		}		

		echo json_encode($response);
	}

	/**
	 * Recebe Requisição AJAX para retornar informações
	 * para alimentar os gráficos de relatório de eventos.
	 * 
	 * @var array
	 */
	public function buscarEventos()
	{
		set_time_limit(0);

		$sms 	= new SmsModel();
		$evento = new Eventos();

		$req = $this->request->getPost();
		
		$ev_codes = [];
		if(array_key_exists("codes", $req))
		{
			foreach($req["codes"] as $cds)
			{
				$ev_codes[] = $cds["name"];
			}			
			
			$dataInicio = $req["dataInicio"];		
			$dataFim = $req['dataFim'];
			$dataFim = new \DateTime($dataFim);
			$dataFim->modify('+1 day');
			$dataFim = $dataFim->format('Y-m-d');

			$diasate = $req["diasate"];
			$diabusca = new \DateTime();
			$diabusca->modify("-$diasate day");
			$diabusca = $diabusca->format('d.m.Y');

			$banco = (!empty($req["banco"])) ? $req['banco'] : "";						

			// Recupera cada id de cliente entre as datas
			$regs_clientes = $sms->buscarDatas($dataInicio, $dataFim, $banco);
			$ids_arr = array();
			foreach($regs_clientes as $rc)
			{
				if($rc->clienteid != "Zenvia")
				{
					$ids_arr[] = $rc->clienteid;
				}
			}			

			$ids_arr = array_chunk(array_unique($ids_arr), 1000);		
			
			$evtResp = [];	
			$count_arr = [];

			foreach ($ev_codes as $code)
			{				
				$evtResp[$code] = 0;
				foreach ($ids_arr as $ids)
				{
					try 
					{															
						try {
							$evtResp[$code] += $evento->verificarEventoCount($ids, $code, $diabusca, $banco);
						} catch (\Exception $th) {
							echo $th->getMessage();
						}					
					} catch (\Exception $th) {
						echo $th->getMessage();
					}						
				}				
			}						
			return json_encode($evtResp);
		}
	}

	/**	 
	 * Geração de tabela Excel para exportação
	 * a partir de array post da principal
	 * 
	 * @var Spreadsheet
	 */
	public function downloadSheet()
	{
		$sms = new SmsModel();
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		
		$datainicio = $this->request->getPost('datainicio');
		$datafim = $this->request->getPost('datafim');
		$datafim = new \DateTime($datafim);
		$datafim->modify('+1 day');
		$datafim = $datafim->format('Y-m-d');
		$id_banco = $this->request->getPost('id_banco');

		$regs = $sms->buscarDatas($datainicio, $datafim, $id_banco);

		$sheet->setCellValue('A1', 'Operadora');
		$sheet->setCellValue('B1', 'Data de envio');
		$sheet->setCellValue('C1', 'Hora de envio');
		$sheet->setCellValue('D1', 'ID Banco');
		$sheet->setCellValue('E1', 'ID Cliente');
		$sheet->setCellValue('F1', 'Celular');
		$sheet->setCellValue('G1', 'Mensagem');		
		$sheet->setCellValue('H1', 'ID SMS');
		$sheet->setCellValue('I1', 'Status Descritivo');
		$sheet->setCellValue('J1', 'Status Confirmação');
		

		$index = 2;
		foreach($regs as $reg){
			$dataCriado = new \DateTime($reg->criado_em);
			$horaCriado = $dataCriado->format('H:i:s');
			$dataCriado = $dataCriado->format('d/m/Y');

			$sheet->setCellValue("A$index", $reg->operadora);
			$sheet->setCellValue("B$index", $dataCriado);
			$sheet->setCellValue("C$index", $horaCriado);
			$sheet->setCellValue("D$index", $reg->id_banco);
			$sheet->setCellValue("E$index", $reg->clienteid);
			$sheet->setCellValue("F$index", $reg->celular);
			$sheet->setCellValue("G$index", $reg->mensagem);			
			$sheet->setCellValue("H$index", $reg->idsms);
			$sheet->setCellValue("I$index", $reg->statusDesc);
			$sheet->setCellValue("J$index", $reg->statusConf);

			$index++;
		}

		
		
		$writer = new Xlsx($spreadsheet);		
		$filename = 'relatorio-sms';		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		
		$writer->save('php://output'); // download file 
		die;
	}	

	/**	 
	 * Geração de tabela Excel para exportação
	 * a partir de post do relatório de eventos
	 * 
	 * @var Spreadsheet
	 */
	public function exportEventos()
	{
		
		set_time_limit(0);

		$sms 	= new SmsModel();
		$evento = new Eventos();
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$req = $this->request->getPost();

		$dataInicio = $req['datainicio'];
		$dataFim = $req['datafim'];
		$dataFim = new \DateTime($dataFim);
		$dataFim->modify('+1 day');
		$dataFim = $dataFim->format('Y-m-d');		
		$banco = (!empty($req["banco"])) ? $req['banco'] : "";	
		
		$diasate = $req["diasate"];
		$diabusca = new \DateTime();
		$diabusca->modify("-$diasate day");
		$diabusca = $diabusca->format('d.m.Y');
		
		$eventos = json_decode($req['eventos']);
		$ev_codes = [];

		foreach($eventos as $ev)
		{
			$ev_codes[] = $ev->name;
		}					

		// Recupera cada id de cliente entre as datas
		$regs_clientes = $sms->buscarDatas($dataInicio, $dataFim, $banco);
		$ids_arr = array();		
		foreach($regs_clientes as $rc)
		{
			if($rc->clienteid != "Zenvia")
			{
				$ids_arr[] = $rc->clienteid;
			}
		}			

		$ids_arr = array_chunk(array_unique($ids_arr), 1000);		
		
		$evtResp = [];

		$sheet->setCellValue('A1', 'CLIENTE');
		$sheet->setCellValue('B1', 'NROPERACAO');
		$sheet->setCellValue('C1', 'REMESSA');
		$sheet->setCellValue('D1', 'DATAHORA');
		$sheet->setCellValue('E1', 'HISTORICO');						
		$sheet->setCellValue('F1', 'SALDO');		
		$sheet->setCellValue('G1', 'COD_EVENTO');

		$index = 2;
		foreach ($ev_codes as $code)
		{				
			// $evtResp[$code] = [];
			foreach ($ids_arr as $ids)
			{				
				try 
				{															
					$rs = $evento->verificarEvento($ids, $code, $diabusca, $banco);									
					if(gettype($rs) == "object")
					{
						// $evtResp[$code][] = $rs;
						$sheet->setCellValue("A$index", $rs->CLIENTE);
						$sheet->setCellValue("B$index", $rs->NROPERACAO);
						$sheet->setCellValue("C$index", $rs->REMESSA);
						$sheet->setCellValue("D$index", $rs->DATAHORA);
						$sheet->setCellValue("E$index", $rs->HISTORICO);									
						$sheet->setCellValue("F$index", $rs->SALDO);		
						$sheet->setCellValue("G$index", $rs->COD_EVENTO);
						
						$index++;					
					}	
					else
					{
						//$evtResp[$code] = array_merge($evtResp[$code], $rs);											
						foreach ($rs as $eR) {
							$sheet->setCellValue("A$index", $eR->CLIENTE);
							$sheet->setCellValue("B$index", $eR->NROPERACAO);
							$sheet->setCellValue("C$index", $eR->REMESSA);
							$sheet->setCellValue("D$index", $eR->DATAHORA);
							$sheet->setCellValue("E$index", $eR->HISTORICO);										
							$sheet->setCellValue("F$index", $eR->SALDO);		
							$sheet->setCellValue("G$index", $eR->COD_EVENTO);							
							$index++;							
						}
					}										
				} catch (\Exception $th) {
					echo $th->getMessage();
				}						
			}				
		}						
		$writer = new Xlsx($spreadsheet);		
		$filename = 'relatorio-eventos-sms';		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		
		$writer->save('php://output'); // download file 
		die;
	}
}
