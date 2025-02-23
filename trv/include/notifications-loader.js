function newNotification(notText, notType) {
	var colorSnackbar = '#339a37';
	if (notType == "error") { colorSnackbar = '#cc0000'; }

	Toastify({
		text: notText,
		gravity: "bottom",
		position: "right",
		stopOnFocus: false,
		escapeMarkup: false,
		style: {
			background: colorSnackbar,
			boxShadow: "2px 2px 13px #a9abae",
			cursor: "default"
		}
	}).showToast();
}

function newNotificationError() { newNotification('Hubo un error', 'error'); }

var createLoader = document.createElement("DIV");
var attributeLoader = document.createAttribute("id");
attributeLoader.value = "overlayLoader";
var attributeLoader2 = document.createAttribute("class");
attributeLoader2.value = "overlayLoader";
var appendLoader = document.body.appendChild(createLoader);
appendLoader.setAttributeNode(attributeLoader); appendLoader.setAttributeNode(attributeLoader2);

document.getElementById('overlayLoader').innerHTML = '<div class= "loaderBox"><div class= "imgProcessing"><img src= "/trv/media/loader.gif" alt= "Cargando..." width= "100%" loading= "lazy"></div><h3 style= "margin-top: 2px;">Cargando...</h3></div>';

function openLoader() { document.getElementById('overlayLoader').style.display = 'block'; }
function closeLoader() { document.getElementById('overlayLoader').style.display = 'none'; }

function thousands_separators(num) {
	var num_parts = num.toString().split(".");
	num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
	return num_parts.join(".");
}