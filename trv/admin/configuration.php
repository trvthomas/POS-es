<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$configLimiteDescuento = 0;
$configBorrarNum = 0;
$configPrefijo = 0;
$configNumeracion = 0;
$configNombreEmpresa = 0;
$configEmailAdmin = 0;

$configCheckPrecioMenor = 0;
$configCheckNegativeStock = 0;
$configCheckAutoEmail = 0;
$configCheckLowStockNotifications = 0;
$configCheckChangeTickets = 0;
$configCheckNewPaymentMethod = "";
$configCheckTRVCloudService = 0;
$configCheckTRVCloudToken = "ERROR";
$configCheckAutoPrinting = 0;
$configCheckPrinterName = "";
$configInvoicesSavingMonths = 4;

$sql = "SELECT * FROM trvsol_configuration";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		if ($row["configName"] == "discountLimit") {
			$configLimiteDescuento = $row["value"];
		} else if ($row["configName"] == "deleteInvoiceNumbersAuto") {
			$configBorrarNum = $row["value"];
		} else if ($row["configName"] == "prefixNumInvoice") {
			$configPrefijo = $row["value"];
		} else if ($row["configName"] == "numInvoice") {
			$configNumeracion = $row["value"];
		} else if ($row["configName"] == "businessName") {
			$configNombreEmpresa = $row["value"];
		} else if ($row["configName"] == "adminEmail") {
			$configEmailAdmin = $row["value"];
		} else if ($row["configName"] == "changePriceLessOriginal") {
			$configCheckPrecioMenor = $row["value"];
		} else if ($row["configName"] == "allowNegativeInventory") {
			$configCheckNegativeStock = $row["value"];
		} else if ($row["configName"] == "sendAutoReports") {
			$configCheckAutoEmail = $row["value"];
		} else if ($row["configName"] == "newPaymentMethod") {
			$configCheckNewPaymentMethod = $row["value"];
		} else if ($row["configName"] == "lowStockNotification") {
			$configCheckLowStockNotifications = $row["value"];
		} else if ($row["configName"] == "changeTickets") {
			$configCheckChangeTickets = $row["value"];
		} else if ($row["configName"] == "trvCloudActive") {
			$configCheckTRVCloudService = $row["value"];
		} else if ($row["configName"] == "trvCloudToken") {
			$configCheckTRVCloudToken = $row["value"];
		} else if ($row["configName"] == "printingAuto") {
			$configCheckAutoPrinting = $row["value"];
		} else if ($row["configName"] == "printingAutoPrinterName") {
			$configCheckPrinterName = $row["value"];
		} else if ($row["configName"] == "saveInvoicesForMonths") {
			$configInvoicesSavingMonths = $row["value"];
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Configuración</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-checkradio.min.css">
</head>

<body>
	<?php include "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Configuración</h3>
		<p>Personalice el comportamiento de su sistema POS para adaptarse a su negocio</p>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="columns">
				<div class="column">
					<h4 class="is-size-5 has-text-info">General</h4>

					<label class="label">Límite de descuento que el vendedor puede aplicar (escriba 0 para desactivar)</label>
					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="number" class="input" placeholder="e.g. 50000, 100000" id="discountLimit" value="<?php echo $configLimiteDescuento; ?>">
							<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" onclick="modifySetting('discountLimit', false)"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="changePriceLessOriginal" onclick="modifySetting('changePriceLessOriginal', true)" <?php if ($configCheckPrecioMenor == 1) {
																																								echo "checked";
																																							} ?>>
						<label class="label" for="changePriceLessOriginal">No permitir cambiar el precio de un producto por un menor valor</label>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="allowNegativeInventory" onclick="modifySetting('allowNegativeInventory', true)" <?php if ($configCheckNegativeStock == 1) {
																																								echo "checked";
																																							} ?>>
						<label class="label" for="allowNegativeInventory">Permitir venta de artículos con existencias negativas</label>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="sendAutoReports" onclick="modifySetting('sendAutoReports', true)" <?php if ($configCheckAutoEmail == 1) {
																																				echo "checked";
																																			} ?>>
						<label class="label" for="sendAutoReports">Enviar reportes por e-mail automáticamente cuando se cierra caja</label>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="lowStockNotification" onclick="modifySetting('lowStockNotification', true)" <?php if ($configCheckLowStockNotifications == 1) {
																																							echo "checked";
																																						} ?>>
						<label class="label" for="lowStockNotification">Enviar notificaciones diarias sobre artículos bajos en stock por e-mail</label>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="changeTickets" onclick="modifySetting('changeTickets', true)" <?php if ($configCheckChangeTickets == 1) {
																																			echo "checked";
																																		} ?>>
						<label class="label" for="changeTickets">Activar tickets para cambio</label>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="newPaymentMethodCheck" onclick="newPaymentMethodCheck()" <?php if ($configCheckNewPaymentMethod != "") {
																																		echo "checked";
																																	} ?>>
						<label class="label" for="newPaymentMethodCheck">Método de pago personalizado</label>
					</div>

					<div class="fade" id="newPaymentMethodDiv" style="<?php if ($configCheckNewPaymentMethod == "") {
																			echo "display: none";
																		} ?>">
						<label class="label">Nombre método de pago</label>
						<div class="field has-addons">
							<div class="control has-icons-left is-expanded">
								<input type="text" class="input" placeholder="e.g. Billetera digital, Nequi, Cheque" id="newPaymentMethod" maxlength="20" value="<?php echo $configCheckNewPaymentMethod; ?>">
								<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
							</div>

							<div class="control">
								<button class="button backgroundDark" onclick="modifySetting('newPaymentMethod', false)"><i class="fas fa-circle-check"></i></button>
							</div>
						</div>
					</div>

					<h4 class="is-size-5 has-text-info">Impresión</h4>
					<div class="field">
						<input type="checkbox" class="is-checkradio" id="printingAuto" onclick="modifySetting('printingAuto', true); if(this.checked == true){ document.getElementById('btnAutoPrinting').style.display= 'block'; }else{ document.getElementById('btnAutoPrinting').style.display= 'none'; }" <?php if ($configCheckAutoPrinting == 1) {
																																																																													echo "checked";
																																																																												} ?>>
						<label class="label" for="printingAuto">Impresión automática</label>
					</div>

					<div id="btnAutoPrinting" style="<?php if ($configCheckAutoPrinting == 0) {
															echo "display: none";
														} ?>">
						<div class="notification is-info">
							La impresión automática únicamente funcionará con impresoras térmicas conectadas mediante cable USB, instaladas con el driver <b>"Generic / Text Only"</b> y compartidas.
						</div>

						<label class="label">Nombre compartido de la impresora</label>
						<div class="field has-addons">
							<div class="control has-icons-left is-expanded">
								<input type="text" class="input" placeholder="Ingrese el nombre exacto de la impresora compartida" id="printingAutoPrinterName" maxlength="100" value="<?php echo $configCheckPrinterName; ?>">
								<span class="icon is-small is-left"><i class="fas fa-print"></i></span>
							</div>

							<div class="control">
								<button class="button backgroundDark" onclick="modifySetting('printingAutoPrinterName', false)"><i class="fas fa-circle-check"></i></button>
							</div>
						</div>

						<a class="button backgroundDark is-fullwidth fade" href="/trv/admin/invoices-design-auto.php">Configuración de impresión</a>
					</div>
				</div>

				<div class="column">
					<h4 class="is-size-5 has-text-info">Comprobantes de venta</h4>

					<label class="label">Borrar numeración automáticamente (dejar vacío para desactivar)</label>
					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="number" class="input" placeholder="e.g. 100, 250" id="deleteInvoiceNumbersAuto" value="<?php echo $configBorrarNum; ?>">
							<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" onclick="modifySetting('deleteInvoiceNumbersAuto', false)"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>

					<label class="label">Agregar prefijo en la numeración (dejar vacío para desactivar)</label>
					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="text" class="input" placeholder="e.g. TRV, COMP" id="prefixNumInvoice" maxlength="6" value="<?php echo $configPrefijo; ?>">
							<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" onclick="modifySetting('prefixNumInvoice', false)"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>

					<label class="label">Numeración actual</label>
					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="number" class="input" placeholder="e.g. 100, 250" id="numInvoice" value="<?php echo $configNumeracion; ?>">
							<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" onclick="modifySetting('numInvoice', false)"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>

					<label class="label">Periodo de guardado de comprobantes de venta (en meses)</label>
					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="number" class="input" placeholder="e.g. 1, 4, 10" id="invoicesSaving" value="<?php echo $configInvoicesSavingMonths; ?>">
							<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" onclick="modifySetting('invoicesSaving', false)"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>
					<p class="is-size-7">Entre mayor sea el periodo de guardado, mayor será la cantidad de información retenida, pudiendo provocar la relentizando del sistema y el computador.</p>

					<h4 class="is-size-5 has-text-info">Sincronización en la nube</h4>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="trvCloudActive" onclick="trvCloudActive()" <?php if ($configCheckTRVCloudService == 1) {
																														echo "checked";
																													} ?>>
						<label class="label" for="trvCloudActive">Activar sincronización en la nube</label>
					</div>

					<div class="fade has-text-centered" id="trvCloudInfoDiv" style="<?php if ($configCheckTRVCloudService != 1) {
																						echo "display: none";
																					} ?>">
						<p>Para configurar y asociar este dispositivo con el servicio en la nube <b>ingrese a <a href="https://www.trvsolutions.com/pos" target="_blank">www.trvsolutions.com/pos</a></b> y cree una cuenta o inicie sesión.
							<br>Luego haga clic en el botón <b>"Asociar nuevo sistema"</b> y escanee el siguiente código QR.
						</p>

						<img src="https://barcode.tec-it.com/barcode.ashx?code=QRCode&data=<?php echo $configCheckTRVCloudToken; ?>&dpi=500" style="width: 120px;margin-bottom: 0;">
						<p><?php echo $configCheckTRVCloudToken; ?></p>
					</div>
				</div>
			</div>

			<div class="has-text-centered">
				<h4 class="is-size-5 has-text-info">Información del negocio</h4>

				<div class="columns">
					<div class="column">
						<label class="label">Nombre de la empresa</label>
						<div class="field has-addons">
							<div class="control has-icons-left is-expanded">
								<input type="text" class="input" placeholder="e.g. TRV Solutions" id="businessName" value="<?php echo $configNombreEmpresa; ?>">
								<span class="icon is-small is-left"><i class="fas fa-store"></i></span>
							</div>

							<div class="control">
								<button class="button backgroundDark" onclick="modifySetting('businessName', false)"><i class="fas fa-circle-check"></i></button>
							</div>
						</div>
					</div>

					<div class="column">
						<label class="label">Correo electrónico del administrador</label>
						<div class="field has-addons">
							<div class="control has-icons-left is-expanded">
								<input type="text" class="input" placeholder="e.g. jhondoe@gmail.com" id="adminEmail" value="<?php echo $configEmailAdmin; ?>">
								<span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
							</div>

							<div class="control">
								<button class="button backgroundDark" onclick="modifySetting('adminEmail', false)"><i class="fas fa-circle-check"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlayTermsCloudService" class="trvModal">
		<div class="trvModal-content trvModal-content-small">
			<span class="delete" onclick="document.getElementById('overlayTermsCloudService').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Servicio Descontinuado</h3>
			</div>

			<div class="trvModal-elements">
				<p>Gracias por su interés en utilizar este servicio. Desafortunadamente, la sincronización en la nube ha sido descontinuada y ya no funciona para esta versión.
					<br>Agradecemos enormemente su apoyo y esperamos poder ofrecerle nuevas soluciones pronto.
				</p>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="document.getElementById('overlayTermsCloudService').style.display='none'">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/admin/include/edit-configuration.php" style="display: none" id="editConfigForm" onsubmit="return editConfigReturn();">
		<input name="editConfigId" id="editConfigId" readonly>
		<input name="editConfigValue" id="editConfigValue" readonly>
		<input type="submit" id="editConfigSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script src="/trv/include/cloudService.js"></script>
	<script>
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

		function newPaymentMethodCheck() {
			var checkboxPayment = document.getElementById('newPaymentMethodCheck').checked;

			if (checkboxPayment == true) {
				document.getElementById('newPaymentMethodDiv').style.display = 'block';
				newNotification('Escriba el nombre del método de pago', 'success');
			} else {
				document.getElementById('newPaymentMethodDiv').style.display = 'none';
				document.getElementById('newPaymentMethod').value = '';
				modifySetting('newPaymentMethod', false);
			}
		}

		function trvCloudActive() {
			var checkboxTRVCloud = document.getElementById('trvCloudActive').checked;

			if (checkboxTRVCloud == true) {
				document.getElementById('overlayTermsCloudService').style.display = 'block';
			} else {
				document.getElementById('trvCloudInfoDiv').style.display = 'none';
				modifySetting('trvCloudActive', false);
			}
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
					} else if (response['configuracion_guardada'] == true) {
						newNotification("Configuración actualizada", "success");
					}

					closeLoader();
				}
			});

			return false;
		}
	</script>
</body>

</html>