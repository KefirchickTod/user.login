<?php
use App\App;
App::get('/login',  \App\Models\User\UserModels::class, function (){
    return resource([
        'include.header',
        'user.login',
    ]);
});
App::get('/register',  \App\Models\User\UserModels::class, function (){
    return '';
});
App::get('/',  \App\Models\User\UserModels::class, function (\App\Recourse $recourse, \App\HTTP\Uri $uri){
    return resource('welcom');
});

App::run();