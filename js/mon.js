/**
 * Created by emclain on 2/15/14.
 */
function getSensors() {
    var cDate = Date();
    $.getJSON('http://127.0.0.1:9000/getsensors/'+screenName, function(jd) {
        var items = [];
        $.each ( jd, function( key, val) {
            var idname = key.replace(':', '');
            if ( $("#"+idname).length == 0 ) {
                // Sensor does not yet exist on the screen so create it, make it draggable, and (if it existed before) restore it's position.
                $("#data").append('<div id="' + idname + '" class="boxes">' + val + "</div>" );
                $("#"+idname).draggable({containment: "parent"}, {delay: 300}, {stop: function (event, ui) { handleDrag(event, ui); }});
                if ( typeof screenConfig.locations[idname] !== 'undefined' ) {
                    // We have a restorable position in our screenConfig so restore it to it's last position.
                    $("#"+idname).css({'position':'relative', 'top':screenConfig.locations[idname].top+'px', 'left':screenConfig.locations[idname].left+'px'});
                }
            } else {
                // Sensor already exists on the screen so we are just going to update it's value.
                $("#"+idname).html(val);
            }
        });
        if ( $("#status").hasClass("ui-state-error") ) {
            $("#status").removeClass("ui-state-error");
            $("#status").addClass("ui-state-highlight");
            $("#statusIcon").removeClass("ui-icon-alert");
            $("#statusIcon").addClass("ui-icon-info");
        }
        $("#statusText").html("Update succeeded on " + cDate.toString());
    })
        .fail(function() {
            $("#status").addClass("ui-state-error");
            $("#status").removeClass("ui-state-highlight");
            $("#statusIcon").addClass("ui-icon-alert");
            $("#statusIcon").removeClass("ui-icon-info");
            $("#statusText").html("Update failed as of " + cDate.toString());
        });
}

function handleDrag(event, ui) {
    var sensorID = ui.helper[0].id;
    var sensorLeft = ui.position.left;
    var sensorTop = ui.position.top;
    $.getJSON('calls.php/?cmd=saveLocation&screen='+screenName+'&sensor='+sensorID+'&left='+sensorLeft+'&top='+sensorTop);
}

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
        screenConfig = jd;
    });

    getSensors();
}