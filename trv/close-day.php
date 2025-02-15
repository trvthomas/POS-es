<?php include_once "include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Cerrar caja</title>

	<?php include_once "include/head-tracking.php"; ?>
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<div class="contentBox loginBox">
		<div class="box has-text-centered boxVoted mt-5">
			<a class="button is-small is-pulled-left" href="/trv/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>
			<span class="icon is-large"><i class="fas fa-moon fa-5x"></i></span>

			<h1 class="is-size-5 mb-0">Cerrar caja</h1>
			<p>Deslice completamente para cerrar este turno</p>

			<div class="field">
				<div class="control">
					<input type="range" class="closeDaySlider" id="sliderCloseDay" min="0" max="100" value="0" onmouseup="closeCash()">
				</div>
			</div>
		</div>
	</div>

	<?php $footerFixed = true;
	include_once "include/footer.php"; ?>

	<form method="POST" action="/trv/include/close-cash.php" style="display: none" id="closeCashForm" onsubmit="return closeCashReturn();">
		<input name="closeCashPass" value="<?php echo $_COOKIE[$prefixCoookie . "DateEnter"] . "T24498"; ?>" readonly>
		<input type="submit" id="closeCashSend" value="Enviar">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function closeCash() {
			var sliderValue = document.getElementById('sliderCloseDay').value;

			if (sliderValue < 100) {
				newNotification('Arrastre hasta el final para cerrar caja', 'error');
				document.getElementById('sliderCloseDay').value = 0;
			} else {
				document.getElementById('closeCashSend').click();

				openLoader();
			}
		}

		function closeCashReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/close-cash.php',
				data: $('#closeCashForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
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