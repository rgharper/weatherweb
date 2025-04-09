<?php
include_once("config.php");
error_reporting(E_ALL); ini_set('display_errors', '1');

$result = mysqli_query($mysqli, "SELECT timestamp FROM weatherstation.weather WHERE stationId=\"Inside1\" ORDER BY timestamp DESC LIMIT 1");
$in_last_update = strtotime(mysqli_fetch_assoc($result)['timestamp']);
$ping_result = exec("/bin/ping -W 1 -c 1 192.168.1.164", $outcome, $in_status);

$result = mysqli_query($mysqli, "SELECT timestamp FROM weatherstation.weather WHERE stationId=\"Outside1\" ORDER BY timestamp DESC LIMIT 1");
$out_last_update = strtotime(mysqli_fetch_assoc($result)['timestamp']);
$ping_result = exec("/bin/ping -W 1 -c 1 192.168.1.165", $outcome, $out_status);

$response = ['in_last_update' => $in_last_update, 'out_last_update' => $out_last_update, 'in_status' => $in_status, 'out_status' => $out_status];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
?>