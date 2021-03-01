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
     * @var void
     */
	public function index()
	{
		if($this->request->getMethod(TRUE) !== "POST")
        {
            return $this->response->setStatusCode(405)
                                ->setBody('Apenas método POST é permitido');
        }
        else
        {
            $sms = new SmsModel();

            $post_data = $this->request->getPost();
            try {
                $modelResponse = $sms->salvarRelatorio($post_data);    
            } catch (\Exception $err) {
                $errMsg = $err->getMessage();
                $this->response->setStatusCode(500)
                            ->setBody("Houve um erro interno: $errMsg");
            }            

            $this->response->setStatusCode(200)
                        ->setBody("Operação de envio de SMS enviado com sucesso. \n $modelResponse");
        }
	}
}
