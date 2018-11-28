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
				Perfil.encuestaView($("#encuestas"), responseData); // TODO: Borrar
				alert("Redirigir a vista de editar");
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
}

}