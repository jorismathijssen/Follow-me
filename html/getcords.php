<?php
include 'config.php';

$conn= new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

$query="SELECT * FROM locations";
$result=$conn->query($query);

if ($result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        echo "id: " . $row["id"]. " - Name: " . $row["name"]. " - Longitude: " . $row["longitude"]. " - Latitude: " . $row["latitude"] . "\n";
    }
} else
{
    echo "test";
}
$conn->close();
?>
