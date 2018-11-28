Register = {

// Crear vista de REGISTER
registerForm: function(domElement) {
	// Cargar plantilla de Register
	$.get('/Templates/login/register.hbs', function(data) {
		var template=Handlebars.compile(data);
		domElement.html(template());

		// Acción a realizar al pulsar el botón de register
		$("#formularioRegister").submit(
			function(eventObject) {
				formJSON = ConvertFormToJSON($("#formularioRegister"));
				console.log(formJSON);
				
				Register.register(formJSON) // Enviar datos a la API por POST

				return false; // No hacer submit
			}
		);

	}, 'html');
},

// Funcion para registrar
register: function(data) {
	$.ajax(
		{
			"method": "POST",
			"url": "/rest/usuario", 

			// POSTear JSON a pelo
			'dataType': 'html',
			'processData': false,
			'contentType': 'application/json',
			"data": JSON.stringify(data),

			"success": function (responseData) {
				MSG.MSGView($("#msg"), "Registered successfully", "success");
				return true;
			},
			"error": function(xhr, status, error) {
				MSG.MSGView($("#msg"), xhr.responseText, "warning");
				return null;
			}
		}
	);
}


}