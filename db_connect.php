<?php

$conn = mysqli_connect('localhost','root','','register1');

//check if connection
if ($conn->connect_error) {
    die("Connection Failed: ". $conn->connect_error);
 }
 

?>