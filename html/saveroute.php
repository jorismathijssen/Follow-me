<?php
include 'config.php';


$looklat = $_POST[ 'looklat'];
$looklng = $_POST[ 'looklng'];
$startlat = $_POST[ 'startlat'];
$startlng = $_POST[ 'startlng'];
$stoplat = $_POST[ 'stoplat'];
$stoplng = $_POST[ 'stoplng'];

$conn= new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

$query="INSERT INTO locations (id, name, longitude, latitude) VALUES (null, 'look', '$looklng', '$looklat')";
$query2="INSERT INTO locations (id, name, longitude, latitude) VALUES (null, 'start'', '$startlng', '$startlat')";
$query3="INSERT INTO locations (id, name, longitude, latitude) VALUES (null, 'stop', '$stoplng', '$stoplat')";

if ($conn->query($query) === TRUE && $conn->query($query2) === TRUE && $conn->query($query3) === TRUE) {
	$value1 = (($conn->insert_id) - 1);
	$value2 = ($conn->insert_id);
	$value3 = (($conn->insert_id) - 2);

	$query4="INSERT INTO route (id, start_pos_id, end_pos_id, look_pos_id) VALUES (null, '$value1', '$value2', '$value3')";
    if($conn->query($query4) === TRUE) {
      echo "success";
  }
  else {
   echo "Error 1: " . $query4 . "<br>" . $conn->error;
}
} else {
    echo "Error 2: " . $query3 . "<br>" . $conn->error;
}

$conn->close();
?>
