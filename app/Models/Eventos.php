<?php namespace App\Models;

use CodeIgniter\FirebirdModel;

class Eventos extends FirebirdModel
{
    protected $table = "EVENTOSCOBRANCA";
    protected $primaryKey = "DATAHORA";

    public function verificarEventoCount($arr, $cod, $datalimit, $banco=0)
    {
        if($banco != 0)
        {
            try {
                set_time_limit(0);
                $resQuery = $this->in('cliente', $arr)
                                ->where('cod_evento', $cod)
                                ->where('cod_credor', $banco)
                                ->where("datahora >=", $datalimit)
                                ->count();
                return $resQuery->COUNT;
            } catch (\Exception $err) {
                throw $err;
            }        
        }
        else
        {
            try {
                set_time_limit(0);
                $resQuery = $this->in('cliente', $arr)
                                ->where('cod_evento', $cod)
                                ->where("datahora >=", $datalimit)
                                ->count();
                return $resQuery->COUNT;
            } catch (\Exception $err) {
                throw $err;
            }        
        }
    }

    public function verificarEvento($arr, $cod, $datalimit, $banco=0)
    {
        try {
            set_time_limit(0);
            if($banco != 0)
            {                
                $resQuery = $this->in('cliente', $arr)
                                ->where('cod_evento', $cod)
                                ->where("cod_credor", $banco)
                                ->where("datahora >=" ,$datalimit)                                
                                ->findAll();                
            } 
            else 
            {                
                $resQuery = $this->in('cliente', $arr)
                                ->where('cod_evento', $cod)
                                ->where("datahora >=" ,$datalimit)
                                ->findAll();
            }
            
            return $resQuery;
        } catch (\Exception $err) {
            throw $err;
        }        
    }

    public function verificarID($id)
    {
        try {
            set_time_limit(0);
            $resQuery = $this->where('cliente', $id)
                            ->first();
            return $resQuery;
        } catch (\Exception $err) {
            throw $err;
        }   
    } 
}