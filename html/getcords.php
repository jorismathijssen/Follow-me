<?php
include 'config.php';

$conn= new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT `locations`.`longitude`, `locations`.`latitude`, `route`.`id` FROM `locations`, `route` WHERE (`locations`.`id` = route.start_pos_id) OR (`locations`.`id` = route.end_pos_id) OR (`locations`.`id` = route.look_pos_id)";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc())
    {
        echo "id: " . $row["id"]. " - Longitude: " . $row["longitude"]. " - Latitude: " . $row["latitude"] . "\n";
    }
} else {
    echo "there are " . $result->num_rows . " points in the list";
}
$conn->close();
?>
