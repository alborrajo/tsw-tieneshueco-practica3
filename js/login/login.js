Login = {

email: undefined,
password: undefined,

// Crear vista de LOGIN
loginForm: function(domElement) {
	// Cargar plantilla de Login
	$.get('/Templates/login/login.hbs', function(data) {
		var template=Handlebars.compile(data);
		domElement.html(template());

		// Acción a realizar al pulsar el botón de login
		$("#formularioLogin").submit(
			function(eventObject) {
				formJSON = ConvertFormToJSON($("#formularioLogin"));
				Login.login(formJSON.email, formJSON.password, Login.loginSuccessCallback, Login.loginErrorCallback); // Enviar datos a la API por POST

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
login: function(email, password, callback, errorCallback) {
	Login.email = email;
	Login.password = password;

	$.ajax(
		{
			"username": email,
			"password": password,
			"url": "/rest/usuario/"+email,

			"success": callback,
			"error": errorCallback,
		}
	)
},

loginSuccessCallback: function(data) {
	Cookies.set('email',Login.email);
	Cookies.set('password',Login.password);

	Navbar.navbarLoggedButtonsView($("#loggedButtons"),true);
	Perfil.perfilView($("main"), data);
},

loginErrorCallback: function(xhr, status, error) {
	Login.email = null;
	Login.password = null;
	MSG.MSGView($("#msg"), xhr.responseText, "warning");
}
 

}