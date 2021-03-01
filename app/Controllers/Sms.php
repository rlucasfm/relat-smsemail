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
}
