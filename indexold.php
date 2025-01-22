<?php
include_once("config.php");

$result = mysqli_query($mysqli, "SELECT * FROM (SELECT * FROM weatherstation.weather WHERE stationId=\"Inside1\" AND timestamp >= NOW() - INTERVAL 1 DAY ORDER BY timestamp DESC LIMIT 1440) AS sub ORDER BY timestamp ASC;");
$inside_data = array();
foreach ($result as $row) {
  $inside_data[] = $row;
}

//free memory associated with result
$result->close();

$result = mysqli_query($mysqli, "SELECT * FROM (SELECT * FROM weatherstation.weather WHERE stationId=\"Outside1\" AND timestamp >= NOW() - INTERVAL 1 DAY ORDER BY timestamp DESC LIMIT 1440) AS sub ORDER BY timestamp ASC;");
$outside_data = array();
foreach ($result as $row) {
  $outside_data[] = $row;
}

//free memory associated with result
$result->close();

//close connection
$mysqli->close();

$itemperature = array();
$ihumidity = array();
$itime = array();

foreach ($inside_data as $point) {
    $itemperature[] = $point["temperature"];
    $ihumidity[] = $point["humidity"];
    $itime[] = $point["timestamp"];
}

$otemperature = array();
$ohumidity = array();
$owinddirection = array();
$owindspeed = array();
$ogust = array();
$otime = array();

foreach ($outside_data as $point) {
    $otemperature[] = $point["temperature"];
    $ohumidity[] = $point["humidity"];
    $owinddirection[] = $point["winddirection"];
    $owindspeed[] = $point["windspeed"];
    $ogust[] = $point["windgust"];
    $otime[] = $point["timestamp"];
}

// $day = array("temperature"=>array(), "humidity"=>array(), "windspeed"=>array(), "winddirection"=>array(), "rainfall"=>array(), "time"=>array());

// while($row = mysqli_fetch_assoc($result)) {
//     $day["temperature"][] = $t["temperature"];
//     $day["humidity"][] = $t["humidity"];
//     $day["windspeed"][] = $t["windspeed"];
//     $day["winddirection"][] = $t["winddirection"];
//     $day["rainfall"][] = $t["rainfall"];
//     $day["time"][] = $t["time"];
// }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Weather Station</title>
        <link rel="stylesheet" href="style.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>   
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
        <meta http-equiv="refresh" content="60">
    </head>

    <div class=frame>
        <div class=row>
            <div class=column>
                <div class="fadebox" style="animation-delay: 0ms">
                    Inside
                </div>
                <div class="fadebox" style="animation-delay: 250ms">
                    <?php
                        echo end($itemperature)
                    ?>&#176C
                </div>
                <div class="fadebox" style="animation-delay: 500ms">
                    <?php
                        echo end($ihumidity)
                    ?>%
                </div>
            </div>
            <div class=column>
                <div class="fadebox" style="animation-delay: 250ms">
                    Outside
                </div>
                <div class="fadebox" style="animation-delay: 500ms">
                    <?php
                        echo end($otemperature)
                    ?>&#176C
                </div>
                <div class="fadebox" style="animation-delay: 750ms">
                    <?php
                        echo end($ohumidity)
                    ?>%
                </div>
            </div>
            <div class="column">
                <div class="fadebox" style="animation-delay: 500ms">
                    Wind
                </div>
                <div class="fadebox" style="animation-delay: 750ms">
                    <?php
                        echo end($owindspeed)
                    ?> rpm
                </div>
                <div class="fadebox" style="animation-delay: 1000ms">
                    <?php
                        if (is_null(end($owinddirection))) {
                            echo "n/a";
                        } else {
                            echo end($owinddirection);
                        }
                    ?>&#176
                </div>
                <div class="fadebox" style="animation-delay: 750ms">
                    Gust:
                    <?php
                        echo end($ogust)
                    ?> rpm
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class=frame>
        <canvas id="inside_th_chart"></canvas>
    </div>
    <br>
    <div class=frame>
        <canvas id="outside_th_chart"></canvas>
    </div>
    <br>
    <div class=frame>
        <canvas id="outside_w_chart"></canvas>
    </div>
</html>
<script>
        var chartdata = {
        labels: <?php echo json_encode($itime)?>,
        datasets : [
                {
            label: 'Temperature (\u{00B0}C)',
            yAxisID: 'default',
            borderColor: 'rgb(220, 161, 161)',
            fill: false,
            data: <?php echo json_encode($itemperature)?>
                },
                {
            label: 'Humidity (%RH)',
            yAxisID: 'percentage',
            borderColor: 'rgb(161, 161, 200)',
            fill: false,
            data: <?php echo json_encode($ihumidity)?>
                }
            ]
        };
        const inside_th_chart = new Chart("inside_th_chart", {
            type: "line",
            data: chartdata,
            options: {
                scales: {
                    yAxes: [
                        {id: 'percentage', type: 'linear', position:'right', min:0, max:100, ticks:{min:0, max:100}},
                        {id: 'default', type: 'linear', position:'left'},
                    ],
                    xAxes:[
                        {id: 'x', type: "time", axis: "x"}
                    ]
                },
                elements: {
                    point: {
                        radius: 0
                    }
                }
            }
        });

        var chartdata = {
        labels: <?php echo json_encode($otime)?>,
        datasets : [
                {
            label: 'Temperature (\u{00B0}C)',
            yAxisID: 'default',
            borderColor: 'rgb(220, 161, 161)',
            fill: false,
            data: <?php echo json_encode($otemperature)?>
                },
                {
            label: 'Humidity (%RH)',
            yAxisID: 'percentage',
            borderColor: 'rgb(161, 161, 200)',
            fill: false,
            data: <?php echo json_encode($ohumidity)?>
                }
            ]
        };
        const outside_th_chart = new Chart("outside_th_chart", {
            type: "line",
            data: chartdata,
            options: {
                scales: {
                    yAxes: [
                        {id: 'percentage', type: 'linear', position:'right', min:0, max:100, ticks:{min:0, max:100}},
                        {id: 'default', type: 'linear', position:'left'},
                    ],
                    xAxes:[
                        {id: 'x', type: "time", axis: "x"}
                    ]
                },
                elements: {
                    point: {
                        radius: 0
                    }
                }
            }
        });

        var chartdata = {
        labels: <?php echo json_encode($otime)?>,
        datasets : [
                {
            label: 'Speed (rpm)',
            yAxisID: 'default',
            borderColor: 'rgb(161, 220, 185)',
            fill: false,
            data: <?php echo json_encode($owindspeed)?>
                },
                {
            label: 'Direction (\u{00B0}T)',
            yAxisID: 'default1',
            borderColor: 'rgb(161, 191, 220)',
            fill: false,
            data: <?php echo json_encode($owinddirection)?>
                },
                {
            label: 'Gust (rpm)',
            yAxisID: 'default2',
            borderColor: 'rgb(220, 161, 161)',
            visible: false,
            fill: false,
            data: <?php echo json_encode($ogust)?>
                }
            ]
        };
        const outside_w_chart = new Chart("outside_w_chart", {
            type: "line",
            data: chartdata,
            options: {
                scales: {
                    yAxes: [
                        {id: 'default', type: 'linear', position:'right'},
                        {id: 'default1', type: 'linear', position:'left', min:0, max:360, ticks:{min:0, max:360}},
                    ],
                    xAxes:[
                        {id: 'x', type: "time", axis: "x"}
                    ]
                },
                elements: {
                    point: {
                        radius: 0
                    }
                }
            }
        });
</script>