<?php
session_start();
// configuration
$dbhost 	= "localhost";
$dbname		= "pdo_ret";
$dbuser		= "root";
$dbpass		= "";
 
// database connection
$conn = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbuser,$dbpass);
 
// new data
 
$user = $_POST['uname'];
$password = $_POST['pword'];
 
// query
$result = $conn->prepare("SELECT * FROM users WHERE username= :hjhjhjh AND password= :asas");
$result->bindParam(':hjhjhjh', $user);
$result->bindParam(':asas', $password);
$result->execute();
$rows = $result->fetch(PDO::FETCH_NUM);
if($rows > 0) {
header("location: home.php");
}
else{
	
}
 
?>
