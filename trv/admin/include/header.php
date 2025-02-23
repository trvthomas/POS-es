<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/DBData.php";

$businessName = "TRV Solutions";

$sql = "SELECT configName, value FROM trvsol_configuration WHERE configName= 'businessName'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();

	if ($row["value"] != "") {
		$businessName = $row["value"];
	}
	if (strlen($businessName) > 20) {
		$businessName = substr($businessName, 0, 15) . "...";
	}

	$btnCloseCash = "";
	if (date("H") >= 18) {
		$btnCloseCash = '<a class="button is-light" href= "/trv/close-day.php"><i class="fas fa-moon"></i> Cerrar caja</a>';
	}

	$onclickNav = "this.classList.toggle('is-active');document.getElementById('headerMobile').classList.toggle('is-active');";

	echo '<nav class="navbar">
	<div class="navbar-brand">
	<a class="navbar-item" href="/trv"><img src="/trv/media/logo.png" style="width: auto;max-height: 4rem;"></a>
	<p style= "display: flex;align-items: center;"><b>' . $businessName . '</b></p>
	
	<a class="navbar-burger" data-target="headerMobile" onclick= "' . $onclickNav . '">
	<span></span>
	<span></span>
	<span></span>
	</a>
	</div>
	
	<div id="headerMobile" class="navbar-menu">
	<div class="navbar-start">
	<a class="navbar-item" href= "/trv"><i class="fas fa-house"></i> Inicio</a>
	<a class="navbar-item" href= "/trv/admin/home.php"><i class="fas fa-user-cog"></i> Admin</a>
	
	<div class="navbar-item has-dropdown is-hoverable">
	<a class="navbar-link"><i class="fas fa-chart-bar"></i> Estadísticas</a>
	
	<div class="navbar-dropdown">
	<a class="navbar-item" href= "/trv/admin/statistics.php"><i class="fas fa-coins"></i> Ventas totales</a>
	<a class="navbar-item" href= "/trv/admin/statistics-users.php"><i class="fas fa-users"></i> Ventas por usuario</a>
	<a class="navbar-item" href= "/trv/admin/statistics-products.php"><i class="fas fa-tshirt"></i> Cantidades vendidas</a>
	<a class="navbar-item" href= "/trv/admin/statistics-vouchers.php"><i class="fas fa-tags"></i> Bonos y vouchers</a>
	</div>
	</div>
	
	
	<div class="navbar-item has-dropdown is-hoverable">
	<a class="navbar-link">Otras acciones</a>
	
	<div class="navbar-dropdown">
	<a class="navbar-item" href= "/trv/admin/products.php"><i class="fas fa-tshirt"></i> Productos</a>
	<a class="navbar-item" href= "/trv/admin/configuration.php"><i class="fas fa-gears"></i> Configuración</a>
	<a class="navbar-item" href= "/trv/admin/invoices-design.php"><i class="fas fa-brush"></i> Diseño comprobantes</a>
	<a class="navbar-item" href= "/trv/admin/barcode-creator.php"><i class="fas fa-barcode"></i> Generador códigos</a>
	<a class="navbar-item" href= "/trv/admin/vouchers.php"><i class="fas fa-tags"></i> Bonos y vouchers</a>
	<a class="navbar-item" href= "/trv/admin/users.php"><i class="fas fa-users"></i> Vendedores</a>
	<a class="navbar-item" href= "/trv/admin/invoices.php"><i class="fas fa-file-invoice-dollar"></i> Comprobantes de venta</a>
	<a class="navbar-item" href= "/trv/inventory/home.php"><i class="fas fa-boxes"></i> Inventario</a>
	</div>
	</div>
	</div>
	
	<div class="navbar-end">
	<div class="navbar-item">
	<div class="buttons">
	' . $btnCloseCash . '
	<a class="button backgroundDark" href= "/trv/new-invoice.php"><i class="fas fa-receipt"></i> Nueva venta (F1)</a>
	</div>
	</div>
	</div>
	</div>
	</nav><br>';
}
?>