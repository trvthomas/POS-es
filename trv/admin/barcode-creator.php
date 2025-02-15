<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Generador de códigos</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-checkradio.min.css">
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Generador de códigos</h3>
		<p>Genere, imprima y descargue códigos de barras y QR fácil y rápidamente con información personalizada</p>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<p class="has-text-centered">Seleccione el tipo de código, luego escriba el valor en el recuadro y haga clic en <b>Generar</b></p>

			<div class="columns has-text-centered">
				<div class="column">
					<div class="field">
						<label class="label">Tipo de código</label>
						<div class="control has-icons-left">
							<span class="select is-fullwidth">
								<select id="selectCodeType">
									<option value="Code128">Code - 128</option>
									<option value="EAN8">EAN - 8</option>
									<option value="EAN13">EAN - 13</option>
									<option value="QRCode">Código QR</option>
								</select>
							</span>

							<span class="icon is-small is-left"><i class="fas fa-barcode"></i></span>
						</div>
					</div>

					<button class="button backgroundDark is-fullwidth" onclick="document.getElementById('overlayTemplateConfig').style.display= 'block';"><i class="fas fa-print"></i> Generar plantilla de códigos</button>
				</div>

				<div class="column">
					<div class="field">
						<label class="label">Valor del código</label>
						<div class="control has-icons-left is-expanded">
							<input type="text" class="input" placeholder="e.g. Texto, enlace o URL" id="barrasInput" onkeydown="onup()">
							<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
						</div>
					</div>

					<div class="buttons is-centered">
						<button class="button backgroundDark is-fullwidth" onclick="generateCode()"><i class="fas fa-barcode"></i> Generar</button>
						<button class="button backgroundDark is-fullwidth" onclick="generateRandom()"><i class="fas fa-shuffle"></i> Generar aleatorio</button>
					</div>
				</div>
			</div>

			<div class="has-text-centered" id="generatedCode" style="display: none">
				<div id="printCode" class="barcodeBox">
					<img alt="Código de barras" id="imgCode" style="width: 200px;">
				</div>

				<p>Haga <b>clic derecho</b> > <b>"Guardar imagen como..."</b> para guardar el código en su computadora</p>
				<button class="button backgroundDark" onclick="printCode('printCode')"><i class="fas fa-print"></i> Imprimir</button>
			</div>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlayTemplateConfig" class="trvModal">
		<div class="trvModal-content trvModal-content">
			<span class="delete" onclick="document.getElementById('overlayTemplateConfig').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Configurar plantilla de códigos</h3>
			</div>

			<div class="trvModal-elements">
				<p>Esta opción le permite imprimir una plantilla con todos los <b>códigos de barras de sus productos activos</b>.
					<br>Configure la plantilla a continuación:
				</p>

				<div class="columns has-text-centered">
					<div class="column">
						<div class="field">
							<label class="label">Tipo de plantilla</label>
							<div class="control has-icons-left">
								<span class="select is-fullwidth">
									<select id="generateTemplateType" oninput="showDescriptionTemplate()">
										<option value="" selected disabled>Seleccione</option>
										<option value="simple">Sencilla</option>
										<option value="detailed">Detallada</option>
										<option value="detailed2">Detallada con precio</option>
									</select>
								</span>

								<span class="icon is-small is-left"><i class="fas fa-table-cells-large"></i></span>
							</div>
						</div>

						<p id="descriptionTemplate" style="margin-top: 2px;"></p>
					</div>

					<div class="column">
						<div class="field">
							<input type="checkbox" class="is-checkradio" id="generateTemplateShowBusinessName">
							<label class="label" for="generateTemplateShowBusinessName">Mostrar nombre de la empresa como título</label>
						</div>

						<div class="field">
							<input type="checkbox" class="is-checkradio" id="generateTemplateShowDateTime">
							<label class="label" for="generateTemplateShowDateTime">Mostrar fecha y hora de generación</label>
						</div>
					</div>
				</div>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth is-light is-danger" onclick="document.getElementById('overlayTemplateConfig').style.display='none'">Cancelar</button>
					</div>
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="generateTemplate()">Generar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="overlayTemplatePreview" class="trvModal">
		<div class="trvModal-content trvModal-content-large">
			<span class="delete" onclick="document.getElementById('overlayTemplatePreview').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Vista previa</h3>
			</div>

			<div class="trvModal-elements">
				<p>El resultado impreso puede variar al mostrado en esta vista previa</p>

				<div id="barcodesTemplate" class="invoiceStyle mt-2" style="width: 90%;height: 500px;margin: auto;overflow: auto;"></div>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="printCode('barcodesTemplate')">Imprimir</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/admin/include/get-barcode-template.php" style="display: none" id="getTemplateForm" onsubmit="return getTemplateReturn();">
		<input name="getTemplateType" id="getTemplateType" readonly>
		<input name="getTemplateShowBusiness" id="getTemplateShowBusiness" readonly>
		<input name="getTemplateShowTime" id="getTemplateShowTime" readonly>
		<input type="submit" id="getTemplateSend" value="Enviar">
	</form>

	<form method="POST" action="/trv/admin/include/generate-pdf-barcode.php" style="display: none">
		<textarea name="getPDFTemplate" id="getPDFTemplate" readonly></textarea>
		<input type="submit" id="getPDFSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function generateCode() {
			document.getElementById('generatedCode').style.display = "none";

			var codeType = document.getElementById('selectCodeType').value;
			var valueCode = document.getElementById('barrasInput').value;

			if (valueCode == "") {
				newNotification('Escriba el valor del código', 'error');
			} else if (navigator.onLine == false) {
				newNotification('Necesita una conexión a internet', 'error');
			} else {
				if (codeType == 'EAN8' && (valueCode.length > 7 || valueCode.length < 7)) {
					newNotification('Utilice 7 carácteres exactamente', 'error');
				} else if (codeType == 'EAN13' && (valueCode.length > 12 || valueCode.length < 12)) {
					newNotification('Utilice 12 carácteres exactamente', 'error');
				} else {
					openLoader();

					document.getElementById('imgCode').src = 'https://barcode.tec-it.com/barcode.ashx?code=' + codeType + '&data=' + valueCode + '&dpi=500';
					generateCodeShow();
				}
			}
		}

		function generateCodeShow() {
			document.getElementById('imgCode').onload = function() {
				document.getElementById('generatedCode').style.display = "block";
				closeLoader();
			}
		}

		function generateRandom() {
			var codeType = document.getElementById('selectCodeType').value;

			if (codeType == 'Code128' || codeType == 'QRCode') {
				document.getElementById("barrasInput").value = Math.floor(Math.random() * 10000000001);
			} else if (codeType == 'EAN8') {
				document.getElementById("barrasInput").value = Math.floor(Math.random() * 100000) + 999999;
			} else if (codeType == 'EAN13') {
				document.getElementById("barrasInput").value = Math.floor(Math.random() * 10000000000) + 99999999999;
			}
			generateCode();
		}

		function printCode(idPrinting) {
			var restorePage = document.body.innerHTML;
			var printContent = document.getElementById(idPrinting).innerHTML;
			document.body.innerHTML = printContent;
			window.print();
			document.body.innerHTML = restorePage;
			closeLoader();
		}

		function onup() {
			if (event.keyCode === 13) {
				generateCode();
			}
		}

		function showDescriptionTemplate() {
			var typeTemp = document.getElementById('generateTemplateType').value;

			if (typeTemp == "simple") {
				document.getElementById('descriptionTemplate').innerHTML = "Imprime únicamente los códigos en formato <b>Code-128</b>";
			} else if (typeTemp == "detailed") {
				document.getElementById('descriptionTemplate').innerHTML = "Imprime los códigos en formato <b>Code-128</b> junto con el nombre del producto";
			} else if (typeTemp == "detailed2") {
				document.getElementById('descriptionTemplate').innerHTML = "Imprime los códigos en formato <b>Code-128</b> junto con el nombre y precio de venta del producto";
			}
		}

		function generateTemplate() {
			var typeTemp = document.getElementById('generateTemplateType').value;
			var showBusiness = document.getElementById('generateTemplateShowBusinessName').checked;
			var showTime = document.getElementById('generateTemplateShowDateTime').checked;

			if (typeTemp == "") {
				newNotification('Seleccione el tipo de plantilla', 'error');
			} else {
				document.getElementById("getTemplateType").value = typeTemp;
				document.getElementById("getTemplateShowBusiness").value = showBusiness;
				document.getElementById("getTemplateShowTime").value = showTime;

				document.getElementById("getTemplateSend").click();
				openLoader();
				document.getElementById('overlayTemplateConfig').style.display = "none";
			}
		}

		function getTemplateReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/get-barcode-template.php',
				data: $('#getTemplateForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['plantilla_codigos'] != "") {
						document.getElementById('barcodesTemplate').innerHTML = response["plantilla_codigos"];
						document.getElementById('getPDFTemplate').value = response["plantilla_codigos_pdf"];
						document.getElementById('overlayTemplatePreview').style.display = "block";
					}

					closeLoader();
				}
			});

			return false;
		}
	</script>
</body>

</html>