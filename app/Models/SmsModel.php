<?php namespace App\Models;

use CodeIgniter\Model;

class SmsModel extends Model
{
    protected $table            = 'sms';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['id', 'celular', 'mensagem', 'clienteid', 'idsms', 'operadora', 'criado_em', 'atualizado_em'];    
    protected $skipValidation   = true;

    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    /**
     * Identifica a operadora do SMS e trata da maneira
     * correta, salvando a informação no banco de dados.
     * 
     * @var string
     */
    public function salvarRelatorio($data_arr)
    {
        $notOp = FALSE;        
        $save_data =[];

        // Verifica a operadora que enviou a partir da variável
        // de POST idOp, que é um número inteiro.
        switch (intval($data_arr['idOp'])) {
            case 1:
                // BestVoice     
                $id = explode('"', $data_arr['status'])[3];
                
                $save_data = [
                    "celular" => $data_arr['numero'],
                    "mensagem"=> $data_arr['mensagem'],
                    "clienteid" => $data_arr['clienteid'],
                    "idsms" => $id,
                    "operadora" => 'BestVoice'
                ];              
                break;
            
            case 2:
                // Zenvia
                $save_data = [
                    "celular" => $data_arr['numero'],
                    "mensagem"=> $data_arr['mensagem'],
                    "clienteid" => $data_arr['clienteid'],
                    "idsms" => 'Zenvia',
                    "operadora" => 'Zenvia'
                ];
                break;

            default:
                $notOp = TRUE;                
                break;
        }
        
        // Se houver uma operadora registrada, salvar o registro.
        if(!$notOp)
        {            
            try {
                $this->insert($save_data);                
            } catch (\Exception $err) {
                throw $err;                
            } 
        } else {
            return "Operadora não encontrada";            
        }
                
        return "Salvamento completo";
    }
}