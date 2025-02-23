<?php include_once "DBData.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once 'PHPMailer/Exception.php';
include_once 'PHPMailer/PHPMailer.php';
include_once 'PHPMailer/SMTP.php';

$existeError = false;
$emailSent = false;

if (isset($_POST["customerBusiness"]) && isset($_POST["customerDesign"]) && isset($_POST["customerEmail"]) && connection_status() == 0) {
	$mail = new PHPMailer(true);
	try {
		$mail->SMTPDebug = SMTP::DEBUG_SERVER;
		$mail->SMTPDebug = 0;
		$mail->isSMTP();
		$mail->Host = phpmailer_host;
		$mail->SMTPAuth = true;
		$mail->Username = phpmailer_username;
		$mail->Password = phpmailer_password;
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$mail->Port = 465;

		//Recipients
		$mail->setFrom(phpmailer_username, 'Sistema POS por TRV Solutions');
		$mail->addAddress($_POST["customerEmail"]);

		// Content
		$mail->isHTML(true);
		$mail->Subject = 'Su compra en ' . $_POST["customerBusiness"];
		$mail->Body = '<html>
		<head>
		<meta charset= "UTF-8">
		<meta name= "viewport" content= "width=device-width, initial-scale=1">
		<title>Su compra en ' . $_POST["customerBusiness"] . '</title>
		<style>
		.prodListPrice{
		float: right;
		padding-right: 15px;
		}
		</style>
		</head>
		<body style= "background-color: #e6e7e8;">
		<div style= "max-width: 640px;background-color: #fff;margin: auto;padding: 10px;">
		<div>' . $_POST["customerDesign"] . '</div>
		<hr>
		<p style= "text-align: center;">Este correo electr&oacute;nico ha sido generado autom&aacute;ticamente por <a href= "https://www.trvsolutions.com" target= "_blank">TRV Solutions</a>, si tiene alguna pregunta o inquietud sobre su compra, cargo, etc., por favor contacte directamente a <b>' . $_POST["customerBusiness"] . '</b>.</p>
		</div>
		</body>
		</html>';

		$mail->send();
		$emailSent = true;
	} catch (Exception $e) {
		$emailSent = false;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'email_enviado' => $emailSent
);
echo json_encode($varsSend);
?>