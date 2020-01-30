<?php

namespace App\Controllers;

use App\ModelTrait;


abstract class Controller
{
    public function index(){

        return resource([
            'head' => 'include.header',
            'content' => 'user.login'
        ])->layout('app');
    }
}