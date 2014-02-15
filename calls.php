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
    default:
        print "ERROR: Invalid CMD\n";
        exit;
}

function sendJson($jsonToSend) {
    header('Content-Type: application/json');
    echo $jsonToSend;
}

function getScreenConfig($screenName) {
    $fileName = CONFIG_DIR.$screenName.'_screen.php';
    if ( !is_file($fileName) ) {
        print "ERROR: Screen requested does not exist! $screenName\n";
        exit;
    }
    return(file_get_contents($fileName));
}

function saveScreenConfig($screenName, $screenConfig) {
    $fileName = CONFIG_DIR.$screenName.'_screen.php';
    if ( !is_file($fileName) ) {
        print "ERROR: Screen requested does not exist!\n";
        exit;
    }
    file_put_contents($fileName, $screenConfig) or die ("Failed saving screen data");
}