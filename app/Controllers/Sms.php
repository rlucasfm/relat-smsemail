<?php

namespace App\Controllers;

use App\Models\SmsModel;

class Sms extends BaseController
{
    /**
     * 
     * Recebe o POST de informações do CRM e direciona ao Modelo
     * Detecta e responde requisições, garantindo que apenas
     * POST será levado ao modelo.
     * 
     * @var response
     */
	public function index()
	{
        // Verifica se o método de requisição é POST
		if($this->request->getMethod(TRUE) !== "POST")
        {
            return $this->response->setStatusCode(405)
                                ->setBody('Apenas método POST é permitido');
        }
        else
        {
            $sms = new SmsModel();

            $post_data = $this->request->getPost();

            // Envia os dados ao modelo e manipula as respostas
            try {
                $modelResponse = $sms->salvarRelatorio($post_data); 
                $this->response->setStatusCode(200)
                            ->setBody("Requisição de salvamento feito com sucesso. \n $modelResponse");
            } catch (\Exception $err) {                
                $errMsg = $err->getMessage();
                $this->response->setStatusCode(500)
                            ->setBody("Houve um erro interno: $errMsg");
            }            

            return $this->response;
        }
	}

    /**
     * 
     * Rotina para avaliar o status das mensagens não avaliadas.
     * Deve ser utilizado em conjunto com CRON Jobs.
     * 
     * @var void
     */
    public function avaliarSMS()
    {
        $sms = new SmsModel();

        // Realiza avaliação dos SMS BestVoice
        try {            
            $resultBV = $sms->avaliarBestVoice();            
        } catch (\Exception $th) {
            $resultBV = $th->getMessage();
        }

        try {            
            $resultZenvia = $sms->avaliarZenvia();            
        } catch (\Exception $th) {
            $resultZenvia = $th->getMessage();
        }
        

        $this->createLog($resultBV);
        $this->createLog($resultZenvia);        
    }

    public function reavaliarSMS()
    {
        $sms = new SmsModel();

        try {            
            $resultReavBestVoice = $sms->reavaliarBestVoice();            
        } catch (\Exception $th) {
            $resultReavBestVoice = $th->getMessage();
        }

        $this->createLog($resultReavBestVoice);
    }

    /**
     * 
     * Procedimento para gerar os LOGS 
     * referentes aos CRON Jobs.
     * Pasta writeable/cron
     * 
     * @var void
     */
    private function createLog($tolog)
    {
        $logName = 'log-'.date('Y-m-d').'.log';
        //if(file_exists(WRITEPATH."cron\\".$logName)) 
        file_put_contents("../../richard-apps/relat-smsemail/writable/logs/cron/".$logName, $tolog, FILE_APPEND);
        
    }
}
