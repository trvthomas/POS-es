<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/inventory/include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Historial de inventario</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
	<link rel="stylesheet" href="/trv/include/libraries/flatpickr.min.css">
	<script src="/trv/include/libraries/flatpickr.js"></script>
	<script src="/trv/include/libraries/flatpickr-es.js"></script>
</head>

<body onload="getInventory(true)">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Historial de inventario</h3>
		<p>Consulte todos los movimientos realizados al inventario de productos</p>

		<div class="box">
			<a class="button is-small is-pulled-left" href="/trv/inventory/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<h3 class="is-size-5 has-text-centered">Filtros</h3>
			<hr>

			<div class="columns">
				<div class="column">
					<div class="field">
						<label class="label">Categoría</label>
						<div class="control has-icons-left">
							<span class="select is-fullwidth">
								<select id="inputCategories" oninput="applyFilters()">
									<option value="0">Todas las categorías</option>
									<option value="entry">Ingreso de mercancía</option>
									<option value="exit">Retiro de mercancía</option>
									<option value="adjust">Ajuste de inventario</option>
									<option value="sales">Venta</option>
									<option value="saleCancel">Venta cancelada</option>
								</select>
							</span>

							<span class="icon is-small is-left"><i class="fas fa-table-cells-large"></i></span>
						</div>
					</div>
				</div>

				<div class="column">
					<label class="label">Filtrar por fecha</label>

					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="date" class="input inputDate" id="fechaDesde">
							<span class="icon is-small is-left"><i class="fas fa-calendar-day"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" title="Establecer rango de fechas" onclick="applyFilters()"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>
				</div>
			</div>
			<div class="has-text-centered"><button class="button backgroundDark" style="display: none" id="btnClearFilters" onclick="clearFilters()"><i class="fas fa-eraser"></i>Limpiar filtros</button></div>
		</div>

		<div class="box">
			<nav class="panel filtersBox is-hidden" id="filtersPanel"><button class="button is-loading is-static is-large">Cargando...</button></nav>

			<div class="list has-visible-pointer-controls" id="historyList"></div>

			<nav class="pagination is-centered paginationBox" id="paginationPanel"></nav>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlayVariableMovement" class="trvModal">
		<div class="trvModal-content trvModal-content-small">
			<span class="delete" onclick="document.getElementById('overlayVariableMovement').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Movimiento de ventas variable</h3>
			</div>

			<div class="trvModal-elements">
				<p>Este movimiento o documento se <b>actualiza constantemente</b> con las ventas del día de hoy.
					<br>Para obtener un <b>reporte final de este movimiento</b> revise la información en otro momento.
				</p>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="document.getElementById('overlayVariableMovement').style.display='none'">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/inventory/include/get-history.php" style="display: none" id="getHistoryForm" onsubmit="return getHistoryReturn();">
		<input name="getHistoryCategory" id="getHistoryCategory" readonly>
		<input name="getHistoryDateFrom" id="getHistoryDateFrom" readonly>
		<input name="getHistoryDateTo" id="getHistoryDateTo" readonly>
		<input name="getHistoryPage" id="getHistoryPage" value="1" readonly>
		<input type="submit" id="getHistorySend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script defer type="text/javascript" src="/trv/include/filters-pagination.js"></script>
	<script>
		var dateFrom = "N/A",
			dateTo = "N/A",
			idCategorySearch = 0;

		function getInventory(update) {
			if (update == true) {
				createFiltersBox(false, '', false, false);

				flatpickrCalendars = flatpickr(".inputDate", {
					mode: "range",
					altInput: true,
					locale: "es",
					dateFormat: "Y-m-d",
					minDate: '2021-01-01',
					maxDate: '<?php echo date("Y-m-d"); ?>'
				});
			}

			document.getElementById('getHistoryCategory').value = idCategorySearch;
			document.getElementById('getHistoryDateFrom').value = dateFrom;
			document.getElementById('getHistoryDateTo').value = dateTo;
			document.getElementById('historyList').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Cargando..." width= "100%" loading= "lazy"></div>';

			document.getElementById('getHistorySend').click();
		}

		function onpageNextPage(actualPage) {
			document.getElementById('getHistoryPage').value = actualPage;
			getInventory(false);
		}

		function applyFilters() {
			var createArrayDates = document.getElementById('fechaDesde').value.split(" a ");
			var date1 = "N/A",
				date2 = "N/A";

			if (createArrayDates != "") {
				date1 = createArrayDates[0];
				if (!createArrayDates[1]) {
					date2 = date1;
				} else {
					date2 = createArrayDates[1];
				}
			}

			var category = document.getElementById('inputCategories').value;

			document.getElementById('btnClearFilters').style.display = '';
			dateFrom = date1;
			dateTo = date2;
			idCategorySearch = category;

			getInventory(false);
		}

		function clearFilters() {
			dateFrom = "N/A", dateTo = "N/A", idCategorySearch = 0;
			document.getElementById('fechaDesde').value = "";
			document.getElementById('inputCategories').value = "0";
			getInventory(false);
			document.getElementById('btnClearFilters').style.display = 'none';
		}

		function getHistoryReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/inventory/include/get-history.php',
				data: $('#getHistoryForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['historial'] != "") {
						document.getElementById('historyList').innerHTML = response['historial'];

						hidePagination(response["ultima_pagina"]);
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>