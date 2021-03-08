<?php namespace App\Models;

use CodeIgniter\FirebirdModel;

class Usuarios extends FirebirdModel
{
    protected $table = "usuarios";
    protected $primaryKey = "codigo";

    public function achar()
    {
        return $this->save(['nome' => 'Teste','cobrador' => 'null']);
    }
}