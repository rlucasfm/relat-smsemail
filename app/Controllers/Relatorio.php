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

	private function downloadSheet($data_arr)
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Hello World !');
		
		$writer = new Xlsx($spreadsheet);
		
		$filename = 'relatorio-sms';
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		
		$writer->save('php://output'); // download file 
		die;
	}
}
