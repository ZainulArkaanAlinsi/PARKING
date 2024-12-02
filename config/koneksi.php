<?php
// error_reporting(0);

$host = "localhost";
$user = "root";
$pass = "";
$db = "parkir";

$con = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    ?>
