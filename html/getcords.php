<?php
include 'config.php';

$conn= new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM `locations`";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
    }
    print json_encode($rows);
} else {
    echo "Error";
}
$conn->close();
?>
