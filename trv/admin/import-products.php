<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Importar productos masivamente</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-steps.min.css">
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
</head>

<body onload="startCreation()">
	<?php include "include/header.php"; ?>

	<div class="contentBox">
		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/products.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>
			<ul class="steps has-content-centered has-gaps" style="margin-bottom: 0;" id="progressBarDiv"></ul>

			<div class="fade" id="step1">
				<h3 class="is-size-5 has-text-centered">Arrastre o seleccione el archivo de Excel</h3>
				<hr><br>

				<div class="columns">
					<div class="column is-two-fifths">
						<a class="button is-fullwidth backgroundDark" href="/trv/media/plantilla-carga-productos-masiva.xlsx" download><i class="fas fa-file-excel"></i> Descargar plantilla</a>

						<hr>
						<h3 class="is-size-5 has-text-centered" style="margin-bottom: 0">Códigos categorías</h3>
						<p class="has-text-centered">En la plantilla de Excel, en el campo de <b>"Categoría"</b> escriba el código correspondiente, a continuación se muestra la lista.</p>

						<div class="list has-visible-pointer-controls">
							<?php
							$sql = "SELECT * FROM trvsol_categories";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									echo '<div class="list-item">
		<div class="list-item-image">
		<figure class="image is-64x64"><div class= "categoryColorImage" style= "background-color: ' . $row["color"] . ';color: ' . $row["color_txt"] . '"><span>' . strtoupper(substr($row["nombre"], 0, 2)) . '</span></div></figure>
		</div>
		
		<div class="list-item-content">
		<div class="list-item-title">' . $row["nombre"] . '</div>
		</div>
		
		<div class="list-item-controls">
		<div class="level">
		<div class="level-item has-text-centered">
		<div>
			<p class="is-size-5">Código</p>
			<p class="is-size-3">' . $row["id"] . '</p>
		</div>
		</div>
		</div>
		</div>
	</div>';
								}
								echo "</table>";
							} else {
								echo "Hubo un error";
							}
							?>
						</div>

					</div>

					<div class="column">
						<form action="/trv/media/uploads/upload-excel-products.php" method="POST" enctype="multipart/form-data" id="uploadExcelForm">
							<input type="file" name="excelFile" accept=".xlsx">
						</form>
					</div>
				</div>
			</div>

			<div class="fade" id="step2" style="display: none">
				<h3 class="is-size-5 has-text-centered">Revise la información a importar</h3>
				<p class="has-text-centered">Revise los datos a importar a continuación, si <b>existen códigos de barras repetidos</b> seleccione qué desea hacer con ellos.</p>
				<hr><br>

				<div class="columns is-centered">
					<div class="column is-half">
						<div class="field has-text-centered">
							<label class="label">Acción a realizar con códigos de barras repetidos</label>
							<div class="control has-icons-left">
								<span class="select is-fullwidth">
									<select id="inputRepeatedBarcodes">
										<option value="1">No subir productos</option>
										<option value="2">Asignar otro código de barras aleatorio</option>
									</select>
								</span>

								<span class="icon is-small is-left"><i class="fas fa-question"></i></span>
							</div>
						</div>
					</div>
				</div>

				<hr>
				<p class="has-text-centered">Se encontraron <b><span id="verifyNumberProds">ERROR</span> elementos</b></p>
				<div class="table-container" id="checkProductsDiv"></div>

				<div class="has-text-centered">
					<button class="button backgroundDark" onclick="addProducts()"><i class="fas fa-circle-check"></i> Importar información</button>
					<br><button class="button is-danger is-inverted is-small" onclick="cancelImport()"><i class="fas fa-circle-xmark"></i> Cancelar y volver a comenzar</button>
				</div>
			</div>

			<div class="fade" id="step3" style="display: none">
				<h3 class="is-size-5 has-text-centered">Resultado de la importación</h3>
				<hr><br>

				<div class="level">
					<div class="level-item has-text-centered">
						<div>
							<p class="heading">Datos recibidos</p>
							<p class="title" id="totalNumberProds">0</p>
						</div>
					</div>
					<div class="level-item has-text-centered">
						<div>
							<p class="heading has-text-success">Datos importados</p>
							<p class="title has-text-success" id="numberProdsImported">0</p>
						</div>
					</div>
				</div>

				<div class="has-text-centered">
					<a href="/trv/admin/products.php" class="button backgroundDark"><i class="fas fa-chevron-left"></i> Volver</a>
				</div>
			</div>
		</div>

		<div class="columns is-hidden">
			<div class="column">
				<button class="button backgroundDark is-fullwidth is-invisible" id="buttonPrevious" onclick="nextStep(-1)"><i class="fas fa-chevron-left"></i> Anterior</button>
			</div>

			<div class="column has-text-right">
				<button class="button backgroundDark is-fullwidth" id="buttonNext" onclick="nextStep(1)">Siguiente <i class="fas fa-chevron-right"></i></button>
				<button class="button backgroundDark is-fullwidth is-hidden" id="buttonPublish" onclick="addProduct()">Crear <i class="fas fa-circle-plus"></i></button>
			</div>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form action="/trv/media/uploads/import-products.php" method="POST" style="display: none" id="importProdsForm" onsubmit="return importProdsReturn();">
		<input id="importProductsFileName" name="importProductsFileName" readonly>
		<input id="importProductsActionRepeated" name="importProductsActionRepeated" readonly>
		<input id="importProductsSend" type="submit" value="Enviar">
	</form>

	<form action="/trv/media/uploads/cancel-import-products.php" method="POST" style="display: none" id="cancelImportProdsForm" onsubmit="return cancelImportProdsReturn();">
		<input id="cancelFileName" name="cancelFileName" readonly>
		<input id="cancelSend" type="submit" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script defer type="text/javascript" src="/trv/include/create-element.js"></script>
	<script>
		function startCreation() {
			createProgressBar(false, JSON.stringify([{
					icon: "file-excel",
					title: "Subir"
				},
				{
					icon: "clipboard-list",
					title: "Verificar"
				},
				{
					icon: "circle-check",
					title: "Resultados"
				}
			]));
		}

		function addProducts() {
			var c = confirm("Por favor confirme esta acción");

			if (c == true) {
				var actionRepeated = document.getElementById('inputRepeatedBarcodes').value;

				document.getElementById('importProductsActionRepeated').value = actionRepeated;
				document.getElementById('importProductsSend').click();

				openLoader();
			}
		}

		function cancelImport() {
			var c = confirm("¿Está seguro que desea cancelar la operación?");

			if (c == true) {
				document.getElementById('cancelSend').click();
				openLoader();
			}
		}

		function importProdsReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/media/uploads/import-products.php',
				data: $('#importProdsForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['productos_importados'] != "" && response['productos_totales'] != "") {
						nextStep(1);
						document.getElementById('numberProdsImported').innerHTML = response['productos_importados'];
						document.getElementById('totalNumberProds').innerHTML = response['productos_totales'];
					}
					closeLoader();
				}
			});

			return false;
		}

		function cancelImportProdsReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/media/uploads/cancel-import-products.php',
				data: $('#cancelImportProdsForm').serialize(),
				dataType: 'json',
				success: function(response) {
					jumpStep(1);
					closeLoader();
				}
			});

			return false;
		}

		$(document).ready(function(e) {
			$("#uploadExcelForm").on('change', (function(e) {
				openLoader();

				$.ajax({
					url: "/trv/media/uploads/upload-excel-products.php",
					type: "POST",
					data: new FormData(this),
					dataType: 'json',
					contentType: false,
					processData: false,
					success: function(data) {
						if (data['error_archivo'] == true) {
							newNotification('El archivo no es compatible o es muy pesado', 'error');
						} else if (data['products_found'] <= 0) {
							newNotification('No se encontraron productos, revise el archivo', 'error');
						} else if (data['url_excel'] != "" && data['products_list'] != "") {
							nextStep(1);

							document.getElementById('checkProductsDiv').innerHTML = data['products_list'];
							document.getElementById('verifyNumberProds').innerHTML = data['products_found'];
							document.getElementById('importProductsFileName').value = data['url_excel'];
							document.getElementById('cancelFileName').value = data['url_excel'];
						}

						closeLoader();
						document.getElementById('uploadExcelForm').reset();
					}
				});
			}));
		});
	</script>
</body>

</html>