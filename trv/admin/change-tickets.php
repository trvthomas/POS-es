<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$changeTicketsActive = "";
$changeTicketsTemplate = "";
$changeTicketsDefaultPrint = "";
$changeTicketsExpireDays = "";
$printingAuto = "";

$sql = "SELECT * FROM trvsol_configuration WHERE configName= 'changeTickets' OR configName= 'changeTicketsTemplate' OR configName= 'changeTicketsPrintDefault' OR configName= 'changeTicketsExpireDays' OR configName= 'printingAuto'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		if ($row["configName"] == "changeTickets") {
			$changeTicketsActive = $row["value"];
		} else if ($row["configName"] == "changeTicketsTemplate") {
			$changeTicketsTemplate = $row["value"];
		} else if ($row["configName"] == "changeTicketsPrintDefault") {
			$changeTicketsDefaultPrint = $row["value"];
		} else if ($row["configName"] == "changeTicketsExpireDays") {
			$changeTicketsExpireDays = $row["value"];
		} else if ($row["configName"] == "printingAuto") {
			$printingAuto = $row["value"];
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Configuración tickets de cambio</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-checkradio.min.css">
	<script src="/trv/include/libraries/tinymce/tinymce.min.js"></script>
</head>

<body>
	<?php include "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Tickets de cambio</h3>
		<p>Modifique los valores por defecto de los tickets de cambio</p>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<p class="has-text-centered">Los tickets de cambio se muestran al <b>final del comprobante de venta</b> y podrán ser utilizados para cambiar los artículos cuando estos son entregados para regalos, por ejemplo.
				<br>A continuación configure el <b>diseño del ticket</b> y seleccione <b>cuántas copias desea imprimir por defecto</b>, este número puede ser modificado al momento de crear una venta.
			</p>

			<div class="buttons is-centered">
				<button class="button is-success" onclick="saveChanges()"><i class="fas fa-floppy-disk"></i> Guardar cambios</button>
				<button class="button backgroundDark" onclick="document.getElementById('overlayFiles').style.display= 'block';"><i class="fas fa-images"></i> Administrador de imágenes</button>
			</div>

			<?php if ($printingAuto == 1) { ?>
				<div class="notification is-warning">El <b>modo de impresión automática está activo</b>, estas configuraciones solo se aplicarán para los comprobantes <b>enviados por e-mail</b>.</div>
			<?php } ?>

			<div class="columns">
				<div class="column">
					<label class="label">Diseño ticket de cambio</label>
					<div style="width: 100%;"><textarea id="editorTicket"><?php echo $changeTicketsTemplate; ?></textarea></div>
				</div>

				<div class="column">
					<label class="label">Número de tickets para imprimir por defecto (max. 5)</label>
					<div class="field">
						<div class="control has-icons-left">
							<input type="number" class="input" placeholder="e.g. 1, 5" id="numberTicketsPrint" max="5" value="<?php echo $changeTicketsDefaultPrint; ?>">
							<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
						</div>
					</div>

					<label class="label">Vigencia para realizar cambios de productos (en días)</label>
					<div class="field">
						<div class="control has-icons-left">
							<input type="number" class="input" placeholder="e.g. 1, 5" id="numberTicketsExpireDays" value="<?php echo $changeTicketsExpireDays; ?>">
							<span class="icon is-small is-left"><i class="fas fa-calendar"></i></span>
						</div>
					</div>
					<p class="is-size-7"><b>Ejemplo</b>: Si escribe 30 (días) en el campo de arriba y la compra se realizó el día <b>1 de noviembre</b>, en el ticket de cambio se mostrará la validez hasta el día <b>1 de diciembre</b></p>
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

	<form method="POST" action="/trv/admin/include/edit-change-tickets.php" style="display: none" id="saveConfigForm" onsubmit="return saveConfigReturn();">
		<input name="saveConfigTemplate" id="saveConfigTemplate" readonly>
		<input name="saveConfigCopies" id="saveConfigCopies" readonly>
		<input name="saveConfigExpire" id="saveConfigExpire" readonly>
		<input type="submit" id="saveConfigSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		var toolbarBtns = "undo redo | formatselect | bold italic underline strikethrough backcolor | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | image table | hr | trv_vars template | preview | code";

		tinymce.init({
			selector: "#editorTicket",
			plugins: "preview paste save code template table hr advlist image lists wordcount",
			menubar: "",
			toolbar: toolbarBtns,
			toolbar_sticky: true,
			templates: [{
				title: "General",
				description: "Plantilla general, 100% personalizable",
				content: '<h2 style="text-align: center;">Ticket para cambio</h2><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Fecha y hora de compra</strong></td><td style="width: 47.9567%;">{{trv_date_purchase}}</td></tr><tr><td style="width: 47.8365%;"><strong>Factura #</strong></td><td style="width: 47.9567%;">{{trv_num_invoice}}</td></tr><tr><td style="width: 47.8365%;"><strong>Atendido por</strong></td><td style="width: 47.9567%;">{{trv_seller}}</td></tr></tbody></table><hr /><p>{{trv_products}}</p>'
			}],
			height: 600,
			toolbar_mode: "wrap",
			language: "es",
			relative_urls: false,
			placeholder: "Diseñe el ticket de cambio utilizando este editor. Recuerde utilizar las variables.",
			setup: function(editor) {
				editor.ui.registry.addMenuButton("trv_vars", {
					text: "Variable",
					fetch: function(callback) {
						var items = [{
								type: "menuitem",
								text: "Fecha y hora de compra (e.g. 01/01/2023 9:00 am)",
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
								text: "Lista de productos sin precio (texto, lista)",
								onAction: function() {
									editor.insertContent("{{trv_products}}");
								}
							}
						];
						callback(items);
					}
				});
			}
		});

		function saveChanges() {
			var ticketTemplate = tinymce.get('editorTicket').getContent();
			var ticketCopies = document.getElementById('numberTicketsPrint').value;
			ticketCopies++;
			ticketCopies--;
			var ticketExpireDays = document.getElementById('numberTicketsExpireDays').value;
			ticketExpireDays++;
			ticketExpireDays--;

			if (ticketTemplate == "" || ticketCopies < 0 || ticketCopies > 5 || ticketExpireDays < 0) {
				newNotification("Revise los campos", "error");
			} else {
				document.getElementById('saveConfigTemplate').value = ticketTemplate;
				document.getElementById('saveConfigCopies').value = ticketCopies;
				document.getElementById('saveConfigExpire').value = ticketExpireDays;

				document.getElementById('saveConfigSend').click();
				openLoader();
			}
		}

		function saveConfigReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/edit-change-tickets.php',
				data: $('#saveConfigForm').serialize(),
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