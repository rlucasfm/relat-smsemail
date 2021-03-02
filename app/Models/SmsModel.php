<?php namespace App\Models;

use CodeIgniter\Model;

class SmsModel extends Model
{
    protected $table            = 'sms';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['id', 'celular', 'mensagem', 'clienteid', 'idsms', 'operadora', 'statusDesc', 'statusConf', 'avaliado', 'criado_em', 'atualizado_em'];    
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
                    "idsms" => str_replace('=','', trim($id)),
                    "operadora" => 'BestVoice',
                    "statusDesc" => "",
                    "statusConf" => "",
                    "avaliado" => 0
                ];              
                break;
            
            case 2:
                // Zenvia
                $save_data = [
                    "celular" => $data_arr['numero'],
                    "mensagem"=> $data_arr['mensagem'],
                    "clienteid" => $data_arr['clienteid'],
                    "idsms" => 'Zenvia',
                    "operadora" => 'Zenvia',
                    "statusDesc" => "",
                    "statusConf" => "",
                    "avaliado" => 0
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

    public function avaliarBestVoice()
    {
        $curl = \Config\Services::curlrequest();
        $usuario = 'MOTAESILVA';
        $chave = 'B3stV0z84';

        // Encontra os registros BestVoice por avaliar
        $naoAvaliados = $this->where('avaliado', 0)
                            ->where('operadora', 'BestVoice')
                            ->findAll();
        $err_arr = [];
        
        // Requere da API as informações de cada registro por ID
        foreach($naoAvaliados as $nv)
        {            
            try {
                $response = $curl->setBody(json_encode(['id' => $nv->idsms]))
                                ->request('POST', 'http://apishort.bestvoice.com.br/bot/consulta-sms-status.php', 
                                ['headers' => [
                                    'usuario'   => $usuario,
                                    'chave'     => $chave
                                ]]);         
                $sms_info = json_decode($response->getBody());

                $statusDesc = $sms_info->statusDescricao;
                $statusConf = $sms_info->confirmacaoDescricao;

                // Atualização de INFO no DB
                $data_update = [
                    'id'         => $nv->id,
                    'statusDesc' => $statusDesc,
                    'statusConf' => $statusConf,
                    'avaliado'   => 1
                ];
                $this->save($data_update);

            } catch (\Exception $err) {
                // Registro de erro individual
                array_push($err_arr, "\nERROR - ".date("Y-m-d h:i:s")." --> Houve uma falha: ".$err->getMessage());
            }            
        }

        $return = (empty($err_arr))         ? "\nNOTICE - ".date("Y-m-d h:i:s")." --> Sucesso em todas as atualizações"   : $err_arr;  
        $return = (empty($naoAvaliados))    ? "\nNOTICE - ".date("Y-m-d h:i:s")." --> Nenhuma alteração a fazer"          : $return ;               
        return $return;
        
    }
}