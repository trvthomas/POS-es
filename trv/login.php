<?php include "include/DBData.php";
include "include/stats.php";
if (isset($_COOKIE[$prefixCoookie . "IdUser"])) {
	header('Location:home.php');
} else if (!isset($_GET["db-optimized"])) {
	header('Location:db-optimization.php');
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Iniciar sesión</title>

	<?php include "include/head-tracking.php"; ?>
</head>

<body onload="checkNewVersion()">
	<?php include "include/header-login.php"; ?>

	<div class="contentBox loginBox">
		<div class="box">
			<h3 class="is-size-4 has-text-centered mb-1">Iniciar sesión</h3>
			<p class="has-text-centered">Ingrese sus datos para ingresar al sistema</p>
			<hr>

			<div class="field">
				<label class="label">Usuario</label>
				<div class="control has-icons-left">
					<div class="select is-fullwidth">
						<select id="usuarioInput">
							<option value="" readonly checked>Seleccione</option>
							<?php
							$sql = "SELECT id, username FROM trvsol_users";
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
				<label class="label">Clave</label>
				<div class="control has-icons-left has-icons-right">
					<input type="password" class="input" placeholder="Ingrese su clave" id="contrasenaInput" onkeyup="onup()" inputmode="numeric">
					<span class="icon is-small is-left"><i class="fas fa-key"></i></span>
					<span class="icon is-small is-right is-clickable" style="pointer-events: all;" onclick="showPass('contrasenaInput')"><i class="fas fa-eye" id="showPassBtncontrasenaInput"></i></span>
				</div>
			</div>

			<div class="field mb-0">
				<label class="label">Base de caja inicial</label>
				<div class="control has-icons-left">
					<input type="number" class="input" placeholder="e.g. 50000, 100000" id="baseInput" onkeyup="onup()" oninput="document.getElementById('verifyBase').innerHTML= thousands_separators(this.value);">
					<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
				</div>
			</div>

			<p>Base caja: <b>$<span id="verifyBase">0</span></b></p>

			<div class="field mt-2">
				<div class="control"><button class="button backgroundDark is-fullwidth" onclick="ingresarLogin()"><i class="fas fa-sign-in-alt"></i> Ingresar</button></div>
			</div>
		</div>
	</div>

	<?php include "include/footer.php"; ?>

	<form method="POST" action="/trv/include/login.php" style="display: none" id="loginForm" onsubmit="return verifLogin();">
		<input name="loginUsername" id="loginUsername" readonly>
		<input type="password" name="loginPass" id="loginPass" readonly>
		<input name="loginCashBase" id="loginCashBase" readonly>
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
			var base = document.getElementById('baseInput').value;
			base++;
			base--;

			if (username == "" || clave == "" || base < 0) {
				newNotification('Complete todos los campos', 'error');
			} else {
				document.getElementById('loginUsername').value = username;
				document.getElementById('loginPass').value = clave;
				document.getElementById('loginCashBase').value = base;

				if (base == 0) {
					var c = confirm("¿Está seguro que quiere aperturar la caja con $0?");

					if (c == true) {
						document.getElementById('loginSend').click();
						openLoader();
					}
				} else {
					document.getElementById('loginSend').click();
					openLoader();
				}
			}
		}

		function verifLogin() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/login.php',
				data: $('#loginForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
						closeLoader();
					} else if (response['credenciales_invalid'] == true) {
						newNotification('Usuario y/o contraseña <b>incorrectos</b>', 'error');
						document.getElementById('loginPass').value = "";
						closeLoader();
					} else if (response['sesion_iniciada'] == true) {
						window.location = "home.php";
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>