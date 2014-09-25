<?php
/**
 * Created by PhpStorm.
 * User: emclain
 * Date: 2/15/14
 * Time: 1:00 PM
 */
define('CONFIG_DIR', 'configs/');
if ( is_file(CONFIG_DIR.'sitedefaults.php'))
    require_once(CONFIG_DIR.'sitedefaults.php');

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
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=(isset($site_title) ? $site_title : 'Arduino Client')?></title>
    <link href="css/redmond/jquery-ui-1.10.4.custom.css" rel="stylesheet">
    <link href="css/normalize.css" rel="stylesheet">
    <script language="javascript">
        var screenName="<?=$_GET['screen']?>";
    </script>
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <link href="css/mon.css" rel="stylesheet">
    <script src="js/mon.js"></script>
</head>
<body>
<script language="javascript">
    /**
     * The below sets up an inline function to run once the page is ready and finished loading
     */
    $(document).ready(setupScreen());
    setInterval(function() { getSensors() }, 10000);
</script>
<div class="container-fluid" id="container">
    <div id="data">
        <div class="ui-widget" id="updatestatus">
            <div class="ui-state-highlight ui-corner-all" style="margin-top: 20px;" id="status">
                <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em; margin-bottom: 10px;"></span>
                <span id="statusText" class="statusText">Never Updated!</span>
            </div>
        </div>
    </div>
    <div id="data-mobile">
        <div class="page-header">
            <h1>Screen: <?=$_GET['screen']?></h1>
        </div>
        <div class="ui-widget" id="updatestatusmobile">
            <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px;" id="statusMobile">
                <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em; margin-bottom: 10px;"></span>
                <span id="statusMobileText" class="statusText">Never Updated!</span>
            </div>
        </div>
    </div>
</div>
</body>
</html>