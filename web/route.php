<?php
use App\App;
App::get('/login',  \App\Controller\UserController::class, function (){
    return '';
});
App::get('/register',  \App\Controller\UserController::class, function (){
    return '';
});
App::get('/',  \App\Controller\UserController::class, function (){
    return '';
});