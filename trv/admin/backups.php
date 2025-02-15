<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$backupsList = "<td>No se encontraron copias de seguridad</td><td></td><td></td>";

$directory = $_SERVER['DOCUMENT_ROOT'] . "/trv/include/backups/";
$scanBackups = scandir($directory);
if ($scanBackups[2]) {
	$backupsList = "";
	for ($x = 2; $x < count($scanBackups); ++$x) {
		$onclickDelete = "deleteBackup1('" . $scanBackups[$x] . "', '" . substr($scanBackups[$x], 0, -4) . "')";

		$backupsList .= '<div class="list-item">
		<div class="list-item-content">
		<div class="list-item-title">' . substr($scanBackups[$x], 0, -4) . '</div>
		</div>
		
		<div class="list-item-controls">
		<div class= "buttons is-right">
		<button class="button is-danger is-light" onclick= "' . $onclickDelete . '"><i class="fas fa-trash-alt"></i> Eliminar</button>
		</div>
		</div>
	</div>';
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Copias de seguridad</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Copias de seguridad</h3>
		<p>Cree y administre las copias de seguridad de su Sistema POS</p>

		<div class="box">
			<a class="button is-small backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<br><br>
			<div class="notification is-warning"><i class="fas fa-triangle-exclamation"></i> Tenga cuidado con estas opciones si no tiene los conocimientos adecuados</div>

			<div class="has-text-centered">
				<div class="columns">
					<div class="column has-text-left">
						<p>Las copias de seguridad se <b>crean automáticamente los días martes y viernes</b> cuando se cierra caja. Las copias de seguridad <b>creadas 6 meses atrás se eliminan</b> automáticamente</p>
					</div>

					<div class="column">
						<button class="button backgroundDark is-fullwidth" onclick="createBackup()"><i class="fas fa-arrows-rotate"></i> Crear copia de seguridad</button>
					</div>
				</div>

				<hr>
				<h3 class="is-size-5" style="margin-bottom: 0">Copias de seguridad actuales</h3>
				<p>Para restablecer los datos de una copia de seguridad específica por favor <b>contáctenos</b></p>
			</div>

			<div class="list has-visible-pointer-controls"><?php echo $backupsList; ?></div>

		</div>
	</div>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlayDeleteBackup" class="trvModal">
		<div class="trvModal-content trvModal-content-small">
			<span class="delete" onclick="document.getElementById('overlayDeleteBackup').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Eliminar copia de seguridad - <span id="deleteBackupTextName">Error</span></h3>
			</div>

			<div class="trvModal-elements">
				<div id="deleteBackupDiv1">
					<p>Por favor confirme su contraseña de administrador para proceder con la eliminación de la copia de seguridad</p>

					<div class="field">
						<label class="label">Contraseña</label>
						<div class="control has-icons-left has-icons-right">
							<input type="password" class="input" placeholder="Ingrese su contraseña" id="deleteBackupAdminPass">
							<span class="icon is-small is-left"><i class="fas fa-key"></i></span>
							<span class="icon is-small is-right" style="pointer-events: all; cursor: pointer;" onclick="showPass('deleteBackupAdminPass')"><i class="fas fa-eye" id="showPassBtndeleteBackupAdminPass"></i></span>
						</div>
					</div>

					<div class="field">
						<div class="control"><button class="button is-danger is-fullwidth" onclick="deleteBackup2()">Continuar <i class="fas fa-chevron-right"></i></button></div>
					</div>
				</div>

				<div id="deleteBackupDiv2" class="fade" style="display: none">
					<p>Hemos enviado un <b>código al correo electrónico registrado</b> en el sistema, por favor escríbalo a continuación</p>

					<div class="field">
						<label class="label">Código de seguridad</label>
						<div class="control has-icons-left">
							<input type="number" class="input" placeholder="Ingrese el código recibido" id="deleteBackupSecurityCode">
							<span class="icon is-small is-left"><i class="fas fa-key"></i></span>
						</div>
					</div>

					<div class="columns mt-5">
						<div class="column">
							<button class="button is-fullwidth is-danger" onclick="document.getElementById('overlayDeleteBackup').style.display='none'">Eliminar copia de seguridad</button>
						</div>
					</div>
					<p class="is-size-7">Al hacer clic en <b>"Eliminar copia de seguridad"</b> se eliminará la copia de seguridad permanentemente y no podrá ser recuperada.</p>
				</div>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/admin/include/create-backup.php" style="display: none" id="createBackupForm" onsubmit="return createBackupReturn();">
		<input name="createBackupToken" value="admin8kg2c" readonly>
		<input type="submit" id="createBackupSend" value="Enviar">
	</form>

	<form method="POST" action="/trv/admin/include/reset-inventory-1.php" style="display: none" id="deleteBackupForm" onsubmit="return deleteBackupReturn();">
		<input type="password" name="resetInventoryPass" id="resetInventoryPass" readonly>
		<input type="submit" id="resetInventorySend" value="Enviar">
	</form>

	<form method="POST" action="/trv/include/admin-delete-backup-2.php" style="display: none" id="deleteBackup2Form" onsubmit="return deleteBackup2Return();">
		<input name="deleteBackup2FileName" id="deleteBackup2FileName" readonly>
		<input name="deleteBackup2Code" id="deleteBackup2Code" readonly>
		<input type="submit" id="deleteBackup2Send" value="Enviar">
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

		function createBackup() {
			document.getElementById('createBackupSend').click();
			openLoader();
		}

		function deleteBackup1(fileNameComplete, fileName) {
			document.getElementById('deleteBackup2FileName').value = fileNameComplete;
			document.getElementById('deleteBackupTextName').innerHTML = fileName;
			document.getElementById('overlayDeleteBackup').style.display = "block";
		}

		function deleteBackup2() {
			var adminPass = document.getElementById('deleteBackupAdminPass').value;

			if (navigator.onLine == false) {
				newNotification('Se requiere una conexión a internet', 'error');
			} else if (adminPass == "") {
				newNotification('Escriba su contraseña de administrador', 'error');
			} else {
				document.getElementById('resetInventoryPass').value = adminPass;
				document.getElementById('resetInventorySend').click();

				openLoader();
			}
		}

		function deleteBackup3() {
			var secCode = document.getElementById('deleteBackupSecurityCode').value;

			if (secCode == "") {
				newNotification('Escriba el código de seguridad', 'error');
			} else {
				document.getElementById('deleteBackup2Code').value = secCode;
				document.getElementById('deleteBackup2Send').click();

				openLoader();
				document.getElementById('overlayDeleteBackup').style.display = 'none';

				openLoader();
			}
		}

		function createBackupReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/create-backup.php',
				data: $('#createBackupForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
						closeLoader();
					} else if (response['backup_creado'] == true) {
						newNotification("Copia de seguridad creada", "success");
						window.location = "/trv/admin/backups.php";
					}
				}
			});

			return false;
		}

		function deleteBackupReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/reset-inventory-1.php',
				data: $('#deleteBackupForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['credenciales_incorrectas'] == true) {
						newNotification('Contraseña incorrecta', 'error');
					} else if (response['email_enviado'] == true) {
						document.getElementById('deleteBackupDiv1').style.display = 'none';
						document.getElementById('deleteBackupDiv2').style.display = 'block';
					}
					closeLoader();
				}
			});

			return false;
		}

		function deleteBackup2Return() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/admin-delete-backup-2.php',
				data: $('#deleteBackup2Form').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Hubo un error', 'error');
					} else if (response['codigo_incorrecto'] == false) {
						newNotification('Código incorrecto, refresque la página', 'error');
					} else if (response['backup_eliminado'] == true) {
						newNotification('Copia de seguridad eliminada', 'success');

						window.location = '/trv/admin/backups.php';
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>