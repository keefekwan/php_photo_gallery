<?php
require_once("/phpMailer/class.phpmailer.php");
require_once("/phpMailer/class.smtp.php");

// $to_name = "";
// $to = "";
// $subject = "Mail Test at ".strftime("%T", time());
// $message = "This is a test";
// $message = wordwrap($message, 70);
// $from_name = "";
// $from = "";

// PHP SMTP version
$mail =           new PHPMailer();

$mail->IsSMTP();
$mail->SMTPAuth   = "true";
$mail->Host 	  = "";
$mail->Port 	  = "465";
$mail->SMTPSecure = "ssl";
$mail->Username   = "";
$mail->Password   = "";
$mail->FromName   =	("");
$mail->Subject 	  = "";
$mail->Body       = "";
$mail->AddAddress("");
$mail->AddReplyTo("");

	// Email send/fail validation
	// if (!$mail->send()) {
	// 	echo "Mail error: " . $mail->ErrorInfo;
	// } else {
	// 	echo "Message has been sent";
	// }

// $mail->FromName = $from_name;
// $mail->From     = $from;
// $mail->AddAddress($to, $to_name);
// $mail->Subject  = $subject;
// $mail->Body     = $message;

$result = $mail->Send();
//echo $result ? "Sent" : "Error";

?>
