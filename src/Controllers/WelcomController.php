<?php


namespace App\Controllers;


class WelcomController
{
    public function index()
    {
        if(isLogin()){
            return resource([
                'head'    => 'include.header',
                'content' => 'welcom',
            ])->layout('app');
        }
        redirect('/login');
    }
}