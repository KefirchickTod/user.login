<?php

namespace App\Models\User;

use App\Models\Model;

class UserModels extends Model
{
    public function index(){
        return resource([
            'include.header',
            'user.login'
        ]);
    }

    public function creat(){

    }
}