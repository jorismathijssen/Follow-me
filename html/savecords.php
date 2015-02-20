<?php
include 'config.php';

$latitude = $_POST[ 'latitude'];
$longitude = $_POST[ 'longitude'];

$conn= new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error)
{
	die("Connection failed: " . $conn->connect_error);
}

$query="INSERT INTO locations (id, longitude, latitude) VALUES (null, '$longitude', '$latitude')";

if ($conn->query($query) === TRUE)
{
	echo "success";
} else
{
	echo "Error: " . $query . "<br>" . $conn->error;
}
$conn->close();
?>
