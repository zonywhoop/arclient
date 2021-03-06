/**
 * Created by emclain on 2/15/14.
 */

// Define global variable to hold our screenConfig
screenConfig=undefined;
errorText=undefined;
editPassword=undefined;

$(function() {
    // Make our updatestatus bar draggable
    $("#updatestatus").draggable({stop: function (event, ui) { handleDrag(event, ui); }});
});

function getSensors() {
    var cDate = Date();
    $.getJSON('calls.php/?cmd=fetchData&screen='+screenName, function(jd) {
        if ( 'ERROR' in jd ) {
            errorText=jd['ERROR'];
            $(".ui-state-highlight").addClass("ui-state-error").removeClass("ui-state-highlight");
            $(".ui-icon").addClass("ui-icon-alert").removeClass("ui-icon-info");
            $(".statusText").html("Server said: "+errorText);
        } else {
            $.each ( jd, function( key, val) {
                var idname = key.replace(':', '');
                var boxVal = val['v'];
                var boxSuf = val['s'];
                var boxType = val['n'];
                var boxColor = val['c'];
                var boxLocation = val['l'];
                if ( $("#"+idname).length == 0 ) {
                    // Sensor does not yet exist on the screen so create it, make it draggable, and (if it existed before) restore it's position.
                    $("#data").append('<div id="' + idname + '" class="boxes" data-toggle="popover" title="'+boxLocation+'"'+
                        ' data-html="true" data-content="<img src=\'calls.php?screen='+screenName+'&cmd=fetchGraph&sensor='+val['i']+'\'>">' + boxVal + boxSuf + '</div>');
                    // Sensor divs for Mobile view
                    $("#data-mobile").append('<div class="panel panel-default">'+
                        '<div id="'+idname+'-label" class="panel-heading label-mobile">'+boxLocation+'</div>'+
                        '<div id="'+idname+'-graph" class="panel-body" style="display:none" ><img src=\'calls.php?screen='+screenName+'&cmd=fetchGraph&sensor='+val['i']+'\'></div>'+
                        '<div id="'+idname+'-data" class="panel-body data-mobile">'+boxVal + boxSuf+'</div>'+
                        '</div>');
                    // Enable jquery-toggle for mobile div view
                    $("#"+idname+'-label').click(function() { $("#"+idname+'-graph').toggle(); });
                    // if an existing location definition exists then apply it to the new map div
                    if ( typeof screenConfig.locations[idname] !== 'undefined' ) {
                        // We have a restorable position in our screenConfig so restore it to it's last position.
                        $("#"+idname).css({
                            'position':'absolute',
                            'top':screenConfig.locations[idname].top+'px',
                            'left':screenConfig.locations[idname].left+'px',
                        });
                    }
                    // Enable popover for any newly created divs with the popover data tag
                    $('[data-toggle="popover"]').popover({
                        'placement': 'bottom'
                    });
                }
                // Sensor already exists on the screen so we are just going to update it's value.
                $("#"+idname).html(boxVal + boxSuf);
                $("#"+idname).css('background-color', boxColor);
                $("#"+idname).css("color", textColor(boxColor));
                // Update value for mobile divs
                $("#"+idname+'-data').html(boxVal+boxSuf);
                $("#"+idname+'-data').css('background-color', boxColor);
                $("#"+idname+'-data').css("color", textColor(boxColor));
                $("#"+idname+'-label').html(boxLocation);
            });
            errorText=undefined;
            if ( $("#status").hasClass("ui-state-error") ) {
                $(".ui-state-error").removeClass("ui-state-error").addClass("ui-state-highlight");
                $(".ui-icon").removeClass("ui-icon-alert").addClass("ui-icon-info");
            }
        }
        // Set our screen status to successfull update
        $(".statusText").html("Last update succeeded on <br /><small>" + cDate.toString() + "</small>");
    })
        .fail(function() {
            $(".ui-state-highlight").addClass("ui-state-error").removeClass("ui-state-highlight");
            $(".ui-icon").addClass("ui-icon-alert").removeClass("ui-icon-info");
            $(".statusText").html("Last update failed on <br /><small>" + cDate.toString() + "</small>");
        });
}

/**
 * Handle element drag events.
 * @param event
 * @param ui
 */
function handleDrag(event, ui) {
    var sensorID = ui.helper[0].id;
    var sensorLeft = ui.position.left;
    var sensorTop = ui.position.top;
    $.getJSON('calls.php/?cmd=saveLocation&screen='+screenName+'&sensor='+sensorID+'&left='+sensorLeft+'&top='+sensorTop+'&password='+editPassword);
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

/**
 * This function determins if the input color is dark or not and returns the correct text color
 * @param color
 * @returns textColorInRGB
 */
function textColor( inColor ) {
    var color = new RGBColor(inColor);
    var foreColor = "#000000";
    if (color.ok) { // 'ok' is true when the parsing was a success
        var brightness = calcBrightness(color);
        var foreColor = (brightness < 130) ? "#FFFFFF" : "#000000";
    }
    return foreColor;
}

/**
 * Calculates the brightness of the input color and returns it's value.. a color < 130 is considered dark.
 * @param color
 * @returns {number}
 */
// http://alienryderflex.com/hsp.html
function calcBrightness(color) {
    return Math.sqrt(
        color.r * color.r * .299 +
            color.g * color.g * .587 +
            color.b * color.b * .114);
}

/**
 * Toggle the display of the password entry dialog
 */
function toggleEditing() {
    if (editPassword !== undefined ) {
        editPassword = undefined;
        $("#enableEdit").removeClass('btn-danger').addClass('btn-default');
        // Disable dragging
        $(".boxes.disable").draggable('disable');
        $("#enableEdit").html("Enable Editing");
    } else {
        $("#enableEdit-form").dialog( "open" );
    }
}

/**
 * Sets the password from the dialog, closes the dialog, and then changes the button wording and color.
 * @returns {boolean}
 */
function enableEditing() {
    editPassword = $("#editPasswordEntry").val();
    $("#enableEdit-form").dialog( "close" );
    $("#enableEdit").removeClass('btn-default').addClass('btn-danger');
    $("#enableEdit").html("Disable Editing");
    // Enable jquery-ui draggable for big "map" view
    $(".boxes").draggable({containment: "parent"}, {delay: 300}, {stop: function (event, ui) { handleDrag(event, ui); }});
    return true;
}