<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$metodoPagoPersonalizado = "";

$sql = "SELECT * FROM trvsol_configuration WHERE configName= 'newPaymentMethod'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();

	$metodoPagoPersonalizado = $row["value"];
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Nuevo voucher</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-steps.min.css">
	<link rel="stylesheet" href="/trv/include/libraries/flatpickr.min.css">
	<script src="/trv/include/libraries/flatpickr.js"></script>
	<script src="/trv/include/libraries/flatpickr-es.js"></script>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-checkradio.min.css">
</head>

<body onload="startCreation()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/vouchers.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>
			<ul class="steps has-content-centered has-gaps" style="margin-bottom: 0;" id="progressBarDiv"></ul>

			<div class="fade" id="step1">
				<h3 class="is-size-5 has-text-centered">Información general</h3>
				<hr><br>

				<div class="columns">
					<div class="column">
						<div class="field">
							<label class="label">Código del bono*</label>
							<div class="control has-icons-left">
								<input type="text" class="input" placeholder="e.g. 10OFF, SUMMERSALE" id="inputVoucherCode" onkeyup="this.value = this.value.toUpperCase();">
								<span class="icon is-small is-left"><i class="fas fa-tag"></i></span>
							</div>
						</div>
					</div>

					<div class="column">
						<div class="field">
							<label class="label">Porcentaje de descuento*</label>
							<div class="control has-icons-left">
								<input type="number" class="input" placeholder="e.g. 10, 50" id="inputVoucherValue">
								<span class="icon is-small is-left"><i class="fas fa-percent"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="fade" id="step2" style="display: none">
				<h3 class="is-size-5 has-text-centered">Configuración general</h3>
				<hr><br>

				<div class="columns">
					<div class="column">
						<div class="field">
							<label class="label">Cantidad disponible para su uso*</label>
							<div class="control has-icons-left">
								<input type="number" class="input" placeholder="e.g. 10, 50" id="inputVoucherAvailable">
								<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
							</div>
						</div>
					</div>

					<div class="column">
						<div class="field">
							<label class="label">Importe mínimo para poder redimir el bono</label>
							<div class="control has-icons-left">
								<input type="number" class="input" placeholder="e.g. 50000, 100000" id="inputVoucherMinimum">
								<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="fade" id="step3" style="display: none">
				<h3 class="is-size-5 has-text-centered">Configuración avanzada</h3>
				<hr><br>

				<div class="columns">
					<div class="column">
						<div class="field">
							<label class="label">Vigencia, válido hasta*</label>
							<div class="control has-icons-left">
								<input type="date" class="input" id="inputVoucherExpiration">
								<span class="icon is-small is-left"><i class="fas fa-calendar-day"></i></span>
							</div>
						</div>
					</div>

					<div class="column">
						<label class="label">Métodos de pago habilitados para utilizar el bono</label>

						<div class="field">
							<input type="checkbox" class="is-checkradio" id="voucherPaymentsCash" checked>
							<label class="label" for="voucherPaymentsCash">Efectivo</label>
						</div>

						<div class="field">
							<input type="checkbox" class="is-checkradio" id="voucherPaymentsCard" checked>
							<label class="label" for="voucherPaymentsCard">Tarjeta</label>
						</div>

						<div class="field">
							<input type="checkbox" class="is-checkradio" id="voucherPaymentsMulti">
							<label class="label" for="voucherPaymentsMulti">Multipago</label>
						</div>

						<div class="field" <?php if ($metodoPagoPersonalizado == "") {
												echo 'style= "display: none"';
											} ?>>
							<input type="checkbox" class="is-checkradio" id="voucherPaymentsOther">
							<label class="label" for="voucherPaymentsOther"><?php echo $metodoPagoPersonalizado; ?></label>
						</div>
					</div>
				</div>
			</div>

		</div>

		<div class="columns">
			<div class="column">
				<button class="button backgroundDark is-fullwidth is-invisible" id="buttonPrevious" onclick="nextStep(-1)"><i class="fas fa-chevron-left"></i> Anterior</button>
			</div>

			<div class="column has-text-right">
				<button class="button backgroundDark is-fullwidth" id="buttonNext" onclick="nextStep(1)">Siguiente <i class="fas fa-chevron-right"></i></button>
				<button class="button backgroundDark is-fullwidth is-hidden" id="buttonPublish" onclick="addVoucher()">Crear <i class="fas fa-circle-plus"></i></button>
			</div>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form method="POST" action="/trv/admin/include/add-voucher.php" style="display: none" id="addVoucherForm" onsubmit="return addVoucherReturn();">
		<input name="addVoucherCode" id="addVoucherCode" readonly>
		<input name="addVoucherAvailable" id="addVoucherAvailable" readonly>
		<input name="addVoucherMinimum" id="addVoucherMinimum" readonly>
		<input name="addVoucherValue" id="addVoucherValue" readonly>
		<input name="addVoucherExpiration" id="addVoucherExpiration" readonly>
		<input name="addVoucherPayment" id="addVoucherPayment" readonly>

		<input type="submit" id="addVoucherSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script defer type="text/javascript" src="/trv/include/create-element.js"></script>
	<script>
		function startCreation() {
			createProgressBar(true, JSON.stringify([{
					icon: "circle-info",
					title: "Info. general"
				},
				{
					icon: "gear",
					title: "Config. general"
				},
				{
					icon: "calendar-day",
					title: "Config. avanzada"
				}
			]));

			flatpickrCalendars = flatpickr("#inputVoucherExpiration", {
				altInput: true,
				locale: "es",
				dateFormat: "Y-m-d",
				minDate: '<?php echo date("Y-m-d"); ?>'
			});
		}

		function addVoucher() {
			var voucherCode = document.getElementById('inputVoucherCode').value;
			var voucherAvailable = document.getElementById('inputVoucherAvailable').value;
			var voucherMinimum = document.getElementById('inputVoucherMinimum').value;
			var voucherValue = document.getElementById('inputVoucherValue').value;
			var voucherExpiration = document.getElementById('inputVoucherExpiration').value;
			var voucherPaymentCash = document.getElementById('voucherPaymentsCash').checked;
			var voucherPaymentCard = document.getElementById('voucherPaymentsCard').checked;
			var voucherPaymentMulti = document.getElementById('voucherPaymentsMulti').checked;
			var voucherPaymentOther = document.getElementById('voucherPaymentsOther').checked;
			voucherAvailable++;
			voucherAvailable--;
			voucherMinimum++;
			voucherMinimum--;

			if (voucherCode == "" || voucherAvailable <= 0 || voucherMinimum < 0 || voucherValue == "" || voucherExpiration == "" || (voucherPaymentCash == false && voucherPaymentCard == false && voucherPaymentMulti == false && voucherPaymentOther == false)) {
				newNotification('Complete y verifique todos los campos', 'error');
			} else {
				var paymentsAccepted = "";
				if (voucherPaymentCash == true) {
					paymentsAccepted += " a:E:p";
				}
				if (voucherPaymentCard == true) {
					paymentsAccepted += " a:T:p";
				}
				if (voucherPaymentMulti == true) {
					paymentsAccepted += " a:M:p";
				}
				if (voucherPaymentOther == true) {
					paymentsAccepted += " a:O:p";
				}

				document.getElementById('addVoucherCode').value = voucherCode;
				document.getElementById('addVoucherAvailable').value = voucherAvailable;
				document.getElementById('addVoucherMinimum').value = voucherMinimum;
				document.getElementById('addVoucherValue').value = voucherValue;
				document.getElementById('addVoucherExpiration').value = voucherExpiration;
				document.getElementById('addVoucherPayment').value = paymentsAccepted;

				document.getElementById('addVoucherSend').click();
				openLoader();
			}
		}

		function addVoucherReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/add-voucher.php',
				data: $('#addVoucherForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
						closeLoader();
					} else if (response['codigo_existe'] == true) {
						newNotification('El código ya está en uso', 'error');
						closeLoader();
					} else if (response['voucher_creado'] == true) {
						window.location = "/trv/admin/vouchers.php";
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>