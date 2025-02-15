<?php header('Location:include/updates/db-optimization.php'); ?>
<!DOCTYPE html>
<html>

<head>
	<title>Optimizando la base de datos, por favor espere...</title>

	<?php include_once "include/head-tracking.php"; ?>
</head>

<body>
	<div id="overlayLoader" class="overlayLoader" style="display: block">
		<div class="loaderBox">
			<div class="imgProcessing">
				<img src="/trv/media/loader.gif" alt="Cargando..." width="100%" loading="lazy">
			</div>

			<h1 class="is-size-3" style="margin: 2px auto;">Optimizando la base de datos, por favor espere</h1>
			<h3 class="is-size-4 has-text-danger" style="margin-top: 2px;">No cierre esta pestaña</h3>
		</div>
	</div>
</body>

</html>