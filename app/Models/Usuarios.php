<?php namespace App\Models;

use CodeIgniter\FirebirdModel;

class Usuarios extends FirebirdModel
{
    protected $table = "usuarios";
    protected $primaryKey = "codigo";

    public function achar($id){
        return $this->find($id);
    }
}