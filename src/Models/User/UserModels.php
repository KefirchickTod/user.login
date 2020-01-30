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
        DB::getInstance()->dynamicInsert($this->table, $this->fillable);
        $_SESSION['log'] = true;
        $_SESSION['token'] = $this->fillable['remember_token'];

    }
}