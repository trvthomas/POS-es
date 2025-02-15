<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$printingAuto = "";
$printingHeadingImg = "";
$printingHeadingInfo = "";
$printingFooterThanksMsg = "";
$printingFooterImg = "";
$printingFooterInfo = "";
$printingFooterBarcode = "";
$printingOpenDrawer = "";
$printingOpenDrawerCard = "";

$headingActualLines = "";
$headingActualLinesNum = 1;
$footerActualLines = "";
$footerActualLinesNum = 1;

$invoicePreview = "";
$previewHeadingLines = "";
$previewFooterLines = "";
$previewNameBusiness = "";

$sql = "SELECT * FROM trvsol_configuration WHERE configName= 'businessName' OR configName= 'printingAuto' OR configName= 'printingHeadingImg' OR configName= 'printingHeadingInfo' OR configName= 'printingFooterThanksMsg' OR configName= 'printingFooterImg' OR configName= 'printingFooterInfo' OR configName= 'printingFooterBarcode' OR configName= 'printingOpenDrawer' OR configName= 'printingOpenDrawerCard'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		if ($row["configName"] == "businessName") {
			$previewNameBusiness = $row["value"];
		} else if ($row["configName"] == "printingAuto") {
			if ($row["value"] == 0) {
				header("Location:invoices-design.php");
			}
			$printingAuto = $row["value"];
		} else if ($row["configName"] == "printingHeadingImg") {
			$printingHeadingImg = $row["value"];
		} else if ($row["configName"] == "printingHeadingInfo") {
			$printingHeadingInfo = $row["value"];

			$arrayHeading = explode("\n", $printingHeadingInfo);
			for ($x = 0; $x < count($arrayHeading); ++$x) {
				$previewHeadingLines .= '<br>' . $arrayHeading[$x];
				$headingActualLines .= '<div class="field">
	<div class="control has-icons-left">
	<input type= "text" class= "input" placeholder= "e.g. Tel, NIT, Correo electrónico" id= "headingAdditional' . $headingActualLinesNum . '" value= "' . $arrayHeading[$x] . '">
	<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
	</div>
	</div>';
				++$headingActualLinesNum;
			}
		} else if ($row["configName"] == "printingFooterThanksMsg") {
			$printingFooterThanksMsg = $row["value"];
		} else if ($row["configName"] == "printingFooterImg") {
			$printingFooterImg = $row["value"];
		} else if ($row["configName"] == "printingFooterInfo") {
			$printingFooterInfo = $row["value"];

			$arrayFooter = explode("\n", $printingFooterInfo);
			for ($x2 = 0; $x2 < count($arrayFooter); ++$x2) {
				$previewFooterLines .= '<br>' . $arrayFooter[$x2];
				$footerActualLines .= '<div class="field">
	<div class="control has-icons-left">
	<input type= "text" class= "input" placeholder= "e.g. Síguenos en redes sociales, promociones" id= "footerAdditional' . $footerActualLinesNum . '" value= "' . $arrayFooter[$x2] . '">
	<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
	</div>
	</div>';
				++$footerActualLinesNum;
			}
		} else if ($row["configName"] == "printingFooterBarcode") {
			$printingFooterBarcode = $row["value"];
		} else if ($row["configName"] == "printingOpenDrawer") {
			$printingOpenDrawer = $row["value"];
		} else if ($row["configName"] == "printingOpenDrawerCard") {
			$printingOpenDrawerCard = $row["value"];
		}
	}
}

//Preview
if ($printingHeadingImg != "") {
	$invoicePreview .= '<img style="display: block; margin-left: auto; margin-right: auto;" src="' . $printingHeadingImg . '" alt="" width="50%" height="auto">';
}
$invoicePreview .= '<h3 class= "is-size-4 has-text-centered">' . $previewNameBusiness . '</h3>
	<div class= "has-text-centered">' . $previewHeadingLines . '</div>';

$invoicePreview .= '<p class= "has-text-centered">----------</p>
	<p><b>Fecha y hora de compra</b>: ' . date("d-m-Y H:i a") . '</p>
	<p><b>Venta #</b>8900</p>
	<p><b>Atendido por</b>: ' . ucfirst($_COOKIE[$prefixCoookie . "UsernameUser"]) . '</p>
	<p class= "has-text-centered">----------</p>
	
	<p><b>Producto 1</b></p><p>5 x $10.000 = $50.000</p>
	<p><b>Producto 2</b></p><p>$20.000</p>
	<p class= "has-text-centered">----------</p>
	<p>Forma de pago: Efectivo</p>
	
	<p class= "has-text-right">Subtotal: $50.000</p>
	<p class= "has-text-right">Descuentos: -$5.000</p>
	<p class= "has-text-right is-size-4"><b>TOTAL: $45.000</b></p>
	<p class= "has-text-right">Recibido: $50.000</p>
	<p class= "has-text-right">Cambio: $5.000</p>
	<p>Notas adicionales: </p>';

$invoicePreview .= '<h3 class= "is-size-4 has-text-centered">' . $printingFooterThanksMsg . '</h3>';
$invoicePreview .= '<div class= "has-text-centered">' . $previewFooterLines . '</div>';
if ($printingFooterImg != "") {
	$invoicePreview .= '<img style="display: block; margin-left: auto; margin-right: auto;" src="' . $printingFooterImg . '" alt="" width="50%" height="auto">';
}
if ($printingFooterBarcode == 1) {
	$invoicePreview .= '<br><img style="display: block; margin-left: auto; margin-right: auto;" src="https://barcode.tec-it.com/barcode.ashx?code=Code128&data=8900&dpi=500" alt="" width="80%" height="auto">';
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Modificar el diseño de los comprobantes</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-checkradio.min.css">
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Diseño comprobantes</h3>
		<p>Personalice el diseño de los comprobantes de venta impresos en el modo automático</p>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="columns">
				<div class="column">
					<h4 class="is-size-5 has-text-info">Encabezado</h4>

					<label class="label">Información adicional (dejar en blanco para eliminar)</label>
					<div class="columns">
						<div class="column">
							<div class="buttons">
								<button class="button backgroundDark is-fullwidth" onclick="addLinesHeading()"><i class="fas fa-circle-plus"></i> Agregar línea</button>
								<button class="button is-success is-fullwidth" onclick="saveChangesHeader()"><i class="fas fa-floppy-disk"></i> Guardar cambios</button>
							</div>
						</div>

						<div class="column">
							<div id="additionalLinesHeading"><?php echo $headingActualLines; ?></div>
						</div>
					</div>

					<h4 class="is-size-5 has-text-info">Pie de página</h4>

					<label class="label">Mensaje de agradecimiento</label>
					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="text" class="input" placeholder="e.g. Gracias por su compra, vuelva pronto" id="printingFooterThanksMsg" value="<?php echo $printingFooterThanksMsg; ?>">
							<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" onclick="modifySetting('printingFooterThanksMsg', false)"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="printingFooterBarcode" onclick="modifySetting('printingFooterBarcode', true)" <?php if ($printingFooterBarcode == 1) {
																																							echo "checked";
																																						} ?>>
						<label class="label" for="printingFooterBarcode">Mostrar código de barras con el número de la venta</label>
					</div>

					<label class="label">Información adicional (dejar en blanco para eliminar)</label>
					<div class="columns">
						<div class="column">
							<div class="buttons">
								<button class="button backgroundDark is-fullwidth" onclick="addLinesFooter()"><i class="fas fa-circle-plus"></i> Agregar línea</button>
								<button class="button is-success is-fullwidth" onclick="saveChangesFooter()"><i class="fas fa-floppy-disk"></i> Guardar cambios</button>
							</div>
						</div>

						<div class="column">
							<div id="additionalLinesFooter"><?php echo $footerActualLines; ?></div>
						</div>
					</div>

					<h4 class="is-size-5 has-text-info">Adicionales</h4>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="printingOpenDrawer" onclick="modifySetting('printingOpenDrawer', true)" <?php if ($printingOpenDrawer == 1) {
																																						echo "checked";
																																					} ?>>
						<label class="label" for="printingOpenDrawer">Abrir cajón monedero automáticamente al finalizar la impresión</label>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="printingOpenDrawerCard" onclick="modifySetting('printingOpenDrawerCard', true)" <?php if ($printingOpenDrawerCard == 1) {
																																								echo "checked";
																																							} ?>>
						<label class="label" for="printingOpenDrawerCard">Abrir cajón monedero si la venta se realiza con el método de pago <b>"Tarjeta"</b></label>
					</div>
				</div>

				<div class="column is-one-third">
					<label class="label has-text-centered">Vista previa</label>
					<div class="notification is-info">La vista previa puede ser <b>diferente</b> a la impresión</div>

					<div class="invoiceStyle" style="width: 100%;"><?php echo $invoicePreview; ?></div>
				</div>
			</div>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form method="POST" action="/trv/admin/include/edit-configuration.php" style="display: none" id="editConfigForm" onsubmit="return editConfigReturn();">
		<input name="editConfigId" id="editConfigId" readonly>
		<input name="editConfigValue" id="editConfigValue" readonly>
		<input type="submit" id="editConfigSend" value="Enviar">
	</form>

	<form method="POST" action="/trv/admin/include/edit-invoices-design-auto.php" style="display: none" id="editDesignForm" onsubmit="return editDesignReturn();">
		<input name="editDesignHeading" id="editDesignHeading" readonly>
		<input name="editDesignFooter" id="editDesignFooter" readonly>
		<input type="submit" id="editDesignSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		var numberIdHeading = <?php echo $headingActualLinesNum; ?>;
		var numberIdFooter = <?php echo $footerActualLinesNum; ?>;

		function modifySetting(idSetting, isCheckbox) {
			var valueConfig = 0;

			if (isCheckbox == true) {
				valueConfig = document.getElementById(idSetting).checked;
				if (valueConfig == true) {
					valueConfig = 1;
				} else {
					valueConfig = 0;
				}
			} else {
				valueConfig = document.getElementById(idSetting).value;
			}

			if (idSetting == "numInvoice") {
				valueConfig++;
				valueConfig--;
			}
			if (idSetting == "numInvoice" && valueConfig < 0) {
				newNotification('La numeración de ventas es incorrecto', 'error');
			} else {
				document.getElementById('editConfigId').value = idSetting;
				document.getElementById('editConfigValue').value = valueConfig;

				document.getElementById('editConfigSend').click();
				openLoader();
			}
		}

		function addLinesHeading() {
			var createHead = document.createElement("DIV");
			var attributeHead1 = document.createAttribute("id");
			attributeHead1.value = "headerDivField" + numberIdHeading;
			var attributeHead2 = document.createAttribute("class");
			attributeHead2.value = "field";
			var appendHead = document.getElementById('additionalLinesHeading').appendChild(createHead);
			appendHead.setAttributeNode(attributeHead1);
			appendHead.setAttributeNode(attributeHead2);

			document.getElementById('headerDivField' + numberIdHeading).innerHTML += '<div class="control has-icons-left"><input type= "text" class= "input" placeholder= "e.g. Tel, NIT, Correo electrónico" id= "headingAdditional' + numberIdHeading + '"><span class="icon is-small is-left"><i class="fas fa-heading"></i></span></div>';
			numberIdHeading++;
		}

		function addLinesFooter() {
			var createFoot = document.createElement("DIV");
			var attributeFoot1 = document.createAttribute("id");
			attributeFoot1.value = "footerDivField" + numberIdFooter;
			var attributeFoot2 = document.createAttribute("class");
			attributeFoot2.value = "field";
			var appendFoot = document.getElementById('additionalLinesFooter').appendChild(createFoot);
			appendFoot.setAttributeNode(attributeFoot1);
			appendFoot.setAttributeNode(attributeFoot2);

			document.getElementById('footerDivField' + numberIdFooter).innerHTML += '<div class="control has-icons-left"><input type= "text" class= "input" placeholder= "e.g. Síguenos en redes sociales, promociones" id= "footerAdditional' + numberIdFooter + '"><span class="icon is-small is-left"><i class="fas fa-heading"></i></span></div>';
			numberIdFooter++;
		}

		function saveChangesHeader() {
			var headingFinal = "";

			for (var x = 1; x < numberIdHeading; x++) {
				var inputHeading = document.getElementById('headingAdditional' + x).value;

				if (inputHeading != "") {
					headingFinal += inputHeading + "\\n";

					console.log(headingFinal);
				}
			}

			document.getElementById('editDesignHeading').value = headingFinal;
			document.getElementById('editDesignFooter').value = "only";

			document.getElementById('editDesignSend').click();
			openLoader();
		}

		function saveChangesFooter() {
			var footerFinal = "";

			for (var x = 1; x < numberIdFooter; x++) {
				var inputFooter = document.getElementById('footerAdditional' + x).value;

				if (inputFooter != "") {
					footerFinal += inputFooter + "\\n";
				}
			}

			document.getElementById('editDesignHeading').value = "only";
			document.getElementById('editDesignFooter').value = footerFinal;

			document.getElementById('editDesignSend').click();
			openLoader();
		}

		function editConfigReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/edit-configuration.php',
				data: $('#editConfigForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
						closeLoader();
					} else if (response['configuracion_guardada'] == true) {
						newNotification("Configuración actualizada", "success");
						window.location = "/trv/admin/invoices-design-auto.php";
					}
				}
			});

			return false;
		}

		function editDesignReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/edit-invoices-design-auto.php',
				data: $('#editDesignForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
						closeLoader();
					} else if (response['configuracion_guardada'] == true) {
						newNotification("Configuración actualizada", "success");
						window.location = "/trv/admin/invoices-design-auto.php";
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>