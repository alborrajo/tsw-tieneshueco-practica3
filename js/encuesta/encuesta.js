Encuesta = {

newEncuesta: function(data) {
	$.ajax(
		{
			"method": "POST",
			"url": "/rest/encuesta", 

			"username": Cookies.get('email'),
			"password": Cookies.get('password'),

			// POSTear JSON a pelo
			'dataType': 'json',
			'processData': false,
			'contentType': 'application/json',
			"data": JSON.stringify(data),

			"success": function (responseData) {
				Perfil.encuestaView($("#encuestas"), responseData);
				return true;
			},
			"error": function(xhr, status, error) {
				MSG.MSGView($("#msg"), xhr.responseText, "warning");
				return null;
			}
		}
	);

},

deleteEncuesta: function(id) {
	$.ajax(
		{
			"method": "DELETE",
			"url": "/rest/encuesta/"+id, 

			"username": Cookies.get('email'),
			"password": Cookies.get('password'),

			"success": function (responseData) {
				$("#id"+id).remove(); // Borrar fila
				return true;
			},
			"error": function(xhr, status, error) {
				MSG.MSGView($("#msg"), xhr.responseText, "warning");
				return null;
			}
		}
	);
},

editEncuesta: function(id) {
	$.ajax(
		{
			"method": "GET",
			"url": "/rest/encuesta/"+id, 

			"username": Cookies.get('email'),
			"password": Cookies.get('password'),

			"success": function (responseData) {
				$("#listaEncuestas").remove();//Eliminar la lista de encuestas
				Encuesta.editEncuestaView(responseData);
				return true;
			},
			"error": function(xhr, status, error) {
				MSG.MSGView($("#msg"), xhr.responseText, "warning");
				return null;
			}
		}
	);
},

editEncuestaView: function(encuestaData) {
	var domElement = $("main");
	console.log(encuestaData);
	$.get('/Templates/encuesta/plantillaEditEncuesta.hbs',function(data)
	{
		var template = Handlebars.compile(data);
		var context = {nombreEncuesta: encuestaData.nombre, linkEncuesta: encuestaData.id};
		var html = template(context);
		domElement.append(html);

		$("#addDateForm"+encuestaData.id).submit(function() {
				Encuesta.addFecha(encuestaData.id,ConvertFormToJSON($("#addDateForm"+encuestaData.id)))
				return false; // Que no envie el formulario
			})
	});

	$.get('/Templates/encuesta/fechaView.hbs',function(data)
	{
		encuestaData.fechas.forEach(function(item) {
			var template = Handlebars.compile(data);
			var context = {idEncuesta:encuestaData.id, idFecha: item.fecha, fecha:item.fecha};
			var html = template(context);
			var divFechas = $("#tablasFechas");
			divFechas.append(html);

			$("#delete"+encuestaData.id+item.fecha).click(function()
			{
				Encuesta.deleteFecha(encuestaData.id, item.fecha);
			}
			);

			var idFecha = item.fecha;

			$.get('/Templates/encuesta/horaView.hbs', function(data)
			{
				console.log(item);
				item.horas.forEach(function(item)
				{
					console.log(item);
					var template = Handlebars.compile(data);
					var context = {horaInicio: item.horaInicio, horaFin: item.horaFin};
					var html = template(context);
					var elemento = $("#fecha"+idFecha);
					elemento.append(html);
				}
				);
				$.get('/Templates/encuesta/newHoraView.hbs',function(data)
				{
					var template = Handlebars.compile(data);
					var html = template();
					var elemento = $("#fecha"+idFecha);
					elemento.append(html);
				}
				);
			}
			)
		});
	}
	
	);
},

deleteFecha: function(idEncuesta, idFecha)
{
	$.ajax(
		{
			"method": "DELETE",
			"url": "/rest/encuesta/"+idEncuesta+"/"+idFecha, 

			"username": Cookies.get('email'),
			"password": Cookies.get('password'),
			
			"success": function (responseData) {
				$("#fecha"+idFecha).remove();
				return true;
			},
			"error": function(xhr, status, error) {
				MSG.MSGView($("#msg"), xhr.responseText, "warning");
				return true;
			}
		}
	);

},

addFecha: function(idEncuesta, Fecha)
{
	$.ajax(
		{
			"method": "POST",
			"url": "/rest/encuesta/"+idEncuesta, 

			"username": Cookies.get('email'),
			"password": Cookies.get('password'),

			// POSTear JSON a pelo
			'dataType': 'json',
			'processData': false,
			'contentType': 'application/json',
			"data": JSON.stringify(Fecha),
			
			"success": function (responseData) {
				Encuesta.editEncuestaView(responseData);
				return true;
			},
			"error": function(xhr, status, error) {
				MSG.MSGView($("#msg"), xhr.responseText, "warning");
				return true;
			}
		}
	);

}

}