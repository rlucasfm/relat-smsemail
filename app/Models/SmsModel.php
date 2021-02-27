<?php namespace App\Models;

use CodeIgniter\Model;

class SmsModel extends Model
{
    protected $table      = 'sms';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['id', 'celular', 'mensagem', 'clienteid', 'idsms'];
    protected $useTimestamps    = false;
    protected $skipValidation   = true;

}