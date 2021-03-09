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
    private $queryStr;    

    protected $db_file = "C:/Program Files (x86)/Virtua/Cobranca/Dados_Interbase/COB_DB_COBRANCA.FDB";
    protected $db_user = "SYSDBA";
    protected $db_pass = "virtuakey";
        
    protected $table;
    protected $primaryKey;

    /**
     * Utiliza a função queryExec() 
     * para inserir dados na tabela
     * a partir de um array de valores.
     * 
     * @param array
     * @return integer
     */
    public function insert($arr_val)
    {
        $pk = $this->findLastKey()+1;
        $key_arr = "($this->primaryKey, ";
        $val_arr = "($pk, ";

        foreach($arr_val as $k => $v)
        {
            $key_arr .= "$k, ";            
            $val_arr .= ($v == 'null' || $v == 'NULL') ? "$v, " : "'$v', ";
        }
        $keys = rtrim($key_arr, ', ').')';
        $values = rtrim($val_arr, ', ').')';
        
        $query = "INSERT INTO $this->table $keys VALUES $values";
        //echo $query;
        try {
            return $this->queryExec($query);
        } catch (\Exception $err) {
            throw $err;
        }
    }

     /**
     * Utiliza a função queryExec() 
     * para atualizar dados na tabela
     * a partir de um array de valores.
     * 
     * @param integer|array
     * @return integer
     */
    public function update($id, $arr_val)
    {        
        $set = '';
        foreach($arr_val as $k => $v)
        {
            $set .= ($v == 'null' || $v == 'NULL') ? "$k = $v, " : "$k = '$v', ";
        }
        $set = rtrim($set, ', ');        
        
        $query = "UPDATE $this->table SET $set WHERE $this->primaryKey = $id";
        
        try {
            return $this->queryExec($query);
        } catch (\Exception $err) {
            throw $err;
        }
    }

    /**
     * Define inteligentemente
     * a utilização da função
     * insert() ou update(), caso 
     * haja uma chave primaria dentro do array.
     * 
     * @param array
     * @return integer
     */
    public function save($arr_val)
    {
        if( array_key_exists($this->primaryKey, $arr_val) )
        {
            $id = $arr_val[$this->primaryKey];
            unset($arr_val[$this->primaryKey]);
            return $this->update($id, $arr_val);
        } else {
            return $this->insert($arr_val);
        }
    }

    /**
     * Define uma parte de query
     * com cláusula WHERE.
     * Pode ser chained.
     * 
     * @param integer|string
     * @return FirebirdModel
     */
    public function where($key, $value)
    {
        $arr_chars = ['>', '<', '=', '<=', '>=', '<>', 'is', 'not'];
        $key_comp = explode(' ', $key);
        $value = ($value == "null" || $value == "NULL") ? $value : "'$value'";

        if( empty($this->queryStr) )
        {
            if( in_array($key_comp[count($key_comp)-1], $arr_chars) )
            {
                $this->queryStr = "WHERE $key $value";
            } else {
                $this->queryStr = "WHERE $key = $value";
            }
        } else {
            if( in_array($key_comp[count($key_comp)-1], $arr_chars) )
            {
                $this->queryStr .= " AND $key $value";
            } else {
                $this->queryStr .= " AND $key = $value";
            } 
        }    
        return $this;
    }

    /**
     * Encontra os dados em um array de
     * dados.
     * 
     * @param string $col_name
     * @param array $in_array
     * @return array
     */
    public function in($col_name, $in_arr)
    {
        $in_sql = '(';
        foreach($in_arr as $in_k){
            $in_sql .= "$in_k, ";
        }
        $in_sql = rtrim($in_sql, ', ').')';
        
        if( empty($this->qureyStr) )
        {
            $this->queryStr = "WHERE $col_name IN $in_sql";
        } else {
            $this->queryStr .= "AND $col_name IN $in_sql"; 
        }  

        return $this;
    }

    /**
     * Encontra o registro com o PK
     * definido.
     * 
     * @param integer
     * @return array
     */
    public function find($id=0)
    {
        if(empty($this->queryStr))
        {
            $query = "SELECT FIRST 1 * FROM $this->table WHERE $this->primaryKey = '$id'";
        } else {
            $query = "SELECT FIRST 1 * FROM $this->table $this->queryStr AND $this->primaryKey = '$id'";
        }

        try {
            return $this->queryFetch($query);                
        } catch (\Exception $th) {
            throw $th;
        }        
    }

    /**
     * Encontra todos os registros
     * da query definida,
     * 
     * @return array
     */
    public function findAll()
    {
        $query = "SELECT * FROM $this->table $this->queryStr";        
        
        try {
            return $this->queryFetch($query);                
        } catch (\Exception $th) {
            throw $th;
        }    
    }

    /**
     * Encontra os últimos $count registros
     * da query definida .
     * 
     * @param integer
     * @return array
     */
    public function first($count=1)
    {
        $query = "SELECT FIRST $count * FROM $this->table $this->queryStr ORDER BY $this->primaryKey";

        try {
            return $this->queryFetch($query);                
        } catch (\Exception $th) {
            throw $th;
        } 
    }

    /**
     * Encontra os últimos $count registros
     * da query definida .
     * 
     * @param integer
     * @return array
     */
    public function last($count=1)
    {
        $query = "SELECT FIRST $count * FROM $this->table $this->queryStr ORDER BY $this->primaryKey DESC";

        try {
            return $this->queryFetch($query);                
        } catch (\Exception $th) {
            throw $th;
        } 
    }    

    /**
     * Abre a conexão com o banco
     * utilizando as informações de
     * configuração definido nas propriedades.
     * 
     * @return Connection
     */
    private function connect()
    {
        $this->connection = ibase_connect($this->db_file, $this->db_user, $this->db_pass) or die('Erro ao conectar:' . ibase_errmsg());        
        return $this->connection;
    }

    /**
     * Fecha a conexão com o banco
     * lidando com concorrências e 
     * garantindo integridade das transactions.
     * 
     * @return Connection
     */
    private function closeConnection()
    {
        $this->connection = ibase_close();
        return $this->connection;
    }

    /**
     * Executa consultas SQL do tipo
     * SELECT retornando um array de
     * arrays associativos com os resultados.
     * 
     * @return array 
     */
    public function queryFetch($query)
    {
        $this->connect();

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

        ibase_free_result($res);
        $this->closeConnection();
    }    

    /**
     * Executa consultas SQL do tipo
     * UPDATE, INSERT e DELETE, retornando
     * o número de linhas afetadas.
     * 
     * @return integer
     */
    public function queryExec($query)
    {
        $this->connect();

        try {
            $res = ibase_query($query);
            return ibase_affected_rows();
        } catch (\Exception $err) {
            throw $err;
            ibase_free_result($res);
            $this->closeConnection();
        }  

        ibase_free_result($res);
        $this->closeConnection();
    }

    /**
     * Encontra a última chave primária incremental
     * do banco, para inserir novos registros
     * 
     * @return array
     */
    private function findLastKey()
    {
        $this->connect();

        $query = "SELECT FIRST 1 $this->primaryKey FROM $this->table ORDER BY $this->primaryKey DESC";
        
        try {
            $res = ibase_query($query);
        } catch (\Exception $err) {
            throw $err;
            ibase_free_result($res);
            $this->closeConnection();
        }

        try {
            $fetched = ibase_fetch_row($res);            
            return $fetched[0];
        } catch(\Exception $err) {
            throw $err;
            ibase_free_result($res);
            $this->closeConnection();
        }

        ibase_free_result($res);
        $this->closeConnection();
    }
}