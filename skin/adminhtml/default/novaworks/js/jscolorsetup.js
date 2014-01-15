Event.observe(window, 'load', function() {
	if(document.getElementById('theming_theme_maincolor').length !== null) {
		var mainColor = new jscolor.color(document.getElementById('theming_theme_maincolor'), {})
		var cornersColor = new jscolor.color(document.getElementById('theming_theme_cornerscolor'), {})
		var lightColor = new jscolor.color(document.getElementById('theming_theme_lightcolor'), {})
		var textColor = new jscolor.color(document.getElementById('theming_theme_textcolor'), {})
		var linksColor = new jscolor.color(document.getElementById('theming_theme_linkscolor'), {})
	}
});