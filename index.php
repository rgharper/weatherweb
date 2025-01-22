<?php
include_once("config.php");
error_reporting(E_ALL); ini_set('display_errors', '1');

$result = mysqli_query($mysqli, "SELECT temperature, timestamp FROM weatherstation.weather WHERE stationId=\"Inside1\" AND timestamp >= NOW() - INTERVAL 1 YEAR AND temperature IS NOT NULL ORDER BY temperature DESC LIMIT 1");
$imax_temp = mysqli_fetch_assoc($result);

$result = mysqli_query($mysqli, "SELECT temperature, timestamp FROM weatherstation.weather WHERE stationId=\"Inside1\" AND timestamp >= NOW() - INTERVAL 1 YEAR AND temperature IS NOT NULL ORDER BY temperature ASC LIMIT 1");
$imin_temp = mysqli_fetch_assoc($result);

$result = mysqli_query($mysqli, "SELECT temperature, timestamp FROM weatherstation.weather WHERE stationId=\"Outside1\" AND timestamp >= NOW() - INTERVAL 1 YEAR AND temperature IS NOT NULL ORDER BY temperature DESC LIMIT 1");
$omax_temp = mysqli_fetch_assoc($result);

$result = mysqli_query($mysqli, "SELECT temperature, timestamp FROM weatherstation.weather WHERE stationId=\"Outside1\" AND timestamp >= NOW() - INTERVAL 1 YEAR AND temperature IS NOT NULL ORDER BY temperature ASC LIMIT 1");
$omin_temp = mysqli_fetch_assoc($result);

$result = mysqli_query($mysqli, "SELECT timestamp FROM weatherstation.weather WHERE stationId=\"Inside1\" ORDER BY timestamp DESC LIMIT 1");
$imost_recent = mysqli_fetch_assoc($result);

$result = mysqli_query($mysqli, "SELECT timestamp FROM weatherstation.weather WHERE stationId=\"Outside1\" ORDER BY timestamp DESC LIMIT 1");
$omost_recent = mysqli_fetch_assoc($result);

$result = mysqli_query($mysqli, "SELECT NOW() - INTERVAL 1 YEAR");
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
    <?php include "nav.php"?>
    <br>
    <flex-frame>
        <stat-column>
            <div class="fadebox" style="animation-delay: 0ms">
                Inside
            </div>
            <div class="fadebox" style="animation-delay: 250ms">
                <?php echo round((float)file_get_contents("$inside_api/temperature"), 2);?>&deg;C
            </div>
            <div class="fadebox" style="animation-delay: 500ms">
                <?php echo round((float)file_get_contents("$inside_api/humidity"), 2);?>%
            </div>
            <?php echo station_status("192.168.1.164", end($imost_recent))?>
        </stat-column>
        <stat-column>
            <div class="fadebox" style="animation-delay: 250ms">
                Outside
            </div>
            <div class="fadebox" style="animation-delay: 500ms">
                <?php echo round((float)file_get_contents("$outside_api/temperature"), 2);?>&deg;C
            </div>
            <div class="fadebox" style="animation-delay: 750ms">
                <?php echo round((float)file_get_contents("$outside_api/humidity"), 2);?>%
            </div>
            <?php echo station_status("192.168.1.165", end($omost_recent))?>
        </stat-column>
        <stat-column>
            <div class="fadebox" style="animation-delay: 500ms">
                Wind
            </div>
            <div class="fadebox" style="animation-delay: 750ms">
                <div id="spinnerA" class="spinner"></div>
                <div id="rpmA" style="display: inline">
                <?php echo round($wind_current[0], 1)?>
                </div> rpm
            </div>
            <div class="fadebox" style="animation-delay: 1000ms">
                <?php
                    $directions = ["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW"];
                    if ($wind_current[0]<1) {
                        echo "CALM - n/a\u{00B0}";
                    } else {
                        echo $directions[(int)$wind_current[2]];
                    }
                ?>
            </div>
            <div class="fadebox" style="animation-delay: 1250ms">
                <div id="spinnerB" class="spinner"></div>
                Gust:
                <div id="rpmB" style="display: inline">
                <?php echo round($wind_current[1], 1)?>
                </div> rpm
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
        Since <?php echo $year_ago["NOW() - INTERVAL 1 YEAR"]?>
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
</html>