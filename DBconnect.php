<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "truth_uncovered";

// Connect and select the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
 } 
//  else {
//      echo "Connection successful<br>";
// }


?>