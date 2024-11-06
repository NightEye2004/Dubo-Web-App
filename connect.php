<?php

$host = 'localhost';
$db_name = 'dobu_martial_arts';
$username = 'root';
$password = '';
$conn=new mysqli($host,$username,$password,$db_name);
if($conn->connect_error){
    echo "Failed to connect to MySQL: " . $conn->connect_error;
}

?>
