<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$listaVouchers = "";

if (isset($_POST["getVouchersToken"]) && $_POST["getVouchersToken"] == "admin38942") {
	$sql = "SELECT * FROM trvsol_vouchers";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$bgColor = "";
			if ($row["totalAvailable"] <= 0) {
				$bgColor = 'has-background-warning-light" title= "Bono utilizado en su totalidad"';
			} else if (strtotime($row["expiration"]) < strtotime(date("Y-m-d"))) {
				$bgColor = 'has-background-danger-light" title= "Bono vencido"';
			}

			$listaVouchers .= '<div class="list-item ' . $bgColor . '">
		<div class="list-item-content">
		<div class="list-item-title">' . $row["code"] . '</div>
		<div class="list-item-description"><span class="tag is-rounded"><b>' . $row["value"] . '% de descuento</b></span> <span class="tag is-rounded is-success is-light">' . number_format($row["totalAvailable"], 0, ",", ".") . ' disponibles</span> <span class="tag is-rounded">Expira: ' . date("d-m-Y", strtotime($row["expiration"])) . '</span></div>
		</div>
		
		<div class="list-item-controls">
		<div class= "buttons is-right">
		<a class="button" href="/trv/admin/edit-voucher.php?id=' . $row["id"] . '"><i class="fas fa-edit"></i> Editar</a>
		<button class="button is-danger is-light" onclick= "deleteVoucher(' . $row["id"] . ')" id= "btnDeleteVoucher' . $row["id"] . '" title= "Eliminar"><i class="fas fa-trash-alt"></i></button>
		<a class="button" href="/trv/admin/statistics-vouchers.php?idVoucher=' . $row["id"] . '" title= "Ver estadísticas del voucher"><i class="fas fa-chart-bar"></i></a>
		</div>
		</div>
	</div>';
		}
	} else {
		$listaVouchers = "<p class= 'has-text-centered is-size-5'><b>No se encontraron resultados</b></p>";
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'vouchers' => $listaVouchers
);
echo json_encode(convertJson($varsSend));
?>