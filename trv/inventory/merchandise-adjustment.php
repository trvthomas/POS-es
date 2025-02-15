<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/inventory/include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Registrar documento de ajuste de inventario</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
</head>

<body onbeforeunload="return confirmationExit()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Documento de ajuste de inventario</h3>
		<p>Realice un conteo de productos y ajuste las cantidades en stock de los artículos seleccionados</p>

		<div class="box">
			<a class="button is-small is-pulled-left" href="/trv/inventory/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div id="countStock1Instructions">
				<h3 class="is-size-5 has-text-centered">Información general</h3>
				<hr>

				<div class="columns">
					<div class="column">
						<h3 class="is-size-5 has-text-centered">Instrucciones</h3>

						<ol class="has-text-left content" style="margin: auto 6px">
							<li><b>Seleccione la categoría</b> de productos de los cuales desea realizar el conteo.</li>
							<li>Realice el conteo de los productos y registre las nuevas unidades en el campo correspondiente.</li>
							<li>Al finalizar haga clic en <b>"Registrar movimiento"</b> para modificar el stock de los artículos</li>
						</ol>
					</div>

					<div class="column has-text-centered">
						<div class="field">
							<label class="label">Notas opcionales</label>
							<div class="control has-icons-left is-expanded">
								<input type="text" class="input" placeholder="Ingrese notas relacionadas a este ajuste" id="inputNotes" maxlength="400">
								<span class="icon is-small is-left"><i class="fas fa-comment-dots"></i></span>
							</div>
							<p class="is-size-7 has-text-danger" style="margin-top: 2px;"><b>No podrá modificar esto más adelante</b></p>
						</div>
					</div>
				</div>

				<div class="has-text-centered"><button class="button backgroundDark" onclick="startCounting()">Comenzar <i class="fas fa-chevron-right"></i></button></div>
			</div>

			<div id="countStock2Selection" class="fade has-text-centered" style="display: none">
				<div class="columns is-centered">
					<div class="column is-half">
						<div class="field">
							<label class="label">Seleccione la categoría de la cual desea realizar el conteo</label>
							<div class="control has-icons-left">
								<span class="select is-fullwidth">
									<select id="inputCategories">
										<option value="0">Todas las categorías</option>
										<?php
										$sql = "SELECT * FROM trvsol_categories";
										$result = $conn->query($sql);

										if ($result->num_rows > 0) {
											while ($row = $result->fetch_assoc()) {
												echo '<option value="' . $row["id"] . '">' . $row["nombre"] . '</option>';
											}
										}
										?>
									</select>
								</span>
								<span class="icon is-small is-left"><i class="fas fa-table-cells-large"></i></span>
							</div>
						</div>
					</div>
				</div>

				<div class="has-text-centered"><button class="button backgroundDark" onclick="selectionCategory()">Continuar <i class="fas fa-chevron-right"></i></button></div>
			</div>

			<div id="countStock3Count" class="fade" style="display: none">
				<h3 class="is-size-5 has-text-centered">Conteo de inventario</h3>
				<div class="has-text-centered"><button class="button is-info is-light" onclick="document.getElementById('overlayInstructions').style.display = 'block'">Instrucciones y recomendaciones</button></div>

				<div class="list has-visible-pointer-controls" id="inventoryList"></div>

				<div class="has-text-centered"><button class="button backgroundDark" onclick="registrarMovimiento()"><i class="fas fa-circle-check"></i> Registrar movimiento</button></div>
			</div>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlayInstructions" class="trvModal">
		<div class="trvModal-content trvModal-content-small">
			<span class="delete" onclick="document.getElementById('overlayInstructions').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Instrucciones</h3>
			</div>

			<div class="trvModal-elements">
				<ol class="has-text-left content my-auto my-4">
					<li><b>Seleccione la categoría</b> de productos de los cuales desea realizar el conteo.</li>
					<li>Realice el conteo de los productos y registre las nuevas unidades en el campo correspondiente.</li>
					<li>Al finalizar haga clic en <b>"Registrar movimiento"</b> para modificar el stock de los artículos</li>
				</ol>

				<h3 class="is-size-5" style="color: var(--dark-color)">Recomendaciones</h3>
				<ol class="has-text-left content my-auto my-4">
					<li><b>No realice ventas</b> u otros movimientos de inventario mientras realiza el conteo, el <b>stock esperado puede cambiar</b>.</li>
					<li><b>No modifique los valores</b> de stock de los productos los cuales <b>no desea</b> realizar el conteo. El sistema ignorará estos artículos.</li>
				</ol>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="document.getElementById('overlayInstructions').style.display='none'">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/inventory/include/adjustment-get-inventory.php" style="display: none" id="getInventoryForm" onsubmit="return getInventoryReturn();">
		<input name="getInventoryCategory" id="getInventoryCategory" readonly>
		<input type="submit" id="getInventorySend" value="Enviar">
	</form>

	<form method="POST" action="/trv/inventory/include/adjustment-inventory.php" style="display: none" id="registerMovementForm" onsubmit="return registerMovementReturn();">
		<input name="registerMovementNotes" id="registerMovementNotes" readonly>
		<input name="registerMovementArray" id="registerMovementArray" readonly>
		<input type="submit" id="registerMovementSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		var searchTerm = "",
			idCategorySearch = 0;
		var numberProds = 0;
		var preguntarParaCerrar = false;

		function getInventory() {
			document.getElementById('getInventoryCategory').value = idCategorySearch;
			document.getElementById('getInventoryQuery').value = searchTerm;
			document.getElementById('inventoryList').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Cargando..." width= "100%" loading= "lazy"></div>';

			document.getElementById('getInventorySend').click();
		}

		function startCounting() {
			document.getElementById('countStock1Instructions').style.display = 'none';
			document.getElementById('countStock2Selection').style.display = 'block';
			document.getElementById('countStock3Count').style.display = 'none';
		}

		function selectionCategory() {
			var categorySelected = document.getElementById('inputCategories').value;

			if (categorySelected == "") {
				newNotification("Seleccione la categoría", "error");
			} else {
				document.getElementById('getInventoryCategory').value = categorySelected;
				document.getElementById('getInventorySend').click();

				document.getElementById('inventoryList').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Cargando..." width= "100%" loading= "lazy"></div>';
				document.getElementById('countStock1Instructions').style.display = 'none';
				document.getElementById('countStock2Selection').style.display = 'none';
				document.getElementById('countStock3Count').style.display = 'block';
				preguntarParaCerrar = true;
			}
		}

		function registrarMovimiento() {
			var inventoryProds = [];
			for (var x = 0; x < numberProds; x++) {
				var inventoryValue = document.getElementById('inventoryProdValue' + x).value;
				var inventoryProdID = document.getElementById('inventoryProdID' + x).value;
				inventoryValue++;
				inventoryValue--;
				inventoryProdID++;
				inventoryProdID--;

				inventoryProds.push({
					prodID: inventoryProdID,
					inventory: inventoryValue
				});
			}

			var notesMovement = document.getElementById('inputNotes').value;

			var c = confirm("Por favor confirme esta acción.");

			if (c == true) {
				document.getElementById('registerMovementNotes').value = notesMovement;
				document.getElementById('registerMovementArray').value = JSON.stringify(inventoryProds);
				document.getElementById('registerMovementSend').click();

				openLoader();
			}
		}

		function confirmationExit() {
			if (preguntarParaCerrar == true) {
				return "¿Seguro que desea salir?";
			}
		}

		function getInventoryReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/inventory/include/adjustment-get-inventory.php',
				data: $('#getInventoryForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['inventario'] != "") {
						document.getElementById('inventoryList').innerHTML = response['inventario'];
						numberProds = response['numero_productos'];
					}
				}
			});

			return false;
		}

		function registerMovementReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/inventory/include/adjustment-inventory.php',
				data: $('#registerMovementForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['documento_registrado'] == true) {
						preguntarParaCerrar = false;
						newNotification("Movimiento registrado", "success");
						setTimeout(function() {
							window.location = "/trv/inventory/home.php";
						}, 2000);
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>