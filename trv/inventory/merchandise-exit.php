<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/inventory/include/verifySession.php";
include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/PHPColors.php";

use Mexitek\PHPColors\Color;

$showCategories = "";
//Categories
$sql3 = "SELECT * FROM trvsol_categories";
$result3 = $conn->query($sql3);

if ($result3->num_rows > 0) {
	while ($row3 = $result3->fetch_assoc()) {
		$secondColor = "#fff";
		$originalColor = new Color($row3["color"]);
		if ($originalColor->isLight()) {
			$secondColor = $originalColor->darken(35);
		} else {
			$secondColor = $originalColor->lighten(35);
		}

		$onclickCategory = "getProdsCategory(" . $row3["id"] . ", '" . $row3["nombre"] . "', '')";

		$showCategories .= '<div class= "column is-one-third-tablet is-half-mobile">
		<div class= "box p-4 boxShadowHover is-clickable" style= "background-color: ' . $row3["color"] . ';" onclick= "' . $onclickCategory . '">
		<div class= "columns is-mobile">
		<div class= "column is-narrow is-size-5" style= "background-color: #' . $secondColor . '; border-radius: 6px 0 0 6px;">
			<span>' . $row3["emoji"] . '</span>
		</div>
		
		<div class= "column has-text-left" style= "border: 2px solid #' . $secondColor . '; color: #' . $secondColor . '; border-radius: 0 6px 6px 0;">
			<h4 class= "is-size-5 mb-1">' . $row3["nombre"] . '</h4>
		</div>
		</div>
		</div>
	</div>';
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Registrar documento de retiro de mercancía</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
</head>

<body onbeforeunload="return confirmationExit()">
	<?php include "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Documento de retiro de mercancía</h3>
		<p>Registre el retiro de artículos y productos</p>

		<div class="box">
			<a class="button is-small is-pulled-left" href="/trv/inventory/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<h3 class="is-size-4 has-text-centered" style="color: var(--dark-color)">Información general</h3>

			<div class="columns has-text-centered">
				<div class="column">
					<div class="field">
						<label class="label">Motivo retiro</label>
						<div class="control has-icons-left">
							<span class="select is-fullwidth">
								<select id="selectType">
									<option value="Pérdida o daño">Pérdida o daño</option>
									<option value="Traslado de mercancía">Traslado de mercancía</option>
									<option value="Perecederos vencidos">Perecederos vencidos</option>
									<option value="Otro">Otro - Escriba en el campo de notas</option>
								</select>
							</span>

							<span class="icon is-small is-left"><i class="fas fa-question"></i></span>
						</div>
					</div>
				</div>

				<div class="column">
					<div class="field">
						<label class="label">Notas opcionales</label>
						<div class="control has-icons-left is-expanded">
							<input type="text" class="input" placeholder="Ingrese notas relacionadas a este movimiento" id="inputNotes" maxlength="400">
							<span class="icon is-small is-left"><i class="fas fa-comment-dots"></i></span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="box">
			<h3 class="is-size-4 has-text-centered" style="color: var(--dark-color)">Selección de productos</h3>

			<div class="columns">
				<div class="column">
					<div class="field has-addons">
						<div class="control" style="width: 40%;">
							<label class="label">Cantidad</label>
						</div>

						<div class="control is-expanded">
							<label class="label">Buscar producto</label>
						</div>
					</div>

					<div class="field has-addons">
						<div class="control has-icons-left" style="width: 40%;">
							<input type="number" class="input" placeholder="e.g. 1, 20" id="cantidadInput" value="1">
							<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
						</div>

						<div class="control has-icons-left is-expanded">
							<input type="text" class="input" placeholder="Buscar o escanear código de barras" id="codigosInput" onkeydown="onup()" onkeyup="this.value = this.value.toUpperCase();" autofocus>
							<span class="icon is-small is-left"><i class="fas fa-barcode"></i></span>
						</div>
					</div>

					<div class="has-text-centered"><button class="button backgroundDark" onclick="document.getElementById('overlaySelectProduct1').style.display = 'block';"><i class="fas fa-tshirt"></i> Mostrar productos</button></div>
				</div>

				<div class="column">
					<div class="block has-text-left">
						<span class="icon is-large is-pulled-left"><i class="fas fa-boxes-stacked fa-2x"></i></span>
						<h4 class="is-size-6 has-text-grey">Productos a retirar</h4>
						<p class="is-size-5 has-text-success"><b><span id="productosAgregarTxt">0</span></b></p>
					</div>

					<hr>
					<div class="has-text-left content" style="max-height: 600px;overflow: auto;" id="productosAgregarDiv"></div>
				</div>
			</div>

			<div class="has-text-centered">
				<button class="button backgroundDark" onclick="registrarMovimiento()"><i class="fas fa-circle-check"></i> Registrar movimiento</button>
				<br><a class="button is-danger is-inverted is-small" href="/trv/inventory/merchandise-exit.php"><i class="fas fa-circle-xmark"></i> Cancelar y volver a comenzar</a>
			</div>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlaySelectProduct1" class="trvModal">
		<div class="trvModal-content trvModal-content-large">
			<span class="delete" onclick="document.getElementById('overlaySelectProduct1').style.display='none'"></span>

			<div class="trvModal-elements">
				<label class="label">Buscar productos</b></label>
				<div class="field has-addons">
					<div class="control has-icons-left is-expanded">
						<input type="text" class="input" placeholder="Buscar por nombre o precio" id="searchProductInput" onkeydown="onup2()">
						<span class="icon is-small is-left"><i class="fas fa-magnifying-glass"></i></span>
					</div>

					<div class="control">
						<button class="button backgroundDark" onclick="searchProduct()"><i class="fas fa-magnifying-glass"></i></button>
					</div>
				</div>

				<div class="columns is-mobile is-multiline is-centered mt-1">
					<?php echo $showCategories; ?>
				</div>
			</div>
		</div>
	</div>

	<div id="overlaySelectProduct2" class="trvModal">
		<div class="trvModal-content trvModal-content-large">
			<span class="delete" onclick="document.getElementById('overlaySelectProduct2').style.display='none'"></span>

			<div class="trvModal-header">
				<button class="button is-small is-pulled-left" onclick="backCategorySelection()"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></button>
				<h3 class="is-size-3 mb-1" id="txtCategory">ERROR</h3>
			</div>

			<div class="trvModal-elements">
				<div class="columns is-multiline is-mobile is-centered mt-1 has-text-left" id="divProductsList"></div>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/include/search-barcode-product.php" style="display: none" id="searchBarcodeForm" onsubmit="return searchBarcodeReturn();">
		<input name="searchBarcodeQuery" id="searchBarcodeQuery" readonly>
		<input type="submit" id="searchBarcodeSend" value="Enviar">
	</form>

	<form method="POST" action="/trv/include/product-selection-1.php" style="display: none" id="prodSelection1Form" onsubmit="return prodSelection1Return();">
		<input name="prodSelection1Category" id="prodSelection1Category" readonly>
		<input name="prodSelection1Search" id="prodSelection1Search" readonly>
		<input type="submit" id="prodSelection1Send" value="Enviar">
	</form>

	<form method="POST" action="/trv/inventory/include/add-substract-inventory.php" style="display: none" id="registerEntryExitForm" onsubmit="return registerEntryExitReturn();">
		<input name="registerEntryExitArray" id="registerEntryExitArray" readonly>
		<input name="registerEntryExitArrayComplete" id="registerEntryExitArrayComplete" readonly>
		<input name="registerEntryExitType" id="registerEntryExitType" readonly>
		<input name="registerEntryExitReason" id="registerEntryExitReason" readonly>
		<input name="registerEntryExitNotes" id="registerEntryExitNotes" readonly>
		<input type="submit" id="registerEntryExitSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script src="/trv/include/get-products.js"></script>
	<script>
		var productList = "",
			numberProducts = 0,
			productsArray = [],
			productsPageArray = [],
			productOrderId = 0;
		var preguntarParaCerrar = false;

		function onup() {
			if (event.keyCode === 13) {
				searchBarcode();
			}
		}

		function onup2() {
			if (event.keyCode === 13) {
				searchProduct();
			}
		}

		function openProdSelection() {
			document.getElementById('overlaySelectProduct1').style.display = 'block';
			document.getElementById('searchProductInput').value = '';
		}

		function searchBarcode() {
			var barcode = document.getElementById('codigosInput').value;

			if (barcode == "") {
				openProdSelection();
			} else {
				document.getElementById('searchBarcodeQuery').value = barcode;
				document.getElementById('searchBarcodeSend').click();

				openLoader();
			}
		}

		function searchProduct() {
			var searchInput = document.getElementById('searchProductInput').value;

			if (searchInput != "") {
				getProdsCategory('', 'Resultados de la búsqueda', searchInput);
			}
		}

		function addProduct(idProd, nombreProd, precioProd, stockProd, priceVariable) {
			var quantity = document.getElementById('cantidadInput').value;
			quantity++;
			quantity--;

			for (var c = 1; c <= quantity; c++) {
				var prodExists = false;
				for (var x2 = 0; x2 < productsPageArray.length; x2++) {
					if (productsPageArray[x2]["id"] == idProd) {
						productsPageArray[x2].quantity += 1;
						prodExists = true;
					}
				}

				if (prodExists == false) {
					productsPageArray.push({
						id: idProd,
						name: nombreProd,
						quantity: 1,
						numberProdSale: productOrderId
					});
					productOrderId++;
				}

				numberProducts++;
			}

			preguntarParaCerrar = true;
			updateValues();
			document.getElementById('overlaySelectProduct2').style.display = "none";
			document.getElementById('cantidadInput').value = "1";
		}

		function deleteProduct(idUnique, idProd) {
			for (var x = 0; x < productsPageArray.length; x++) {
				if (productsPageArray[x]["numberProdSale"] == idUnique && productsPageArray[x]["id"] == idProd) {
					var positionProd = productsPageArray.map(function(e) {
						return e.numberProdSale;
					}).indexOf(idUnique);

					productsPageArray.splice(positionProd, 1);
					numberProducts--;
				}
			}

			updateValues();
		}

		function subtractUnit(idUnique, idProd) {
			for (var x = 0; x < productsPageArray.length; x++) {
				if (productsPageArray[x]["numberProdSale"] == idUnique && productsPageArray[x]["id"] == idProd) {
					productsPageArray[x].quantity -= 1;
					numberProducts--;
				}
			}

			updateValues();
		}

		function updateValues() {
			productList = "", productsArray = [];

			for (var x = 0; x < productsPageArray.length; x++) {
				var onclickDelete = 'deleteProduct(' + productsPageArray[x]["numberProdSale"] + ', ' + productsPageArray[x]["id"] + ')';
				var onclickNewUnit = "addProduct(" + productsPageArray[x]["id"] + ", '" + productsPageArray[x]["name"] + "')";
				var onclickMinus1Unit = 'subtractUnit(' + productsPageArray[x]["numberProdSale"] + ', ' + productsPageArray[x]["id"] + ')';

				if (productsPageArray[x]["quantity"] > 1) {
					productList += '<p class= "is-size-5"><b>' + productsPageArray[x]["name"] + ' <span class= "prodListPrice"> <i class="fas fa-circle-plus fa-fw is-clickable" style= "margin-right:2px;margin-left:2px;" title= "Aumentar 1 unidad" onclick= "' + onclickNewUnit + '"></i> <i class="fas fa-circle-minus fa-fw is-clickable" style= "margin-left: 2px;" title= "Restar 1 unidad" onclick= "' + onclickMinus1Unit + '"></i></span></b><br>' + productsPageArray[x]["quantity"] + ' unidades</p>';
				} else {
					productList += '<p class= "is-size-5"><b>' + productsPageArray[x]["name"] + ' <span class= "prodListPrice"><i class="fas fa-circle-plus fa-fw is-clickable" style= "margin-right:2px;margin-left:2px;" title= "Aumentar 1 unidad" onclick= "' + onclickNewUnit + '"></i> <i class="fas fa-trash-can fa-fw is-clickable" style= "margin-left: 2px;" title= "Eliminar de la lista" onclick= "' + onclickDelete + '"></i></span></b></p>';
				}

				for (var x2 = 0; x2 < productsPageArray[x]["quantity"]; x2++) {
					productsArray.push(productsPageArray[x]["id"]);
				}
			}

			document.getElementById('productosAgregarDiv').innerHTML = productList;
			document.getElementById('productosAgregarTxt').innerHTML = thousands_separators(numberProducts);
		}

		function registrarMovimiento() {
			var reasonMovement = document.getElementById('selectType').value;
			var notesMovement = document.getElementById('inputNotes').value;

			if (productsArray.length <= 0) {
				newNotification('Seleccione los productos a ingresar', 'error');
			} else if (reasonMovement == "") {
				newNotification('Seleccione la razón del movimiento', 'error');
			} else if (reasonMovement == "Otro" && notesMovement == "") {
				newNotification('Escriba como <b>notas la razón del movimiento</b>', 'error');
			} else {
				var c = confirm("Por favor confirme esta acción. Va a registrar una salida de " + thousands_separators(numberProducts) + " productos.");

				if (c == true) {
					document.getElementById('registerEntryExitArray').value = JSON.stringify(productsArray);
					document.getElementById('registerEntryExitArrayComplete').value = JSON.stringify(productsPageArray);
					document.getElementById('registerEntryExitType').value = "exit";
					document.getElementById('registerEntryExitReason').value = reasonMovement;
					document.getElementById('registerEntryExitNotes').value = notesMovement;
					document.getElementById('registerEntryExitSend').click();

					openLoader();
				}
			}
		}

		function confirmationExit() {
			if (preguntarParaCerrar == true) {
				return "¿Seguro que desea salir?";
			}
		}

		function searchBarcodeReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/search-barcode-product.php',
				data: $('#searchBarcodeForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotificationError();
					} else if (response['codigo_existe'] == true) {
						var codigoFinal = response['codigo_id'];
						codigoFinal++;
						codigoFinal--;
						addProduct(codigoFinal, response['codigo_nombre'], response['codigo_precio'], response['codigo_stock']);
					} else {
						document.getElementById('searchProductInput').value = document.getElementById('codigosInput').value;
						searchProduct();
					}

					closeLoader();
					document.getElementById('codigosInput').value = "";
				}
			});

			return false;
		}

		function registerEntryExitReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/inventory/include/add-substract-inventory.php',
				data: $('#registerEntryExitForm').serialize(),
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