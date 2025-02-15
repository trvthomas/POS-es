Highcharts.setOptions({
	colors: ['var(--dark-color)', 'var(--light-color)', 'var(--normal-color)'],
	lang: {
		contextButtonTitle: 'Imprimir y descargar',
		printChart: 'Imprimir',
		downloadPNG: 'Descargar como imagen',
		downloadPDF: 'Descargar como PDF',
		downloadSVG: 'Descargar como SVG',
		downloadCSV: 'Descargar como CSV',
		downloadXLS: 'Descargar como Excel',
		loading: 'Cargando...',
		shortMonths: [
			'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
		]
	},
	exporting: {
		sourceWidth: 1500,
		sourceHeight: 600,
		buttons: {
			contextButton: {
				menuItems: ["printChart", "separator", "downloadPNG", "downloadPDF", "downloadSVG", "separator", "downloadCSV", "downloadXLS"]
			}
		}
	}
});