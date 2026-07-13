<?php

$conn = new mysqli("localhost", "root", "", "clinic_project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {


    // echo "Connection success"; 
}


?>