<?php
/**
 * Created by PhpStorm.
 * User: emclain
 * Date: 2/15/14
 * Time: 1:00 PM
 */
define('CONFIG_DIR', 'configs/');

function getScreens() {
    if (is_dir(CONFIG_DIR)) {
        if ($dh = opendir(CONFIG_DIR)) {
            while (($file = readdir($dh)) !== false) {
                $matches = null;
                if ( preg_match('/^([A-Za-z0-9]+)_screen.php/', $file, $matches) ) {
                    print "<a href=".$_SELF."?screen=".$matches[1].">".$matches[1]."</a><br />\n";
                }
            }
            closedir($dh);
        }
    }
}

if ( !isset($_GET['screen']) ) {
    getScreens();
    exit;
}

?>
<!DOCTYPE html>
<html ng-app="arclient">
<head>
    <title>Arduino Client</title>
    <link href="css/redmond/jquery-ui-1.10.4.custom.css" rel="stylesheet">
    <script language="javascript">
        var screenName="<?=$_GET['screen']?>";
        var screenConfig;
    </script>
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="js/mon.js"></script>
    <style>
        #data {
            position: absolute;
            left: 240px;
            top: 140px;
            z-index: -1;
            background-repeat: no-repeat;
        }
        .boxes {
            width: 50px;
            height: 15px;
            padding: 5px;
            border: 1px darkblue solid;
        }
    </style>
</head>
<body>
<script language="javascript">
    $(document).ready(setupScreen());
    setInterval(function() { getSensors() }, 10000);
</script>
<div class="ui-widget" style="width:600px;">
    <div class="ui-state-highlight ui-corner-all" style="margin-top: 20px;" id="status">
        <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;" id="statusIcon"></span>
        <span id="statusText">Never Updated!</span>
    </div>
</div>
<div id="data"></div>
</body>
</html>