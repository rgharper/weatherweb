<?php
include_once("config.php");
$result = mysqli_query($mysqli, "SELECT timestamp, temperature, stationId, YEAR(timestamp) FROM weatherstation.temperature_records ORDER BY timestamp DESC");
$result = mysqli_fetch_all($result);

$records = [];
foreach ($result as $row) {
    $records[$row[3]][$row[2]][] = [$row[0], $row[1]];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($records);
?>