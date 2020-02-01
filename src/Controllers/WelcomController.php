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
            ])->layout('app')->render();
        }
        redirect('login');
    }

    public function logout(){
        unset($_SESSION['log']);
        redirect('login');
    }
}