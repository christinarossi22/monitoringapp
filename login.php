<?php
session_start();
require_once('config.php');
 
// database connection
$conn = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbuser,$dbpass);
 
// new data
 
$user = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
$hash = md5($password); 
 
// query
$statement = $conn->prepare("SELECT * FROM Users WHERE Email=:email AND password=:pass LIMIT 1");
$statement->bindParam(':email', $user);
$statement->bindParam(':pass', $hash);
$statement->execute();
$rows = $statement->rowCount();
if($rows > 0) {
    $_SESSION['user'] = $user;
    header("location: dashboard.php");
}
else{
	header("location: index.php");
}
