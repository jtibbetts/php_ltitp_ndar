<?php

require_once('lib.php');


class Ndar_LTI_Tool_Provider extends LTI_Tool_Provider
{

    function onLaunch()
    {

        global $db;

        echo('IN LAUNCH');
//        $ndarBase = 'http://test.newdealartregistry.org';
//        $roleString = implode('|', $this->user->roles);
//        if (stripos($roleString, "Instructor")) {
//            $location = $ndarBase . '/adminLogin?email=ltiuser@kinexis.com&password=lticlass';
//        }
//        header("Location: " . $location);
//        die();

        exit;

    }

    function onError()
    {

        var_export($this->details);
        var_export($_POST);
        exit;

    }
}

openlog('php', LOG_CONS | LOG_NDELAY | LOG_PID, LOG_USER | LOG_PERROR);

// Initialise database
$db = NULL;

if (init($db)) {
    $data_connector = LTI_Data_Connector::getDataConnector(DB_TABLENAME_PREFIX, $db);

    $tool = new Ndar_LTI_Tool_Provider($data_connector);
    $tool->handle_request();
}

?>
