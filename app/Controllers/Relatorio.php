<?php

namespace App\Controllers;

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
	 * Geração de tabela Excel para exportação
	 * a partir de array de entrada.
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
}
