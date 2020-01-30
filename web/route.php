<?php
use App\App;
App::get('/login',  \App\Controllers\UserController::class);
App::get('/register',  \App\Controllers\UserController::class);
App::get('/',  \App\Controllers\UserController::class);

App::get('/welcom',  \App\Controllers\UserController::class);

App::run();