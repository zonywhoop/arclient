<?php
/**
 * Created by PhpStorm.
 * User: emclain
 * Date: 2/15/14
 * Time: 1:33 PM
 */


$screenConfig = Array(
    'name' => "Birmingham 201 Summit",
    'image' => "http://nagios.cloudopscenter.net:8080/dctemps/images/201dc1.jpg",
    'width' => '1080px',
    'height' => '720px',
    'locations' => array(),
);

print json_encode($screenConfig);