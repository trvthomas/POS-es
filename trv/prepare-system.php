<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trvsol_pos";
$conn2 = new mysqli($servername, $username, $password);

$sql = "CREATE DATABASE IF NOT EXISTS trvsol_pos";
$conn2->query($sql);

$authorizeEntry = false;
$backupsList = "<td>No se encontraron copias de seguridad</td><td></td><td></td>";

$directory = "include/backups/";
$scanBackups = scandir($directory);

if (isset($scanBackups[2])) {
	$backupsList = "";
	for ($x = 2; $x < count($scanBackups); ++$x) {
		$backupsList .= '<tr>
	<td>' . substr($scanBackups[$x], 0, -4) . '</td>
	</tr>';
	}
}

$conn = new mysqli($servername, $username, $password, $dbname);

$sql2 = "CREATE TABLE IF NOT EXISTS trvsol_configuration(
	id int(11) NOT NULL AUTO_INCREMENT,
	configName text NOT NULL,
	value text NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
$conn->query($sql2);

$sql = "SELECT * FROM trvsol_configuration WHERE configName='businessName' OR configName='templateInvoice' OR configName='templateDayReport'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		if ($row["value"] == "") {
			$authorizeEntry = true;
		}
	}
} else {
	$authorizeEntry = true;
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Configuración inicial</title>

	<?php include "include/head-tracking.php"; ?>
	<script src="/trv/include/libraries/tinymce/tinymce.min.js"></script>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-steps.min.css">
</head>

<body onload="startCreation()">
	<nav class="navbar">
		<div class="navbar-brand">
			<a class="navbar-item" href="/trv"><img src="/trv/media/logo.png" style="width: 100%;max-height: 4rem;"></a>

			<a class="navbar-burger" data-target="headerMobile" onclick="this.classList.toggle('is-active');document.getElementById('headerMobile').classList.toggle('is-active');">
				<span></span>
				<span></span>
				<span></span>
			</a>
		</div>

		<div id="headerMobile" class="navbar-menu">
			<div class="navbar-end">
				<div class="navbar-item">
					<a class="button backgroundDark" href="/trv/prepare-system.php"><i class="fas fa-screwdriver-wrench"></i> Configurar sistema</a>
				</div>
			</div>
		</div>
	</nav><br>

	<div class="contentBox">
		<?php if ($authorizeEntry == true) { ?>
			<div class="box">
				<ul class="steps has-content-centered has-gaps" style="margin-bottom: 0;" id="progressBarDiv"></ul>

				<div class="fade" id="step1">
					<h3 class="is-size-5 has-text-centered">Bienvenido</h3>
					<hr><br>

					<p style="text-align: justify">Gracias por elegir el Sistema POS de <b>TRV Solutions</b>, estamos seguros de que será de gran ayuda para controlar todas las ventas y movimientos de su negocio.
						<br>Antes de comenzar a usarlo, es necesario realizar algunas configuraciones iniciales, haga clic en el botón <b>"Siguiente"</b> para comenzar.
					</p>

					<div class="notification is-info is-light">Este proyecto ahora es de código abierto y no se mantiene activamente. Aunque está bien probado, puede encontrar errores leves. Le invitamos a contribuir al proyecto en nuestro <a href="https://github.com/trvthomas/POS-es" target="_blank">repositorio de GitHub</a></div>
				</div>

				<div class="fade" id="step2" style="display: none">
					<h3 class="is-size-5 has-text-centered">Restaurar copia de seguridad</h3>
					<hr><br>

					<p>Si existen copias de seguridad se mostrarán a continuación. Si desea <b>restablecer los datos</b> de su Sistema POS por favor ubique la copia de seguridad y <b>contáctenos</b>, de contrario haga clic en "Siguiente".</p>

					<table class="table is-striped is-fullwidth">
						<tr>
							<th>Fecha (AAAA-MM-DD-HH-MM)</th>
						</tr>

						<?php echo $backupsList; ?>
					</table>
				</div>

				<div class="fade" id="step3" style="display: none">
					<h3 class="is-size-5 has-text-centered">Información de su negocio</h3>
					<hr><br>

					<div class="columns">
						<div class="column">
							<div class="field">
								<label class="label">Nombre de su empresa</label>
								<div class="control has-icons-left">
									<input type="text" class="input" placeholder="e.g. TRV Solutions" id="inputConfigName">
									<span class="icon is-small is-left"><i class="fas fa-shop"></i></span>
								</div>
							</div>
						</div>

						<div class="column">
							<div class="field">
								<label class="label">Correo electrónico del administrador</label>
								<div class="control has-icons-left">
									<input type="text" class="input" placeholder="e.g. jhondoe@gmail.com" id="inputConfigEmail">
									<span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="fade" id="step4" style="display: none">
					<h3 class="is-size-5 has-text-centered">Comprobantes de venta y cierre de caja</h3>
					<hr><br>

					<div class="columns">
						<div class="column">
							<p class="has-text-centered"><b>Comprobante de venta</b></p>
							<div style="width: 100%;"><textarea id="inputConfigSale"><h1 style= "text-align: center">NOMBRE DE SU NEGOCIO</h1><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Fecha y hora de compra</strong></td><td style="width: 47.9567%;">{{trv_date_purchase}}</td></tr><tr><td style="width: 47.8365%;"><strong>Factura #</strong></td><td style="width: 47.9567%;">{{trv_num_invoice}}</td></tr><tr><td style="width: 47.8365%;"><strong>Atendido por</strong></td><td style="width: 47.9567%;">{{trv_seller}}</td></tr></tbody></table><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Descripci&oacute;n</strong></td><td style="width: 47.9567%; text-align: right;"><span style="margin-right: 15px;"><strong>Precio</strong></span></td></tr></tbody></table><p>{{trv_products}}</p><hr /><p><strong>Forma de pago: </strong>{{trv_payment_method}}</p><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Subtotal</strong></td><td style="width: 47.9567%;">${{trv_subtotal}}</td></tr><tr><td style="width: 47.8365%;"><strong>Descuentos</strong></td><td style="width: 47.9567%;">-${{trv_discount}}</td></tr><tr><td style="width: 47.8365%;"><strong>TOTAL</strong></td><td style="width: 47.9567%;"><strong>${{trv_total}}</strong></td></tr><tr><td style="width: 47.8365%;"><strong>Recibido</strong></td><td style="width: 47.9567%;">${{trv_change_received}}</td></tr><tr><td style="width: 47.8365%;"><strong>Cambio</strong></td><td style="width: 47.9567%;">${{trv_change}}</td></tr></tbody></table><p style="text-align: left;"><strong>Notas adicionales:</strong> {{trv_notes}}</p><h2 style="text-align: center;">Gracias por su compra, vuelva pronto</h2></textarea></div>
						</div>

						<div class="column">
							<p class="has-text-centered"><b>Cierre de caja</b></p>
							<div style="width: 100%;"><textarea id="inputConfigClose"><h2 style="text-align: center;">Cierre de caja</h2><hr /><table style="border-collapse: collapse; width: 100%; height: 59px;" border="0"><tbody><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Fecha y hora de entrada</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_enter}}</td></tr><tr style="height: 17px;"><td style="width: 47.8365%; height: 17px;"><strong>Fecha y hora de salida</strong></td><td style="width: 47.9567%; height: 17px;">{{trv_daysumm_exit}}</td></tr><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Vendedor</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_seller}}</td></tr></tbody></table><hr /><p>Base de caja inicial: <strong>${{trv_daysumm_cash_base}}</strong></p><p><strong>{{trv_daysumm_number_sales}}</strong> ventas realizadas</p><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Ventas en efectivo</strong></td><td style="width: 47.9567%;">${{trv_daysumm_cash_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Ventas en tarjeta</strong></td><td style="width: 47.9567%;">${{trv_daysumm_card_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Ventas en otro método de pago</strong></td><td style="width: 47.9567%;">${{trv_daysumm_other_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Venta total</strong></td><td style="width: 47.9567%;"><strong>${{trv_daysumm_total_sales}}</strong></td></tr></tbody></table><h2 style="text-align: center;">Informes</h2><p>{{trv_daysumm_reports}}</p></textarea></div>
						</div>
					</div>
				</div>

				<div class="fade" id="step5" style="display: none">
					<h3 class="is-size-5 has-text-centered">Usuario principal (administrador)</h3>
					<hr><br>

					<div class="columns">
						<div class="column">
							<div class="field">
								<label class="label">Nombre de usuario</label>
								<div class="control has-icons-left">
									<input type="text" class="input" placeholder="e.g. admin, javier, rosa" id="inputConfigUsername">
									<span class="icon is-small is-left"><i class="fas fa-user"></i></span>
								</div>
							</div>
						</div>

						<div class="column">
							<div class="field">
								<label class="label">Contraseña</label>
								<div class="control has-icons-left has-icons-right">
									<input type="password" class="input" placeholder="Cree una contraseña" id="inputConfigPass">
									<span class="icon is-small is-left"><i class="fas fa-key"></i></span>
									<span class="icon is-small is-right" style="pointer-events: all; cursor: pointer;" onclick="showPass('inputConfigPass')"><i class="fas fa-eye" id="showPassBtninputConfigPass"></i></span>
								</div>
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
					<button class="button backgroundDark is-fullwidth is-hidden" id="buttonPublish" onclick="finishConfig()">Finalizar <i class="fas fa-circle-check"></i></button>
				</div>
			</div>
		<?php } else { ?>
			<div class="box has-text-centered">
				<h1>No puede efectuar esta configuración</h1>
				<p><a href="/trv/home.php">Vuelva a la página de inicio</a> o contáctenos.</p>
			</div>
		<?php } ?>
	</div>

	<br>
	<footer>&copy; <?php echo date("Y") ?>, TRV Solutions - <a style="color: #fff" href="https://www.trvsolutions.com" target="_blank">www.trvsolutions.com</a> - Sistema POS</footer>

	<form method="POST" action="/trv/include/prepare-system.php" style="display: none" id="prepareSystemForm" onsubmit="return prepareSystemReturn();">
		<input name="prepareSystemBusinessName" id="prepareSystemBusinessName" readonly>
		<input name="prepareSystemBusinessEmail" id="prepareSystemBusinessEmail" readonly>
		<input name="prepareSystemSaleTemplate" id="prepareSystemSaleTemplate" readonly>
		<input name="prepareSystemCloseTemplate" id="prepareSystemCloseTemplate" readonly>
		<input name="prepareSystemUsername" id="prepareSystemUsername" readonly>
		<input name="prepareSystemPassword" id="prepareSystemPassword" readonly>

		<input type="submit" id="prepareSystemSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script defer type="text/javascript" src="/trv/include/create-element.js"></script>
	<script>
		function startCreation() {
			createProgressBar(true, JSON.stringify([{
					icon: "circle-info",
					title: "Bienvenida"
				},
				{
					icon: "clock-rotate-left",
					title: "Copia de seguridad"
				},
				{
					icon: "store",
					title: "Info. del negocio"
				},
				{
					icon: "brush",
					title: "Diseño comprobantes"
				},
				{
					icon: "user-gear",
					title: "Usuario principal"
				},
			]));

			createDesigns();
		}

		function showPass(idInput) {
			var getInput = document.getElementById(idInput);
			if (getInput.type === "password") {
				getInput.type = "text";
				document.getElementById("showPassBtn" + idInput).className = "fas fa-eye-slash";
			} else {
				getInput.type = "password";
				document.getElementById("showPassBtn" + idInput).className = "fas fa-eye";
			}
		}

		function createDesigns() {
			var toolbarBtns = "undo redo | formatselect | bold italic underline strikethrough backcolor | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | image table | hr | trv_vars template | preview | code";

			tinymce.init({
				selector: "#inputConfigSale",
				plugins: "preview paste save code template table hr advlist image lists wordcount",
				menubar: "",
				toolbar: toolbarBtns,
				toolbar_sticky: true,
				templates: [{
					title: "General",
					description: "Plantilla general, 100% personalizable",
					content: '<p><img style="display: block; margin-left: auto; margin-right: auto;" src="/trv/media/logo.png" alt="" width="20%" height="auto" /></p><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Fecha y hora de compra</strong></td><td style="width: 47.9567%;">{{trv_date_purchase}}</td></tr><tr><td style="width: 47.8365%;"><strong>Factura #</strong></td><td style="width: 47.9567%;">{{trv_num_invoice}}</td></tr><tr><td style="width: 47.8365%;"><strong>Atendido por</strong></td><td style="width: 47.9567%;">{{trv_seller}}</td></tr></tbody></table><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Descripci&oacute;n</strong></td><td style="width: 47.9567%; text-align: right;"><span style="margin-right: 15px;"><strong>Precio</strong></span></td></tr></tbody></table><p>{{trv_products}}</p><hr /><p><strong>Forma de pago: </strong>{{trv_payment_method}}</p><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Subtotal</strong></td><td style="width: 47.9567%;">${{trv_subtotal}}</td></tr><tr><td style="width: 47.8365%;"><strong>Descuentos</strong></td><td style="width: 47.9567%;">${{trv_discount}}</td></tr><tr><td style="width: 47.8365%;"><strong>TOTAL</strong></td><td style="width: 47.9567%;"><strong>${{trv_total}}</strong></td></tr><tr><td style="width: 47.8365%;"><strong>Recibido</strong></td><td style="width: 47.9567%;">${{trv_change_received}}</td></tr><tr><td style="width: 47.8365%;"><strong>Cambio</strong></td><td style="width: 47.9567%;">${{trv_change}}</td></tr></tbody></table><p style="text-align: left;"><strong>Notas adicionales:</strong> {{trv_notes}}</p><h2 style="text-align: center;">Gracias por su compra, vuelva pronto</h2>'
				}],
				height: 600,
				toolbar_mode: "wrap",
				language: "es",
				placeholder: "Diseñe el comprobante de venta utilizando este editor. Recuerde utilizar las variables.",
				setup: function(editor) {
					editor.ui.registry.addMenuButton("trv_vars", {
						text: "Variable",
						fetch: function(callback) {
							var items = [{
									type: "menuitem",
									text: "Fecha y hora de compra",
									onAction: function() {
										editor.insertContent("{{trv_date_purchase}}");
									}
								},
								{
									type: "menuitem",
									text: "Número de venta",
									onAction: function() {
										editor.insertContent("{{trv_num_invoice}}");
									}
								},
								{
									type: "menuitem",
									text: "Nombre vendedor",
									onAction: function() {
										editor.insertContent("{{trv_seller}}");
									}
								},
								{
									type: "menuitem",
									text: "Lista de productos",
									onAction: function() {
										editor.insertContent("{{trv_products}}");
									}
								},
								{
									type: "menuitem",
									text: "Forma de pago",
									onAction: function() {
										editor.insertContent("{{trv_payment_method}}");
									}
								},
								{
									type: "menuitem",
									text: "Subtotal",
									onAction: function() {
										editor.insertContent("{{trv_subtotal}}");
									}
								},
								{
									type: "menuitem",
									text: "Descuento",
									onAction: function() {
										editor.insertContent("{{trv_discount}}");
									}
								},
								{
									type: "menuitem",
									text: "Total",
									onAction: function() {
										editor.insertContent("{{trv_total}}");
									}
								},
								{
									type: "menuitem",
									text: "Cambio - Recibido",
									onAction: function() {
										editor.insertContent("{{trv_change_received}}");
									}
								},
								{
									type: "menuitem",
									text: "Cambio - Cambio",
									onAction: function() {
										editor.insertContent("{{trv_change}}");
									}
								},
								{
									type: "menuitem",
									text: "Notas",
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
				selector: "#inputConfigClose",
				plugins: "preview paste save code template table hr advlist image lists wordcount",
				menubar: "",
				toolbar: toolbarBtns,
				toolbar_sticky: true,
				templates: [{
					title: "General",
					description: "Plantilla general, 100% personalizable",
					content: '<p><img style="display: block; margin-left: auto; margin-right: auto;" src="/trv/media/logo.png" alt="" width="20%" height="auto" /></p><h2 style="text-align: center;">Cierre de caja</h2><hr /><table style="border-collapse: collapse; width: 100%; height: 59px;" border="0"><tbody><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Fecha y hora de entrada</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_enter}}</td></tr><tr style="height: 17px;"><td style="width: 47.8365%; height: 17px;"><strong>Fecha y hora de salida</strong></td><td style="width: 47.9567%; height: 17px;">{{trv_daysumm_exit}}</td></tr><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Vendedor</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_seller}}</td></tr></tbody></table><hr /><p>Base de caja inicial: <strong>${{trv_daysumm_cash_base}}</strong></p><p><strong>{{trv_daysumm_number_sales}}</strong> ventas realizadas</p><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Ventas en efectivo</strong></td><td style="width: 47.9567%;">${{trv_daysumm_cash_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Ventas en tarjeta</strong></td><td style="width: 47.9567%;">${{trv_daysumm_card_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Venta total</strong></td><td style="width: 47.9567%;"><strong>${{trv_daysumm_total_sales}}</strong></td></tr></tbody></table><h2 style="text-align: center;">Informes</h2><p>{{trv_daysumm_reports}}</p>'
				}],
				height: 600,
				toolbar_mode: "wrap",
				language: "es",
				placeholder: "Diseñe el ticket de cierre de caja utilizando este editor. Recuerde utilizar las variables.",
				setup: function(editor) {
					editor.ui.registry.addMenuButton("trv_vars", {
						text: "Variable",
						fetch: function(callback) {
							var items = [{
									type: "menuitem",
									text: "Fecha y hora de apertura caja",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_enter}}");
									}
								},
								{
									type: "menuitem",
									text: "Fecha y hora de cierre caja",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_exit}}");
									}
								},
								{
									type: "menuitem",
									text: "Nombre vendedor",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_seller}}");
									}
								},
								{
									type: "menuitem",
									text: "Base de caja incial",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_cash_base}}");
									}
								},
								{
									type: "menuitem",
									text: "Num. ventas realizadas",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_number_sales}}");
									}
								},
								{
									type: "menuitem",
									text: "Ventas en efectivo",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_cash_sales}}");
									}
								},
								{
									type: "menuitem",
									text: "Ventas en tarjeta",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_card_sales}}");
									}
								},
								{
									type: "menuitem",
									text: "Venta total",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_total_sales}}");
									}
								},
								{
									type: "menuitem",
									text: "Informes",
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
		}

		function finishConfig() {
			var businessName = document.getElementById('inputConfigName').value;
			var businessEmail = document.getElementById('inputConfigEmail').value;
			var templateSale = tinymce.get('inputConfigSale').getContent();
			var templateCloseCash = tinymce.get('inputConfigClose').getContent();
			var usernameUser = document.getElementById('inputConfigUsername').value;
			var passwordUser = document.getElementById('inputConfigPass').value;

			if (businessName == "" || templateSale == "" || templateCloseCash == "" || usernameUser == "" || passwordUser == "") {
				newNotification('Complete todos los campos', 'error');
			} else if (businessEmail.includes('@') == false && businessEmail.includes('.') == false) {
				newNotification('E-mail inválido', 'error');
			} else {
				document.getElementById('prepareSystemBusinessName').value = businessName;
				document.getElementById('prepareSystemBusinessEmail').value = businessEmail;
				document.getElementById('prepareSystemSaleTemplate').value = templateSale;
				document.getElementById('prepareSystemCloseTemplate').value = templateCloseCash;
				document.getElementById('prepareSystemUsername').value = usernameUser;
				document.getElementById('prepareSystemPassword').value = passwordUser;

				document.getElementById('prepareSystemSend').click();
				openLoader();
			}
		}

		function prepareSystemReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/prepare-system.php',
				data: $('#prepareSystemForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['configuracion_aplicada'] == true) {
						window.location = "/trv/home.php";
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>