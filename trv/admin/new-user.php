<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Nuevo vendedor</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<div class="contentBox loginBox">
		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/users.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<h3 class="is-size-5 has-text-centered">Crear vendedor</h3>
			<hr><br>

			<div class="field">
				<label class="label has-text-centered">Nombre de usuario*</label>
				<div class="control has-icons-left">
					<input type="text" class="input" placeholder="e.g. Jhon, María" id="inputUsername" maxlength="100">
					<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
				</div>
			</div>

			<div class="field">
				<label class="label has-text-centered">Contraseña</label>
				<div class="control has-icons-left has-icons-right">
					<input type="password" class="input" placeholder="Contraseña del usuario" id="inputPassword">
					<span class="icon is-small is-left"><i class="fas fa-key"></i></span>
					<span class="icon is-small is-right" style="pointer-events: all; cursor: pointer;" onclick="showPass('inputPassword')"><i class="fas fa-eye" id="showPassBtninputPassword"></i></span>
				</div>
			</div>
		</div>

		<div class="columns">
			<div class="column">
				<button class="button backgroundDark is-fullwidth" id="buttonPublish" onclick="addUser()">Crear <i class="fas fa-circle-plus"></i></button>
			</div>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form method="POST" action="/trv/admin/include/add-user.php" style="display: none" id="addUserForm" onsubmit="return addUserReturn();">
		<input name="addUserName" id="addUserName" readonly>
		<input name="addUserPass" id="addUserPass" readonly>

		<input type="submit" id="addUserSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
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

		function addUser() {
			var userName = document.getElementById('inputUsername').value;
			var userPass = document.getElementById('inputPassword').value;

			if (userName == "" || userPass == "") {
				newNotification('Complete todos los campos', 'error');
			} else {
				document.getElementById('addUserName').value = userName;
				document.getElementById('addUserPass').value = userPass;

				document.getElementById('addUserSend').click();
				openLoader();
			}
		}

		function addUserReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/add-user.php',
				data: $('#addUserForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['nombre_existe'] == true) {
						newNotification('El nombre de usuario ya está en uso', 'error');
					} else if (response['usuario_creado'] == true) {
						window.location = "/trv/admin/users.php";
					}
					closeLoader();
				}
			});

			return false;
		}
	</script>
</body>

</html>