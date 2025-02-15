<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/DBData.php";
if (isset($_COOKIE[$prefixCoookie . "TemporaryIdUser"])) {
	header('Location:home.php');
}
if (!isset($_COOKIE[$prefixCoookie . "IdUser"]) || !isset($_COOKIE[$prefixCoookie . "UsernameUser"])) {
	header('Location:/trv/home.php');
} ?>
<!DOCTYPE html>
<html>

<head>
	<title>Iniciar sesión - Acceso temporal</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<div class="contentBox loginBox">
		<div class="box">
			<h3 class="is-size-5 has-text-centered">Acceso temporal</h3>
			<p class="has-text-centered">Inicie sesión con un usuario con <b>permisos de administrador</b> para acceder por máximo 1 hora al panel administrativo</p>
			<hr>

			<div class="field">
				<label class="label">Usuario</label>
				<div class="control has-icons-left">
					<div class="select is-fullwidth">
						<select id="usuarioInput">
							<option value="" readonly checked>Seleccione</option>
							<?php
							$sql = "SELECT id, username FROM trvsol_users WHERE admin=1";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									echo '<option value="' . $row["username"] . '">' . $row["username"] . '</option>';
								}
							}
							?>
						</select>
					</div>
					<span class="icon is-small is-left"><i class="fas fa-user"></i></span>
				</div>
			</div>

			<div class="field">
				<label class="label">Contraseña</label>
				<div class="control has-icons-left has-icons-right">
					<input type="password" class="input" placeholder="Ingrese su contraseña" id="contrasenaInput" onkeyup="onup()">
					<span class="icon is-small is-left"><i class="fas fa-key"></i></span>
					<span class="icon is-small is-right" style="pointer-events: all; cursor: pointer;" onclick="showPass('contrasenaInput')"><i class="fas fa-eye" id="showPassBtncontrasenaInput"></i></span>
				</div>
			</div>

			<div class="field">
				<div class="control"><button class="button backgroundDark is-fullwidth" onclick="ingresarLogin()"><i class="fas fa-sign-in-alt"></i> Ingresar</button></div>
			</div>
		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form method="POST" action="/trv/admin/include/login-temporary.php" style="display: none" id="loginForm" onsubmit="return verifLogin();">
		<input name="loginUsername" id="loginUsername" readonly>
		<input type="password" name="loginPass" id="loginPass" readonly>
		<input type="submit" id="loginSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function onup() {
			if (event.keyCode === 13) {
				ingresarLogin();
			}
		}

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

		function ingresarLogin() {
			var username = document.getElementById('usuarioInput').value;
			var clave = document.getElementById('contrasenaInput').value;

			if (username == "" || clave == "") {
				newNotification('Complete todos los campos', 'error');
			} else {
				document.getElementById('loginUsername').value = username;
				document.getElementById('loginPass').value = clave;
				document.getElementById('loginSend').click();

				openLoader();
			}
		}

		function verifLogin() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/login-temporary.php',
				data: $('#loginForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
						closeLoader();
					} else if (response['credenciales_invalid'] == true) {
						newNotification('Credenciales inválidas o no tiene permisos de administrador', 'error');
						document.getElementById('loginPass').value = "";
						closeLoader();
					} else if (response['sesion_iniciada'] == true) {
						window.location = "/trv/admin/home.php";
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>