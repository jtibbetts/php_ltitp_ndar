<?php

function logstr($str) {
    syslog(LOG_WARNING, $str);
}

function signed_message($function_type, $env, $db_connector) {
    class My_LTI_Tool_Provider extends LTI_Tool_Provider {

        function __construct($data_connector = '', $callbackHandler = NULL) {
            parent::__construct($data_connector, $callbackHandler);
        }

        function onLaunch() {
            echo "In onLaunch\n";
            switch ($this->function_type) {
                case 'ndar':
                    echo ('ndar');
                    break;
                case 'echo':
                    phpinfo();
            }
            echo ('done');
        }

    }

//    var_dump($db_connector);

    $tool = new My_LTI_Tool_Provider($db_connector, $function_type, $env);
//    var_dump($tool);
    $tool->handle_request();

}

function unsigned_message($function_type, $env, $db_connector) {
    switch ($function_type) {
        case 'config':
            return var_dump($env);
            break;
        case 'info':
            return phpinfo();
            break;
        default:
            echo "undefined function_type";
    }
}

# mainline

require_once('LTI_Tool_Provider.php');

openlog('php', LOG_CONS | LOG_NDELAY | LOG_PID, LOG_USER | LOG_PERROR);
//syslog(LOG_WARNING, 'starting TP');

$configs = include('config.php');

$db = new mysqli($configs['db_server'], $configs['db_user'], $configs['db_password'], $configs['db_schema']);
$db_connector = LTI_Data_Connector::getDataConnector('', $db);

$consumer = new LTI_Tool_Consumer('testing.edu', $db_connector);
$consumer->name = '12345';
$consumer->secret = 'secret';
$consumer->save();

# enable tool consumer
if (!is_null($consumer->created)) {
    $consumer->enabled = TRUE;
    $consumer->save();
}

//var_dump($consumer);

$full_path = $_SERVER['REQUEST_URI'];
$paths = explode('/', $full_path);

if (count($paths) >= 2) {
    $path = $paths[1];
} else {
    echo $full_path;
    return;
}

switch ($path) {
    case 'config':
        return unsigned_message('config', $_SERVER, $db_connector);
        break;
    case 'echo':
        return signed_message('echo', $_SERVER, $db_connector);
    case 'info':
        return unsigned_message('info', $_SERVER, $db_connector);
        break;
    case 'ndar':
        return signed_message('ndar', $_SERVER, $db_connector);
        break;
    default:
        echo "<h3>Page not found</h3>";
        http_response_code(404);
}