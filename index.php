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

if ( !isset($_GET['screen']) || !is_file(CONFIG_DIR.'/'.$_GET['screen']."_screen.php") ) {
    getScreens();
    exit;
}

$curScreen = $_GET['screen'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=(isset($site_title) ? $site_title : 'Arduino Client')?></title>
    <link href="css/normalize.css" rel="stylesheet">
    <script language="javascript">
        var screenName="<?php echo $curScreen; ?>";
    </script>
    <link href="css/jquery-ui.min.css" rel="stylesheet">
    <link href="css/jquery-ui.structure.min.css" rel="stylesheet">
    <link href="css/jquery-ui.theme.min.css" rel="stylesheet">
    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/rgbcolor.js"></script>
    <link href="css/mon.css" rel="stylesheet">
    <script src="js/mon.js"></script>
    <?php if (is_file("css/".$curScreen.".css") ) { echo "<link href=\"css/".$curScreen.".css\" rel=\"stylesheet\">\n"; } ?>
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
        <div id="enableEdit-form" title="Enable Editing">
            <p class="dialogNotes">Changes will not be saved unless the server accepts the entered passwrd.</p>
            <form>
                <fieldset>
                    <label for="password">Password</label>
                    <input type="password" name="editPasswordEntry" id="editPasswordEntry" value="xxxxxxx" class="text ui-widget-content ui-corner-all">
                    <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
                </fieldset>
            </form>
        </div>
        <div id="editDiv"><button type="button" id="enableEdit" class="btn btn-primary" onclick="javascript:toggleEditing();">Enable Editing</button></div>
        <script>
            dialog = $( "#enableEdit-form" ).dialog({
                autoOpen: false,
                height: 200,
                width: 350,
                modal: true,
                buttons: {
                    "Enable Editing": enableEditing,
                    Cancel: function() {
                        dialog.dialog( "close" );
                    }
                },
                close: function() {
                    form[ 0 ].reset();
                }
            });
            form = dialog.find( "form" ).on( "submit", function( event ) {
                event.preventDefault();
                enableEditing();
            });
        </script>
    </div>
    <div id="data-mobile">
        <div class="page-header">
            <h1>Screen: <?php echo $curScreen; ?></h1>
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