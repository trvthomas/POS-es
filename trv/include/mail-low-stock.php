<?php include_once "DBData.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once 'PHPMailer/Exception.php';
include_once 'PHPMailer/PHPMailer.php';
include_once 'PHPMailer/SMTP.php';

$existeError = false;
$emailSent = false;

if (isset($_POST["sendMailLowStockEmail"]) && connection_status() == 0) {
	$productsList = '';

	$sql = "SELECT nombre, precio, barcode, stock, activo FROM trvsol_products WHERE activo=1 AND stock <= 3 ORDER BY stock ASC";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$productsList .= '<tr>
	<td>' . $row["stock"] . '</td>
	<td>' . $row["nombre"] . '<br>' . $row["barcode"] . '<br>$' . number_format($row["precio"], 0, ",", ".") . '</td>
	</tr>';
		}
	}

	if ($productsList != "") {
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
			$mail->addAddress($_POST["sendMailLowStockEmail"]);

			// Content
			$mail->isHTML(true);
			$mail->Subject = 'Productos con Bajo Stock - ' . date("d-m-Y");
			$mail->Body = '<html>
			<head>
			<meta charset= "UTF-8">
			<meta name= "viewport" content= "width=device-width, initial-scale=1">
			<title>Productos con Bajo Stock</title>
			<style>
			table{
			border-collapse: collapse;
			width: 90%;
			margin: auto;
			}
			
			th, td{
			border: 2px solid #c9cdcf;
			text-align: left;
			padding: 8px;
			}
			
			th{ background-color: #c9cdcf; }
			</style>
			</head>
			<body style= "background-color: #e6e7e8;text-align: center;">
			<div style= "max-width: 640px;background-color: #fff;margin: auto;">
			<img src= "/trv/media/banner-email.webp" style= "width: 100%;" alt= "Logo TRV Solutions">
			
			<h1 style= "margin-bottom: 2px;">Productos con bajo stock detectados</h1>
			<p style= "margin-top: 2px;">Le notificamos que uno o m&aacute;s productos en su inventario est&aacute;n bajos en stock o completamente agotados, como puede ver a continuaci&oacute;n.</p>
			
			<h3>Productos agotados o casi agotados - ' . date("d-m-Y") . '</h3>
			<table>
			<tr>
			<th>Stock Actual</th>
			<th>Informaci&oacute;n del Producto</th>
			</tr>
			
			' . $productsList . '
			</table>
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
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'email_enviado' => $emailSent
);
echo json_encode($varsSend);
?>