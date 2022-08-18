<?php
if(isset($_GET["logout"]))
{
	@session_start();
	session_unset();
}
?>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./style.css">
<script src="./jquery-3.4.1.min.js"></script>
<title>سامانه‌ی آموزش از راه دور البرز</title>
<script>
$(document).ready(function() {
	$("#login").click(function() {
		$("#login").html("لطفاً صبر کنید...");
		$.post("./ajax/login.php", {phone: $("#phone").val() , password: $("#password").val()}, function(data) {
			if(data == "ok")
				window.location.href = "./panel.php";
			else
			{
				$("#login").html("ورود به سامانه");
				$("#phone").val("");
				$("#password").val("");
				$("#phone").focus();
				alert("شماره موبایل یا رمز اشتباه است.");
			}
		});
	});
});
</script>

</head>
<body>
<div id="container">
<h2>سامانه‌ی آموزش از راه دور البرز</h2>
<center><table border=0>
<tr><td>شماره‌ی موبایل </td><td><input type="text" id="phone" /></td></tr>
<tr><td>رمز عبور </td><td><input type="password" id="password" /></td></tr>
</table></center><br>
<a href="#" id="login" class="btn">ورود به سامانه</a><br><hr>
<a href="./reg.php" class="btn">ثبت نام در سامانه</a>
<a href="https://t.me/MatinKhosravi" class="btn">ارتباط با مسئول سیستم</a>
</div>
</body>
</html>