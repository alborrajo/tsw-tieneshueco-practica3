Navbar = {

	navbarView: function(domElement) {
		// Cargar navbar
		$.get('/Templates/navbar/navbar.hbs', function (data) {
			var template=Handlebars.compile(data);
			domElement.html(template());

			// Set click action for locale buttons
			$(".btn-locale").click(function() {
				setLocale( $(this).attr('id') ); // Set clicked button ID as locale
			});

		}, 'html');
	},


	navbarLoggedButtonsView: function(domElement) {
		$.get('/Templates/navbar/loggedButtons.hbs', function (data) {
			var template=Handlebars.compile(data);
			domElement.html(template());

			$("#logoutButton").click(function() {
				Cookies.remove('email');
				Cookies.remove('password');
				Cookies.remove('locale');
				window.location = '/';
			});

			$("#newEncuestaForm").submit(function() {
				Encuesta.newEncuesta(ConvertFormToJSON($("#newEncuestaForm")))
				return false; // Que no envie el formulario
			})
		});
	}

}