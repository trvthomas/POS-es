<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$templateInvoice = "";
$templateDayReport = "";
$printingAuto = "";

$sql = "SELECT * FROM trvsol_configuration WHERE configName= 'templateInvoice' OR configName= 'templateDayReport' OR configName= 'printingAuto'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		if ($row["configName"] == "templateInvoice") {
			$templateInvoice = $row["value"];
		} else if ($row["configName"] == "templateDayReport") {
			$templateDayReport = $row["value"];
		} else if ($row["configName"] == "printingAuto") {
			$printingAuto = $row["value"];
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Modificar el diseño de los comprobantes</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<script src="/trv/include/libraries/tinymce/tinymce.min.js"></script>
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Diseño comprobantes</h3>
		<p>Personalice el diseño de los comprobantes de venta y cierres de caja, tanto impresos como enviados por e-mail</p>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="buttons is-centered">
				<button class="button is-success" onclick="saveChanges()"><i class="fas fa-floppy-disk"></i> Guardar cambios</button>
				<button class="button backgroundDark" onclick="document.getElementById('overlayFiles').style.display= 'block';"><i class="fas fa-images"></i> Administrador de imágenes</button>
			</div>

			<?php if ($printingAuto == 1) { ?>
				<div class="notification is-warning">El <b>modo de impresión automática está activo</b>, estas configuraciones solo se aplicarán para los comprobantes <b>enviados por e-mail y en PDF</b>.
					<br><a class="button is-warning is-inverted is-outlined" href="/trv/admin/invoices-design-auto.php">Modificar comprobantes impresos</a>
				</div>
			<?php } ?>

			<div class="columns has-text-centered">
				<div class="column">
					<label class="label">Comprobante de venta</label>
					<div style="width: 100%;"><textarea id="editorComprobante"><?php echo $templateInvoice; ?></textarea></div>
				</div>

				<div class="column">
					<label class="label">Cierre de caja</label>
					<div style="width: 100%;"><textarea id="editorCierreCaja"><?php echo $templateDayReport; ?></textarea></div>
				</div>
			</div>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlayFiles" class="trvModal">
		<div class="trvModal-content trvModal-content">
			<span class="delete" onclick="document.getElementById('overlayFiles').style.display='none'"></span>

			<div class="trvModal-elements">
				<iframe src="/trv/admin/images-manager.php" height="800" width="100%" style="border: none"></iframe>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/admin/include/edit-invoices-design.php" style="display: none" id="editDesignForm" onsubmit="return editDesignReturn();">
		<input name="editDesignTemplateInvoice" id="editDesignTemplateInvoice" readonly>
		<input name="editDesignTemplateDaySummary" id="editDesignTemplateDaySummary" readonly>
		<input type="submit" id="editDesignSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		var toolbarBtns = "undo redo | formatselect | bold italic underline strikethrough backcolor | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | image table | trv_vars hr template preview code";

		tinymce.init({
			selector: "#editorComprobante",
			plugins: "preview save code template table advlist image lists wordcount",
			menubar: "",
			toolbar: toolbarBtns,
			toolbar_sticky: true,
			templates: [{
				title: "General",
				description: "Plantilla general, 100% personalizable",
				content: '<p><img style="display: block; margin-left: auto; margin-right: auto;" src="/trv/media/logo.png" alt="" width="20%" height="auto" /></p><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Fecha y hora de compra</strong></td><td style="width: 47.9567%;">{{trv_date_purchase}}</td></tr><tr><td style="width: 47.8365%;"><strong>Factura #</strong></td><td style="width: 47.9567%;">{{trv_num_invoice}}</td></tr><tr><td style="width: 47.8365%;"><strong>Atendido por</strong></td><td style="width: 47.9567%;">{{trv_seller}}</td></tr></tbody></table><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Descripci&oacute;n</strong></td><td style="width: 47.9567%; text-align: right;"><span style="margin-right: 15px;"><strong>Precio</strong></span></td></tr></tbody></table><p>{{trv_products}}</p><hr /><p><strong>Forma de pago: </strong>{{trv_payment_method}}</p><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Subtotal</strong></td><td style="width: 47.9567%;">${{trv_subtotal}}</td></tr><tr><td style="width: 47.8365%;"><strong>Descuentos</strong></td><td style="width: 47.9567%;">-${{trv_discount}}</td></tr><tr><td style="width: 47.8365%;"><strong>TOTAL</strong></td><td style="width: 47.9567%;"><strong>${{trv_total}}</strong></td></tr><tr><td style="width: 47.8365%;"><strong>Recibido</strong></td><td style="width: 47.9567%;">${{trv_change_received}}</td></tr><tr><td style="width: 47.8365%;"><strong>Cambio</strong></td><td style="width: 47.9567%;">${{trv_change}}</td></tr></tbody></table><p style="text-align: left;"><strong>Notas adicionales:</strong> {{trv_notes}}</p><h2 style="text-align: center;">Gracias por su compra, vuelva pronto</h2>'
			}],
			height: 600,
			toolbar_mode: "wrap",
			language: "es",
			relative_urls: false,
			placeholder: "Diseñe el comprobante de venta utilizando este editor. Recuerde utilizar las variables.",
			setup: function(editor) {
				editor.ui.registry.addMenuButton("trv_vars", {
					text: "Variable",
					fetch: function(callback) {
						var items = [{
								type: "menuitem",
								text: "Fecha y hora de compra (texto)",
								onAction: function() {
									editor.insertContent("{{trv_date_purchase}}");
								}
							},
							{
								type: "menuitem",
								text: "Número de venta (e.g. 450 o PRE450)",
								onAction: function() {
									editor.insertContent("{{trv_num_invoice}}");
								}
							},
							{
								type: "menuitem",
								text: "Nombre vendedor (e.g. Jhon)",
								onAction: function() {
									editor.insertContent("{{trv_seller}}");
								}
							},
							{
								type: "menuitem",
								text: "Lista de productos (texto, lista)",
								onAction: function() {
									editor.insertContent("{{trv_products}}");
								}
							},
							{
								type: "menuitem",
								text: "Forma de pago (e.g. Efectivo)",
								onAction: function() {
									editor.insertContent("{{trv_payment_method}}");
								}
							},
							{
								type: "menuitem",
								text: "Subtotal (e.g. 50.000)",
								onAction: function() {
									editor.insertContent("{{trv_subtotal}}");
								}
							},
							{
								type: "menuitem",
								text: "Descuento (e.g. 5.000)",
								onAction: function() {
									editor.insertContent("{{trv_discount}}");
								}
							},
							{
								type: "menuitem",
								text: "Total (e.g. 45.000)",
								onAction: function() {
									editor.insertContent("{{trv_total}}");
								}
							},
							{
								type: "menuitem",
								text: "Cambio - Recibido (e.g. 5.000 Tarjeta $10.000)",
								onAction: function() {
									editor.insertContent("{{trv_change_received}}");
								}
							},
							{
								type: "menuitem",
								text: "Cambio - Cambio (e.g. 10.000)",
								onAction: function() {
									editor.insertContent("{{trv_change}}");
								}
							},
							{
								type: "menuitem",
								text: "Notas (texto)",
								onAction: function() {
									editor.insertContent("{{trv_notes}}");
								}
							}
						];
						callback(items);
					}
				});
			}
		});

		//Cierre de caja
		tinymce.init({
			selector: "#editorCierreCaja",
			plugins: "preview save code template table advlist image lists wordcount",
			menubar: "",
			toolbar: toolbarBtns,
			toolbar_sticky: true,
			templates: [{
					title: "General",
					description: "Plantilla general, 100% personalizable",
					content: '<h2 style="text-align: center;">Cierre de caja</h2><hr /><table style="border-collapse: collapse; width: 100%; height: 59px;" border="0"><tbody><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Fecha y hora de entrada</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_enter}}</td></tr><tr style="height: 17px;"><td style="width: 47.8365%; height: 17px;"><strong>Fecha y hora de salida</strong></td><td style="width: 47.9567%; height: 17px;">{{trv_daysumm_exit}}</td></tr><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Vendedor</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_seller}}</td></tr></tbody></table><hr /><p>Base de caja inicial: <strong>${{trv_daysumm_cash_base}}</strong></p><p><strong>{{trv_daysumm_number_sales}}</strong> ventas realizadas</p><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Ventas en efectivo</strong></td><td style="width: 47.9567%;">${{trv_daysumm_cash_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Ventas en tarjeta</strong></td><td style="width: 47.9567%;">${{trv_daysumm_card_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Venta total</strong></td><td style="width: 47.9567%;"><strong>${{trv_daysumm_total_sales}}</strong></td></tr></tbody></table><h2 style="text-align: center;">Informes</h2><p>{{trv_daysumm_reports}}</p>'
				},
				{
					title: "General con método de pago personalizado",
					description: "Plantilla general con método de pago personalizado, si activo. 100% personalizable",
					content: '<h2 style="text-align: center;">Cierre de caja</h2><hr /><table style="border-collapse: collapse; width: 100%; height: 59px;" border="0"><tbody><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Fecha y hora de entrada</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_enter}}</td></tr><tr style="height: 17px;"><td style="width: 47.8365%; height: 17px;"><strong>Fecha y hora de salida</strong></td><td style="width: 47.9567%; height: 17px;">{{trv_daysumm_exit}}</td></tr><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Vendedor</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_seller}}</td></tr></tbody></table><hr /><p>Base de caja inicial: <strong>${{trv_daysumm_cash_base}}</strong></p><p><strong>{{trv_daysumm_number_sales}}</strong> ventas realizadas</p><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Ventas en efectivo</strong></td><td style="width: 47.9567%;">${{trv_daysumm_cash_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Ventas en tarjeta</strong></td><td style="width: 47.9567%;">${{trv_daysumm_card_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Ventas {{trv_daysumm_other_name}}</strong></td><td style="width: 47.9567%;">${{trv_daysumm_other_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Venta total</strong></td><td style="width: 47.9567%;"><strong>${{trv_daysumm_total_sales}}</strong></td></tr></tbody></table><h2 style="text-align: center;">Informes</h2><p>{{trv_daysumm_reports}}</p>'
				}
			],
			height: 600,
			toolbar_mode: "wrap",
			language: "es",
			relative_urls: false,
			placeholder: "Diseñe el ticket de cierre de caja utilizando este editor. Recuerde utilizar las variables.",
			setup: function(editor) {
				editor.ui.registry.addMenuButton("trv_vars", {
					text: "Variable",
					fetch: function(callback) {
						var items = [{
								type: "menuitem",
								text: "Fecha y hora de apertura caja (e.g. 01/01/2023 9:00 am)",
								onAction: function() {
									editor.insertContent("{{trv_daysumm_enter}}");
								}
							},
							{
								type: "menuitem",
								text: "Fecha y hora de cierre caja (e.g. 01/01/2023 9:00 pm)",
								onAction: function() {
									editor.insertContent("{{trv_daysumm_exit}}");
								}
							},
							{
								type: "menuitem",
								text: "Nombre vendedor (e.g. Jhon)",
								onAction: function() {
									editor.insertContent("{{trv_daysumm_seller}}");
								}
							},
							{
								type: "menuitem",
								text: "Base de caja incial (e.g. 50.000 Turno Jhon: $52.000)",
								onAction: function() {
									editor.insertContent("{{trv_daysumm_cash_base}}");
								}
							},
							{
								type: "menuitem",
								text: "Num. ventas realizadas (e.g. 15)",
								onAction: function() {
									editor.insertContent("{{trv_daysumm_number_sales}}");
								}
							},
							{
								type: "menuitem",
								text: "Ventas en efectivo (e.g. 50.000)",
								onAction: function() {
									editor.insertContent("{{trv_daysumm_cash_sales}}");
								}
							},
							{
								type: "menuitem",
								text: "Ventas en tarjeta (e.g. 50.000)",
								onAction: function() {
									editor.insertContent("{{trv_daysumm_card_sales}}");
								}
							},
							{
								type: "menuitem",
								text: "Nombre método pago personalizado (si aplica) (e.g. Billetera digital)",
								onAction: function() {
									editor.insertContent("{{trv_daysumm_other_name}}");
								}
							},
							{
								type: "menuitem",
								text: "Ventas método pago personalizado (si aplica) (e.g. 50.000)",
								onAction: function() {
									editor.insertContent("{{trv_daysumm_other_sales}}");
								}
							},
							{
								type: "menuitem",
								text: "Venta total (e.g. 100.000)",
								onAction: function() {
									editor.insertContent("{{trv_daysumm_total_sales}}");
								}
							},
							{
								type: "menuitem",
								text: "Informes (texto)",
								onAction: function() {
									editor.insertContent("{{trv_daysumm_reports}}");
								}
							}
						];
						callback(items);
					}
				});
			}
		});

		function saveChanges() {
			var templateInvoice = tinymce.get('editorComprobante').getContent();
			var templateDayReport = tinymce.get('editorCierreCaja').getContent();

			if (templateInvoice == "" || templateDayReport == "") {
				newNotification("Las plantillas no pueden estar vacías", "error");
			} else {
				document.getElementById('editDesignTemplateInvoice').value = templateInvoice;
				document.getElementById('editDesignTemplateDaySummary').value = templateDayReport;

				document.getElementById('editDesignSend').click();
				openLoader();
			}
		}

		function editDesignReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/edit-invoices-design.php',
				data: $('#editDesignForm').serialize(),
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