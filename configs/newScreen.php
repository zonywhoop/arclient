<?php
/**
 * Created by PhpStorm.
 * User: emclain
 * Date: 2/15/14
 * Time: 1:33 PM
 */

/**
 * Name : The name that is displayed at the top of the client screen
 * serverURL: the url of the associated arServer - e.g. where do the collectors report in?
 * image: background image for screen
 * width: The width of the data div - size of the image
 * height: The height of the data div -size of the image
 * locations: leave blank - this is where the draggable div locations are stored.
 */

$screenConfig = Array(
    'name' => "My Screen",
    'serverURL' => 'http://localhost:8000/getsensors/screenname'
    'image' => "http://nagios.cloudopscenter.net:8080/dctemps/images/201dc1.jpg",
    'width' => '1080px',
    'height' => '720px',
    'locations' => array(),
);

print json_encode($screenConfig);