<?php
include("./inc/user-check.php");
include("./inc/database.php");

$db = new_db();

$query = "select `file` from `lessons` where `id` = :id and `class` = :class;";
$query = $db->prepare($query);
$query->bindParam(":id", $_GET["id"]);
$query->bindParam(":class", $_SESSION["uclass"]);
$query->execute();
$query = $query->fetchAll();

if(count($query) == 0)
{
	header ("Location: ./index.php?logout=1");
	die();
}
else
{
	$file = "./courses/". $query[0]["file"];
	$query = "insert into `download_log` (`user`, `lesson`) values (:user, :lesson);";
	$query = $db->prepare($query);
	$query->bindParam(":user", $_SESSION["uid"]);
	$query->bindParam(":lesson", $_GET["id"]);
	$query->execute();
	
	header("Location: $file");
}


?>