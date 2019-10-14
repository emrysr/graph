<?php
    /*
    All Emoncms code is released under the GNU Affero General Public License.
    See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
    */

    global $path, $embed;
    $userid = 0;
    $v = 4;
    
    if (isset($_GET['userid'])) $userid = (int) $_GET['userid'];
    
    $feedidsLH = "";
    if (isset($_GET['feedidsLH'])) $feedidsLH = $_GET['feedidsLH'];

    $feedidsRH = "";
    if (isset($_GET['feedidsRH'])) $feedidsRH = $_GET['feedidsRH'];

    $load_saved = "";
    if (isset($_GET['load'])) $load_saved = $_GET['load'];
    
    $apikey = "";
    if (isset($_GET['apikey'])) $apikey = $_GET['apikey'];
?>

<!--[if IE]><script src="<?php echo $path;?>Lib/flot/excanvas.min.js"></script><![endif]-->

<style>
    [v-cloak] {
        visibility: hidden
    }
</style>
<link href="<?php echo $path; ?>Lib/bootstrap-datetimepicker-0.0.11/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<link href="<?php echo $path; ?>Modules/graph/graph.css?v=<?php echo $v; ?>" rel="stylesheet">

<script src="<?php echo $path;?>Lib/flot/jquery.flot.min.js"></script>
<script src="<?php echo $path;?>Lib/flot/jquery.flot.time.min.js"></script>
<script src="<?php echo $path;?>Lib/flot/jquery.flot.selection.min.js"></script>
<script src="<?php echo $path;?>Lib/flot/jquery.flot.touch.min.js"></script>
<script src="<?php echo $path;?>Lib/flot/jquery.flot.togglelegend.min.js"></script>
<script src="<?php echo $path;?>Lib/flot/jquery.flot.resize.min.js"></script>
<script src="<?php echo $path; ?>Lib/flot/jquery.flot.stack.min.js"></script>
<script src="<?php echo $path;?>Modules/graph/vis.helper.js?v=<?php echo $v; ?>"></script>
<script src="<?php echo $path;?>Lib/misc/clipboard.js?v=<?php echo $v; ?>"></script>
<script src="<?php echo $path; ?>Lib/bootstrap-datetimepicker-0.0.11/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo $path; ?>Lib/vue.min.js"></script>

<h3><?php echo _('Data viewer'); ?></h3>
<div id="error" style="display:none"></div>

<div id="navigation" style="padding-bottom:5px;">
    <button class='btn graph_time' type='button' data-time='1' title="<?php echo _('Day') ?>"><?php echo _('D') ?></button>
    <button class='btn graph_time' type='button' data-time='7' title="<?php echo _('Week') ?>"><?php echo _('W') ?></button>
    <button class='btn graph_time' type='button' data-time='30' title="<?php echo _('Month') ?>"><?php echo _('M') ?></button>
    <button class='btn graph_time' type='button' data-time='365' title="<?php echo _('Year') ?>"><?php echo _('Y') ?></button>
    <button id='graph_zoomin' class='btn' title="<?php echo _('Zoom In') ?>">+</button>
    <button id='graph_zoomout' class='btn' title="<?php echo _('Zoom Out') ?>">-</button>
    <button id='graph_left' class='btn' title="<?php echo _('Earlier') ?>"><</button>
    <button id='graph_right' class='btn' title="<?php echo _('Later') ?>">></button>
    
    <div class="input-prepend input-append" style="float:right; margin-right:22px">
    <span class="add-on"><?php echo _('Show') ?></span>
    <span class="add-on"><?php echo _('missing data') ?>: <input type="checkbox" id="showmissing" style="margin-top:1px" /></span>
    <span class="add-on"><?php echo _('legend') ?>: <input type="checkbox" id="showlegend" style="margin-top:1px" /></span>
    <span class="add-on"><?php echo _('feed tag') ?>: <input type="checkbox" id="showtag" style="margin-top:1px" /></span>
    </div>
    
    <div style="clear:both"></div>
</div>

<div id="histogram-controls" style="padding-bottom:5px; display:none;">
    <div class="input-prepend input-append">
        <span class="add-on" style="width:100px"><b><?php echo _('Histogram') ?></b></span>
        <span class="add-on" style="width:75px"><?php echo _('Type') ?></span>
        <select id="histogram-type" style="width:150px">
            <option value="timeatvalue" ><?php echo _('Time at value') ?></option>
            <option value="kwhatpower" ><?php echo _('kWh at Power') ?></option>
        </select>
        <span class="add-on" style="width:75px"><?php echo _('Resolution') ?></span>
        <input id="histogram-resolution" type="text" style="width:60px"/>
    </div>
    
    <button id="histogram-back" class="btn" style="float:right"><?php echo _('Back to main view') ?></button>
</div>
<div id="legend"></div>
<div id="placeholder_bound" style="width:100%; height:400px;">
    <div id="placeholder"></div>
</div>

<div id="info" style="padding-top:20px; display:none">
    
    <div class="input-prepend input-append" style="padding-right:5px">
        <span class="add-on" style="width:50px"><?php echo _('Start') ?></span>
        <span id="datetimepicker1">
            <input id="request-start" data-format="dd/MM/yyyy hh:mm:ss" type="text" style="width:140px" />
            <span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
        </span>
    </div>
    
    <div class="input-prepend input-append" style="padding-right:5px">
        <span class="add-on" style="width:50px"><?php echo _('End') ?></span>
        <span id="datetimepicker2">
            <input id="request-end" data-format="dd/MM/yyyy hh:mm:ss" type="text" style="width:140px" />
            <span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
        </span>
    </div>
    
    <div class="input-prepend input-append" style="padding-right:5px">
        <span class="add-on" style="width:50px"><?php echo _('Type') ?></span>
        <select id="request-type" style="width:130px">
            <option value="interval"><?php echo _('Fixed Interval') ?></option>
            <option><?php echo _('Daily') ?></option>
            <option><?php echo _('Weekly') ?></option>
            <option><?php echo _('Monthly') ?></option>
            <option><?php echo _('Annual') ?></option>
        </select>
        
    </div>
    <div class="input-prepend input-append" style="padding-right:5px">
        
        <span class="fixed-interval-options">
            <input id="request-interval" type="text" style="width:60px" />
            <span class="add-on"><?php echo _('Fix') ?> <input id="request-fixinterval" type="checkbox" style="margin-top:1px" /></span>
            <span class="add-on"><?php echo _('Limit to data interval') ?> <input id="request-limitinterval" type="checkbox" style="margin-top:1px" checked></span>
        </span>
    </div>
    <div class="input-prepend input-append" style="padding-right:5px">
        <span class="add-on"><?php echo _('Timezone') ?></span>
        <span class="timezone-options">
            <select id="timezone">
                <optgroup label="<?php echo _('System') ?>">
                    <option id="browser_timezone"></option>
                    <option id="user_timezone"></option>
                </optgroup>
                <optgroup label="<?php echo _('World Timezones') ?>" id="all_timezones">
                </optgroup>
            </select>
        </span>
    </div>
    <div id="yaxis_left" class="input-append input-prepend">
        <span class="add-on" style="width:80px"><?php echo _('Y-axis').'('._('Left').')' ?>:</span>
        <span class="add-on" style="width:30px"><?php echo _('min') ?></span>
        <input id="yaxis-min" type="text" style="width:50px" value="auto">
        <span class="add-on" style="width:30px"><?php echo _('max') ?></span>
        <input id="yaxis-max" type="text" style="width:50px;" value="auto">
        <button class="btn reset-yaxis"><?php echo _('Reset') ?></button>
    </div>
    <div id="yaxis_right" class="input-append input-prepend">
        <span class="add-on" style="width:80px"><?php echo _('Y-axis').'('._('Right').')' ?>:</span>
        <span class="add-on" style="width:30px"><?php echo _('min') ?></span>
        <input id="yaxis-min2" type="text" style="width:50px" value="auto">
        <span class="add-on" style="width:30px"><?php echo _('max') ?></span>
        <input id="yaxis-max2" type="text" style="width:50px;" value="auto">
        <button class="btn reset-yaxis"><?php echo _('Reset') ?></button>
    </div>

    <button id="reload" class="btn" style="vertical-align:top"><?php echo _('Reload') ?></button>
    
    <div id="window-info" style=""></div><br>
    
    <div class="feed-options hide">
        <div class="feed-options-header">
            <div class="feed-options-show-options btn btn-default hide"><?php echo _('Show options') ?></div>
            <div class="feed-options-show-stats btn btn-default"><?php echo _('Show statistics') ?></div>
            <a href="#tables" class="feed-options-title">
                <span class="caret pull-left"></span>
                <?php echo _('Feeds in view') ?>
            </a>
        </div>

        <div id="tables" class="collapse">
            <table id="feed-options-table" class="table">
                <tr>
                    <th></th>
                    <th><?php echo _('Feed') ?></th>
                    <th><?php echo _('Type') ?></th>
                    <th><?php echo _('Color') ?></th>
                    <th><?php echo _('Fill') ?></th>
                    <th><?php echo _('Stack') ?></th>
                    <th style='text-align:center'><?php echo _('Scale') ?></th>
                    <th style='text-align:center'><?php echo _('Delta') ?></th>
                    <th style='text-align:center'><?php echo _('Average') ?></th>
                    <th><?php echo _('DP') ?></th><th style="width:120px"></th>
                </tr>
                <tbody id="feed-controls"></tbody>
            </table>
            
            <table id="feed-stats-table" class="table hide">
                <tr>
                    <th></th>
                    <th><?php echo _('Feed') ?></th>
                    <th><?php echo _('Quality') ?></th>
                    <th><?php echo _('Min') ?></th>
                    <th><?php echo _('Max') ?></th>
                    <th><?php echo _('Diff') ?></th>
                    <th><?php echo _('Mean') ?></th>
                    <th><?php echo _('Stdev') ?></th>
                    <th><?php echo _('Wh') ?></th>
                </tr>
                <tbody id="feed-stats"></tbody>
            </table>
        </div>
    </div>
    <br>
    
    <div class="input-prepend input-append">
        <button class="btn" id="showcsv" ><?php echo _('Show CSV Output') ?></button>
        <span class="add-on csvoptions"><?php echo _('Time format') ?>:</span>
        <select id="csvtimeformat" class="csvoptions">
            <option value="unix"><?php echo _('Unix timestamp') ?></option>
            <option value="seconds"><?php echo _('Seconds since start') ?></option>
            <option value="datestr"><?php echo _('Date-time string') ?></option>
        </select>
        <span class="add-on csvoptions"><?php echo _('Null values') ?>:</span>
        <select id="csvnullvalues" class="csvoptions">
            <option value="show"><?php echo _('Show') ?></option>
            <option value="lastvalue"><?php echo _('Replace with last value') ?></option>
            <option value="remove"><?php echo _('Remove whole line') ?></option>
        </select>
        <span class="add-on csvoptions"><?php echo _('Headers') ?>:</span>
        <select id="csvheaders" class="csvoptions">
            <option value="showNameTag"><?php echo _('Show name and tag') ?></option>
            <option value="showName"><?php echo _('Show name') ?></option>
            <option value="hide"><?php echo _('Hide') ?></option>
        </select>
    </div>

    <div id="download-buttons" class="csvoptions btn-group input-prepend">
        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
            <?php echo _('Download') ?>
            <span class="caret" style="border-top-color:black!important"></span>
        </a>
        <ul class="dropdown-menu">
            <li>
                <form id="download_csv" data-download>
                    <input type="hidden" data-format value="csv">
                    <input type="hidden" data-path value="<?php echo $path ?>">
                    <input type="hidden" data-action value="graph/download">
                    <input type="hidden" name="ids">
                    <input type="hidden" name="start">
                    <input type="hidden" name="end">
                    <input type="hidden" name="headers">
                    <input type="hidden" name="timeformat">
                    <input type="hidden" name="interval">
                    <input type="hidden" name="nullvalues">
                    <button class="btn btn-link csvoptions">CSV</button>
                </form>
            </li>
            <li>
                <form id="download_json" data-download>
                    <input type="hidden" data-format value="json">
                    <input type="hidden" data-path value="<?php echo $path ?>">
                    <input type="hidden" data-action value="graph/download">
                    <input type="hidden" name="ids">
                    <input type="hidden" name="start">
                    <input type="hidden" name="end">
                    <input type="hidden" name="headers">
                    <input type="hidden" name="timeformat">
                    <input type="hidden" name="interval">
                    <input type="hidden" name="nullvalues">
                    <button class="btn btn-link csvoptions">JSON</button>
                </form>
            </li>
        </ul>
    </div>

    <div class="input-append"><!-- just to match the styling of the other items -->
        <button onclick="copyToClipboardCustomMsg(document.getElementById('csv'), 'copy-csv-feedback','Copied')" class="csvoptions btn hidden" id="copy-csv" type="button"><?php echo _('Copy') ?> <i class="icon-share-alt"></i></button>
    </div>

    <span id="copy-csv-feedback" class="csvoptions"></span>
    
    <textarea id="csv" style="width:98%; height:500px; display:none; margin-top:10px"></textarea>
</div>


<script>
    var apikey = "<?php echo $apikey; ?>";
    var apikeystr = "";
    if (apikey!="") apikeystr = "&apikey="+apikey;
</script>

<script>
/**
     * @todo replace this with moment.js translated date/time strings
     * see feed and input views for example of translated dates
     * eg. moment().fromUnix(timestamp).format('ll') // format unix timestamp as per user's locale
     * @see Lib/misc/moment.min.js
     **/
    function printdate(timestamp)
    {
        var date = new Date();
        var thisyear = date.getFullYear()-2000;

        var date = new Date(timestamp);
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var year = date.getFullYear()-2000;
        var month = months[date.getMonth()];
        var day = date.getDate();

        var minutes = date.getMinutes();
        if (minutes<10) minutes = "0"+minutes;

        var datestr = date.getHours()+":"+minutes+" "+day+" "+month;
        if (thisyear!=year) datestr +=" "+year;
        return datestr;
    };
</script>
<script src="<?php echo $path;?>Modules/graph/graph.js?v=<?php echo $v; ?>"></script>
<script src="<?php echo $path;?>Lib/moment.min.js"></script>
<script>
    var _user = {
        lang : "<?php if (isset($_SESSION['lang'])) echo $_SESSION['lang']; ?>"
    }
    _locale_loaded = function (event){
        // callback when locale file loaded
        graph_reloaddraw(); // redraw xaxis with correct monthNames and dayNames
    }
</script>
<script src="<?php echo $path; ?>Lib/user_locale.js"></script>
<script src="<?php echo $path; ?>Lib/misc/gettext.js"></script>

<script>
    var session = <?php echo $session; ?>;
    var userid = <?php echo $userid; ?>;
    var feedidsLH = "<?php echo $feedidsLH; ?>";
    var feedidsRH = "<?php echo $feedidsRH; ?>";
    var load_savegraphs = "<?php echo $load_saved; ?>";

    var _lang = <?php
        $lang['Select a feed'] = _('Select a feed');
        $lang['Please select a feed from the Feeds List'] = _('Please select a feed from the Feeds List');
        $lang['Select graph'] = _('Select graph');
        echo json_encode($lang) . ';';
        echo "\n";
    ?>
    
    // Load user feeds
    if (session) {
        $.ajax({
            url: path+"feed/list.json"+apikeystr, async: false, dataType: "json",
            success: function(data_in) { feeds = data_in; }
        });
    // Load public feeds for a particular user
    } else if (userid) {
        $.ajax({
            url: path+"feed/list.json?userid="+userid, async: false, dataType: "json",
            success: function(data_in) { feeds = data_in; }
        });
    }

    // stops a part upgrade error - this change requires emoncms/emoncms repo to also be updated
    // keep button hidden if new version of clipboard.js is not available
    if (typeof copyToClipboardCustomMsg === 'function') {
        document.getElementById('copy-csv').classList.remove('hidden');
    } else {
        copyToClipboardCustomMsg = function () {}
    }
    
    if (load_savegraphs=="") {

        // Assign active feedid from URL
        var urlparts = window.location.pathname.split("graph/");
        if (urlparts.length==2) {
            var feedids = urlparts[1].split(",");
                for (var z in feedids) {
                    var feedid = parseInt(feedids[z]);
                     
                    if (feedid) {
                        var f = getfeed(feedid);
                    if (f==false) f = getfeedpublic(feedid);
                    if (f!=false) feedlist.push({id:feedid, name:f.name, tag:f.tag, yaxis:1, fill:0, scale: 1.0, delta:false, dp:1, plottype:'lines'});
                      }
                }
        }
        
        // Left hand feed ids property
        if (feedidsLH!="") {
            var feedids = feedidsLH.split(",");
                for (var z in feedids) {
                    var feedid = parseInt(feedids[z]);
                     
                    if (feedid) {
                        var f = getfeed(feedid);
                    if (f==false) f = getfeedpublic(feedid);
                    if (f!=false) feedlist.push({id:feedid, name:f.name, tag:f.tag, yaxis:1, fill:0, scale: 1.0, delta:false, dp:1, plottype:'lines'});
                      }
                }
        }

        // Right hand feed ids property
        if (feedidsRH!="") {
            var feedids = feedidsRH.split(",");
                for (var z in feedids) {
                    var feedid = parseInt(feedids[z]);
                     
                    if (feedid) {
                        var f = getfeed(feedid);
                    if (f==false) f = getfeedpublic(feedid);
                    if (f!=false) feedlist.push({id:feedid, name:f.name, tag:f.tag, yaxis:2, fill:0, scale: 1.0, delta:false, dp:1, plottype:'lines'});
                      }
                }
        }
    }

    graph_init_editor();
    
    graph_resize();
    
    var timeWindow = 3600000*24.0*7;
    var now = Math.round(+new Date * 0.001)*1000;
    view.start = now - timeWindow;
    view.end = now;
    view.calc_interval();
    
    graph_reloaddraw();

    $(function(){
        // manually add hide/show
        $('#tables').collapse()

        // trigger hide/show
        $('.feed-options-title').on('click', function (event) {
            event.preventDefault();
            event.target.querySelector('.caret').classList.toggle('open');
            $('#tables').collapse('toggle');
        })
    });

    <?php
    $translations = array(
        "Received data not in correct format. Check the logs for more details" => _("Received data not in correct format. Check the logs for more details"),
        "Request error" => _("Request error"),
        "User" => _("User"),
        "Authentication Required" => _("Authentication Required")
    );
    printf("var translations = %s;\n",json_encode($translations));
    ?>

</script>


<script>
    $(function () {
        var user = {};
        var timezones = [];
        var $timezone = $('#timezone');
        var $all_timezones = $('#all_timezones');
        var $user_timezone = $('#user_timezone');
        var $browser_timezone = $('#browser_timezone');

        $.getJSON(path + 'user/gettimezones.json')
        .done( function(result) {
            var out = '';
            for (t in result) {
                var tz = result[t];
                out += '<option value="' + tz.id + '">' + tz.id + ' (' + tz.gmt_offset_text + ')</option>';
                timezones[tz.id] = {
                    label: tz.gmt_offset_text,
                    value: tz.gmt_offset_secs
                }
            }
            $all_timezones.html(out);
        }).then( function() {
            $.getJSON(path + 'user/get.json')
            .done( function(response) {
                if(response.hasOwnProperty('success') && response.success === false) {
                    $user_timezone.text(_('User') +': ' + _('Authentication Required'));
                } else {
                    let user = response;
                    $user_timezone.val(user.timezone).text(_('User') +': ' + user.timezone +' (' + timezones[user.timezone].label + ')');
                }
            })
            
            let browser_tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            let label = timezones.hasOwnProperty(browser_tz) ? timezones[browser_tz].label : browser_tz;
            $browser_timezone.val(browser_tz).text('Browser: ' + browser_tz + ' ('+ label + ')');
        })

        $timezone.on('change', function(event) {
            graph_changeTimezone($(event.target).val());
        });

    })
</script>
