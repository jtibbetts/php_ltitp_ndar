<?php
/*
 *  rating - Rating: an example LTI tool provider
 *  Copyright (C) 2015  Stephen P Vickers
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along
 *  with this program; if not, write to the Free Software Foundation, Inc.,
 *  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 *  Contact: stephen@spvsoftwareproducts.com
 *
 *  Version history:
 *    1.0.00   2-Jan-13  Initial release
 *    1.0.01  17-Jan-13  Minor update
 *    1.1.00   5-Jun-13  Added Outcomes service option
 *    1.2.00  20-May-15  Changed to use class method overrides for handling LTI requests
 *                       Added support for Content-Item message
*/

/*
 * This page provides general functions to support the application.
 */

require_once('db.php');

###  Uncomment the next line to log error messages
//  error_reporting(E_ALL);

###
###  Initialise application session and database connection
###
function init(&$db, $checkSession = NULL) {

    $ok = TRUE;

// Set timezone
    if (!ini_get('date.timezone')) {
        date_default_timezone_set('UTC');
    }

// Set session cookie path
    ini_set('session.cookie_path', getAppPath());

// Open session
    session_name(SESSION_NAME);
    session_start();

    if (!is_null($checkSession) && $checkSession) {
        $ok = isset($_SESSION['consumer_key']) && isset($_SESSION['resource_id']) && isset($_SESSION['user_consumer_key']) &&
            isset($_SESSION['user_id']) && isset($_SESSION['isStudent']);
    }

    if (!$ok) {
        $_SESSION['error_message'] = 'Unable to open session.';
    } else {
// Open database connection
        $db = open_db(!$checkSession);
        $ok = $db !== FALSE;
        if (!$ok) {
            if (!is_null($checkSession) && $checkSession) {
// Display a more user-friendly error message to LTI users
                $_SESSION['error_message'] = 'Unable to open database.';
            }
        } else if (!is_null($checkSession) && !$checkSession) {
// Create database tables (if needed)
            $ok = init_db($db);  // assumes a MySQL/SQLite database is being used
            if (!$ok) {
                $_SESSION['error_message'] = 'Unable to initialise database.';
            }
        }
    }

    return $ok;

}

###
###  Get the web path to the application
###
function getAppPath() {

    $root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $dir = str_replace('\\', '/', dirname(__FILE__));

    $path = str_replace($root, '', $dir) . '/';

    return $path;

}


###
###  Get the application domain URL
###
function getHost() {

    $scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on")
        ? 'http'
        : 'https';
    $url = $scheme . '://' . $_SERVER['HTTP_HOST'];

    return $url;

}


###
###  Get the URL to the application
###
function getAppUrl() {

    $url = getHost() . getAppPath();

    return $url;

}


###
###  Return a string representation of a float value
###
function floatToStr($num) {

    $str = sprintf('%f', $num);
    $str = preg_replace('/0*$/', '', $str);
    if (substr($str, -1) == '.') {
        $str = substr($str, 0, -1);
    }

    return $str;

}


###
###  Return the value of a POST parameter
###
function postValue($name, $defaultValue = NULL) {

    $value = $defaultValue;
    if (isset($_POST[$name])) {
        $value = $_POST[$name];
    }

    return $value;

}


/**
 * Returns a string representation of a version 4 GUID, which uses random
 * numbers.There are 6 reserved bits, and the GUIDs have this format:
 *     xxxxxxxx-xxxx-4xxx-[8|9|a|b]xxx-xxxxxxxxxxxx
 * where 'x' is a hexadecimal digit, 0-9a-f.
 *
 * See http://tools.ietf.org/html/rfc4122 for more information.
 *
 * Note: This function is available on all platforms, while the
 * com_create_guid() is only available for Windows.
 *
 * Source: https://github.com/Azure/azure-sdk-for-php/issues/591
 *
 * @return string A new GUID.
 */
function getGuid() {

    return sprintf('%04x%04x-%04x-%04x-%02x%02x-%04x%04x%04x',
        mt_rand(0, 65535),
        mt_rand(0, 65535),        // 32 bits for "time_low"
        mt_rand(0, 65535),        // 16 bits for "time_mid"
        mt_rand(0, 4096) + 16384, // 16 bits for "time_hi_and_version", with
        // the most significant 4 bits being 0100
        // to indicate randomly generated version
        mt_rand(0, 64) + 128,     // 8 bits  for "clock_seq_hi", with
        // the most significant 2 bits being 10,
        // required by version 4 GUIDs.
        mt_rand(0, 256),          // 8 bits  for "clock_seq_low"
        mt_rand(0, 65535),        // 16 bits for "node 0" and "node 1"
        mt_rand(0, 65535),        // 16 bits for "node 2" and "node 3"
        mt_rand(0, 65535)         // 16 bits for "node 4" and "node 5"
    );

}

?>