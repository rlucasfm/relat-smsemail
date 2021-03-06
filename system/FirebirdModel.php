<?php 

/**
 * Arquivo criado por Richard Lucas F. de Mendonça
 *
 * (c) RL Soft <richardlucas@richardlucas.com>
 *
 * Licença GPLv3 - GNU License.
 */

namespace CodeIgniter;

/**
 * Class FirebirdModel
 *
 * O FirebirdModel provisiona métodos pensados em diminuir as dificuldades
 * de trabalhar com o banco de dados Interbase Firebird, aplicados a uma
 * estrutura PHP 7.3+, especificamente CodeIgniter 4
 *
 * Ele irá:
 *      - Facilitar a busca de dados
 *      - Permitir alterações e apagamentos seguros
 *      - Lida por sí com problemas de concorrência
 */

class FirebirdModel
{
    private $connection;

    protected $db_file = "C:/Program Files (x86)/Virtua/Cobranca/Dados_Interbase/COB_DB_COBRANCA.FDB";
    protected $db_user = "SYSDBA";
    protected $db_pass = "virtuakey";

    protected $table;
    protected $primaryKey;

    private function connect()
    {
        $this->connection = ibase_connect($this->db_file, $this->db_user, $this->db_pass) or die('Erro ao conectar:' . ibase_errmsg());        
        return $this->connection;
    }

    private function closeConnection()
    {
        $this->connection = ibase_close();
        return $this->connection;
    }

    public function find($id=0)
    {
        $this->connect();

        $query = "SELECT * FROM $this->table WHERE $this->primaryKey = '$id'";
        
        try {
            $res = ibase_query($query);
        } catch (\Exception $err) {
            throw $err;
            ibase_free_result($res);
            $this->closeConnection();
        }        
        
        try {
            $result_arr = array();
            $row_count = 0;

            while($row = ibase_fetch_object($res))
            {            
                $result_arr[] = $row;
                $row_count++;
            }

            if($row_count == 1)
            {
                return $result_arr[0];
            } else {
                return $result_arr;
            }
        } catch (\Exception $err) {
            throw $err;
            ibase_free_result($res);
            $this->closeConnection();
        }
                
    }
}