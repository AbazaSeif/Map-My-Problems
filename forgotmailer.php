<?php
	ob_start();
	session_start();
	require_once "class.phpmailer.php";
	require_once "class.smtp.php";

	$file = fopen("password.txt", "r") or die("Password not found");

	$key = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);

	$m = new MongoClient();
	$db = $m -> map;
	$collection = $db -> forgot;
	$collection -> insert(array('email' => $_POST["email"], 'key' => $key));


	$mail=new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$url = 'profileupdate.php';

	$body = 'Open this link to reset your password. <br><br><b>map.sivasubramanyam.me/profileupdate.php?id='.$code.'</b>';


	$mail->IsSMTP();
	$mail->Host       = 'smtp.zoho.com';

	$mail->SMTPSecure = 'tls';
	$mail->Port       = 587;
	$mail->SMTPDebug  = 1;
	$mail->SMTPAuth   = true;
	$mail->IsHTML = true;

	$mail->Username   = 'contact@sivasubramanyam.me';
	$mail->Password   = fgets($file);
	fclose($file);

	$mail->SetFrom('contact@sivasubramanyam.me', 'MapMyProblems');

	$mail->Subject    = 'Password reset link - MapMyProblems';
	$mail->MsgHTML($body);

	$mail->AddAddress($_POST["email"], $_POST["email"]);

	$mail->send();
	$_SESSION['login-error'] = 2;
	header('Location:forgot.php');


?>