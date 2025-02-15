<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/printing/autoload.php";

use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

function autoPrintInvoice($saleId, $changeTicketsNum, $printOnlyChangeTickets)
{
	global $conn;

	$configBusinessName = "";
	$configChangeTickets = 0;
	$configChangeTicketsExpireDays = 0;
	$configPrinterName = "";
	$configHeading = "";
	$configThanksMsg = "";
	$configFooter = "";
	$configBarcode = 0;
	$configDrawer = 1;
	$configDrawerCard = 1;

	$sqlC = "SELECT * FROM trvsol_configuration WHERE configName= 'businessName' OR configName= 'changeTickets' OR configName= 'changeTicketsExpireDays' OR configName= 'printingAutoPrinterName' OR configName= 'printingHeadingInfo' OR configName= 'printingFooterThanksMsg' OR configName= 'printingFooterInfo' OR configName= 'printingFooterBarcode' OR configName= 'printingOpenDrawer' OR configName= 'printingOpenDrawerCard'";
	$resultC = $conn->query($sqlC);

	if ($resultC->num_rows > 0) {
		while ($rowC = $resultC->fetch_assoc()) {
			if ($rowC["configName"] == "businessName") {
				$configBusinessName = $rowC["value"];
			} else if ($rowC["configName"] == "changeTickets") {
				$configChangeTickets = $rowC["value"];
			} else if ($rowC["configName"] == "changeTicketsExpireDays") {
				$configChangeTicketsExpireDays = $rowC["value"];
			} else if ($rowC["configName"] == "printingAutoPrinterName") {
				$configPrinterName = $rowC["value"];
			} else if ($rowC["configName"] == "printingHeadingInfo") {
				$configHeading = $rowC["value"];
			} else if ($rowC["configName"] == "printingFooterThanksMsg") {
				$configThanksMsg = $rowC["value"];
			} else if ($rowC["configName"] == "printingFooterInfo") {
				$configFooter = $rowC["value"];
			} else if ($rowC["configName"] == "printingFooterBarcode") {
				$configBarcode = $rowC["value"];
			} else if ($rowC["configName"] == "printingOpenDrawer") {
				$configDrawer = $rowC["value"];
			} else if ($rowC["configName"] == "printingOpenDrawerCard") {
				$configDrawerCard = $rowC["value"];
			}
		}
	}

	$sql = "SELECT * FROM trvsol_invoices WHERE id= " . $saleId;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		try {
			$connector = new WindowsPrintConnector($configPrinterName);
			$printer = new Printer($connector);

			if ($printOnlyChangeTickets != true) {
				//Business name
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setEmphasis(true);
				$printer->setTextSize(3, 3);
				$printer->text($configBusinessName . "\n");
				$printer->setTextSize(1, 1);

				//Additional lines
				$printer->setEmphasis(false);
				$printer->text($configHeading);

				//Divider
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->text("--------------------\n");

				//Fecha y hora
				$printer->setEmphasis(true);
				$printer->setJustification(Printer::JUSTIFY_LEFT);
				$printer->text("Fecha y hora de compra: ");
				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_RIGHT);
				$printer->text($row["fechaComplete"] . "\n");

				//Venta #
				$printer->setEmphasis(true);
				$printer->setJustification(Printer::JUSTIFY_LEFT);
				$printer->text("Venta #");
				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_RIGHT);
				$printer->text($row["numero"] . "\n");

				//Atendido por
				$printer->setEmphasis(true);
				$printer->setJustification(Printer::JUSTIFY_LEFT);
				$printer->text("Atendido por: ");
				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_RIGHT);
				$printer->text($row["vendedor"] . "\n");

				//Divider
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->text("--------------------\n");

				//Productos
				$replacedString = str_replace("{{new_line}}", "\n", $row["productosArray_autoPrint"]);
				$decoded = json_decode($replacedString, true);
				for ($x = 0; $x < count($decoded); ++$x) {
					$printer->setEmphasis(true);
					$printer->setJustification(Printer::JUSTIFY_LEFT);
					$printer->text($decoded[$x]["line1"]);
					$printer->setEmphasis(false);
					$printer->setJustification(Printer::JUSTIFY_LEFT);
					$printer->text($decoded[$x]["line2"]);
				}

				//Divider
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->text("--------------------\n");

				//Forma de pago
				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_LEFT);
				$printer->text("Forma de pago: " . $row["formaPago"] . "\n");

				//Valores
				$calcTotal = $row["subtotal"] - $row["descuentos"];

				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_RIGHT);
				$printer->text("Subtotal: $" . number_format($row["subtotal"], 0, ",", ".") . "\n");
				$printer->text("Descuentos: -$" . number_format($row["descuentos"], 0, ",", ".") . "\n");
				$printer->setEmphasis(true);
				$printer->setTextSize(2, 2);
				$printer->text("TOTAL: $" . number_format($calcTotal, 0, ",", ".") . "\n");
				$printer->setEmphasis(false);
				$printer->setTextSize(1, 1);
				$printer->text("Recibido: $" . $row["recibido"] . "\n");
				$printer->text("Cambio: $" . number_format($row["cambio"], 0, ",", ".") . "\n");

				//Notas adicionales
				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_LEFT);
				$printer->text("Notas adicionales: " . $row["notas"] . "\n");

				//Thank you message
				$printer->setEmphasis(true);
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setTextSize(2, 2);
				$printer->text($configThanksMsg . "\n");
				$printer->setTextSize(1, 1);

				//Additional lines
				$printer->setEmphasis(false);
				$printer->text($configFooter);

				//Barcode
				if ($configBarcode == 1) {
					$printer->barcode($row["numero"]);
					$printer->text("\n");
					$printer->setEmphasis(true);
					$printer->text($row["numero"]);
				}

				//TRV SOLUTIONS
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->text("\n--------------------");
				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->text("\nSoftware por TRV Solutions (" . date("Y") . ")\nwww.trvsolutions.com");

				$printer->feed(3);
				$printer->cut(Printer::CUT_PARTIAL);
			}

			//Change tickets
			if ($configChangeTickets == 1 && $changeTicketsNum > 0 && $changeTicketsNum <= 5) {
				for ($x2 = 0; $x2 < $changeTicketsNum; ++$x2) {
					//Business name
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setEmphasis(true);
					$printer->setTextSize(1, 1);
					$printer->text($configBusinessName . "\n");
					$printer->setTextSize(1, 2);
					$printer->text("Ticket para cambio\n");
					$printer->setTextSize(1, 1);

					//Divider
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->text("--------------------\n");

					//Fecha y hora
					$printer->setEmphasis(true);
					$printer->setJustification(Printer::JUSTIFY_LEFT);
					$printer->text("Fecha y hora de compra: ");
					$printer->setEmphasis(false);
					$printer->setJustification(Printer::JUSTIFY_RIGHT);
					$printer->text($row["fechaComplete"] . "\n");

					//Atendido por
					$printer->setEmphasis(true);
					$printer->setJustification(Printer::JUSTIFY_LEFT);
					$printer->text("Atendido por: ");
					$printer->setEmphasis(false);
					$printer->setJustification(Printer::JUSTIFY_RIGHT);
					$printer->text($row["vendedor"] . "\n");

					//Vencimiento
					$printer->setEmphasis(true);
					$printer->setJustification(Printer::JUSTIFY_LEFT);
					$printer->setTextSize(2, 2);
					$printer->text("Válido hasta: ");
					$printer->setEmphasis(false);
					$printer->setJustification(Printer::JUSTIFY_RIGHT);
					$printer->text(date("d-m-Y h:i a", strtotime($row["fechaComplete"] . " +" . $configChangeTicketsExpireDays . " days")) . "\n");
					$printer->setTextSize(1, 1);

					//Divider
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->text("--------------------\n");

					//Barcode
					$printer->barcode($row["numero"]);
					$printer->text("\n");
					$printer->setEmphasis(true);
					$printer->text($row["numero"]);

					$printer->feed(3);
					$printer->cut(Printer::CUT_PARTIAL);
				}
			}

			if ($configDrawer == 1) {
				if ($configDrawerCard == 1 || ($configDrawerCard == 0 && $row["formaPago"] != "Tarjeta")) {
					$printer->pulse();
				}
			}
		} catch (Exception $e) {
			$errorPrint = true;
		} finally {
			$printer->close();
		}
	}
}
?>