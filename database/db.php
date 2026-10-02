<?php
$conn = mysqli_connect("localhost","root","","university_laptop_scheme");

if(!$conn){
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>
