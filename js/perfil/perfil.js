Perfil = {

	perfilView: function(domElement, userData) {
		$.get('/Templates/perfil/perfil.hbs', function (data) {
			var template=Handlebars.compile(data);
			domElement.html(template());

			userData.encuestas.forEach(function(item) {
				Perfil.encuestaView($("#encuestas"), item);
			});
			userData.encuestasCompartidas.forEach(function(item) {
				Perfil.encuestaCompartidaView($("#encuestasCompartidas"), item);
			});

		});
	},

	encuestaView: function(domElement, encuestaData) {
		console.log(encuestaData);
		$.get('/Templates/perfil/encuesta.hbs', function (data) {
			var template=Handlebars.compile(data);
			domElement.append(template(encuestaData));

			$("#delete"+encuestaData.id).click(function() {
				Encuesta.deleteEncuesta(encuestaData.id);
			});
		});
	},

	encuestaCompartidaView: function(domElement, encuestaData) {
		$.get('/Templates/perfil/encuestaCompartida.hbs', function (data) {
			var template=Handlebars.compile(data);
			domElement.append(template(encuestaData));
		});
	}

}