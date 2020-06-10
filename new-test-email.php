<?php
//require_once('conn.php');

$id = '10492';
$site_id = '663980';
$Email = 'mark@hyhamilton.com';
$first_name = 'Mark';
$join_url = 'https://zoom.us/j/123456789';
$subject = 'Test Email!';
$message = 'Super basic test email. Noting to it, really.';


$headers[] = "From: honoryoga@honoryoga.com";
$headers[] = "MIME-Version: 1.0";
//$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
$headers[] = "Content-type: text/html; charset=iso-8859-1";
// die('<br>About to send email:<br>Send to:'.$Email.'<br>Subject: '.$subject.'<br>Headers: '.$headers.'<br>'.'Message: '.$message);
$mail = mail($Email,$subject,$message,implode("\r\n", $headers));
//$mail = mail($Email, $subject, 'Hello World', implode("\r\n", $headers));
//echo('Mail Sent.');

file_put_contents('mail.txt',$mail);
if ($mail) 
{
    echo('<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<h1>&nbsp;</h1>
<h1>&nbsp;</h1>
<h1>&nbsp;</h1>
<h1>Server says mail was sent.</h1>
</body>
</html>');
} else {
    echo('<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<h1>&nbsp;</h1>
<h1>&nbsp;</h1>
<h1>&nbsp;</h1>
<h1>Server says mail send failed.</h1>
</body>
</html>');

}
?>