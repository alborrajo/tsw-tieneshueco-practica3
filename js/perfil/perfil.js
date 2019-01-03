Perfil = {

	perfilView: function(domElement, userData) {
		$.get('/Templates/perfil/perfil.hbs', function (data) {
			var template=Handlebars.compile(data);
			domElement.html(template());

			// Si hay encuestas
			if(userData.encuestas && userData.encuestas.length) {
				// Por cada encuesta
				userData.encuestas.forEach(function(item) {
					Perfil.encuestaView($("#encuestas"), item);
				});
			}
			
			//Si hay encuestas compartidas
			if(userData.encuestasCompartidas && userData.encuestasCompartidas.length) {
				// Por cada encuesta compartida
				userData.encuestasCompartidas.forEach(function(item) {
					Perfil.encuestaCompartidaView($("#encuestasCompartidas"), item);
				});
			}

		});
	},

	encuestaView: function(domElement, encuestaData) {
		$.get('/Templates/perfil/encuesta.hbs', function (data) {
			// Cargar plantilla
			var templeit=Handlebars.compile(data);
			domElement.append(templeit(encuestaData));
			
			$("#delete"+encuestaData.id).click(function() {
				Encuesta.deleteEncuesta(encuestaData.id);
			});

			$("#edit"+encuestaData.id).click(function(){
				Encuesta.editEncuesta(encuestaData.id);
			});

			$("#delete"+encuestaData.id).click(function() {
				Encuesta.deleteEncuesta(encuestaData.id);
			});
		});
		setLocale(Cookies.get('locale'));
	},

	encuestaCompartidaView: function(domElement, encuestaData) {
		$.get('/Templates/perfil/encuestaCompartida.hbs', function (data) {
			// Cargar plantilla
			var template=Handlebars.compile(data);
			domElement.append(template(encuestaData));
		});
		setLocale(Cookies.get('locale'));
	}
	

}