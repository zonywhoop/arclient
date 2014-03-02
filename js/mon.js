/**
 * Created by emclain on 2/15/14.
 */

// Define global variable to hold our screenConfig
screenConfig=undefined;
errorText=undefined;

$(function() {
    // Make our updatestatus bar draggable
    $("#updatestatus").draggable({stop: function (event, ui) { handleDrag(event, ui); }});
});

function getSensors() {
    var cDate = Date();
    //$.getJSON(reportServer+'/getsensors/'+screenName, function(jd) {
    $.getJSON('calls.php/?cmd=fetchData&screen='+screenName, function(jd) {
        if ( 'ERROR' in jd ) {
            errorText=jd['ERROR'];
            $("#status").addClass("ui-state-error");
            $("#status").removeClass("ui-state-highlight");
            $("#statusIcon").addClass("ui-icon-alert");
            $("#statusIcon").removeClass("ui-icon-info");
            $("#statusText").html("Server said: "+errorText);
        } else {
            $.each ( jd, function( key, val) {
                var idname = key.replace(':', '');
                var boxVal = val['v'];
                var boxSuf = val['s'];
                var boxType = val['n'];
                var boxColor = val['c'];
                if ( $("#"+idname).length == 0 ) {
                    // Sensor does not yet exist on the screen so create it, make it draggable, and (if it existed before) restore it's position.
                    $("#data").append('<div id="' + idname + '" class="boxes">' + boxVal + boxSuf + "</div>" );
                    $("#"+idname).draggable({containment: "parent"}, {delay: 300}, {stop: function (event, ui) { handleDrag(event, ui); }});
                    if ( typeof screenConfig.locations[idname] !== 'undefined' ) {
                        // We have a restorable position in our screenConfig so restore it to it's last position.
                        $("#"+idname).css({
                            'position':'relative',
                            'top':screenConfig.locations[idname].top+'px',
                            'left':screenConfig.locations[idname].left+'px',
                            'background-color': boxColor
                        });
                    }
                } else {
                    // Sensor already exists on the screen so we are just going to update it's value.
                    $("#"+idname).html(boxVal + boxSuf);
                    $("#"+idname).css('background-color', boxColor);
                }
                errorText=undefined;
            });
            if ( $("#status").hasClass("ui-state-error") ) {
                $("#status").removeClass("ui-state-error");
                $("#status").addClass("ui-state-highlight");
                $("#statusIcon").removeClass("ui-icon-alert");
                $("#statusIcon").addClass("ui-icon-info");
            }
            $("#statusText").html("Last update succeeded on <br /><small>" + cDate.toString() + "</small>");
        }
    })
        .fail(function() {
            $("#status").addClass("ui-state-error");
            $("#status").removeClass("ui-state-highlight");
            $("#statusIcon").addClass("ui-icon-alert");
            $("#statusIcon").removeClass("ui-icon-info");
            $("#statusText").html("Last update failed on <br /><small>" + cDate.toString() + "</small>");
        });
}

function handleDrag(event, ui) {
    var sensorID = ui.helper[0].id;
    var sensorLeft = ui.position.left;
    var sensorTop = ui.position.top;
    $.getJSON('calls.php/?cmd=saveLocation&screen='+screenName+'&sensor='+sensorID+'&left='+sensorLeft+'&top='+sensorTop);
}

/**
 * This function sets up the screen and kicks off the first sensor load.
 * This function is called by $(document).ready() as soon as the browser has the page ready.
 */
function setupScreen() {
    $.getJSON('calls.php/?cmd=get&screen='+screenName, function(jd) {
        var items = [];
        $.each (jd, function (key, val) {
            if ( key == 'name'  ) {
                $("#screenName").html(val);
            } else if ( key == 'image' ) {
                $("#data").css('background-image', "url("+val+")");
            } else if ( key == 'width' ) {
                $("#data").css('width', val);
            } else if ( key == 'height' ) {
                $("#data").css('height', val);
            }
        });
        screenConfig = jd; // Store the json style data in our screenConfig global variable for later use
        // Restore updatestatus to it's last position.
        if ( "updatestatus" in screenConfig.locations ) {
            // We have a restorable position in our screenConfig so restore it to it's last position.
            $("#updatestatus").css({'position':'relative', 'top':screenConfig.locations["updatestatus"].top+'px', 'left':screenConfig.locations["updatestatus"].left+'px'});
        }

    });
    getSensors();
}