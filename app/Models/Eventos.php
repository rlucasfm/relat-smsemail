<?php namespace App\Models;

use CodeIgniter\FirebirdModel;

class Eventos extends FirebirdModel
{
    protected $table = "EVENTOSCOBRANCA";
    protected $primaryKey = "DATAHORA";

    public function respondentes($arr)
    {
        try {
            return $this->in('cliente', $arr)->where('cod_evento', 5)->findAll();
        } catch (\Exception $err) {
            throw $err;
        }        
    }
}