<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/inventory/include/verifySession.php";

$autorizarEntrar = false;

$movementDate = "";
$movementDateComplete = "";
$movementType = "";
$movementTypeColor = "#19191a";
$movementReason = "";
$movementNotes = "";
$movementArrayComplete = "";
$movementProdsModified = "";
$tableView = "";

if (isset($_GET["id"])) {
	$sql = "SELECT * FROM trvsol_inventory WHERE id=" . $_GET["id"];
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$autorizarEntrar = true;

		$movementDate = $row["date"];
		$movementDateComplete = date("d-m-Y h:i a", strtotime($row["date"] . " " . $row["hour"]));

		if ($row["type"] == "entry") {
			$movementType = "Ingreso de mercancía";
			$movementTypeColor = "#660066";
		} else if ($row["type"] == "exit") {
			$movementType = "Retiro de mercancía";
			$movementTypeColor = "#ff6600";
		} else if ($row["type"] == "adjust") {
			$movementType = "Ajuste de inventario";
			$movementTypeColor = "#006699";
		} else if ($row["type"] == "sales") {
			$movementType = "Venta de productos";
			$movementTypeColor = "#008000";
		} else if ($row["type"] == "saleCancel") {
			$movementType = "Venta cancelada";
			$movementTypeColor = "#ef4d4d";
		}

		$movementReason = $row["reason"];
		$movementNotes = $row["notes"];
		$movementArrayComplete = $row["productsArrayComplete"];
		$movementProdsModified = $row["productsAdded"];
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Detalles del movimiento - <?php echo $movementType; ?> - <?php echo $movementDateComplete; ?></title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<?php if ($autorizarEntrar == true) { ?>
		<div class="contentBox">
			<div class="box has-text-centered">
				<a class="button is-small is-pulled-left" href="/trv/inventory/inventory-history.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

				<h3 class="is-size-5">Detalles del movimiento - <?php echo $movementType; ?></h3>
				<p><span class="tag is-rounded" style="background-color: <?php echo $movementTypeColor; ?>; color: #fff;"><?php echo $movementType; ?></span> <span class="tag is-rounded"><b><i class="fas fa-calendar-day"></i> <?php echo $movementDateComplete; ?></b></span></p>

				<div class="columns">
					<div class="column">
						<div class="block">
							<h4 class="is-size-6 has-text-grey">Motivo</h4>
							<p class="is-size-5 has-text-success"><b><?php echo $movementReason; ?></b></p>
						</div>
					</div>

					<div class="column">
						<div class="block">
							<h4 class="is-size-6 has-text-grey">Notas</h4>
							<p class="is-size-5"><b><?php if ($movementNotes != "") {
														echo $movementNotes;
													} else {
														echo '<span class= "has-text-grey">No aplica</span>';
													} ?></b></p>
						</div>
					</div>
				</div>

				<?php if ($movementType == "Venta de productos" && $movementDate == date("Y-m-d")) { ?>
					<div class="notification is-light is-info">Este documento puede cambiar en el transcurso del día. <i class="fas fa-circle-info is-clickable" title="Ver más información" onclick="document.getElementById('overlayVariableMovement').style.display= 'block';"></i></div>
				<?php } ?>
			</div>

			<div class="box">
				<button class="button is-small is-info is-light is-active" onclick="changeView('Grid')" id="buttonViewGrid"><i class="fas fa-table-cells-large"></i> Vista ampliada</button>
				<button class="button is-small is-info is-light" onclick="changeView('Table')" id="buttonViewTable"><i class="fas fa-bars"></i> Vista compacta</button>

				<div id="contentGrid" class="fade">
					<div class="list has-visible-pointer-controls">
						<?php
						$decoded = json_decode($movementArrayComplete, true);
						for ($x = 0; $x < count($decoded); ++$x) {
							$sql2 = "SELECT id, nombre, precio, barcode, imagen, categoryID, variable_price FROM trvsol_products WHERE id=" . $decoded[$x]["id"];
							$result2 = $conn->query($sql2);
							if ($result2->num_rows > 0) {
								$row2 = $result2->fetch_assoc();

								$colorDifference = "#008000";
								$imageProduct = "/trv/media/imagen-no-disponible.png";
								if ($decoded[$x]["difference"] < 0) {
									$colorDifference = "#ef4d4d";
								}
								if ($row2["imagen"] != "") {
									$imageProduct = $row2["imagen"];
								}

								$sql3 = "SELECT * FROM trvsol_categories WHERE id=" . $row2["categoryID"];
								$result3 = $conn->query($sql3);

								if ($result3->num_rows > 0) {
									$row3 = $result3->fetch_assoc();

									$finalPrice = '$' . number_format($row2["precio"], 0, ",", ".");
									if ($row2["variable_price"] == 1) {
										$finalPrice = 'Precio variable';
									}

									echo '<div class="list-item">
		<div class="list-item-image">
		<figure class="image is-64x64"><img src="' . $imageProduct . '" class= "is-rounded" style= "border: 2px solid ' . $row3["color"] . ';"></figure>
		</div>
		
		<div class="list-item-content">
		<div class="list-item-title">' . $row2["nombre"] . '</div>
		<div class="list-item-description"><span class="tag is-rounded is-success is-light">' . $finalPrice . '</span> <span class="tag is-rounded"><i class="fas fa-barcode"></i> ' . $row2["barcode"] . '</span></div>
		<div class="list-item-description"><span class="tag is-rounded" style= "background-color: ' . $row3["color"] . '; color: ' . $row3["color_txt"] . ';">' . $row3["nombre"] . '</span></div>
		</div>
		
		<div class="list-item-controls">
		<div class= "columns has-text-centered">
		<div class= "column">
			<div class= "block">
			<h4 class= "is-size-6 has-text-grey">Stock inicial</h4>
			<p class= "is-size-5 has-text-success"><b>' . $decoded[$x]["stock_before"] . '</b></p>
			</div>
		</div>
		
		<div class= "column">
			<div class= "block">
			<h4 class= "is-size-6 has-text-grey">Diferencia</h4>
			<p class= "is-size-5" style= "color: ' . $colorDifference . ';"><b>' . $decoded[$x]["difference"] . '</b></p>
			</div>
		</div>
		
		<div class= "column">
			<div class= "block">
			<h4 class= "is-size-6 has-text-grey">Stock final</h4>
			<p class= "is-size-5"><b>' . $decoded[$x]["stock_after"] . '</b></p>
			</div>
		</div>
		</div>
		</div>
	</div>';

									$tableView .= '<tr>
	<td><b>' . $row2["nombre"] . '</b><br>' . $finalPrice . '<br>' . $row2["barcode"] . '</td>
	<td>' . $decoded[$x]["stock_before"] . '</td>
	<td style= "color: ' . $colorDifference . '"><b>' . $decoded[$x]["difference"] . '</b></td>
	<td>' . $decoded[$x]["stock_after"] . '</td>
	</tr>';
								}
							}
						}
						?>
					</div>
				</div>

				<div id="contentTable" class="fade" style="display: none">
					<table class="table is-striped is-fullwidth">
						<thead>
							<tr>
								<th>Producto</th>
								<th>Stock inicial</th>
								<th>Diferencia</th>
								<th>Stock final</th>
							</tr>
						</thead>
						<tbody>
							<?php echo $tableView; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

		<div id="overlayVariableMovement" class="trvModal">
			<div class="trvModal-content trvModal-content-small">
				<span class="delete" onclick="document.getElementById('overlayVariableMovement').style.display='none'"></span>

				<div class="trvModal-header">
					<h3 class="is-size-3 mb-1">Movimiento de ventas variable</h3>
				</div>

				<div class="trvModal-elements">
					<p>Este movimiento o documento se <b>actualiza constantemente</b> con las ventas del día de hoy.
						<br>Para obtener un <b>reporte final de este movimiento</b> revise la información en otro momento.
					</p>

					<div class="columns mt-5">
						<div class="column">
							<button class="button is-fullwidth backgroundDark" onclick="document.getElementById('overlayVariableMovement').style.display='none'">Cerrar</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
		<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
		<script>
			function changeView(viewType) {
				document.getElementById('contentGrid').style.display = 'none';
				document.getElementById('contentTable').style.display = 'none';
				document.getElementById('content' + viewType).style.display = 'block';
				document.getElementById('buttonViewGrid').classList.remove('is-active');
				document.getElementById('buttonViewTable').classList.remove('is-active');
				document.getElementById('buttonView' + viewType).classList.add('is-active');
			}
		</script>
	<?php } else { ?>
		<div class="contentBox">
			<div class="box has-text-centered">
				<h1 class="is-size-4">Hubo un error</h1>
				<p>Verifique el enlace e intente nuevamente</p>
			</div>
		</div>
	<?php } ?>
</body>

</html>