<?php namespace App\Models;

use CodeIgniter\Model;

class SmsModel extends Model
{
    protected $table            = 'sms';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['id', 'celular', 'mensagem', 'clienteid', 'idsms', 'operadora', 'statusDesc', 'statusConf', 'id_banco', 'avaliado', 'criado_em', 'atualizado_em'];    
    protected $skipValidation   = true;

    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    /**
     * Identifica a operadora do SMS e trata da maneira
     * correta, salvando a informação no banco de dados.
     * 
     * Retorna um status informativo.
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
                    "mensagem"=> str_replace("=","",substr($data_arr['mensagem'], 0, -3)),
                    "clienteid" => $data_arr['clienteid'],
                    "idsms" => str_replace('=','', trim($id)),
                    "operadora" => 'BestVoice',
                    "statusDesc" => "",
                    "statusConf" => "",
                    "id_banco" => $data_arr['idBanco'],
                    "avaliado" => 0
                ];              
                break;
            
            case 2:
                // Zenvia
                $save_data = [
                    "celular" => $data_arr['numero'],
                    "mensagem"=> str_replace("=","",substr($data_arr['mensagem'], 0, -3)),
                    "clienteid" => $data_arr['clienteid'],
                    "idsms" => 'Zenvia',
                    "operadora" => 'Zenvia',
                    "statusDesc" => "",
                    "statusConf" => "",
                    "id_banco" => $data_arr['idBanco'],
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

     /**
     * Avalia os registros em DB que ainda não tem
     * retorno da operadora (BestVoice) e atualiza
     * os seus status.
     * 
     * Retorna logs de sucesso ou erro.
     * 
     * @var string
     */
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
                set_time_limit(300);
                $response = $curl->setBody(json_encode(['id' => $nv->idsms]))
                                ->request('POST', 'http://apishort.bestvoice.com.br/bot/consulta-sms-status.php', 
                                ['headers' => [
                                    'usuario'   => $usuario,
                                    'chave'     => $chave
                                ],
                                'connect_timeout' => 0,
                                'timeout' => 0]);         
                $sms_info = json_decode($response->getBody());

                $statusDesc = $sms_info->statusDescricao;
                $statusConf = $sms_info->confirmacaoDescricao;

                $statusConf = ($sms_info->confirmacaoDescricao == 'NAO_ENTREGUE')   ? 'NAO_ENTREGUES'   : $statusConf;
                $statusConf = ($sms_info->statusDescricao == 'DESCONHECIDO')        ? 'DESCONHECIDO'    : $statusConf;
                $statusConf = ($sms_info->statusDescricao == 'REJEITADA')           ? 'REJEITADA'       : $statusConf;

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
                array_push($err_arr, "\nERROR - ".date("Y-m-d H:i:s")." --> Houve uma falha: ".$err->getMessage());
            }            
        }

        // Casos de sucesso, alterações completas ou sem alterações a fazer.
        $return = (empty($err_arr))         ? "\nNOTICE - ".date("Y-m-d H:i:s")." --> Sucesso em todas as atualizações - BestVoice"   : $err_arr;  
        $return = (empty($naoAvaliados))    ? "\nNOTICE - ".date("Y-m-d H:i:s")." --> Nenhuma alteração a fazer - BestVoice"          : $return ;               
        return $return;
        
    }

    /**
     * Reavalia os registros em DB que já tem
     * retorno da operadora (BestVoice) e atualiza
     * os seus status.
     * 
     * Retorna logs de sucesso ou erro.
     * 
     * @var string
     */
    public function reavaliarBestVoice()
    {
        $curl = \Config\Services::curlrequest();
        $usuario = 'MOTAESILVA';
        $chave = 'B3stV0z84';

        // Encontra os registros BestVoice por avaliar
        $dataHj = new \DateTime(date('Y-m-d'));
		$dataHj->modify('+1 day');
		$dataHj = $dataHj->format('Y-m-d');

        $naoAvaliados = $this->where('avaliado', 1)
                            ->where('operadora', 'BestVoice')
                            ->where('atualizado_em <=', $dataHj)
                            ->findAll();
        $err_arr = [];
        
        // Requere da API as informações de cada registro por ID
        foreach($naoAvaliados as $nv)
        {            
            try {
                set_time_limit(300);
                $response = $curl->setBody(json_encode(['id' => $nv->idsms]))
                                ->request('POST', 'http://apishort.bestvoice.com.br/bot/consulta-sms-status.php', 
                                ['headers' => [
                                    'usuario'   => $usuario,
                                    'chave'     => $chave
                                ],
                                'connect_timeout' => 0,
                                'timeout' => 0]);         
                $sms_info = json_decode($response->getBody());

                $statusDesc = $sms_info->statusDescricao;
                $statusConf = $sms_info->confirmacaoDescricao;

                $statusConf = ($sms_info->confirmacaoDescricao == 'NAO_ENTREGUE')   ? 'NAO_ENTREGUES'   : $statusConf;
                $statusConf = ($sms_info->statusDescricao == 'DESCONHECIDO')        ? 'DESCONHECIDO'    : $statusConf;
                $statusConf = ($sms_info->statusDescricao == 'REJEITADA')           ? 'REJEITADA'       : $statusConf;

                // Atualização de INFO no DB
                $data_update = [
                    'id'         => $nv->id,
                    'statusDesc' => $statusDesc,
                    'statusConf' => $statusConf,
                    'avaliado'   => 2
                ];
                $this->save($data_update);

            } catch (\Exception $err) {
                // Registro de erro individual
                array_push($err_arr, "\nERROR - ".date("Y-m-d H:i:s")." --> Houve uma falha: ".$err->getMessage());
            }            
        }

        // Casos de sucesso, alterações completas ou sem alterações a fazer.
        $return = (empty($err_arr))         ? "\nNOTICE - ".date("Y-m-d H:i:s")." --> Sucesso em todas as atualizações - Reavaliar BestVoice"   : $err_arr;  
        $return = (empty($naoAvaliados))    ? "\nNOTICE - ".date("Y-m-d H:i:s")." --> Nenhuma alteração a fazer - Reavaliar BestVoice"          : $return ;               
        return $return;
        
    }

    /**
     * Avalia os registros em DB que ainda não tem
     * retorno da operadora (Zenvia) e atualiza
     * os seus status.
     * 
     * Retorna logs de sucesso ou erro.
     * 
     * @var string
     */
    public function avaliarZenvia()
    {        
        $curl = \Config\Services::curlrequest();
        $usuario = 'MOTAESILVA';
        $chave = 'B3stV0z84';

        // Encontra os registros Zenvia por avaliar
        $naoAvaliados = $this->where('avaliado', 0)
                            ->where('operadora', 'Zenvia')
                            ->findAll();
        $err_arr = [];
        
        // Requere da API as informações de cada registro por ID
        foreach($naoAvaliados as $nv)
        {            
            try {
                // Atualização de INFO no DB
                $data_update = [
                    'id'         => $nv->id,
                    'statusDesc' => 'ZENVIA',
                    'statusConf' => 'ZENVIA',
                    'avaliado'   => 1
                ];
                $this->save($data_update);

            } catch (\Exception $err) {
                // Registro de erro individual
                array_push($err_arr, "\nERROR - ".date("Y-m-d H:i:s")." --> Houve uma falha: ".$err->getMessage());
            }            
        }

        // Casos de sucesso, alterações completas ou sem alterações a fazer.
        $return = (empty($err_arr))         ? "\nNOTICE - ".date("Y-m-d H:i:s")." --> Sucesso em todas as atualizações - Zenvia"   : $err_arr;  
        $return = (empty($naoAvaliados))    ? "\nNOTICE - ".date("Y-m-d H:i:s")." --> Nenhuma alteração a fazer  - Zenvia"          : $return ;               
        return $return;
        
    }

    /**
     * Busca no banco de dados as informações
     * entre as dadas requisitadas
     * 
     * @var array
     */
    public function buscarDatas($dataInicio, $dataFim, $id_banco = "")
    {
        try {
            if($id_banco == "")
            {
                $data = $this->where('criado_em >=', $dataInicio)
                            ->where('criado_em <=', $dataFim)
                            ->findAll();                        
            } else {
                $data = $this->where('criado_em >=', $dataInicio)
                            ->where('criado_em <=', $dataFim)
                            ->where('id_banco =', $id_banco)
                            ->findAll();                        
            }
        } catch (\Exception $err) {
            throw $err;
        }        

        return $data;
    }
}