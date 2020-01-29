<?php

session_start();

date_default_timezone_set('Europe/Kiev'); //http://www.php.net/manual/en/timezones.php
mb_language('uni');
mb_internal_encoding('UTF-8');

define('DB_HOST', 'localhost');
define('DB_NAME', 'user_login');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_UTC', '+2:00');