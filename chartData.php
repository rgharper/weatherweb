<?php
include_once('config.php');
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (count($_GET) == 2) {
  $from = $_GET['from'];
  $to = $_GET['to'];

  $query = "
  SELECT
    *
  FROM
    weatherstation.weather
  WHERE
    stationId = 'Inside1'
    AND TIMESTAMPDIFF(SECOND, '1970-01-01 00:00:00', timestamp) >= floor($from/1000)
    AND TIMESTAMPDIFF(SECOND, '1970-01-01 00:00:00', timestamp) <= floor($to/1000)
  ORDER BY
    timestamp ASC;
  ";
  // echo $query;

  $result = mysqli_query($mysqli, $query);
  $inside_data = array();
  foreach ($result as $row) {
    $inside_data[] = $row;
  }

  $query = "
  SELECT
    *
  FROM
    weatherstation.weather
  WHERE
    stationId = 'Outside1'
    AND TIMESTAMPDIFF(SECOND, '1970-01-01 00:00:00', timestamp) >= floor($from/1000)
    AND TIMESTAMPDIFF(SECOND, '1970-01-01 00:00:00', timestamp) <= floor($to/1000)
  ORDER BY
    timestamp ASC;
  ";
  $result = mysqli_query($mysqli, $query);
  $outside_data = array();
  foreach ($result as $row) {
    $outside_data[] = $row;
  }
} 
else {
  $result = mysqli_query($mysqli, 'SELECT * FROM (SELECT * FROM weatherstation.weather WHERE stationId=\'Inside1\' AND timestamp >= NOW() - INTERVAL 1 DAY) AS sub ORDER BY timestamp ASC;');
  $inside_data = array();
  foreach ($result as $row) {
    $inside_data[] = $row;
  }

  $result = mysqli_query($mysqli, 'SELECT * FROM (SELECT * FROM weatherstation.weather WHERE stationId=\'Outside1\' AND timestamp >= NOW() - INTERVAL 1 DAY) AS sub ORDER BY timestamp ASC;');
  $outside_data = array();
  foreach ($result as $row) {
    $outside_data[] = $row;
  }
}

$data = array(
  'inside' => $inside_data,
  'outside' => $outside_data,
);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);
