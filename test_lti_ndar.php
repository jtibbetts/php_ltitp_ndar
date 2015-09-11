<?php

require_once('LTI_Tool_Provider.php');

openlog('php', LOG_CONS | LOG_NDELAY | LOG_PID, LOG_USER | LOG_PERROR);
syslog(LOG_WARNING, 'starting TP');

$configs = include('config.php');

echo var_dump($configs);
echo var_dump($configs['db_server']);

$db = new mysqli($configs['db_server'], $configs['db_user'], $configs['db_password'], $configs['db_schema']);
$db_connector = LTI_Data_Connector::getDataConnector('', $db);

$consumer = new LTI_Tool_Consumer('testing.edu', $db_connector);
$consumer->name = 'Testing';
$consumer->secret = 'ThisIsASecret!';
$consumer->save();

echo var_dump($consumer);