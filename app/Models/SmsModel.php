<?php namespace App\Models;

use CodeIgniter\Model;

class SmsModel extends Model
{
    protected $table            = 'sms';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['id', 'celular', 'mensagem', 'clienteid', 'idsms'];
    protected $useTimestamps    = false;
    protected $skipValidation   = true;

    /**
     * Identifica a operadora do SMS e trata da maneira
     * correta, salvando a informação no banco de dados.
     * 
     * @var string
     */
    public function salvarRelatorio($data_arr)
    {
        
        switch ($data_arr['idOp']) {
            case '1':
                // BestVoice
                break;
            
            case '2':
                // Zenvia
                break;

            default:
                $result = "Operadora não reconhecida";
                break;
        }

        return $result;
    }
}