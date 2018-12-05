Perfil = {

	perfilView: function(domElement, userData) {
		$.get('/Templates/perfil/perfil.hbs', function (data) {
			var template=Handlebars.compile(data);
			domElement.html(template());

			// Si hay encuestas
			if(userData.encuestas && userData.encuestas.length) {
				$.get('/Templates/perfil/encuesta.hbs', function (data) {
					// Cargar plantilla
					var template=Handlebars.compile(data);

					// Aplicarla por cada encuesta
					userData.encuestas.forEach(function(item) {
						Perfil.encuestaView(template, $("#encuestas"), item);
					});
				});
			}
			
			//Si hay encuestas compartidas
			if(userData.encuestasCompartidas && userData.encuestasCompartidas.length) {
				$.get('/Templates/perfil/encuestaCompartida.hbs', function (data) {
					// Cargar plantilla
					var template=Handlebars.compile(data);

					// Aplicarla por cada encuesta compartida
					userData.encuestasCompartidas.forEach(function(item) {
						Perfil.encuestaCompartidaView(template, $("#encuestasCompartidas"), item);
					});
				});
			}

		});
	},

	encuestaView: function(template, domElement, encuestaData) {
		domElement.append(template(encuestaData));
		
		$("#delete"+encuestaData.id).click(function() {
			Encuesta.deleteEncuesta(encuestaData.id);
		});
	},

	encuestaCompartidaView: function(template, domElement, encuestaData) {
		domElement.append(template(encuestaData));
	}

}