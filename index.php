<?php
include_once("config.php");
error_reporting(E_ALL); ini_set('display_errors', '1');

$result = mysqli_query($mysqli, "SELECT temperature, timestamp FROM weatherstation.temperature_records WHERE stationId=\"Inside1\" AND YEAR(timestamp) = YEAR(NOW()) AND temperature IS NOT NULL ORDER BY temperature DESC LIMIT 1");
$imax_temp = mysqli_fetch_assoc($result);

$result = mysqli_query($mysqli, "SELECT temperature, timestamp FROM weatherstation.temperature_records WHERE stationId=\"Inside1\" AND YEAR(timestamp) = YEAR(NOW()) AND temperature IS NOT NULL ORDER BY temperature ASC LIMIT 1");
$imin_temp = mysqli_fetch_assoc($result);

$result = mysqli_query($mysqli, "SELECT temperature, timestamp FROM weatherstation.temperature_records WHERE stationId=\"Outside1\" AND YEAR(timestamp) = YEAR(NOW()) AND temperature IS NOT NULL ORDER BY temperature DESC LIMIT 1");
$omax_temp = mysqli_fetch_assoc($result);

$result = mysqli_query($mysqli, "SELECT temperature, timestamp FROM weatherstation.temperature_records WHERE stationId=\"Outside1\" AND YEAR(timestamp) = YEAR(NOW()) AND temperature IS NOT NULL ORDER BY temperature ASC LIMIT 1");
$omin_temp = mysqli_fetch_assoc($result);

$result = mysqli_query($mysqli, "SELECT YEAR(NOW())");
$year_ago = mysqli_fetch_assoc($result);

$result->close();

$mysqli->close();

$wind_current = explode(',', file_get_contents("$outside_api/wind"));

function station_status($ip, $last_seen) {
    $ping_result = exec("/bin/ping -W 1 -c 1 $ip", $outcome, $ping_status);
    if ($ping_status == 0) {
        if (time()-strtotime($last_seen) > 600) {
            $colour = "rgb(224, 195, 76)";
        }
        else {
            $colour = "rgb(126, 199, 119)";
        }
        $text = "Updated ".time()-strtotime($last_seen)."s ago";
    }
    else {
        $colour = "rgb(212, 93, 93)";
        $text = "Offline since $last_seen";
    }
    return "<div class=indicator style=\"background-color: $colour;\">$text</div>";
}
?>

<!DOCTYPE html>
<html lang='en'>
    <head>
        <title>Weather Station</title>
        <link rel="stylesheet" href="style.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js" integrity="sha512-ZwR1/gSZM3ai6vCdI+LVF1zSq/5HznD3ZSTk7kajkaj4D292NLuduDCO1c/NT8Id+jE58KYLKT7hXnbtryGmMg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
        <script src= "loadCharts.js" type="text/javascript"></script>
        <script src= "reloader.js" type="text/javascript"></script>
        <script src= "rpmVisualise.js" type="text/javascript" defer></script>
        <script src= "loadCharts.js" type="text/javascript"></script>
        <meta charset="utf-8">
        <link rel="icon" href="weather.svg" sizes="any" type="image/svg+xml">
    </head>
    <body>
    <flex-frame>
        <a href="index.php">Current</a>
        <a href="historical.html">Historical</a>
        <a href="extra.html">Extra</a>
    </flex-frame>
    <br>
    <flex-frame>
        <stat-column>
            <div class="fadebox" style="animation-delay: 0ms">
                Inside
            </div>
            <div class="fadebox" id="in_temperature" style="animation-delay: 250ms">
            </div>
            <div class="fadebox" id="in_humidity" style="animation-delay: 500ms">
            </div>
            <div id="inside_status" class="indicator"> </div>
        </stat-column>
        <stat-column>
            <div class="fadebox" style="animation-delay: 250ms">
                Outside
            </div>
            <div class="fadebox" id="out_temperature" style="animation-delay: 500ms">
            </div>
            <div class="fadebox" id="out_humidity" style="animation-delay: 750ms">
            </div>
            <div id="outside_status" class="indicator"> </div>
        </stat-column>
        <stat-column>
            <div class="fadebox" style="animation-delay: 500ms">
                Wind
            </div>
            <div class="fadebox" style="animation-delay: 750ms">
                <div id="spinnerA" class="spinner"></div>
                <div id="rpmA" style="display: inline">
                </div> rpm
                <div id="kmhA" style="display: inline">
                </div> km/h
            </div>
            <div class="fadebox" id="wind_dir" style="animation-delay: 1000ms">
            </div>
            <div class="fadebox" style="animation-delay: 1250ms">
                <div id="spinnerB" class="spinner"></div>
                Gust:
                <div id="rpmB" style="display: inline">
                </div> rpm
                <div id="kmhB" style="display: inline">
                </div> km/h
            </div>
        </stat-column>
    </flex-frame>
    <br>
        <h1 class=heading>Inside Data</h1>
    <br>
    <flex-frame>
        <canvas id="inside_th_chart"></canvas>
    </flex-frame>
    <br>
    <div class=heading>
        <h1>Outside Data</h1>
    </div>
    <br>
    <flex-frame>
        <canvas id="outside_th_chart"></canvas>
    </flex-frame>
    <br>
    <flex-frame>
        <canvas id="outside_w_chart"></canvas>
    </flex-frame>
    <br>
    <div class=heading>
        <h1>Yearly Records</h1>
        For <?php echo $year_ago["YEAR(NOW())"]?>
    </div>
    <br>
    <plain-frame>
        <h2>Inside</h2>
        <flex-box>
            <stat-column>
                <div class="fadebox" style="animation-delay: 0ms">
                    Maximum
                </div>
                <div class="fadebox" style="animation-delay: 250ms">
                    Minimum
                </div>
            </stat-column>
            <stat-column>
                <div class="fadebox" style="animation-delay: 250ms">
                    <?php echo $imax_temp["temperature"]?>&deg;C
                </div>
                <div class="fadebox" style="animation-delay: 500ms">
                    <?php echo $imin_temp["temperature"]?>&deg;C
                </div>
            </stat-column>
            <stat-column>
                <div class="fadebox" style="animation-delay: 500ms">
                    <?php echo $imax_temp["timestamp"]?>
                </div>
                <div class="fadebox" style="animation-delay: 750ms">
                    <?php echo $imin_temp["timestamp"]?>
                </div>
            </stat-column>
        </flex-box>
    </plain-frame>
    <br>
    <plain-frame>
        <h2>Outside</h2>
        <flex-box>
            <stat-column>
                <div class="fadebox" style="animation-delay: 0ms">
                    Maximum
                </div>
                <div class="fadebox" style="animation-delay: 250ms">
                    Minimum
                </div>
            </stat-column>
            <stat-column>
                <div class="fadebox" style="animation-delay: 250ms">
                    <?php echo $omax_temp["temperature"]?>&deg;C
                </div>
                <div class="fadebox" style="animation-delay: 500ms">
                    <?php echo $omin_temp["temperature"]?>&deg;C
                </div>
            </stat-column>
            <stat-column>
                <div class="fadebox" style="animation-delay: 500ms">
                    <?php echo $omax_temp["timestamp"]?>
                </div>
                <div class="fadebox" style="animation-delay: 750ms">
                    <?php echo $omin_temp["timestamp"]?>
                </div>
            </stat-column>
        </flex-box>
    </plain-frame>
<script>
    // help with updating the charts

    var interval = {
    // to keep a reference to all the intervals
    intervals : new Set(),
    
    // create another interval
    make(...args) {
        var newInterval = setInterval(...args);
        this.intervals.add(newInterval);
        return newInterval;
    },

    // clear a single interval
    clear(id) {
        this.intervals.delete(id);
        return clearInterval(id);
    },

    // clear all intervals
    clearAll() {
        for (var id of this.intervals) {
            this.clear(id);
        }
    }
};
    function stop() {
        interval.clearAll
        Chart.helpers.each(Chart.instances, function(instance){instance.destroy()})
        chart = newchart();
    }

    function start(timer) {
        interval.clearAll
        Chart.helpers.each(Chart.instances, function(instance){instance.destroy()})
        update_charts();
        interval.make(update, timer);
        // blank = false;
    }

    insideip = "192.168.1.164:1100";
    outsideip = "192.168.1.165:1100";
    // stat updater
    update();
    update_charts();
    interval.make(update, 1000);
    async function update() {
        // server must have cors enabled
        // inside temperature
        var response = await fetch("http://"+insideip+"/temperature");
        var temperature = await response.text();
        temperature = Number.parseFloat(temperature).toFixed(2);

        document.getElementById("in_temperature").innerHTML = temperature+"&deg;C";

        // inside humidity
        var response = await fetch("http://"+insideip+"/humidity");
        var humidity = await response.text();
        humidity = Number.parseFloat(humidity).toFixed(2);

        document.getElementById("in_humidity").innerHTML = humidity+"%";

        // outside temperature
        var response = await fetch("http://"+outsideip+"/temperature");
        var temperature = await response.text();
        temperature = Number.parseFloat(temperature).toFixed(2);

        document.getElementById("out_temperature").innerHTML = temperature+"&deg;C";

        // outside humidity
        var response = await fetch("http://"+outsideip+"/humidity");
        var humidity = await response.text();
        humidity = Number.parseFloat(humidity).toFixed(2);

        document.getElementById("out_humidity").innerHTML = humidity+"%";

        // wind
        var response = await fetch("/windcal/factor.text");
        var factor = Number.parseFloat(await response.text());

        var response = await fetch("http://"+outsideip+"/wind");
        var wind = await response.text();
        wind = wind.split(",");
        document.getElementById("rpmA").innerHTML = Number.parseFloat(wind[0]).toFixed(2);
        document.getElementById("rpmB").innerHTML = Number.parseFloat(wind[1]).toFixed(2);
        
        document.getElementById("kmhA").innerHTML = Number.parseFloat(wind[0]*factor).toFixed(2);
        document.getElementById("kmhB").innerHTML = Number.parseFloat(wind[1]*factor).toFixed(2);

        const directions = ["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW"];
        if (wind[0]<1) {
            document.getElementById("wind_dir").innerHTML = "CALM";
        } else {
            document.getElementById("wind_dir").innerHTML = directions[wind[2]];
        }
        update_visualiser();

        // station status
        var response = await fetch("stationStatus.php");
        var status = await response.json();

        // stupid stupid time zones
        const tzoffset = (new Date().getTimezoneOffset())*60;
        const now = ((Date.now()/1000)-tzoffset);

        in_delta = now - status["in_last_update"];
        out_delta = now - status["out_last_update"];

        // if (in_delta.toFixed(0) >= 600 || out_delta.toFixed(0) >= 600) {

        //     start(update, 1000)
        // }

        if (status["in_status"] == 0) {
            inside_status.innerHTML = "Updated "+in_delta.toFixed(0)+"s ago";
            if (in_delta > 600) {
                inside_status.style.backgroundColor = "rgb(224, 195, 76)";
            } else {
                inside_status.style.backgroundColor = "rgb(126, 199, 119)";
            }
        } else {
            inside_status.innerHTML = "Offline";
            inside_status.style.backgroundColor = "rgb(212, 93, 93)";
        }
        
        if (status["out_status"] == 0) {
            outside_status.innerHTML = "Updated "+out_delta.toFixed(0)+"s ago";
            if (out_delta > 600) {
                outside_status.style.backgroundColor = "rgb(224, 195, 76)";
            } else {
                outside_status.style.backgroundColor = "rgb(126, 199, 119)";
            }
        } else {
            outside_status.innerHTML = "Offline";
            outside_status.style.backgroundColor = "rgb(212, 93, 93)";
        }
    }
</script>
</body>
</html>