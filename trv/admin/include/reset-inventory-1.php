<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once $_SERVER['DOCUMENT_ROOT'] . '/trv/include/PHPMailer/Exception.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/trv/include/PHPMailer/PHPMailer.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/trv/include/PHPMailer/SMTP.php';

$existeError = false;
$emailSent = false;

if (isset($_POST["resetInventoryPass"]) && connection_status() == 0) {
	$credentialsIncorrect = true;
	$securityCode = rand(1111, 9999);

	$sql = "SELECT * FROM trvsol_users WHERE id= " . $_COOKIE[$prefixCoookie . "IdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "UsernameUser"] . "' AND password= '" . $_POST["resetInventoryPass"] . "' AND admin=1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$credentialsIncorrect = false;

		$sql4 = "UPDATE trvsol_users SET securityCode='" . $securityCode . "' WHERE id= " . $_COOKIE[$prefixCoookie . "IdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "UsernameUser"] . "'";
		$conn->query($sql4);
	} else if (isset($_COOKIE[$prefixCoookie . "TemporaryIdUser"])) {
		$sql2 = "SELECT * FROM trvsol_users WHERE id= " . $_COOKIE[$prefixCoookie . "TemporaryIdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "TemporaryUsernameUser"] . "' AND password= '" . $_POST["resetInventoryPass"] . "' AND admin=1";
		$result2 = $conn->query($sql2);

		if ($result2->num_rows > 0) {
			$credentialsIncorrect = false;

			$sql4 = "UPDATE trvsol_users SET securityCode='" . $securityCode . "' WHERE id= " . $_COOKIE[$prefixCoookie . "TemporaryIdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "TemporaryUsernameUser"] . "'";
			$conn->query($sql4);
		}
	}

	if (!$credentialsIncorrect) {
		$sql3 = "SELECT * FROM trvsol_configuration WHERE configName= 'adminEmail'";
		$result3 = $conn->query($sql3);

		if ($result3->num_rows > 0) {
			$row3 = $result3->fetch_assoc();

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
				$mail->addAddress($row3["value"]);

				// Content
				$mail->isHTML(true);
				$mail->Subject = 'Codigo de seguridad - ' . $securityCode;
				$mail->Body = '<html>
				<head>
				<meta charset= "UTF-8">
				<meta name= "viewport" content= "width=device-width, initial-scale=1">
				<title>C&oacute;digo de seguridad</title>
				</head>
				<body style= "background-color: #e6e7e8;text-align: center;">
				<div style= "max-width: 640px;background-color: #fff;margin: auto;">
				<img src= "/trv/media/banner-email.webp" style= "width: 100%;" alt= "Logo TRV Solutions">
				
				<h1 style= "margin-bottom: 2px;">C&oacute;digo de seguridad</h1>
				<p style= "margin-top: 2px;">Ha recibido una solicitud para realizar una de las siguientes acciones:
				<br>- Restablecer su inventario de productos
				<br>- Eliminar una copia de seguridad
				<br>Para continuar con la solicitud, ingrese el <b>siguiente c&oacute;digo</b> en su sistema POS.</p>
				
				<p>Código de seguridad
				<br><span style= "font-size: 42px"><b>' . $securityCode . '</b></span></p>
				<hr>
				<p style= "text-align: center;">Este correo electr&oacute;nico ha sido generado autom&aacute;ticamente por <a href= "https://www.trvsolutions.com" target= "_blank">TRV Solutions</a>.</p>
				</div>
				</body>
				</html>';

				$mail->send();
				$emailSent = true;
			} catch (Exception $e) {
				$emailSent = false;
			}
		}
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'email_enviado' => $emailSent,
	'credenciales_incorrectas' => $credentialsIncorrect
);
echo json_encode(convertJson($varsSend));
?>