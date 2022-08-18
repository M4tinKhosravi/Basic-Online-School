<?php
include ("../inc/database.php");

$db = new_db();

$query = "insert into `questions` (`sender`, `lesson`, `text`) values (:sender, :lesson, :text);";
$query = $db->prepare($query);
$query->bindParam(":sender", $_POST["sender"]);
$query->bindParam(":lesson", $_POST["lesson"]);
$query->bindParam(":text", $_POST["qtext"]);
$query->execute();

?>