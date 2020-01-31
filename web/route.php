<?php

use App\App;

App::get('/user', \App\Controllers\WelcomController::class);

App::map(['GET', 'POST'],['pattern' => '/login','model' => \App\Controllers\UserController::class]);

App::get('/register', \App\Controllers\UserController::class);

App::get('/', \App\Controllers\UserController::class);




App::run();