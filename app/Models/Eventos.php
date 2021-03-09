<?php namespace App\Models;

use CodeIgniter\FirebirdModel;

class Eventos extends FirebirdModel
{
    protected $table = "EVENTOSCOBRANCA";
    protected $primaryKey = "DATAHORA";

    public function achar()
    {
        return $this->in('cod_evento', [9034, 9005, 9023])->first(10);
    }
}