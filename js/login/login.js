Login = {

// Crear vista de LOGIN
loginForm: function(domElement) {
	// Cargar plantilla de Login
	$.get('/Templates/login/login.hbs', function(data) {
		var template=Handlebars.compile(data);
		domElement.html(template());

		// Acción a realizar al pulsar el botón de login
		$("#formularioLogin").submit(
			function(eventObject) {
				//alert(JSON.stringify(ConvertFormToJSON($("#formularioLogin"))));
				formJSON = ConvertFormToJSON($("#formularioLogin"));
				Login.login(formJSON.email, formJSON.password); // Enviar datos a la API por POST

				return false; // No hacer submit
			}
		);

		// Acción a realizar al pulsar el boton de Registrarse en el formulario de login
		$("#registerButton").click(function() {
			Register.registerForm(domElement);
		});

	}, 'html');
},


// Funcion para iniciar sesión
login: function(email, password) {
	$.ajax(
		{
			"username": email,
			"password": password,
			"url": "/rest/usuario/"+email,

			"success": function(data) {
				Cookies.set('email',email);
				Cookies.set('password',password);

				Navbar.navbarLoggedButtonsView($("#loggedButtons"));
				Perfil.perfilView($("main"), data);
				return data; // Retornar datos obtenidos de la BD
			},
			"error": function(xhr,status,error) {
				MSG.MSGView($("#msg"), xhr.responseText, "warning");
				return null;
			}
		}
	)
},


}