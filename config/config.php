<?php 

ob_start(); // Turns on output buffering

if (!isset($_SESSION)) {
   session_start();
}

$timezone = date_default_timezone_set("Europe/Warsaw");

// $con = mysqli_connect("https://www.mkwk018.cba.pl/mysql/", "corwin1519", "Dupa123", "corwin1519");
$con = mysqli_connect("localhost", "root", "vertrigo", "socialmedia");

if (mysqli_connect_errno()) {
   echo "Failed to connect: " . mysqli_connect_errno();
}

?>