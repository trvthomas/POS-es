<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/stats.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Vendedores</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
</head>

<body onload="getInfo()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<div class="columns">
			<div class="column">
				<h3 class="is-size-5">Vendedores</h3>
				<p>Controle los vendedores que tienen acceso al sistema y otorge los permisos necesarios a cada uno</p>
			</div>

			<div class="column is-one-third">
				<a class="button backgroundDark is-fullwidth" href="/trv/admin/new-user.php"><i class="fas fa-circle-plus"></i> Nuevo vendedor</a>
			</div>
		</div>

		<div class="box">
			<a class="button is-small backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="list has-visible-pointer-controls" id="usersList"></div>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form method="POST" action="/trv/admin/include/get-users.php" style="display: none" id="getUsersForm" onsubmit="return getUsersReturn();">
		<input name="getUsersToken" value="admin38942" readonly>
		<input type="submit" id="getUsersSend" value="Enviar">
	</form>

	<form method="POST" action="/trv/admin/include/set-admin-user.php" style="display: none" id="setAdminForm" onsubmit="return setAdminReturn();">
		<input id="setadminUserId" name="setadminUserId" readonly>
		<input id="setadminUserAction" name="setadminUserAction" readonly>
		<input id="setadminUserSend" type="submit" value="Enviar">
	</form>

	<form method="POST" action="/trv/admin/include/set-inventory-user.php" style="display: none" id="setInventoryForm" onsubmit="return setInventoryReturn();">
		<input id="setInventoryUserId" name="setInventoryUserId" readonly>
		<input id="setInventoryUserAction" name="setInventoryUserAction" readonly>
		<input id="setInventoryUserSend" type="submit" value="Enviar">
	</form>

	<form method="POST" action="/trv/admin/include/delete-user.php" style="display: none" id="userDeleteForm" onsubmit="return userDelete();">
		<input id="userDeleteId" name="userDeleteId" readonly>
		<input id="userDeleteSend" type="submit" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function getInfo() {
			document.getElementById('usersList').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Cargando..." width= "100%" loading= "lazy"></div>';
			document.getElementById('getUsersSend').click();
		}

		function deleteUser(idUser) {
			var c = confirm("¿Está seguro? Esta acción no se puede deshacer y eliminará todas las estadísticas del usuario");

			if (c == true) {
				document.getElementById('userDeleteId').value = idUser;
				document.getElementById('userDeleteSend').click();

				document.getElementById('btnDeleteUser' + idUser).disabled = true;
				document.getElementById('btnDeleteUser' + idUser).innerHTML = '<i class= "fas fa-spinner fa-spin"></i>';
			}
		}

		function setAdminUser(idUser, action) {
			document.getElementById('setadminUserId').value = idUser;
			document.getElementById('setadminUserAction').value = action;
			document.getElementById('setadminUserSend').click();

			document.getElementById('btnSetAdmin' + idUser).disabled = true;
			document.getElementById('btnSetAdmin' + idUser).innerHTML = '<i class= "fas fa-spinner fa-spin"></i>';
		}

		function setInventoryUser(idUser, action) {
			document.getElementById('setInventoryUserId').value = idUser;
			document.getElementById('setInventoryUserAction').value = action;
			document.getElementById('setInventoryUserSend').click();

			document.getElementById('btnSetInventory' + idUser).disabled = true;
			document.getElementById('btnSetInventory' + idUser).innerHTML = '<i class= "fas fa-spinner fa-spin"></i>';
		}

		function getUsersReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/get-users.php',
				data: $('#getUsersForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['usuarios'] != "") {
						document.getElementById('usersList').innerHTML = response['usuarios'];
					}
				}
			});

			return false;
		}

		function userDelete() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/delete-user.php',
				data: $('#userDeleteForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['usuario_eliminado'] == true) {
						newNotification('Usuario eliminado', 'success');
					}
					getInfo();
				}
			});

			return false;
		}

		function setAdminReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/set-admin-user.php',
				data: $('#setAdminForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['administrador_establecido'] == true) {
						newNotification('Información actualizada', 'success');
					}
					getInfo();
				}
			});

			return false;
		}

		function setInventoryReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/set-inventory-user.php',
				data: $('#setInventoryForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['inventario_establecido'] == true) {
						newNotification('Información actualizada', 'success');
					}
					getInfo();
				}
			});

			return false;
		}
	</script>
</body>

</html>