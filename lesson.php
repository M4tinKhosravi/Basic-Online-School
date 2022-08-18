<?php
include("./inc/user-check.php");
include("./inc/database.php");
$db = new_db();

if(isset($_POST["lid"]))
{	
	$target_dir = "./homeworks/";
	$file_name = "C" . $_POST["lid"] . "_" . "U" . $_SESSION["uid"] . "." . strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
	$target_file = $target_dir . $file_name;
	$uploadOk = 1;
	
		
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "<b>Sorry, your file was not uploaded.</b><br>";
	} else {
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			$query = "insert into `homeworks` (`sender`, `lesson`, `file`) values (:sender, :lesson, :file);";
			$query = $db->prepare($query);
			$query->bindParam(":sender", $_SESSION["uid"]);
			$query->bindParam(":lesson", $_POST["lid"]);
			$query->bindParam(":file", $file_name);
			$query->execute();
			header("Location: ./lesson.php?id=". $_POST["lid"]);
			
		} else {
			echo "<b>Sorry, there was an error uploading your file.</b><br>";
		}
	}
}

function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}


if(!isset($_GET["id"]))
{
	header("./index.php?logout=1");
	die();
}

$query = "select `title`, `file`, `time` from `lessons` where `id` = :id and `class` = :class;";
$query = $db->prepare($query);
$query->bindParam(":id", $_GET["id"]);
$query->bindParam(":class", $_SESSION["uclass"]);
$query->execute();
$lesson = $query->fetchAll();
if(count($lesson) == 0)
	$lesson = false;
else
	$lesson = $lesson[0];

include("./inc/jdf.php");

?>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./style.css">
<script src="./jquery-3.4.1.min.js"></script>
<title>
<?php
if($lesson != false)
	echo $lesson["title"];
?>
</title>
<script>
$(document).ready(function() {
	$("#sendq").click(function() {
		$("#sendq").html("در حال ارسال...");
		$.post("./ajax/sendq.php", {sender: $("#sender").val() , lesson: $("#lessonid").val() , qtext: $("#question").val()}, function(data) {
			$("#question").val("");
			$("#question").focus();
			$("#sendq").html("ارسال...");
		});
	});
});
</script>
</head>
<body>
<div id="container" style="text-align: right;">
<h2 style="text-align: center;">
<?php
echo $lesson["title"];
?>
</h2>
<div id="nav" name="nav" style="text-align: center;">
<a href="./panel.php#lessons" class="btn">درس‌های ارائه‌شده</a>
<!--<a href="./panel.php#chpwd" class="btn">تغییر رمز</a>!-->
<a href="./index.php?logout=1" class="btn">خروج از سیستم</a>
</div>
<div id="lesson" name="lesson">
<?php
if($lesson == false)
	echo "این درس وجود ندارد!";
else
{
	echo "<h3><a href=\"./get.php?id=". $_GET["id"] ."\">دانلود فایل درس از اینجا</a></h3>";
	
	$date = $lesson["time"];
	$array = explode(' ', $date);
	list($year, $month, $day) = explode('-', $array[0]);
	list($hour, $minute, $second) = explode(':', $array[1]);
	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
	$date = jdate("l d F Y", $timestamp);
	
	$format = explode(".", $lesson["file"]);
	$format = strtoupper($format[count($format) - 1]);
	
	$fsize = human_filesize(filesize("./courses/". $lesson["file"])) . "B";
	$fsize = str_replace(array("KB", "MB", "GB"), array (" کیلوبایت", " مگابایت", " گیگابایت"), $fsize);
	$fsize = str_replace(array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0"), array("۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "۰"), $fsize);
	
	echo "این درس در تاریخ $date در قالب یک فایل $format به حجم $fsize ارائه شده.";
	
	$query = "select `id` from `homeworks` where `sender` = :sender and `lesson` = :lesson;";
	$query = $db->prepare($query);
	$query->bindParam(":sender", $_SESSION["uid"]);
	$query->bindParam(":lesson", $_GET["id"]);
	$query->execute();
	$query = $query->fetchAll();
	if(count($query) == 0)
	{	
		echo "<hr>";
		
		echo "<h3 style=\"color: #141081; font-weight: bold;\">ارسال تمرین</h3>
<div style=\"font-size: small\">
دقت کنید که برای هر درس تنها می‌توانید یک فایل تمرین ارسال کنید.<br>
اگر قصد ارسال بیش از یک فایل را دارید، آن را به صورت ZIP یا RAR آرشیو کنید.
</div><br>
<form action=\"./lesson.php\" method=\"post\" enctype=\"multipart/form-data\">
<input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\">
<input type=\"hidden\" name=\"lid\" id=\"lid\" value=\"". $_GET["id"] ."\">
<input type=\"submit\" value=\"ارسال تمرین\">
</form>";
	}

	echo "<hr>";
	
	echo "<h3 style=\"color: #141081; font-weight: bold;\">ارسال سؤال</h3>
<div style=\"font-size: small\" id=\"status\">
لطفاً هر سؤال را به صورت جداگانه ارسال کنید.
<br>
سؤالات شما به دست استاد مربوطه خواهد رسید.
</div><br>
<input type=\"text\" id=\"question\" style=\"width: 70%\">
<input type=\"hidden\" id=\"lessonid\" value=\"". $_GET["id"] ."\">
<input type=\"hidden\" id=\"sender\" value=\"". $_SESSION["uid"] ."\">
<a href=\"#\" class=\"btn\" id=\"sendq\">ارسال...</a>";

}
?>
</div>

</div>
</body>
</html>