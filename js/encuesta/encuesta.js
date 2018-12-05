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
			},
			"error": function(xhr, status, error) {
				MSG.MSGView($("#msg"), xhr.responseText, "warning");
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
			},
			"error": function(xhr, status, error) {
				MSG.MSGView($("#msg"), xhr.responseText, "warning");
			}
		}
	);
},

voteEncuesta: function(formJSON) {
	$.ajax(
		{
			"method": "POST",
			"url": "/rest/encuesta/"+formJSON.id+"/"+formJSON.fecha+"/"+formJSON.horaInicio+"/"+formJSON.horaFin+"/voto", 

			"username": Login.email,
			"password": Login.password,

			"success": function (responseData) {
				var formId = md5(Login.email+formJSON.fecha+formJSON.horaInicio+formJSON.horaFin);
				
				$.get("/Templates/encuesta/trUsuario/tdUnvote.hbs", function(tdUnvoteTemplateData) {
					var tdUnvoteTemplate = Handlebars.compile(tdUnvoteTemplateData);

					$("#"+formId).html(tdUnvoteTemplate({
						"formId":formId,
						"id": formJSON.id,
						"fecha": formJSON.fecha,
						"horaInicio": formJSON.horaInicio,
						"horaFin": formJSON.horaFin
					}));

					// Acción a realizar al pulsar el botón
					$("#form"+formId).submit(
						function(eventObject) {
							formJSON = ConvertFormToJSON($("#form"+formId));
							Encuesta.unvoteEncuesta(formJSON);

							return false; // No hacer submit
						}
					);
				});
			},
			"error": function(xhr, status, error) {
				MSG.MSGView($("#msg"), xhr.responseText, "warning");
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
			},
			"error": function(xhr, status, error) {
				MSG.MSGView($("#msg"), xhr.responseText, "warning");
			}
		}
	);
},

unvoteEncuesta: function(formJSON) {
	$.ajax(
		{
			"method": "DELETE",
			"url": "/rest/encuesta/"+formJSON.id+"/"+formJSON.fecha+"/"+formJSON.horaInicio+"/"+formJSON.horaFin+"/voto", 

			"username": Login.email,
			"password": Login.password,

			"success": function (responseData) {
				var formId = md5(Login.email+formJSON.fecha+formJSON.horaInicio+formJSON.horaFin);

				$.get("/Templates/encuesta/trUsuario/tdVote.hbs", function(tdVoteTemplateData) {
					var tdVoteTemplate = Handlebars.compile(tdVoteTemplateData);

					$("#"+formId).html(tdVoteTemplate({
						"formId":formId,
						"id": formJSON.id,
						"fecha": formJSON.fecha,
						"horaInicio": formJSON.horaInicio,
						"horaFin": formJSON.horaFin
					}));

					// Acción a realizar al pulsar el botón
					$("#form"+formId).submit(
						function(eventObject) {
							formJSON = ConvertFormToJSON($("#form"+formId));
							Encuesta.voteEncuesta(formJSON);

							return false; // No hacer submit
						}
					);
				});
			},
			"error": function(xhr, status, error) {
				MSG.MSGView($("#msg"), xhr.responseText, "warning");
			}
		}
	);
},

participarEncuesta: function(id) {
	$.ajax({
        "url": "/rest/encuesta/"+id,

        "success": function(data) {
			Encuesta.participarEncuestaView($("main"), data);
        },
        "error": function(xhr,status,error) {
            MSG.MSGView($("#msg"), "Error loading poll", "warning");
        }
    });
},

participarEncuestaView: function(domElement, encuestaData) {
	$.get('/Templates/encuesta/participarEncuesta.hbs', function(data) {
		var template=Handlebars.compile(data);
		domElement.html(template(encuestaData));

		// Guarda un mapa 3D con todos los votos ordenados por usuario y por fecha
		var votosPorUsuario = {};

		// Si hay fechas
		if(encuestaData.fechas && encuestaData.fechas.length) {
			$.get('/Templates/encuesta/th.hbs', function(thTemplateData) {
				var thTemplate = Handlebars.compile(thTemplateData);

				// Por cada fecha añadir casilla con la fecha
				encuestaData.fechas.forEach(function(fecha) {
					$("#dates").append(thTemplate({
						"colSpan": fecha.horas.length,
						"text": fecha.fecha
					}));

					// Por cada hora en esta fecha, añadir casilla con la hora
					if(fecha.horas && fecha.horas.length) {
						fecha.horas.forEach(function(hora) {
							$("#hours").append(thTemplate({
								"colSpan": "1",
								"text": hora.horaInicio+" - "+hora.horaFin
							}));

							// Añadir al mapa 3D los votos en esta hora
							if(hora.votos && hora.votos.length) {
								hora.votos.forEach(function(usuario) {
									if(!votosPorUsuario[usuario]) {votosPorUsuario[usuario] = {};}
									if(!votosPorUsuario[usuario][fecha.fecha]) {votosPorUsuario[usuario][fecha.fecha] = {};}
									votosPorUsuario[usuario][fecha.fecha][hora.horaInicio+" - "+hora.horaFin] = true;
								})
							}
						});

					}
					
				});
			});
		}

		console.log(votosPorUsuario);

		// Aborto de código para cargar plantillas por ajax
		$.get('/Templates/encuesta/trUsuario/trUsuario.hbs', function(trUsuarioTemplateData) {
			var trUsuarioTemplate=Handlebars.compile(trUsuarioTemplateData);
			$.get("/Templates/encuesta/trUsuario/tdVoted.hbs", function(tdVotedTemplateData) {
				var tdVotedTemplate = Handlebars.compile(tdVotedTemplateData);
				$.get("/Templates/encuesta/trUsuario/tdEmpty.hbs", function(tdEmptyTemplateData) {
					var tdEmptyTemplate = Handlebars.compile(tdEmptyTemplateData);
					$.get("/Templates/encuesta/trUsuario/tdVote.hbs", function(tdVoteTemplateData) {
						var tdVoteTemplate = Handlebars.compile(tdVoteTemplateData);
						$.get("/Templates/encuesta/trUsuario/tdUnvote.hbs", function(tdUnvoteTemplateData) {
							var tdUnvoteTemplate = Handlebars.compile(tdUnvoteTemplateData);

							for(var usuario in votosPorUsuario) {
								$("#userTuples").append(trUsuarioTemplate({"usuarioVotante": usuario}));

								// Si es el usuario logeado
								if(usuario == Login.email) {
									encuestaData.fechas.forEach(function (fecha) {
										fecha.horas.forEach(function (hora) {
											var formId = md5(usuario+fecha.fecha+hora.horaInicio+hora.horaFin);

											// Si existe el voto por el usuario en la fecha y la hora
											if(votosPorUsuario && votosPorUsuario[usuario] && votosPorUsuario[usuario][fecha.fecha] && votosPorUsuario[usuario][fecha.fecha][hora.horaInicio+" - "+hora.horaFin]) {
												$("#"+$.escapeSelector(usuario)).append(tdUnvoteTemplate({
													"formId":formId,
													"id": encuestaData.id,
													"fecha": fecha.fecha,
													"horaInicio": hora.horaInicio,
													"horaFin": hora.horaFin
												}));

												// Acción a realizar al pulsar el botón
												$("#form"+formId).submit(
													function(eventObject) {
														formJSON = ConvertFormToJSON($("#form"+formId));
														Encuesta.unvoteEncuesta(formJSON);

														return false; // No hacer submit
													}
												);
											}
											else {
												$("#"+$.escapeSelector(usuario)).append(tdVoteTemplate({
													"formId":formId,
													"id": encuestaData.id,
													"fecha": fecha.fecha,
													"horaInicio": hora.horaInicio,
													"horaFin": hora.horaFin
												}));

												// Acción a realizar al pulsar el botón
												$("#form"+formId).submit(
													function(eventObject) {
														formJSON = ConvertFormToJSON($("#form"+formId));
														Encuesta.voteEncuesta(formJSON);

														return false; // No hacer submit
													}
												);
											}
										});
									});
								}
								// Si no es el usuario logeado
								else {
									encuestaData.fechas.forEach(function (fecha) {
										fecha.horas.forEach(function (hora) {
											// Si existe el voto por el usuario en la fecha y la hora
											if(votosPorUsuario && votosPorUsuario[usuario] && votosPorUsuario[usuario][fecha.fecha] && votosPorUsuario[usuario][fecha.fecha][hora.horaInicio+" - "+hora.horaFin]) {
												$("#"+$.escapeSelector(usuario)).append(tdVotedTemplate());
											}
											else {
												$("#"+$.escapeSelector(usuario)).append(tdEmptyTemplate());
											}
										});
									});
								}
	
							}

							//Si no hay tupla para el usuario logeado actual
							if(Login.email && !votosPorUsuario[Login.email]) {
								$("#userTuples").append(trUsuarioTemplate({"usuarioVotante": Login.email}));
								
								encuestaData.fechas.forEach(function (fecha) {
									fecha.horas.forEach(function (hora) {
										var formId = md5(usuario+fecha.fecha+hora.horaInicio+hora.horaFin);

										$("#"+$.escapeSelector(Login.email)).append(tdVoteTemplate({
											"formId":formId,
											"id": encuestaData.id,
											"fecha": fecha.fecha,
											"horaInicio": hora.horaInicio,
											"horaFin": hora.horaFin
										}));

										// Acción a realizar al pulsar el botón
										$("#form"+formId).submit(
											function(eventObject) {
												formJSON = ConvertFormToJSON($("#form"+formId));
												Encuesta.voteEncuesta(formJSON);

												return false; // No hacer submit
											}
										);
									});
								});
							}

						});
					});
				});
			});
		});

	});
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

}

}