<?php
include ("../inc/database.php");

@session_start();

$db = new_db();

$query = "select * from `users` where `phone` = :phone AND `password` = :password;";
$query = $db->prepare($query);
$query->bindParam(":phone", $_POST["phone"]);
$query->bindParam(":password", $_POST["password"]);
$query->execute();
$query = $query->fetchAll();
if(count($query) == 0)
	echo "wrong";
else
{
	$_SESSION["uid"] = $query[0]["id"];
	$_SESSION["uname"] = $query[0]["name"];
	$_SESSION["uclass"] = $query[0]["class"];
	
	echo "ok";
}

?>