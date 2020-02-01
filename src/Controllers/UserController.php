<?php

namespace App\Controllers;

use App\Models\User\UserModels;

class UserController extends Controller
{
    public function index(){
        if(!isLogin()){

            return resource([
                'head' => 'include.header',
                'content' => 'user.login'
            ])->layout('app')->render();
        }
        return redirect('user');
    }

    /**
     * @throws \Exception
     */
    public function creat(){
        $post = array_map('htmlspecialchars', $_POST);

        $validation = UserModels::check($post, [
            'username',
            'email',
            'password'
        ]);


        $password = md5(strip_tags(htmlspecialchars($post['password'])));
        $name = strip_tags(htmlspecialchars($post['username']));
        $remember_token = bin2hex(random_bytes(16));
        $email = '';
        if(filter_var($post['email'], FILTER_VALIDATE_EMAIL)){
            $email = $post['email'];
        }
        $created_at = date("Y-m-d H-i-s");
        $user = new UserModels();
        $data = compact('password','name','remember_token','email','created_at');
        $save = $user->fill($data)->save();
        if($validation == false || $save == false){
            $post['save'] = $save;
            redirect('login', ['isset_user' => '1']);
            return;
        }

        redirect('user');
        return;

    }
}