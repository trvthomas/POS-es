<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Nuevo producto</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-steps.min.css">
	<link rel="stylesheet" href="/trv/include/libraries/bulma-checkradio.min.css">
</head>

<body onload="startCreation()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/products.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>
			<ul class="steps has-content-centered has-gaps" style="margin-bottom: 0;" id="progressBarDiv"></ul>

			<div class="fade" id="step1">
				<h3 class="is-size-4 has-text-centered" style="color: var(--dark-color)">Información general</h3>
				<hr><br>

				<div class="field">
					<label class="label">Nombre del producto*</label>
					<div class="control has-icons-left">
						<input type="text" class="input" placeholder="e.g. Camiseta manga corta, Gafas de sol marca X" id="inputNombre">
						<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
					</div>
				</div>

				<div class="columns">
					<div class="column">
						<div class="field">
							<label class="label">Código o identificador único*</label>
							<div class="control has-icons-left">
								<input type="text" class="input" placeholder="e.g. 123456, CAMISETA01" id="inputCodigo" onkeyup="this.value = this.value.toUpperCase();">
								<span class="icon is-small is-left"><i class="fas fa-barcode"></i></span>
							</div>
						</div>
					</div>

					<div class="column">
						<div class="field">
							<label class="label">Categoría</label>
							<div class="control has-icons-left">
								<span class="select is-fullwidth">
									<select id="inputCategoria">
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
			</div>

			<div class="fade" id="step2" style="display: none">
				<h3 class="is-size-4 has-text-centered" style="color: var(--dark-color)">Precios y costos</h3>
				<hr><br>

				<div class="columns">
					<div class="column">
						<div id="divStaticPrice">
							<div class="field">
								<label class="label">Precio de venta (impuestos incluidos)*</label>
								<div class="control has-icons-left">
									<input type="number" class="input" placeholder="e.g. 50000, 100000" id="inputPrecio">
									<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
								</div>
							</div>
						</div>

						<div class="field mt-1">
							<input type="checkbox" class="is-checkradio" id="checkboxVariablePrice" onclick="toggleVariablePrice()">
							<label class="label" for="checkboxVariablePrice">Producto con precio variable</label>
						</div>

						<div id="divVariablePrice" style="display: none">
							<div class="notification is-light is-info">- Configure a continuación la lista de precios de venta que puede tener este producto.
								<br>- El vendedor podrá seleccionar entre la <b>lista de precios</b> o podrá establecer un <b>valor personalizado mayor al mínimo establecido</b>
							</div>

							<div class="field">
								<label class="label">Valor mínimo</label>
								<div class="control has-icons-left">
									<input type="number" class="input" placeholder="e.g. 50000, 100000" id="inputMinimumPrice">
									<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
								</div>
								<p class="is-size-7">Esta configuración aplica únicamente si la opción <b>"No permitir cambiar el precio de un producto por un menor valor"</b> está activa en la configuración</p>
							</div>

							<button class="button backgroundDark is-fullwidth" onclick="variablePriceAdd()"><i class="fas fa-circle-plus"></i> Agregar precio</button>
							<div class="mt-2" id="listVariablePrices"></div>
							<p class="is-size-7 mt-0">Los campos vacíos se ignoran</p>
						</div>
					</div>

					<div class="column">
						<div class="field">
							<label class="label">Precio de compra</label>
							<div class="control has-icons-left">
								<input type="number" class="input" placeholder="e.g. 50000, 100000" id="inputPrecioCompra">
								<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="fade" id="step3" style="display: none">
				<h3 class="is-size-4 has-text-centered" style="color: var(--dark-color)">Imagen del producto</h3>
				<hr><br>

				<form action="/trv/media/uploads/upload-image-product.php" method="POST" enctype="multipart/form-data" id="formProductImage" style="display: none">
					<input type="file" name="productImage" id="productImage" accept="image/*" class="newProductImages">
					<input id="uploadImageDeleteURL" name="uploadImageDeleteURL" style="display: none" readonly>
				</form>

				<div class="newProductImageBox">
					<div style="width: 100%;cursor: pointer;" title="Cambiar imagen"><img src="/trv/media/select-image.png" alt="Imagen no disponible" id="previewImage" onclick="selectImage()"></div>

					<div class="buttons is-centered">
						<button class="button" title="Cambiar imagen" onclick="selectImage()"><i class="fas fa-arrows-rotate"></i></button>
						<button class="button is-danger is-light" title="Eliminar imagen" onclick="deleteImageSet()" id="deleteImgBtn"><i class="fas fa-trash-alt"></i></button>
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
				<button class="button backgroundDark is-fullwidth is-hidden" id="buttonPublish" onclick="addProduct()">Crear <i class="fas fa-circle-plus"></i></button>
			</div>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form method="POST" action="/trv/admin/include/add-product.php" style="display: none" id="addProdForm" onsubmit="return addProdReturn();">
		<input name="addProdName" id="addProdName" readonly>
		<input name="addProdImage" id="addProdImage" readonly>
		<input name="addProdPrice" id="addProdPrice" readonly>
		<input name="addProdBarcode" id="addProdBarcode" readonly>
		<input name="addProdCategory" id="addProdCategory" readonly>
		<input name="addProdPurchasePrice" id="addProdPurchasePrice" readonly>
		<input name="addProdIsVariable" id="addProdIsVariable" readonly>
		<input name="addProdArrayPrices" id="addProdArrayPrices" readonly>

		<input type="submit" id="addProdSend" value="Enviar">
	</form>

	<form action="/trv/media/uploads/delete-image-product.php" method="POST" style="display: none" id="deleteImageForm" onsubmit="return deleteImage();">
		<input id="deleteImageURL" name="deleteImageURL" readonly>
		<input id="deleteImageSend" type="submit" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script defer type="text/javascript" src="/trv/include/create-element.js"></script>
	<script>
		var uploadingImage = false;

		function startCreation() {
			createProgressBar(true, JSON.stringify([{
					icon: "circle-info",
					title: "Info. general"
				},
				{
					icon: "dollar-sign",
					title: "Precios y costos"
				},
				{
					icon: "image",
					title: "Imagen"
				}
			]));
		}

		function selectImage() {
			if (uploadingImage == true) {
				newNotification('Se está cargando otra imagen, por favor espere', 'error');
			} else {
				document.getElementById('uploadImageDeleteURL').value = document.getElementById('addProdImage').value;
				document.getElementById('productImage').click();
			}
		}

		function deleteImageSet() {
			document.getElementById('deleteImageURL').value = document.getElementById('addProdImage').value;
			document.getElementById('deleteImgBtn').innerHTML = '<i class= "fas fa-spinner fa-spin"></i>';
			document.getElementById('deleteImageSend').click();
		}

		function addProduct() {
			var prodNombre = document.getElementById('inputNombre').value;
			var prodPrecio = document.getElementById('inputPrecio').value;
			var prodCompra = document.getElementById('inputPrecioCompra').value;
			var prodCategoria = document.getElementById('inputCategoria').value;
			var prodBarcode = document.getElementById('inputCodigo').value;
			var prodIsVariable = 0;
			prodPrecio++;
			prodPrecio--;
			prodCompra++;
			prodCompra--;
			getArrayVariablePrices();

			if (document.getElementById('checkboxVariablePrice').checked == true) {
				prodIsVariable = 1;
				prodPrecio = document.getElementById('inputMinimumPrice').value;
			}

			if (prodNombre == "" || prodPrecio < 0 || prodCompra < 0 || prodCategoria == "" || prodBarcode == "") {
				newNotification('Verifique los campos', 'error');
			} else {
				document.getElementById('addProdName').value = prodNombre;
				document.getElementById('addProdPrice').value = prodPrecio;
				document.getElementById('addProdBarcode').value = prodBarcode;
				document.getElementById('addProdCategory').value = prodCategoria;
				document.getElementById('addProdPurchasePrice').value = prodCompra;
				document.getElementById('addProdIsVariable').value = prodIsVariable;
				document.getElementById('addProdArrayPrices').value = JSON.stringify(arrayPrices);

				document.getElementById('addProdSend').click();
				openLoader();
			}
		}

		function toggleVariablePrice() {
			var check = document.getElementById('checkboxVariablePrice').checked;

			if (check == true) {
				document.getElementById('divVariablePrice').style.display = 'block';
				document.getElementById('divStaticPrice').style.display = 'none';
			} else {
				document.getElementById('divVariablePrice').style.display = 'none';
				document.getElementById('divStaticPrice').style.display = 'block';
			}
		}

		var numberIdInputs = 0;
		var arrayPrices = [];

		function variablePriceAdd() {
			var createInp = document.createElement("DIV");
			var attributeInp1 = document.createAttribute("id");
			attributeInp1.value = "fieldVariablePrice" + numberIdInputs;
			var attributeInp2 = document.createAttribute("class");
			attributeInp2.value = "field";
			var appendInp = document.getElementById('listVariablePrices').appendChild(createInp);
			appendInp.setAttributeNode(attributeInp1);
			appendInp.setAttributeNode(attributeInp2);

			document.getElementById('fieldVariablePrice' + numberIdInputs).innerHTML += '<div class="control has-icons-left"><input type= "number" class= "input" placeholder= "e.g. 50000, 100000" id= "inputVariablePrice' + numberIdInputs + '"><span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span></div>';
			numberIdInputs++;
		}

		function getArrayVariablePrices() {
			arrayPrices = [];
			for (var x = 0; x < numberIdInputs; x++) {
				var input = document.getElementById('inputVariablePrice' + x).value;
				input++;
				input--;

				if (input > 0) {
					arrayPrices.push(input);
				}
			}
		}

		function confirmationExit() {
			if (preguntarParaCerrar == true) {
				return "¿Seguro que desea salir?";
			}
		}

		function addProdReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/add-product.php',
				data: $('#addProdForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
						closeLoader();
					} else if (response['codigo_existe'] == true) {
						newNotification('El código ya está en uso', 'error');
						closeLoader();
					} else if (response['producto_creado'] == true) {
						window.location = "/trv/admin/products.php";
					}
				}
			});

			return false;
		}

		function deleteImage() {
			$.ajax({
				type: 'POST',
				url: '/trv/media/uploads/delete-image-product.php',
				data: $('#deleteImageForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Error al eliminar la imagen', 'error');
					} else if (response['img_deleted'] == true) {
						document.getElementById('addProdImage').value = "";
						document.getElementById('previewImage').src = "/trv/media/select-image.png";
					}
					document.getElementById('deleteImgBtn').innerHTML = '<i class= "fas fa-trash-alt"></i>';
				}
			});

			return false;
		}

		$(document).ready(function(e) {
			$("#formProductImage").on('change', (function(e) {
				document.getElementById('previewImage').src = '/trv/media/loader.gif';
				uploadingImage = true;

				$.ajax({
					url: "/trv/media/uploads/upload-image-product.php",
					type: "POST",
					data: new FormData(this),
					dataType: 'json',
					contentType: false,
					processData: false,
					success: function(data) {
						if (data['error_imagen'] == true) {
							newNotification('La imagen es muy pesada o grande', 'error');
							document.getElementById('previewImage').src = "/trv/media/select-image.png"
						} else if (data['url_imagen'] != "") {
							document.getElementById('addProdImage').value = data['url_imagen'];
							document.getElementById('previewImage').src = data['url_imagen'];
						}

						document.getElementById('productImage').value = "";
						uploadingImage = false;
					}
				});
			}));
		});
	</script>
</body>

</html>