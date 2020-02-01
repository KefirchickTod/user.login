<?php

namespace App\Models\User;

use App\DB;
use App\Models\Model;

class UserModels extends Model
{
    protected $fillable = [
        'password',
        'name',
        'remember_token',
        'email',
        'created_at'
    ];
    protected $table = 'user';

    public function save(){

        $isUser =  DB::getInstance()->selectSql('user', '*',"email = '{$this->fillable['email']}'");
        if(empty($isUser) && !isset($isUser[0])){

            DB::getInstance()->dynamicInsert($this->table, $this->fillable);
            $_SESSION['log'] = true;
            $_SESSION['token'] = $this->fillable['remember_token'];
            return true;
        }
        return false;

    }
}