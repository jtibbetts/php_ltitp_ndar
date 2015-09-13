<?php

require_once('lib.php');


class Ndar_LTI_Tool_Provider extends LTI_Tool_Provider {

    function onLaunch() {

        global $db;

        $ndarBase = 'http://test.newdealartregistry.org';
        $roleString = implode('|', $this->user->roles);
        if (stripos($roleString, "Instructor")) {
            $location = $ndarBase . '/adminLogin?email=ltiuser@kinexis.com&password=lticlass';
        } else {
            $location = $ndarBase;
        }
        header("Location: " . $location);
        die();

        exit;

    }

    function onError() {

        $msg = $this->message;
        if ($this->debugMode && !empty($this->reason)) {
            $msg = $this->reason;
        }
        $title = APP_NAME;

        $this->error_output = <<< EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-language" content="EN" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>{$title}</title>
<link href="css/simple_it.css" media="screen" rel="stylesheet" type="text/css" />
<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/jquery.rateit.min.js" type="text/javascript"></script>
<link href="css/simple.css" media="screen" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Error</h1>
<p style="font-weight: bold; color: #f00;">{$msg}</p>
</body>
</html>
EOD;
    }

}

// Initialise database
$db = NULL;
if (init($db)) {
    $data_connector = LTI_Data_Connector::getDataConnector(DB_TABLENAME_PREFIX, $db);
    $tool = new Ndar_LTI_Tool_Provider($data_connector);
    $tool->handle_request();
}

?>
