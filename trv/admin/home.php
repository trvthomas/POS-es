<?php include "include/verifySession.php";

$ticketsCambio = 0;

$sql = "SELECT * FROM trvsol_configuration WHERE configName= 'changeTickets'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$ticketsCambio = $row["value"];
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Inicio - Panel de administración</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
	<link rel="stylesheet" href="/trv/include/libraries/flatpickr.min.css">
	<script src="/trv/include/libraries/flatpickr.js"></script>
	<script src="/trv/include/libraries/flatpickr-es.js"></script>
	<script src="/trv/include/libraries/graphs/code/highcharts.js"></script>
	<script src="/trv/include/libraries/graphs/code/highcharts-more.js"></script>
	<script src="/trv/include/libraries/graphs/code/highcharts-3d.js"></script>
	<script src="/trv/include/libraries/graphs/code/modules/exporting.js"></script>
	<script src="/trv/include/libraries/graphs/code/modules/data.js"></script>
	<script src="/trv/include/libraries/graphs/code/modules/export-data.js"></script>
	<script src="/trv/include/libraries/graphs/code/modules/series-label.js"></script>
	<script src="/trv/include/graph-options.js"></script>
</head>

<body onload="loadStats()">
	<?php include "include/header.php"; ?>

	<div class="contentBox has-text-centered">
		<div class="columns is-multiline is-centered has-text-left">
			<div class="column is-one-third">
				<div class="box is-shadowless">
					<h4 class="is-size-6">Ventas Mensuales Realizadas</h4>
					<h3 class="is-size-3"><i class="fas fa-receipt fa-fw"></i> <span id="statsSales">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third">
				<div class="box is-shadowless pastel-bg-green">
					<h4 class="is-size-6">Venta Mensuales</h4>
					<h3 class="is-size-3"><i class="fas fa-sack-dollar fa-fw"></i> <span id="statsSalesMoney">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third">
				<div class="box is-shadowless">
					<h4 class="is-size-6">Promedio Diario</h4>
					<h3 class="is-size-3"><i class="fas fa-magnifying-glass-chart fa-fw"></i> <span id="statsAverage">...</span></h3>
				</div>
			</div>
		</div>

		<div class="columns has-text-centered">
			<div class="column">
				<div class="box" id="statsSalesChart"></div>
				<table class="is-hidden" id="statsChartTable"></table>
			</div>
			<div class="column">
				<div class="box">
					<h3 class="is-size-5">Productos Más Vendidos</h3>
					<hr>

					<div id="statsProducts" class="list has-visible-pointer-controls has-text-left"></div>
				</div>
			</div>
		</div>

		<div class="columns is-multiline is-centered">
			<div class="column is-one-third">
				<a href="/trv/admin/statistics.php">
					<div class="box pastel-bg-orange">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-chart-bar fa-2x"></i> Estadísticas</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/configuration.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-gears fa-2x"></i> Configuración</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/invoices-design.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-brush fa-2x"></i> Diseño Comprobantes</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/products.php">
					<div class="box pastel-bg-bluepurple">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-tshirt fa-2x"></i> Productos</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/barcode-creator.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-barcode fa-2x"></i> Generador de Códigos</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/vouchers.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-tag fa-2x"></i> Bonos y Vouchers</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/inventory/home.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-boxes fa-2x"></i> Inventario</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/users.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-users fa-2x"></i> Vendedores</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/invoices.php">
					<div class="box pastel-bg-yellow">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-file-invoice-dollar fa-2x"></i> Comprobantes de Venta</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<div class="box is-clickable" onclick="document.getElementById('overlayDaySummaries').style.display= 'block';">
					<h3 class="is-size-5"><i class="is-pulled-left fas fa-moon fa-2x"></i> Cierres de Caja</h3>
				</div>
			</div>

			<?php if ($ticketsCambio == 1) { ?>
				<div class="column is-one-third">
					<a href="/trv/admin/change-tickets.php">
						<div class="box">
							<h3 class="is-size-5"><i class="is-pulled-left fas fa-right-left fa-2x"></i> Configuración Tickets de Cambio</h3>
						</div>
					</a>
				</div>
			<?php } ?>

			<div class="column is-one-third">
				<a href="/trv/admin/backups.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-arrows-rotate fa-2x"></i> Copias de Seguridad</h3>
					</div>
				</a>
			</div>

			<?php if (isset($_COOKIE[$prefixCoookie . "TemporaryIdUser"])) { ?>
				<div class="column is-one-third">
					<a href="/trv/admin/logout-temporary.php">
						<div class="box">
							<h3 class="is-size-5"><i class="is-pulled-left fas fa-right-from-bracket fa-2x"></i> Cerrar Sesión</h3>
						</div>
					</a>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlayDaySummaries" class="trvModal">
		<div class="trvModal-content trvModal-content-small">
			<span class="delete" onclick="document.getElementById('overlayDaySummaries').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Seleccione una fecha para ver el reporte</h3>
			</div>

			<div class="trvModal-elements">
				<div class="field">
					<div class="control has-icons-left">
						<input type="date" class="input" id="fechaReporteCierre">
						<span class="icon is-small is-left"><i class="fas fa-calendar-day"></i></span>
					</div>
				</div>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth is-light is-danger" onclick="document.getElementById('overlayDaySummaries').style.display='none'">Cancelar</button>
					</div>
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="goToDaySummary()"><i class="fas fa-circle-check"></i> Aceptar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form action="/trv/admin/include/get-home-sales.php" method="POST" style="display: none" id="getInfoForm" onsubmit="return getInfoReturn();">
		<input name="getInfoToken" value="pos4862" readonly>
		<input id="getInfoSend" type="submit" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function loadStats() {
			flatpickrCalendars = flatpickr("#fechaReporteCierre", {
				altInput: true,
				locale: "es",
				dateFormat: "Y-m-d",
				minDate: '2021-01-01',
				maxDate: '<?php echo date("Y-m-d", strtotime("-1 day")); ?>'
			});

			document.getElementById('getInfoSend').click();
		}

		function goToDaySummary() {
			var dateSelected = document.getElementById('fechaReporteCierre').value;

			if (dateSelected == "") {
				newNotification('Seleccione una fecha', 'error');
			} else {
				openLoader();
				document.getElementById('overlayDaySummaries').style.display = 'none';

				window.location = "/trv/admin/copy-day-summary.php?day=" + dateSelected;
			}
		}

		function generateChart() {
			Highcharts.chart("statsSalesChart", {
				data: {
					table: "statsChartTable"
				},
				chart: {
					type: "line"
				},
				title: {
					text: "Ventas"
				},
				subtitle: {
					text: "Últimos 5 días"
				},
				yAxis: {
					allowDecimals: false,
					title: {
						text: "Ventas"
					}
				},
				tooltip: {
					formatter: function() {
						return "<b>" + this.series.name + "</b><br>$" + thousands_separators(this.point.y);
					}
				}
			});
		}

		function getInfoReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/get-home-sales.php',
				data: $('#getInfoForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotificationError();
					} else if (response['sales'] != "") {
						document.getElementById('statsSales').innerHTML = thousands_separators(response['sales']);
						document.getElementById('statsSalesMoney').innerHTML = "$" + thousands_separators(response['sales_money']);
						document.getElementById('statsAverage').innerHTML = "$" + thousands_separators(response['sales_average']);
						document.getElementById('statsChartTable').innerHTML = "<tr><th>Fecha</th> <th>Ventas</th></tr>" + response['sales_table'];
						document.getElementById('statsProducts').innerHTML = response['sales_products'];

						if (response['sales_table'] == "") {
							document.getElementById('statsSalesChart').innerHTML = "<p class= 'has-text-centered is-size-5'><b>No se encontraron resultados</b></p>";
						} else {
							generateChart();
						}
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>