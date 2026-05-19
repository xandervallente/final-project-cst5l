<?php

$SERVER_NAME = getenv('MYSQLHOST')     ?: getenv('DB_HOST')  ?: 'mysql.railway.internal';
$USERNAME    = getenv('MYSQLUSER')     ?: getenv('DB_USER')  ?: 'root';
$PASSWORD    = getenv('MYSQLPASSWORD') ?: getenv('DB_PASS')  ?: '';
$DB_NAME     = getenv('MYSQLDATABASE') ?: getenv('DB_NAME')  ?: 'railway';
$PORT        = (int)(getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: 3306);
