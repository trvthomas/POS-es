<?php include "include/verifySession.php";

$authorizeEnter = false;

$printingTemplate = "";
$idInvoice = "";
$saleNumber = "";
$seller = "";
$numberProds = 0;
$cancelled = 0;
$cancelledBy = "";
$changeTickets = 0;
$source = "";

$numberProdsSingPlu = "s";

if (isset($_GET["id"])) {
	if (isset($_GET["source"])) {
		$source = $_GET["source"];
	}

	if (isset($_GET["id"])) {
		$sql = "SELECT * FROM trvsol_invoices WHERE id=" . $_GET["id"];
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();

			$authorizeEnter = true;

			$idInvoice = $row["id"];
			$saleNumber = $row["numero"];
			$seller = $row["vendedor"];
			$decoded = json_decode($row["productosArray"], true);
			$numberProds = count($decoded);
			if ($numberProds == 1) {
				$numberProdsSingPlu = "";
			}

			if ($row["cancelada"] == 1) {
				$cancelled = 1;
				$cancelledBy = $row["canceladaPor"];
			}

			$sql3 = "SELECT * FROM trvsol_configuration WHERE configName= 'changeTickets' AND value= '1'";
			$result3 = $conn->query($sql3);
			if ($result3->num_rows > 0) {
				$changeTickets = 1;
			}

			$sql2 = "SELECT * FROM trvsol_configuration WHERE configName= 'templateInvoice'";
			$result2 = $conn->query($sql2);
			if ($result2->num_rows > 0) {
				$row2 = $result2->fetch_assoc();

				$totalSale = $row["subtotal"] - $row["descuentos"];

				$find =    array("{{trv_date_purchase}}", "{{trv_num_invoice}}", "{{trv_seller}}", "{{trv_products}}", "{{trv_payment_method}}", "{{trv_subtotal}}", "{{trv_discount}}", "{{trv_total}}", "{{trv_change_received}}", "{{trv_change}}", "{{trv_notes}}");
				$replace = array($row["fechaComplete"], $row["numero"], $row["vendedor"], $row["productos"], $row["formaPago"], number_format($row["subtotal"], 0, ",", "."), number_format($row["descuentos"], 0, ",", "."), number_format($totalSale, 0, ",", "."), $row["recibido"], number_format($row["cambio"], 0, ",", "."), $row["notas"]);

				$printingTemplate = str_replace($find, $replace, $row2["value"]);
				$printingTemplate .= '<div style= "text-align: center">---------- ----------
	<p style= "font-size: 14px;">Software por TRV Solutions (' . date("Y") . ').
	<br><b>www.trvsolutions.com</b></p>
	</div>';
			}
		}
	} else {
		$printingTemplate = "Hubo un error";
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Detalles comprobante de venta <?php echo $saleNumber; ?></title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
</head>

<body>
	<?php if ($source != "quickview") {
		include "include/header.php";
	} ?>

	<?php if ($authorizeEnter == true) { ?>
		<div class="contentBox loginBox has-text-centered" style="margin-top: 10px;">
			<?php if ($source == "quickview") { ?>
				<a class="button is-small is-pulled-right" style="border: none;" href="/trv/invoice-details.php?id=<?php echo $_GET["id"]; ?>&source=web" target="_blank" title="Abrir en una nueva pestaña"><span class="icon is-small"><i class="fas fa-up-right-from-square"></i></span></a>
			<?php } else { ?>
				<a class="button is-small is-pulled-left" style="border: none;" href="/trv/day-invoices.php"><span class="icon is-small" title="Volver"><i class="fas fa-chevron-left"></i></span></a>
			<?php } ?>

			<div class="box">
				<h3 class="is-size-5">Comprobante de venta <?php echo $saleNumber; ?></h3>
				<p><span class="tag is-rounded"><b><?php echo $numberProds; ?> producto<?php echo $numberProdsSingPlu; ?></b></span> <?php if ($cancelled == 1) { ?> <span class="tag is-rounded is-danger is-light">Venta cancelada</span> <?php } ?></p>

				<div class="has-text-left">
					<?php if ($cancelled != 1) { ?>
						<br>
						<div class="buttons is-centered">
							<button class="button backgroundDark is-fullwidth" onclick="printCopy(<?php echo $idInvoice; ?>)"><i class="fas fa-print"></i> Imprimir</button>
							<?php if ($changeTickets == 1) { ?><button class="button is-fullwidth" onclick="printChangeTickets(<?php echo $idInvoice; ?>)"><i class="fas fa-right-left"></i> Imprimir tickets de cambio</button><?php } ?>
						</div>
					<?php } else { ?>
						<p class="has-text-centered" style="color: #ef4d4d"><b>Venta cancelada por: <?php echo $cancelledBy; ?></b></p>
					<?php } ?>

					<div class="invoiceStyle content" style="width: 100%;"><?php echo $printingTemplate; ?></div>
				</div>
			</div>
		</div>

		<div id="printInvoiceDiv" style="display: none;"></div>

		<form method="POST" action="/trv/include/generate-sale-template.php" style="display: none" id="generateTemplateForm" onsubmit="return generateTemplateReturn();">
			<input name="generateTemplateIDInvoice" id="generateTemplateIDInvoice" readonly>
			<input name="generateTemplatePrintOrSend" id="generateTemplatePrintOrSend" readonly>
			<input name="generateTemplateAutoChangeTickets" id="generateTemplateAutoChangeTickets" readonly>
			<input type="submit" id="generateTemplateSend" value="Enviar">
		</form>

		<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
		<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
		<script>
			var printOrSend = "",
				numberChangeTicketsPrint = 0;

			function printCopy(idInvoice) {
				openLoader();
				printOrSend = "P";

				document.getElementById('generateTemplateIDInvoice').value = idInvoice;
				document.getElementById('generateTemplatePrintOrSend').value = printOrSend;
				document.getElementById('generateTemplateAutoChangeTickets').value = "";
				document.getElementById('generateTemplateSend').click();
			}

			function printChangeTickets(idInvoice) {
				var printNumTickets = prompt("Escriba el número de tickets de cambio para imprimir");
				printNumTickets++;
				printNumTickets--;

				if (printNumTickets < 1 || printNumTickets > 5) {
					newNotification('Puede imprimir un <b>máximo de 5 tickets de cambio</b>', 'error');
				} else {
					numberChangeTicketsPrint = printNumTickets;
					openLoader();
					printOrSend = "P2";

					document.getElementById('generateTemplateIDInvoice').value = idInvoice;
					document.getElementById('generateTemplatePrintOrSend').value = printOrSend;
					document.getElementById('generateTemplateAutoChangeTickets').value = numberChangeTicketsPrint;
					document.getElementById('generateTemplateSend').click();
				}
			}

			function printInvoice() {
				openLoader();

				setTimeout(function() {
					var restorePage = document.body.innerHTML;
					var printContent = document.getElementById("printInvoiceDiv").innerHTML;
					document.body.innerHTML = '<div class= "content">' + printContent + '</div>';
					window.print();
					document.body.innerHTML = restorePage;
					closeLoader();
				}, 2000);
			}

			function generateTemplateReturn() {
				$.ajax({
					type: 'POST',
					url: '/trv/include/generate-sale-template.php',
					data: $('#generateTemplateForm').serialize(),
					dataType: 'json',
					success: function(response) {
						if (response['errores'] == true) {
							newNotification('Hubo un error', 'error');
							closeLoader();
						} else if (response['plantilla_impresion'] != "") {
							invoiceTemplate = response['plantilla_impresion'];

							if (response['auto_print'] != true) {
								if (printOrSend == "P") {
									document.getElementById('printInvoiceDiv').innerHTML = response['plantilla_impresion'];
									printInvoice();
								} else if (printOrSend == "P2") {
									document.getElementById('printInvoiceDiv').innerHTML = "";
									for (var xT = 1; xT <= numberChangeTicketsPrint; xT++) {
										document.getElementById('printInvoiceDiv').innerHTML += "<hr class= 'dottedHr'>" + response['plantilla_tickets_cambio'];
									}

									printInvoice();
								}
							} else {
								closeLoader();
							}
						}
					}
				});

				return false;
			}
		</script>
	<?php } else { ?>
		<div class="contentBox">
			<div class="box has-text-centered">
				<h1 class="is-size-4">Hubo un error</h1>
				<p>Verifique el enlace e intente nuevamente</p>
			</div>
		</div>
	<?php } ?>
</body>

</html>