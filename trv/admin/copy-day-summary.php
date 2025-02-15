<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$printingTemplate = "";
$adminEmail = "";
$dateShow = "";
$autoPrinting = false;

$autoPrintingDate = "";
$autoPrintingMonth = "";
$autoPrintingYear = "";

if (isset($_GET["day"])) {
	$dateShow = date("d-m-Y", strtotime($_GET["day"]));

	$sql = "SELECT * FROM trvsol_stats WHERE mes=" . date("m") . " AND year=" . date("Y");
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$decoded = json_decode($row["estadisticas"], true);

		for ($x = 0; $x < count($decoded); ++$x) {
			if (date('Y-m-d', strtotime($decoded[$x]["date"])) == date('Y-m-d', strtotime($_GET["day"]))) {
				$sql2 = "SELECT * FROM trvsol_configuration WHERE configName= 'templateDayReport'";
				$result2 = $conn->query($sql2);

				if ($result2->num_rows > 0) {
					$row2 = $result2->fetch_assoc();

					$sql4 = "SELECT * FROM trvsol_configuration WHERE configName= 'newPaymentMethod'";
					$result4 = $conn->query($sql4);

					if ($result4->num_rows > 0) {
						$row4 = $result4->fetch_assoc();

						$totalSales = $decoded[$x]["cashSales"] + $decoded[$x]["cardSales"] + $decoded[$x]["otherSales"];

						$find =    array("{{trv_daysumm_enter}}", "{{trv_daysumm_exit}}", "{{trv_daysumm_seller}}", "{{trv_daysumm_cash_base}}", "{{trv_daysumm_number_sales}}", "{{trv_daysumm_cash_sales}}", "{{trv_daysumm_card_sales}}", "{{trv_daysumm_other_name}}", "{{trv_daysumm_other_sales}}", "{{trv_daysumm_total_sales}}", "{{trv_daysumm_reports}}");
						$replace = array($decoded[$x]["entryDate"], $decoded[$x]["closedDate"], $decoded[$x]["seller"], $decoded[$x]["initialCash"], number_format($decoded[$x]["numberSales"], 0, ",", "."), number_format($decoded[$x]["cashSales"], 0, ",", "."), number_format($decoded[$x]["cardSales"], 0, ",", "."), $row4["value"], number_format($decoded[$x]["otherSales"], 0, ",", "."), number_format($totalSales, 0, ",", "."), $decoded[$x]["reports"]);

						$printingTemplate = str_replace($find, $replace, $row2["value"]);
						$printingTemplate .= '<div style= "text-align: center">---------- ----------
	<p style= "font-size: 14px;">Software por TRV Solutions (' . date("Y") . ').
	<br><b>www.trvsolutions.com</b></p>
	</div>';

						$sql3 = "SELECT * FROM trvsol_configuration WHERE configName= 'adminEmail' OR configName= 'printingAuto'";
						$result3 = $conn->query($sql3);

						if ($result3->num_rows > 0) {
							while ($row3 = $result3->fetch_assoc()) {
								if ($row3["configName"] == "adminEmail") {
									$adminEmail = $row3["value"];
								} else if ($row3["configName"] == "printingAuto") {
									$autoPrinting = $row3["value"];
								}
							}
						}
					}
				}
			}
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Copia cierre de caja - Resumen del día <?php echo $dateShow; ?></title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
</head>

<body>
	<?php include "include/header.php"; ?>

	<?php if ($dateShow != "") { ?>
		<div class="contentBox">
			<h3 class="is-size-5">Resumen del día <?php echo $dateShow; ?></h3>
			<p>Vea el resumen de las ventas realizadas el día <?php echo $dateShow; ?></p>

			<div class="box">
				<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

				<div class="columns">
					<div class="column">
						<div id="printSummaryDiv" class="invoiceStyle content"><?php echo $printingTemplate; ?></div>
					</div>

					<div class="column">
						<div class="columns is-multiline is-centered has-text-centered">
							<div class="column is-one-third">
								<div class="box is-shadowless is-clickable has-background-success-light" onclick="printSummary()">
									<span class="icon is-large"><i class="fas fa-print fa-2x"></i></span>
									<p><b>Imprimir resumen</b></p>
								</div>
							</div>

							<div class="column is-one-third">
								<div class="box is-shadowless is-clickable has-background-warning-light" onclick="sendReportEmail()">
									<span class="icon is-large"><i class="fas fa-paper-plane fa-2x"></i></span>
									<p><b>Enviar reporte</b></p>
								</div>
							</div>
						</div>

						<p>A continuación se muestra el resumen de las ventas del día <b><?php echo $dateShow; ?></b>. Puede realizar las <b>siguientes acciones</b>:
							<br><b>"Imprimir resumen":</b> Imprime un ticket con el resumen de ventas.
							<br><b>"Enviar reporte":</b> Envía el reporte al e-mail del administrador.
						</p>
					</div>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<h1 class="is-size-1 has-text-centered">Hubo un error</h1>
	<?php } ?>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form action="/trv/include/mail-close-cash.php" method="POST" style="display: none" id="sendMailForm" onsubmit="return sendMailReturn();">
		<input name="sendDaySummaryEmail" id="sendDaySummaryEmail" value="<?php echo $adminEmail; ?>">
		<input name="sendDaySummaryDesign" value='<?php echo $printingTemplate; ?>'>
		<input id="sendDaySummarySend" type="submit" value="Enviar">
	</form>

	<form action="/trv/include/autoPrintingCloseCash.php" method="POST" style="display: none" id="autoPrintForm" onsubmit="return autoPrintReturn();">
		<input name="autoPrintDate" id="autoPrintDate" value="<?php echo $autoPrintingDate; ?>">
		<input name="autoPrintMonth" id="autoPrintMonth" value="<?php echo $autoPrintingMonth; ?>">
		<input name="autoPrintYear" id="autoPrintYear" value="<?php echo $autoPrintingYear; ?>">
		<input id="autoPrintSend" type="submit" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function printSummary() {
			if (<?php echo $autoPrinting; ?> == 1) {
				document.getElementById('autoPrintSend').click();
				openLoader();
			} else {
				var restorePage = document.body.innerHTML;
				var printContent = document.getElementById("printSummaryDiv").innerHTML;
				document.body.innerHTML = printContent;
				window.print();
				document.body.innerHTML = restorePage;
			}
		}

		function sendReportEmail() {
			var emailAdmin = document.getElementById('sendDaySummaryEmail').value;

			if (emailAdmin == "") {
				newNotification('Correo electrónico no configurado', 'error');
			} else if (navigator.onLine == false) {
				newNotification('Se requiere una conexión a internet', 'error');
			} else {
				document.getElementById('sendDaySummarySend').click();
				openLoader();
			}
		}

		function sendMailReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/mail-close-cash.php',
				data: $('#sendMailForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error al enviar el e-mail', 'error');
					} else if (response['email_enviado'] == true) {
						newNotification('E-mail enviado exitosamente', 'success');
					}
					closeLoader();
				}
			});

			return false;
		}

		function autoPrintReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/autoPrintingCloseCash.php',
				data: $('#autoPrintForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores']) {
						newNotification('Hubo un error', 'error');
					} else if (response['impreso']) {
						newNotification('Impreso correctamente', 'success');
					}
					closeLoader();
				}
			});

			return false;
		}
	</script>
</body>

</html>