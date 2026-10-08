<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "submit";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {

    $db_connected = false;

} else {

    $db_connected = true;

    $conn->set_charset("utf8mb4");
}

?>