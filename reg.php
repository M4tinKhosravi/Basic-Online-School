<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./style.css">
<script src="./jquery-3.4.1.min.js"></script>
<title>سامانه‌ی آموزش از راه دور البرز</title>
<script>
$(document).ready(function() {
	var status = 0;
	$("#sub_btn").click(function() {
		$("#sub_btn").html("لطفاً صبر کنید...");
		if(status == 0)
		{
			$.post("./ajax/reg_check.php" , {phone : $("#phone").val()} , function(data) {
				if(data == "ok")
				{
					$("#code_tr").css("display", "table-row");
					$("#code").focus();
					$("#sub_btn").html("ثبت نام");
					status = 1;
				}
				else
				{
					$("#sub_btn").html("مرحله‌ی بعد");
					alert("شماره‌ی وارد شده تکراری است!");
					$("#phone").val("");
					$("#phone").focus();
				}
			});
		}
		if(status == 1)
		{
			$.post("./ajax/reg.php", {name: $("#name").val() , classs: $("#classs").val() , phone: $("#phone").val() , password: $("#password").val() , code: $("#code").val()}, function(data) {
				if(data == "ok")
				{
					alert("ثبت نام با موفقیت انجام شد");
					window.location.href = "./index.php";
				}
				else
				{
					alert("کد تأیید اشتباه است.");
					$("#code").val("");
					$("#code").focus();
					$("#sub_btn").html("ثبت نام");
				}
			});
		}
	});
	
});
</script>
</head>
<body>
<div id="container">
<h2>ساخت حساب کاربری جدید</h2>
<center><table border=0>
<tr><td>نام و نام خانوادگی </td><td><input type="text" id="name" /></td></tr>
<tr><td>پایه‌ی تحصیلی</td><td><select id="classs">
<option value=""></option>
<option value="10">دهم</option>
<option value="11">یازدهم</option
</select></td></tr>
<tr><td>شماره موبایل </td><td><input type="text" id="phone" /></td></tr>
<tr><td>رمز عبور </td><td><input type="password" id="password" /></td></tr>
<tr id="code_tr" style="display: none;"><td>کد تأیید </td><td><input type="text" id="code" /></td></tr>
</table></center><br>
<a href="#" id="sub_btn" class="btn">مرحله‌ی بعد</a><br><hr>
<a href="./index.php" class="btn">صفحه‌ی اصلی سایت</a>
</div>
</body>
</html>