<?php
include ("../inc/database.php");

@session_start();

$db = new_db();

if($_POST["code"] == $_SESSION["ver_code"])
{
	$query = "insert into `users` (`phone`, `name`, `class`, `password`) VALUES (:phone, :name, :class, :password);";
	$query = $db->prepare($query);
	$query->bindParam(":phone", $_POST["phone"]);
	$query->bindParam(":name", $_POST["name"]);
	$query->bindParam(":class", $_POST["classs"]);
	$query->bindParam(":password", $_POST["password"]);
	$query->execute();
	
	echo "ok";
}
else
	echo "wrong";
?>