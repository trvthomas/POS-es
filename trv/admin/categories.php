<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Categorías</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
	<link rel="stylesheet" href="/trv/include/libraries/pickr-monolith.min.css">
	<script type="text/javascript" src="/trv/include/libraries/pickr.min.js"></script>
</head>

<body onload="getInfo(true)">
	<?php include "include/header.php"; ?>

	<div class="contentBox">
		<div class="columns">
			<div class="column">
				<h3 class="is-size-5">Categorías</h3>
				<p>Organice sus productos y divídalos por categorías</p>
			</div>

			<div class="column is-one-third">
				<button class="button backgroundDark is-fullwidth" onclick="document.getElementById('overlayNewCategory').style.display= 'block';document.getElementById('newCategoryNameInput').value = '';"><i class="fas fa-circle-plus"></i> Nueva categoría</button>
			</div>
		</div>

		<div class="box">
			<a class="button is-small backgroundNormal" href="/trv/admin/products.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="list has-visible-pointer-controls" id="categoriesList"></div>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlayNewCategory" class="trvModal">
		<div class="trvModal-content trvModal-content-small">
			<span class="delete" onclick="document.getElementById('overlayNewCategory').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Agregar categoría</h3>
			</div>

			<div class="trvModal-elements">
				<div class="field">
					<label class="label">Nombre de la categoría</label>
					<div class="control has-icons-left">
						<input type="text" class="input" placeholder="e.g. Bebidas, Camisetas" id="newCategoryNameInput">
						<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
					</div>
				</div>

				<div class="field">
					<label class="label">Seleccione un color</label>
					<div id="colorSelect"></div>
					<input type="color" class="is-hidden" id="newCategoryColorInput" value="#ededf7">
				</div>

				<div class="field">
					<label class="label has-text-centered">Ícono de identificación</label>
					<div class="control" onclick="openEmojiPicker('new')">
						<input type="text" class="input is-clickable" placeholder="Haga clic aquí para seleccionar un ícono" id="newCategoryEmoji" disabled>
					</div>
				</div>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth is-light is-danger" onclick="document.getElementById('overlayNewCategory').style.display='none'">Cancelar</button>
					</div>
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="addCategory()">Agregar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="overlayEditCategory" class="trvModal">
		<div class="trvModal-content trvModal-content-small">
			<span class="delete" onclick="document.getElementById('overlayEditCategory').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Editar categoría</h3>
			</div>

			<div class="trvModal-elements">
				<div class="field">
					<label class="label">Nombre de la categoría</label>
					<div class="control has-icons-left">
						<input type="text" class="input" placeholder="e.g. Bebidas, Camisetas" id="editCategoryNameInput">
						<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
					</div>
				</div>

				<div class="field">
					<label class="label">Seleccione un color</label>
					<div id="colorSelect2"></div>
					<input type="color" class="is-hidden" id="editCategoryColorInput" value="#ededf7">
				</div>

				<div class="field">
					<label class="label has-text-centered">Ícono de identificación</label>
					<div class="control" onclick="openEmojiPicker('edit')">
						<input type="text" class="input is-clickable" placeholder="Haga clic aquí para seleccionar un ícono" id="editCategoryEmoji" disabled>
					</div>
				</div>

				<div class="notification is-light is-info">Para conservar el ícono actual deje el campo de arriba <b>en blanco</b>; no seleccione un nuevo ícono</div>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth is-light is-danger" onclick="document.getElementById('overlayEditCategory').style.display='none'">Cancelar</button>
					</div>
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="editCategoryFinal()">Guardar cambios</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="overlaySelectEmoji" class="trvModal">
		<div class="trvModal-content trvModal-content-small">
			<span class="delete" onclick="document.getElementById('overlaySelectEmoji').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Seleccionar ícono de identificación</h3>
			</div>

			<div class="trvModal-elements">
				<div class="field">
					<label class="label has-text-centered">Ícono de identificación</label>

					<div class="level" id="emojiSelected" style="display: none">
						<div class="level-item has-text-centered">
							<div>
								<p class="heading">Ícono seleccionado</p>
								<p class="title" id="emojiSelectedSpan">0</p>
							</div>
						</div>
					</div>

					<div class="emojiPicker show"><emoji-picker id="emojiPickerAdd" locale="es" data-source="https://cdn.jsdelivr.net/npm/emoji-picker-element-data@1.0.0/es/cldr/data.json"></emoji-picker></div>
				</div>


				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth is-light is-danger" onclick="document.getElementById('overlaySelectEmoji').style.display='none'">Cancelar</button>
					</div>
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="confirmSelectionEmoji()">Seleccionar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/admin/include/get-categories.php" style="display: none" id="getCategoriesForm" onsubmit="return getCategoriesReturn();">
		<input name="getCategoriesToken" value="admin38942" readonly>
		<input type="submit" id="getCategoriesSend" value="Enviar">
	</form>

	<form method="POST" action="/trv/admin/include/delete-category.php" style="display: none" id="categoryDeleteForm" onsubmit="return categoryDelete();">
		<input id="categoryDeleteId" name="categoryDeleteId" readonly>
		<input id="categoryDeleteSend" type="submit" value="Enviar">
	</form>

	<form method="POST" action="/trv/admin/include/add-category.php" style="display: none" id="categoryAddForm" onsubmit="return categoryAdd();">
		<input id="categoryAddName" name="categoryAddName" readonly>
		<input id="categoryAddColor" name="categoryAddColor" readonly>
		<input id="categoryAddEmoji" name="categoryAddEmoji" readonly>
		<input id="categoryAddSend" type="submit" value="Enviar">
	</form>

	<form method="POST" action="/trv/admin/include/edit-category.php" style="display: none" id="categoryEditForm" onsubmit="return categoryEdit();">
		<input id="categoryEditName" name="categoryEditName" readonly>
		<input id="categoryEditColor" name="categoryEditColor" readonly>
		<input id="categoryEditEmoji" name="categoryEditEmoji" readonly>
		<input id="categoryEditID" name="categoryEditID" readonly>
		<input id="categoryEditSend" type="submit" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
	<script>
		var pickr, pickr2;

		function getInfo(update) {
			if (update == true) {
				pickr = Pickr.create({
					el: '#colorSelect',
					theme: 'monolith',
					lockOpacity: true,
					default: '#ededf7',
					position: 'bottom-middle',
					swatches: [
						'#ededf7', '#ffc0cb', '#ccffcc', '#ffffcc', '#ffcc80', '#e6e7e8', '#7a8085', '#660066', '#008000', '#996633', '#ff6600', '#006699', '#ff3399', '#e6e600'
					],
					components: {
						preview: true,
						opacity: false,
						hue: true,

						interaction: {
							hex: true,
							rgba: false,
							hsla: false,
							hsva: false,
							cmyk: false,
							input: true,
							cancel: true,
							clear: false,
							save: true
						}
					},
					i18n: {
						'ui:dialog': 'Seleccionar un color',
						'btn:toggle': 'Seleccionar un color',
						'btn:swatch': 'Paleta de colores',
						'btn:last-color': 'Utilizar último color seleccionado',
						'btn:save': 'Seleccionar',
						'btn:cancel': 'Cancelar',
						'btn:clear': 'Predeterminado',
						'aria:btn:save': 'Seleccionar y cerrar',
						'aria:btn:cancel': 'Cancelar y cerrar',
						'aria:btn:clear': 'Seleccionar prdeterminado y cerrar',
						'aria:input': 'Ingrese un código de color',
						'aria:palette': 'Paleta de colores',
						'aria:hue': 'Deslizador  de matiz',
						'aria:opacity': 'Deslizador'
					}
				});
				pickr.on('change', instance => {
					pickr.applyColor();
				});
				pickr.on('save', (color, instance) => {
					var col = color.toHEXA();
					var completeColor = "#" + col[0] + col[1] + col[2]
					document.getElementById('newCategoryColorInput').value = completeColor;
				});

				pickr2 = Pickr.create({
					el: '#colorSelect2',
					theme: 'monolith',
					lockOpacity: true,
					default: '#ededf7',
					position: 'bottom-middle',
					swatches: [
						'#ededf7', '#ffc0cb', '#ccffcc', '#ffffcc', '#ffcc80', '#e6e7e8', '#7a8085', '#660066', '#008000', '#996633', '#ff6600', '#006699', '#ff3399', '#e6e600'
					],
					components: {
						preview: true,
						opacity: false,
						hue: true,

						interaction: {
							hex: true,
							rgba: false,
							hsla: false,
							hsva: false,
							cmyk: false,
							input: true,
							cancel: true,
							clear: false,
							save: true
						}
					},
					i18n: {
						'ui:dialog': 'Seleccionar un color',
						'btn:toggle': 'Seleccionar un color',
						'btn:swatch': 'Paleta de colores',
						'btn:last-color': 'Utilizar último color seleccionado',
						'btn:save': 'Seleccionar',
						'btn:cancel': 'Cancelar',
						'btn:clear': 'Predeterminado',
						'aria:btn:save': 'Seleccionar y cerrar',
						'aria:btn:cancel': 'Cancelar y cerrar',
						'aria:btn:clear': 'Seleccionar prdeterminado y cerrar',
						'aria:input': 'Ingrese un código de color',
						'aria:palette': 'Paleta de colores',
						'aria:hue': 'Deslizador  de matiz',
						'aria:opacity': 'Deslizador'
					}
				});
				pickr2.on('change', instance => {
					pickr2.applyColor();
				});
				pickr2.on('save', (color, instance) => {
					var col = color.toHEXA();
					var completeColor = "#" + col[0] + col[1] + col[2]
					document.getElementById('editCategoryColorInput').value = completeColor;
				});
			}

			document.getElementById('categoriesList').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Cargando..." width= "100%" loading= "lazy"></div>';
			document.getElementById('getCategoriesSend').click();
		}

		document.querySelector('emoji-picker').addEventListener('emoji-click', event => selectEmoji(event.detail.unicode));

		var emojiPickerSelection = 'add',
			savingEmoji = '';

		function selectEmoji(emojiValue) {
			savingEmoji = emojiValue;
			document.getElementById('emojiSelected').style.display = 'block';
			document.getElementById('emojiSelectedSpan').innerHTML = savingEmoji;
		}

		function openEmojiPicker(addEdit) {
			if (navigator.onLine == false) {
				newNotification('Se requiere una conexión a internet', 'error');
			} else {
				emojiPickerSelection = addEdit;
				document.getElementById('overlaySelectEmoji').style.display = 'block';
				document.getElementById('emojiSelected').style.display = 'none';
			}
		}

		function confirmSelectionEmoji() {
			document.getElementById('overlaySelectEmoji').style.display = 'none';
			document.getElementById(emojiPickerSelection + 'CategoryEmoji').value = savingEmoji;
		}

		function deleteCategory(idCategory) {
			var c = confirm("¿Está seguro? Todos los productos pertenecientes a esta categoría pasarán a 'Sin categoría'");

			if (c == true) {
				document.getElementById('categoryDeleteId').value = idCategory;
				document.getElementById('categoryDeleteSend').click();

				document.getElementById('btnDeleteCategory' + idCategory).disabled = true;
				document.getElementById('btnDeleteCategory' + idCategory).innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
			}
		}

		function addCategory() {
			var nameCat = document.getElementById('newCategoryNameInput').value;
			var colorCat = document.getElementById('newCategoryColorInput').value;
			var emojiCat = document.getElementById('newCategoryEmoji').value;

			if (nameCat == "" || colorCat == "" || emojiCat == "") {
				newNotification('Complete todos los campos', 'error');
			} else {
				document.getElementById('categoryAddName').value = nameCat;
				document.getElementById('categoryAddColor').value = colorCat;
				document.getElementById('categoryAddEmoji').value = emojiCat;
				document.getElementById('categoryAddSend').click();

				openLoader();
				document.getElementById('overlayNewCategory').style.display = "none";
			}
		}

		function editCategory(idCat, nameCat, colorCat) {
			document.getElementById('categoryEditID').value = idCat;
			document.getElementById('editCategoryNameInput').value = nameCat;
			document.getElementById('editCategoryEmoji').value = "";
			pickr2.setColor(colorCat);

			document.getElementById('overlayEditCategory').style.display = "block";
		}

		function editCategoryFinal() {
			var nameCat = document.getElementById('editCategoryNameInput').value;
			var colorCat = document.getElementById('editCategoryColorInput').value;
			var emojiCat = document.getElementById('editCategoryEmoji').value;

			if (nameCat == "" || colorCat == "") {
				newNotification('Complete todos los campos', 'error');
			} else {
				document.getElementById('categoryEditName').value = nameCat;
				document.getElementById('categoryEditColor').value = colorCat;
				document.getElementById('categoryEditEmoji').value = emojiCat;
				document.getElementById('categoryEditSend').click();

				openLoader();
				document.getElementById('overlayEditCategory').style.display = "none";
			}
		}

		function getCategoriesReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/get-categories.php',
				data: $('#getCategoriesForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['categorias'] != "") {
						document.getElementById('categoriesList').innerHTML = response['categorias'];
					}
				}
			});

			return false;
		}

		function categoryDelete() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/delete-category.php',
				data: $('#categoryDeleteForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Error al eliminar la categoría', 'error');
					} else if (response['categoria_eliminada'] == true) {
						newNotification('Categoría eliminada', 'success');
					}
					getInfo(false);
				}
			});

			return false;
		}

		function categoryAdd() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/add-category.php',
				data: $('#categoryAddForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['categoria_agregada'] == true) {
						newNotification('Categoría agregada', 'success');
					}
					getInfo(false);
					closeLoader();
				}
			});

			return false;
		}

		function categoryEdit() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/edit-category.php',
				data: $('#categoryEditForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['categoria_editada'] == true) {
						newNotification('Información actualizada', 'success');
					}
					getInfo(false);
					closeLoader();
				}
			});

			return false;
		}
	</script>
</body>

</html>