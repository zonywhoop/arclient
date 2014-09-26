<?php
/**
 * Created by PhpStorm.
 * User: emclain
 * Date: 2/15/14
 * Time: 1:01 PM
 *
 * This script is here to handle the callbacks of the client - primarily to store and retrieve the sensor
 * locations on the map and handle map specific data.
 */

define('CONFIG_DIR', 'configs/');
if ( is_file(CONFIG_DIR.'sitedefaults.php'))
    require_once(CONFIG_DIR.'sitedefaults.php');

if ( !isset($_REQUEST['cmd']) ) {
    print "ERROR: No CMD\n";
    exit;
}

if ( !isset($_REQUEST['screen']) ) {
    print "ERROR: Screen is invalid\n";
    exit;
}

switch ( $_REQUEST['cmd'] ) {
    case "saveLocation":
        if ( !isset($_GET['sensor']) || !isset($_GET['top']) || !isset($_GET['left']) ) {
            print "ERROR: locations invalid\n";
            exit;
        }
        $screenName = $_GET['screen'];
        $screenConfig = json_decode(getScreenConfig($screenName), true);
        $screenConfig['locations'][$_GET['sensor']] = array('top' => $_GET['top'], 'left' => $_GET['left']);
        saveScreenConfig($screenName, json_encode($screenConfig));
        break;
    case "get":
        $screenConfig = getScreenConfig($_GET['screen']);
        sendJson($screenConfig);
        break;
    case "fetchData":
        $screenConfig = getArrayScreenConfig($_GET['screen']);
        if ( !array_key_exists('serverURL', $screenConfig) ) {
           sendJson(array('ERROR'=>'Config missing serverURL'));
           exit;
        }
        if ( ($jsonData = file_get_contents($screenConfig['serverURL'])) === FALSE ) {
            sendJson(array('ERROR'=>'Failed to retrieve data'));
            exit;
        }
        sendJson($jsonData);
        break;
    case "fetchGraph":
        $screenConfig = getArrayScreenConfig($_GET['screen']);
        if (!array_key_exists('graphiteURL', $screenConfig)) {
            print "Error: Config missing graphiteURL<br />\n";
            exit;
        }
        print file_get_contents($screenConfig['graphiteURL'].'?target='.$_GET['sensor'].$graphitePrefs);
        break;
    default:
        print "ERROR: Invalid CMD\n";
        exit;
}

function sendJson($jsonToSend) {
    header('Content-Type: application/json');
    if ( is_array($jsonToSend) ) {
        echo json_encode($jsonToSend);
    } else {
        echo $jsonToSend;
    }
}

function getScreenConfig($screenName) {
    $fileName = CONFIG_DIR.$screenName.'_screen.php';
    if ( !is_file($fileName) ) {
        print "ERROR: Screen requested does not exist! $screenName\n";
        exit;
    }
    return(file_get_contents($fileName));
}

function getArrayScreenConfig($screenName) {
    return(json_decode(getScreenConfig($screenName), true));
}

function saveScreenConfig($screenName, $screenConfig) {
    $fileName = CONFIG_DIR.$screenName.'_screen.php';
    if ( !is_file($fileName) ) {
        print "ERROR: Screen requested does not exist!\n";
        exit;
    }
    file_put_contents($fileName, $screenConfig) or die ("Failed saving screen data");
}