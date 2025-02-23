<?php include_once "DBData.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once 'PHPMailer/Exception.php';
include_once 'PHPMailer/PHPMailer.php';
include_once 'PHPMailer/SMTP.php';

$emailSent = false;

//Ventas
$days = date('t', mktime(0, 0, 0, date("m"), 1, date("Y")));
$arrayMonth = array();
for ($x = 1; $x <= $days; ++$x) {
	$pushArray = array(
		'date' => date("Y-m") . '-' . $x,
		'entryDate' => "",
		'closedDate' => "",
		'seller' => "",
		'initialCash' => "",
		'numberSales' => 0,
		'cashSales' => 0,
		'cardSales' => 0,
		'otherSales' => 0,
		'reports' => "",
		'goal' => 0
	);
	$arrayMonth[] = $pushArray;
}

$sql = "SELECT * FROM trvsol_stats WHERE mes=" . date("m") . " AND year=" . date("Y");
$result = $conn->query($sql);
if ($result->num_rows == 0) {
	$sql2 = "INSERT INTO trvsol_stats (mes, year, estadisticas)
	VALUES ('" . date("m") . "', '" . date("Y") . "', '" . json_encode($arrayMonth) . "')";
	$conn->query($sql2);
}

//Productos
$sql3 = "SELECT * FROM trvsol_products";
$result3 = $conn->query($sql3);
if ($result3->num_rows > 0) {
	while ($row3 = $result3->fetch_assoc()) {
		$arrayProducts = array();

		for ($x = 1; $x <= 12; ++$x) {
			$days = date('t', mktime(0, 0, 0, $x, 1, date("Y")));
			for ($x2 = 1; $x2 <= $days; ++$x2) {
				$pushArray2 = array(
					'month' => $x,
					'date' => date("Y") . '-' . $x . '-' . $x2,
					'quantitiesSold' => 0
				);
				$arrayProducts[] = $pushArray2;
			}
		}

		$sql4 = "SELECT * FROM trvsol_products_stats WHERE year=" . date("Y") . " AND productId=" . $row3["id"];
		$result4 = $conn->query($sql4);
		if ($result4->num_rows == 0) {
			$sql5 = "INSERT INTO trvsol_products_stats (year, productId, estadisticas)
	VALUES ('" . date("Y") . "', '" . $row3["id"] . "', '" . json_encode($arrayProducts) . "')";
			$conn->query($sql5);
		}
	}
}

//Usuarios
$sql6 = "SELECT * FROM trvsol_users";
$result6 = $conn->query($sql6);
if ($result6->num_rows > 0) {
	while ($row6 = $result6->fetch_assoc()) {
		$arrayUsers = array();

		for ($x = 1; $x <= 12; ++$x) {
			$days = date('t', mktime(0, 0, 0, $x, 1, date("Y")));
			for ($x2 = 1; $x2 <= $days; ++$x2) {
				$pushArray3 = array(
					'month' => $x,
					'date' => date("Y") . '-' . $x . '-' . $x2,
					'cashSales' => 0,
					'cardSales' => 0,
					'otherSales' => 0
				);
				$arrayUsers[] = $pushArray3;
			}
		}

		$sql7 = "SELECT * FROM trvsol_users_stats WHERE year=" . date("Y") . " AND userId=" . $row6["id"];
		$result7 = $conn->query($sql7);
		if ($result7->num_rows == 0) {
			$sql8 = "INSERT INTO trvsol_users_stats (year, userId, estadisticas)
	VALUES ('" . date("Y") . "', '" . $row6["id"] . "', '" . json_encode($arrayUsers) . "')";
			$conn->query($sql8);
		}
	}
}

//Bonos y vouchers
$sql9 = "SELECT * FROM trvsol_vouchers";
$result9 = $conn->query($sql9);
if ($result9->num_rows > 0) {
	while ($row9 = $result9->fetch_assoc()) {
		$arrayVouchers = array();

		for ($x = 1; $x <= 12; ++$x) {
			$days = date('t', mktime(0, 0, 0, $x, 1, date("Y")));
			for ($x2 = 1; $x2 <= $days; ++$x2) {
				$pushArray4 = array(
					'month' => $x,
					'date' => date("Y") . '-' . $x . '-' . $x2,
					'uses' => 0
				);
				$arrayVouchers[] = $pushArray4;
			}
		}

		$sql10 = "SELECT * FROM trvsol_vouchers_stats WHERE year=" . date("Y") . " AND voucherId=" . $row9["id"];
		$result10 = $conn->query($sql10);
		if ($result10->num_rows == 0) {
			$sql11 = "INSERT INTO trvsol_vouchers_stats (year, voucherId, estadisticas)
	VALUES ('" . date("Y") . "', '" . $row9["id"] . "', '" . json_encode($arrayVouchers) . "')";
			$conn->query($sql11);
		}
	}
}

//Eliminar comprobantes de más de 4 meses
$sql20 = "SELECT * FROM trvsol_configuration WHERE configName= 'saveInvoicesForMonths'";
$result20 = $conn->query($sql20);
if ($result20->num_rows > 0) {
	$row20 = $result20->fetch_assoc();

	$sql12 = "SELECT * FROM trvsol_invoices WHERE fecha < '" . date("Y-m-d", strtotime("-" . $row20["value"] . " months")) . "'";
	$result12 = $conn->query($sql12);
	if ($result12->num_rows > 0) {
		$row12 = $result12->fetch_assoc();

		$sql13 = "DELETE FROM trvsol_invoices WHERE id=" . $row12["id"];
		$conn->query($sql13);
	}
}

//Eliminar backups de mas de 6 meses
$directoryBackups = $_SERVER['DOCUMENT_ROOT'] . "/trv/include/backups/";
$scanBackups = scandir($directoryBackups);
if (isset($scanBackups[2])) {
	for ($x = 2; $x < count($scanBackups); ++$x) {
		$backupConvertToDate = substr(substr($scanBackups[$x], 0, -4), 7, 10);

		if (date("Y-m-d", strtotime($backupConvertToDate)) < date("Y-m-d", strtotime("-6 months"))) {
			unlink($directoryBackups . $scanBackups[$x]);
		}
	}
}

//Enviar reporte mensual
$justEndedMonth = date("m", strtotime("-32 days"));
$compareWithMonth = date("m", strtotime("-2 months"));

$actualYear = date("Y", strtotime("-32 days"));
$compareWithYear = date("Y", strtotime("-2 months"));

$sql14 = "SELECT * FROM trvsol_stats WHERE mes=" . $justEndedMonth . " AND year=" . $actualYear . " AND reportSent=0";
$result14 = $conn->query($sql14);
if ($result14->num_rows > 0 && connection_status() == 0) {
	$row14 = $result14->fetch_assoc();

	$months = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
	$printingTemplate = "Hubo un error al generar el reporte, por favor cont&aacute;ctenos.";

	$emailTips = array(
		"Ingrese al panel de administraci&oacute;n en la secci&oacute;n <b>Usuarios</b> y oprima el bot&oacute;n de estad&iacute;sticas para ver <b>cu&aacute;ntas ventas realiz&oacute;</b> el usuario seleccionado en un intervalo de tiempo espec&iacute;fico.",
		"Si desea configurar un descuento por porcentaje puede hacerlo f&aacute;cilmente en la secci&oacute;n <b>Bonos y vouchers</b> en el panel administrativo.",
		"¿Recibe otro medio de pago <b>diferente a efectivo y tarjeta</b>? Configure un <b>m&eacute;todo de pago personalizado</b> en la configuraci&oacute;n del sistema, as&iacute; podr&aacute; llevar un control de las ventas de una forma m&aacute;s f&aacute;cil.",
		"Oprima el bot&oacute;n <b>F1 de su teclado</b> para crear una nueva venta r&aacute;pidamente.",
		"Su sistema POS cuenta con un <b>control de inventario seguro</b> que resta unidades autom&aacute;ticamente y agrega mercanc&iacute;a con tan solo dos clics.",
		"Agilice el proceso de venta y <b>configure los c&oacute;digos de barras</b> de cada uno de los productos, luego imprima una plantilla de c&oacute;digos en la secci&oacute;n <b>Generador de c&oacute;digos</b>.",
		"Personalice el <b>ticket de compra</b> y notifique a sus clientes sobre promociones, eventos o pol&iacute;ticas de cambios y garant&iacute;a. Para hacerlo ingrese a la secci&oacute;n <b>Dise&ntilde;o comprobantes</b> en el panel de administraci&oacute;n.",
		"Su Sistema POS le ofrece <b>estad&iacute;sticas detalladas</b> sobre las ventas diarias, mensuales, anuales, por usuario, las unidades vendidas de cada uno de los productos y las redenciones de los bonos de descuento. Ingrese a la secci&oacute;n <b>Estad&iacute;sticas</b> en el panel administrativo para ver estos reportes.",
		"Como administrador podr&aacute; ver todos los <b>comprobantes de venta</b> de los &uacute;ltimos 4 meses. Podr&aacute; imprimir un duplicado, enviar el comprobante por e-mail, cancelar la venta o descargar el ticket como PDF."
	);
	$randNum = rand(1, count($emailTips)) - 1;
	$emailTip = $emailTips[$randNum];

	$justEndedName = $months[$justEndedMonth - 1];
	$justEndedNumberSales = 0;
	$justEndedTotalSales = 0;
	$justEndedCashSales = 0;
	$justEndedCardSales = 0;
	$justEndedOtherSales = 0;

	$compareWithName = $months[$compareWithMonth - 1];
	$compareWithNumberSales = 0;
	$compareWithTotalSales = 0;
	$compareWithCashSales = 0;
	$compareWithCardSales = 0;
	$compareWithOtherSales = 0;

	$bestSellingProds = "No es posible calcular esta estad&iacute;stica";
	$bestSellingProdsCloud = "No es posible calcular esta estadística";
	$bestSellingProdTop = 1;

	$sql15 = "SELECT * FROM trvsol_stats WHERE mes=" . $compareWithMonth . " AND year=" . $compareWithYear;
	$result15 = $conn->query($sql15);
	if ($result15->num_rows > 0) {
		$row15 = $result15->fetch_assoc();

		$decoded2 = json_decode($row15["estadisticas"], true);
		for ($x2 = 0; $x2 < count($decoded2); ++$x2) {
			$compareWithNumberSales += $decoded2[$x2]["numberSales"];
			$compareWithCashSales += $decoded2[$x2]["cashSales"];
			$compareWithCardSales += $decoded2[$x2]["cardSales"];
			$compareWithOtherSales += $decoded2[$x2]["otherSales"];
		}

		$compareWithTotalSales = $compareWithCashSales + $compareWithCardSales + $compareWithOtherSales;
	}

	$sql17 = "SELECT nombre, precio, barcode, categoryID, ventasMensuales FROM trvsol_products ORDER BY ventasMensuales DESC LIMIT 3";
	$result17 = $conn->query($sql17);
	if ($result17->num_rows > 0) {
		$bestSellingProds = "";
		$bestSellingProdsCloud = "";
		while ($row17 = $result17->fetch_assoc()) {
			$sql22 = "SELECT color, color_txt, nombre FROM trvsol_categories WHERE id=" . $row17["categoryID"];
			$result22 = $conn->query($sql22);

			if ($result22->num_rows > 0) {
				$row22 = $result22->fetch_assoc();

				$bestSellingProds .= $bestSellingProdTop . ". <b>" . $row17["ventasMensuales"] . " unidades vendidas del producto:</b> " . $row17["nombre"] . " " . $row17["barcode"] . " $" . number_format($row17["precio"], 0, ",", ".") . "<br>";

				$bestSellingProdsCloud .= '<div class="list-item"><div class="list-item-image"><figure class="image is-64x64"><div class= "categoryColorImage" style= "background-color: ' . $row22["color"] . ';color: ' . $row22["color_txt"] . '"><span>#' . $bestSellingProdTop . '</span></div></figure></div><div class="list-item-content"><div class="list-item-title">' . $row17["nombre"] . '</div><div class="list-item-description"><span class="tag is-rounded" style= "background-color: ' . $row22["color"] . '; color: ' . $row22["color_txt"] . ';">' . $row22["nombre"] . '</span></div></div><div class="list-item-controls"><div class="level"><div class="level-item has-text-centered"><div><p class="is-size-5">Unidades vendidas</p><p class="is-size-3 has-text-success">' . $row17["ventasMensuales"] . '</p></div></div></div></div></div>';

				++$bestSellingProdTop;
			}
		}
	}

	$averageSalesArray = array();
	$averageSales = 0;
	$statsBestDay = "No es posible calcular esta estad&iacute;stica";
	$statsBestDayNums = 0;

	$decoded = json_decode($row14["estadisticas"], true);
	for ($x = 0; $x < count($decoded); ++$x) {
		$justEndedNumberSales += $decoded[$x]["numberSales"];
		$justEndedCashSales += $decoded[$x]["cashSales"];
		$justEndedCardSales += $decoded[$x]["cardSales"];
		$justEndedOtherSales += $decoded[$x]["otherSales"];

		array_push($averageSalesArray, ($decoded[$x]["cashSales"] + $decoded[$x]["cardSales"] + $decoded[$x]["otherSales"]));

		$sumSalesDay = $decoded[$x]["cashSales"] + $decoded[$x]["cardSales"] + $decoded[$x]["otherSales"];

		if ($sumSalesDay > $statsBestDayNums) {
			$statsBestDay = $decoded[$x]["date"];
			$statsBestDayNums = $sumSalesDay;
		}
	}

	$calcNumAverage = 0;
	$calcAverage = 0;

	for ($x3 = 0; $x3 < count($averageSalesArray); ++$x3) {
		$calcAverage += $averageSalesArray[$x3];
		++$calcNumAverage;
	}

	if ($calcNumAverage == 0) {
		$calcNumAverage = 1;
	}
	$averageSales = round($calcAverage / $calcNumAverage);

	$justEndedTotalSales = $justEndedCashSales + $justEndedCardSales + $justEndedOtherSales;

	$differenceNumberSales = $justEndedNumberSales - $compareWithNumberSales;
	$differenceTotalSales = $justEndedTotalSales - $compareWithTotalSales;
	$differenceCashSales = $justEndedCashSales - $compareWithCashSales;
	$differenceCardSales = $justEndedCardSales - $compareWithCardSales;
	$differenceOtherSales = $justEndedOtherSales - $compareWithOtherSales;

	$sql16 = "SELECT * FROM trvsol_configuration WHERE configName= 'templateMonthlyReport'";
	$result16 = $conn->query($sql16);
	if ($result16->num_rows > 0) {
		$row16 = $result16->fetch_assoc();

		$sql31 = "SELECT * FROM trvsol_configuration WHERE configName= 'newPaymentMethod'";
		$result31 = $conn->query($sql31);
		if ($result31->num_rows > 0) {
			$row31 = $result31->fetch_assoc();

			$trvCloudActive = 0;
			$trvCloudToken = "";

			$sql21 = "SELECT * FROM trvsol_configuration WHERE configName= 'trvCloudActive' OR configName= 'trvCloudToken'";
			$result21 = $conn->query($sql21);
			if ($result21->num_rows > 0) {
				while ($row21 = $result21->fetch_assoc()) {
					if ($row21["configName"] == "trvCloudActive") {
						$trvCloudActive = $row21["value"];
					} else if ($row21["configName"] == "trvCloudToken") {
						$trvCloudToken = $row21["value"];
					}
				}
			}

			$find =    array("{{trv_monthy_name}}", "{{trv_monthy_sales_number}}", "{{trv_monthy_sales_cash}}", "{{trv_monthy_sales_card}}", "{{trv_monthy_sales_other}}", "{{trv_monthy_sales_total}}", "{{trv_monthy_best_selling_prod}}", "{{trv_monthy_name_past}}", "{{trv_monthy_diff_sales_number_past}}", "{{trv_monthy_diff_sales_cash_past}}", "{{trv_monthy_diff_sales_card_past}}", "{{trv_monthy_diff_sales_other_past}}", "{{trv_monthy_diff_sales_total_past}}", "{{trv_monthy_diff_sales_number_diff}}", "{{trv_monthy_diff_sales_cash_diff}}", "{{trv_monthy_diff_sales_card_diff}}", "{{trv_monthy_diff_sales_other_diff}}", "{{trv_monthy_diff_sales_total_diff}}", "{{trv_tip}}", "{{trv_monthly_name_other_payment}}");
			$replace = array($justEndedName, number_format($justEndedNumberSales, 0, ",", "."), number_format($justEndedCashSales, 0, ",", "."), number_format($justEndedCardSales, 0, ",", "."), number_format($justEndedOtherSales, 0, ",", "."), number_format($justEndedTotalSales, 0, ",", "."), $bestSellingProds, $compareWithName, number_format($compareWithNumberSales, 0, ",", "."), number_format($compareWithCashSales, 0, ",", "."), number_format($compareWithCardSales, 0, ",", "."), number_format($compareWithOtherSales, 0, ",", "."), number_format($compareWithTotalSales, 0, ",", "."), number_format($differenceNumberSales, 0, ",", "."), number_format($differenceCashSales, 0, ",", "."), number_format($differenceCardSales, 0, ",", "."), number_format($differenceOtherSales, 0, ",", "."), number_format($differenceTotalSales, 0, ",", "."), $emailTip, $row31["value"]);

			$printingTemplate = str_replace($find, $replace, $row16["value"]);
			$printingTemplate .= '<div style= "text-align: center">---------- ----------
	<p style= "font-size: 14px;">Software por TRV Solutions (' . date("Y") . ').
	<br><b>www.trvsolutions.com</b></p>
	</div>';

			$sql18 = "SELECT * FROM trvsol_configuration WHERE configName= 'adminEmail'";
			$result18 = $conn->query($sql18);
			if ($result18->num_rows > 0) {
				$row18 = $result18->fetch_assoc();

				if ($row18["value"] != "") {
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
						$mail->addAddress($row18["value"]);

						// Content
						$mail->isHTML(true);
						$mail->Subject = 'Reporte Mensual';
						$mail->Body = '<html>
						<head>
						<meta charset= "UTF-8">
						<meta name= "viewport" content= "width=device-width, initial-scale=1">
						<title>Reporte Mensual</title>
						</head>
						<body style= "background-color: #e6e7e8;">
						<div style= "max-width: 640px;background-color: #fff;margin: auto;">
						<div>' . $printingTemplate . '</div>
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

					if ($emailSent == true) {
						$sql19 = "UPDATE trvsol_stats SET reportSent=1 WHERE mes=" . $justEndedMonth . " AND year=" . $actualYear;
						$conn->query($sql19);

						$sql32 = "UPDATE trvsol_products SET ventasMensuales=0";
						$conn->query($sql32);
					}
				} else {
					$sql19 = "UPDATE trvsol_stats SET reportSent=1 WHERE mes=" . $justEndedMonth . " AND year=" . $actualYear;
					$conn->query($sql19);
				}
			}
		}
	}
}
?>