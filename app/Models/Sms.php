<?php namespace App\Models;

use CodeIgniter\Model;

class Sms extends Model
{
    protected $table            = 'sms';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['id', 'celular', 'mensagem', 'clienteid', 'idsms'];
    protected $useTimestamps    = false;
    protected $skipValidation   = true;

    public function gravar($celular, $mensagem, $cliente, $idsms)
    {
        $data = [
            'celular' => $celular,
            'mensagem' => $mensagem,
            'clienteid' => $cliente,
            'idsms' => $idsms
        ];

        try {
            $this->save($data);
            return('Registro de SMS salvo');
        } catch (\Exception $err) {
            throw $err;
        }  
    }
}