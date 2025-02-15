<?php include "DBData.php";

$existeError = false;
$salesList = "";

if (isset($_POST["getDayInvoicesPayment"]) && isset($_POST["getDayInvoicesSearch"])) {
	$sql2 = "SELECT * FROM trvsol_invoices WHERE fecha='" . date("Y-m-d") . "'";

	if ($_POST["getDayInvoicesPayment"] != "0" && $_POST["getDayInvoicesPayment"] != "C") {
		$sql2 .= " AND formaPago='" . $_POST["getDayInvoicesPayment"] . "'";
	} else if ($_POST["getDayInvoicesPayment"] == "C") {
		$sql2 .= " AND cancelada='1'";
	}

	if ($_POST["getDayInvoicesSearch"] != "") {
		$sql2 .= " AND (numero LIKE '%" . $_POST["getDayInvoicesSearch"] . "%' OR subtotal LIKE '%" . $_POST["getDayInvoicesSearch"] . "%')";
	}

	$result2 = $conn->query($sql2);

	if ($result2->num_rows > 0) {
		while ($row2 = $result2->fetch_assoc()) {
			$totalVenta = $row2["subtotal"] - $row2["descuentos"];

			$onclick1 = "shareElement(" . $row2["id"] . ", '" . $row2["numero"] . "')";
			$onclick2 = "sendEmail('" . $row2["id"] . "')";
			$onclick3 = "cancelSale('" . $row2["id"] . "')";

			$decoded = json_decode($row2["productosArray"], true);
			$bgColor = "";
			if ($row2["cancelada"] == true) {
				$bgColor = "has-background-danger-light";
			}

			$pluralAddS = "s";
			if (count($decoded) == 1) {
				$pluralAddS = "";
			}

			$paymentBgColor = "pastel-bg-green";
			if ($row2["formaPago"] == "Tarjeta") {
				$paymentBgColor = "pastel-bg-purple";
			} else if ($row2["formaPago"] == "Multipago") {
				$paymentBgColor = "pastel-bg-darkorange";
			} else if ($row2["formaPago"] != "Efectivo") {
				$paymentBgColor = "pastel-bg-cyan";
			}

			$salesList .= '<div class="list-item ' . $bgColor . '">
		<div class="list-item-content">
		<div class="list-item-title">Venta ' . $row2["numero"] . '</div>
		<div class="list-item-description"><span class="tag is-rounded is-success is-light">$' . number_format($totalVenta, 0, ",", ".") . '</span> <span class="tag is-rounded">' . $row2["fechaComplete"] . '</span></div>
		<div class="list-item-description"><span class="tag is-rounded ' . $paymentBgColor . '">' . $row2["formaPago"] . '</span> <span class="tag is-rounded"><b>' . count($decoded) . ' producto' . $pluralAddS . '</b></span></div>
		</div>
		
		<div class="list-item-controls">
		<div class= "buttons is-right">
		<button class="button backgroundDark" onclick= "' . $onclick1 . '"><i class="fas fa-clipboard-list"></i> Detalles</button>';

			if ($row2["cancelada"] != true) {
				$salesList .= '<button class="button is-light is-info" onclick= "' . $onclick2 . '" title= "Enviar por e-mail"><i class="fas fa-envelope"></i></button>
		<button class="button is-light is-danger" onclick= "' . $onclick3 . '" title= "Cancelar venta"><i class="fas fa-ban"></i></button>
		</div>
		</div>
	</div>';
			} else {
				$salesList .= '<button class="button is-danger" disabled>Venta cancelada por: ' . $row2["canceladaPor"] . '</button>
		</div>
		</div>
	</div>';
			}
		}
	} else {
		$salesList = "<p class= 'has-text-centered is-size-5'><b>No se encontraron resultados</b></p>";
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'comprobantes' => $salesList
);
echo json_encode(convertJson($varsSend));
?>