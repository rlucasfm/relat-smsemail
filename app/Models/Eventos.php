<?php namespace App\Models;

use CodeIgniter\FirebirdModel;

class Eventos extends FirebirdModel
{
    protected $table = "EVENTOSCOBRANCA";
    protected $primaryKey = "DATAHORA";

    public function achar()
    {
        return $this->where('remessa', 2006171125)->last();
    }
}