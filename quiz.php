<?php
include("./inc/user-check.php");
include("./inc/database.php");
$db = new_db();

function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

if(isset($_POST["qid"]))
{
	
	$query = "select `name`, `answers`, `questions` from `quiz` where `id` = :id";
	$query = $db->prepare($query);
	$query->bindParam(":id", $_POST["qid"]);
	$query->execute();
	$lesson = $query->fetchAll();
	if(count($lesson) == 0)
	{
		header("./index.php?logout=1");
		die();
	}
	else
		$lesson = $lesson[0];
	
	$w = 0;
	$b = 0;
	$answers = str_split($lesson["answers"]);
	$sheet = "";
	for($i = 0; $i < $lesson["questions"]; $i++)
	{
		$sheet .= $_POST["q_$i"];
		
		if($_POST["q_$i"] == "blank")
			$b++;
		elseif($_POST["q_$i"] != $answers[$i])
			$w++;
	}
	
	$correct = $lesson["questions"] - $w - $b;
	$score = round(((3* $correct - $w)/(3* $lesson["questions"])) * 100, 2);
	$query = "insert into `quiz_results` (`user`, `quiz`, `answer`, `result`) VALUES (". $_SESSION["uid"] .", ". $_POST["qid"] .", '$sheet', '$score');";
	$query = $db->prepare($query);
	$query->execute();
	echo "Log: $w, $b, $correct, $score<br>";
}

elseif(!isset($_GET["id"]))
{
	header("./index.php?logout=1");
	die();
}

else
{
	$query = "select `name`, `file`, `questions`, `end` from `quiz` where `id` = :id and `class` = :class and `start` < CURRENT_TIMESTAMP()";
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
}

?>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./style.css">
<script src="./jquery-3.4.1.min.js"></script>
<script>
$(document).ready(function() {
	$("#submit").click(function () {
		$("#answers").submit();
	});
});
</script>
<title>
<?php
if($lesson != false)
	echo $lesson["name"];
?>
</title>
</head>
<body>
<div id="container" style="text-align: right;">
<h2 style="text-align: center;">
<?php
echo $lesson["name"];
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
	echo "چنین آزمونی برای کلاس شما وجود ندارد یا هنوز زمان آن نرسیده.";
else
{
	if(isset($score))
		echo "آزمون با موفقیت به پایان رسید. درصد کسب شده توسط شما: $score<br>کلید آزمون به زودی در سایت قرار خواهد گرفت.<br>موفق باشید...";
	
	else
	{
		$fsize = human_filesize(filesize("./courses/". $lesson["file"])) . "B";
		$fsize = str_replace(array("KB", "MB", "GB"), array (" کیلوبایت", " مگابایت", " گیگابایت"), $fsize);
		$fsize = str_replace(array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0"), array("۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "۰"), $fsize);
		
		echo "<h3><a href=\"./courses/". $lesson["file"] ."\">دانلود فایل آزمون به حجم $fsize از اینجا</a></h3>";
		
		$date = $lesson["end"];
		$array = explode(' ', $date);
		list($year, $month, $day) = explode('-', $array[0]);
		list($hour, $minute, $second) = explode(':', $array[1]);
		$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
		$date = jdate("H:i", $timestamp);
		
		echo "<b>زمان آزمون تا ساعت $date<br>حتماً تا یک دقیقه پیش از این زمان پاسخ را ارسال نمایید.</b><br>";
		
		echo "<form action='quiz.php' method='post' id='answers'>";
		for($i = 0; $i < $lesson["questions"]; $i++)
		{
			echo str_replace(array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0"), array("۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "۰"), $i+1) .") ";
			echo "<input type='radio' name='q_$i' value='blank' checked>سفید";
			echo "<input type='radio' name='q_$i' value='1' style='margin-right: 2vw;'>الف";
			echo "<input type='radio' name='q_$i' value='2' style='margin-right: 2vw;'>ب";
			echo "<input type='radio' name='q_$i' value='3' style='margin-right: 2vw;'>ج";
			echo "<input type='radio' name='q_$i' value='4' style='margin-right: 2vw;'>د";
			echo "<br>";
		}
		echo "<hr><input type='hidden' name='qid' value='". $_GET["id"] ."'><a class='btn' id='submit' href='#'>ارسال پاسخ‌ها</a></form>";
	}
}
?>
</div>

</div>
</body>
</html>