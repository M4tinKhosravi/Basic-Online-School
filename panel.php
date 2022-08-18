<?php
include("./inc/user-check.php");
include("./inc/database.php");
include("./inc/jdf.php");
$db = new_db();
?>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./style.css">
<script src="./jquery-3.4.1.min.js"></script>
<title>سامانه‌ی آموزش از راه دور البرز</title>

</head>
<body>
<div id="container" style="text-align: right;">
<h2 style="text-align: center;">
<?php
echo $_SESSION["uname"] . " عزیز";
?>
</h2>
<div id="nav" name="nav" style="text-align: center;">
<a href="./panel.php#lessons" class="btn">درس‌های ارائه‌شده</a>
<!--<a href="./panel.php#chpwd" class="btn">تغییر رمز</a>!-->
<a href="./index.php?logout=1" class="btn">خروج از سیستم</a>
</div>
<hr>
<div id="lessons" name="lessons">
<h2>آزمون‌های پیش رو</h2>
<?php
$query = "select `id`, `name`, `start` from `quiz` where `class` = :class and `end` > current_timestamp() order by `end` asc;";
$query = $db->prepare($query);
$query->bindParam(":class", $_SESSION["uclass"]);
$query->execute();
$query = $query->fetchAll();
if(count($query) == 0)
	echo "آزمون فعالی در سیستم وجود ندارد.";
else
{
	foreach($query as $lesson)
	{
		$date = $lesson["start"];
		$array = explode(' ', $date);
		list($year, $month, $day) = explode('-', $array[0]);
		list($hour, $minute, $second) = explode(':', $array[1]);
		$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
		$date = jdate("l d F Y H:i", $timestamp);
		
		echo "<li>";
		echo "<a href=\"./quiz.php?id=". $lesson["id"] ."\">". $lesson["name"] ."</a> (شروع آزمون ". $date .")<br>";
		echo "</li>";
	}
}

?>
<hr>
<h2>درس‌های ارائه‌شده</h2>
<ul>
<?php
$query = "select `id`, `title`, `time`, (select count(`id`) from `homeworks` where `homeworks`.`sender` = :uid and `homeworks`.`lesson` = `lessons`.`id`) as 'homework' from `lessons` where `class` = :class order by `homework` asc, `time` desc;";
$query = $db->prepare($query);
$query->bindParam(":class", $_SESSION["uclass"]);
$query->bindParam(":uid", $_SESSION["uid"]);
$query->execute();
$query = $query->fetchAll();
if(count($query) == 0)
	echo "هنوز درسی برای کلاس شما ارائه نشده.";
else
{
	foreach($query as $lesson)
	{
		$date = $lesson["time"];
		$array = explode(' ', $date);
		list($year, $month, $day) = explode('-', $array[0]);
		list($hour, $minute, $second) = explode(':', $array[1]);
		$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
		$date = jdate("l d F Y", $timestamp);
		
		echo "<li>";
		echo "<a href=\"./lesson.php?id=". $lesson["id"] ."\">". $lesson["title"] ."</a> (ارائه‌شده در ". $date .")<br>";
		if($lesson["homework"] == 0)
			echo "<ul><li style=\"color: #B96C00; font-weight: bold;\">شما تمرین این درس را حل نکرده‌اید</li></ul>";
		echo "</li>";
	}
}
?>
</ul>
</div>
<!--<hr>
<div id="chpwd" name="chpwd">
<h2>تغییر رمز عبور</h2>
<center>
<table border=0>
<tr><td>رمز فعلی </td><td><input type="password" id="currentpwd"></td></tr>
<tr><td>رمز جدید </td><td><input type="password" id="newpwd"></td></tr>
<tr><td>تکرار رمز</td><td><input type="password" id="pwd2"></td></tr>
</table>
<a href="#" class="btn" id="changepwd">تغییر رمز</a>
</center>
</div>!-->
</div>
</body>
</html>