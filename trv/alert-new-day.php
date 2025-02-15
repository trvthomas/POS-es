<?php include_once "include/DBData.php";
include_once "include/stats.php";
if ($_COOKIE[$prefixCoookie . "DateEnter"] == date("Y-m-d") || !isset($_COOKIE[$prefixCoookie . "IdUser"]) || !isset($_COOKIE[$prefixCoookie . "UsernameUser"])) {
	header("Location:home.php");
} ?>
<!DOCTYPE html>
<html>

<head>
	<title>Nuevo día</title>

	<?php include_once "include/head-tracking.php"; ?>
</head>

<body>
	<?php include_once "include/header-login.php"; ?>

	<div class="contentBox loginBox">
		<div class="box has-text-centered boxVoted mt-5">
			<span class="icon is-large"><i class="fas fa-moon fa-5x"></i></span>

			<h1 class="is-size-2" style="margin-bottom: 0">Cierre de caja</h1>
			<p>El usuario <b><?php echo $_COOKIE[$prefixCoookie . "UsernameUser"]; ?></b> ingresó al sistema el día <b><?php echo date("d-m-Y", strtotime($_COOKIE[$prefixCoookie . "DateEnter"])); ?></b>. Para continuar utilizando el sistema, por favor cierre caja y abra un nuevo turno.</p>

			<button class="button backgroundDark is-fullwidth" onclick="closeCash()"><i class="fas fa-moon iconInButton"></i> Cerrar caja</button>
		</div>
	</div>

	<?php $footerFixed = true;
	include_once "include/footer.php"; ?>

	<form method="POST" action="/trv/include/close-cash.php" style="display: none" id="closeCashForm" onsubmit="return closeCashReturn();">
		<input name="closeCashPass" value="<?php echo $_COOKIE[$prefixCoookie . "DateEnter"] . "T24498"; ?>" readonly>
		<input name="closeCashName" value="<?php echo $_COOKIE[$prefixCoookie . "UsernameUser"]; ?>" readonly>
		<input type="submit" id="closeCashSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function closeCash() {
			document.getElementById('closeCashSend').click();
			openLoader();
		}

		function closeCashReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/close-cash.php',
				data: $('#closeCashForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true || response['usuario_incorrecto'] == true) {
						newNotification('Hubo un error', 'error');
						closeLoader();
					} else if (response['caja_cerrada'] != false) {
						window.location = "/trv/day-summary.php?day=" + response['caja_cerrada'];
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>