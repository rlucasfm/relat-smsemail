<?php

namespace App\Controllers;

class Sms extends BaseController
{
	public function index()
	{
		if($this->request->getMethod(TRUE) !== "POST")
        {
            echo "Este endpoint trata apenas requisições HTTP POST";
        }
        else
        {
            $post_data = $this->request->getPost();

            echo "<pre>";
            var_dump($post_data);
            echo "</pre>";
        }
	}
}
