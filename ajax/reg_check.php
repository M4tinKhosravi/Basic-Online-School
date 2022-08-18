<?php
include ("../inc/database.php");

@session_start();

$db = new_db();

$query = $db -> prepare ("select `id` from `users` where `phone` = :phone;");
$query->bindParam(":phone", $_POST["phone"]);
$query->execute();
$query = $query->fetchAll();
if(count($query) == 0)
{
	$_SESSION["ver_code"] = rand(100000, 999999);
	
	$text = "کد تأیید شما در سامانه‌ی آموزش از راه دور المپیاد: ". $_SESSION["ver_code"];
	
	$url = "http://ippanel.com/class/sms/webservice/send_url.php";
	$param = array
					(
						'from'=>'10009589',
						'to' => $_POST["phone"],
						'msg' => $text,
						'uname' => 'matinkhosravi',
						'pass'=>'g0tchaw0rk!'
					);
	
	
	$ch = curl_init();
	$data = http_build_query($param);
	$getUrl = $url."?".$data;
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_URL, $getUrl);
	curl_setopt($ch, CURLOPT_TIMEOUT, 80);
	$response = curl_exec($ch);

	echo "ok";
}
else
	echo "dupl";
?>