<?php include_once "include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Informes y reportes</title>

	<?php include_once "include/head-tracking.php"; ?>
</head>

<body onload="getReports()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Informes y reportes</h3>
		<p>Cree reportes o notas para el cierre de caja, <b>no se modificarán</b> los valores de las ventas</p>

		<div class="box">
			<a class="button is-small is-pulled-left" href="/trv/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<br><br><label class="label">Agregar un informe</label>
			<div class="field has-addons">
				<div class="control has-icons-left is-expanded">
					<input type="text" class="input" placeholder="e.g. Devolución dinero, Pago a proveedor" id="informesInput" maxlength="150" autofocus>
					<span class="icon is-small is-left"><i class="fas fa-comment-dots"></i></span>
				</div>

				<div class="control">
					<button class="button backgroundDark" onclick="crearInforme()"><i class="fas fa-circle-plus iconInButton"></i> Agregar</button>
				</div>
			</div>

			<hr>
			<h3 class="is-size-4">Informes del día</h3>
			<div id="informesDiv" style="max-height: 400px;overflow: auto;"></div>
		</div>
	</div>

	<?php include_once "include/footer.php"; ?>

	<form method="POST" action="/trv/include/get-reports.php" style="display: none" id="getReportsForm" onsubmit="return getReportsReturn();">
		<input name="getReportsID" value="seller189" readonly>
		<input type="submit" id="getReportsSend" value="Enviar">
	</form>

	<form method="POST" action="/trv/include/new-report.php" style="display: none" id="newReportTextForm" onsubmit="return newReportTextReturn();">
		<input name="newReportText" id="newReportText" readonly>
		<input type="submit" id="newReportTextSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function getReports() {
			document.getElementById('informesDiv').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Cargando..." width= "100%" loading= "lazy"></div>';
			document.getElementById('getReportsSend').click();
		}

		function crearInforme() {
			var informe = document.getElementById('informesInput').value;

			if (informe == "") {
				newNotification('Escriba el informe', 'error');
			} else {
				document.getElementById('newReportText').value = informe;
				document.getElementById('newReportTextSend').click();

				openLoader();
			}
		}

		function getReportsReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/get-reports.php',
				data: $('#getReportsForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else {
						document.getElementById('informesDiv').innerHTML = response["reportes"];
					}
				}
			});

			return false;
		}

		function newReportTextReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/new-report.php',
				data: $('#newReportTextForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response["reporte_creado"] == true) {
						newNotification("Informe agregado", "success");
						getReports();

						document.getElementById('informesInput').value = "";
					}

					closeLoader();
				}
			});

			return false;
		}
	</script>
</body>

</html>