MSG = {
	MSGView: function(domElement, message, status) {
		$.get('/Templates/msg/msg.hbs', function (data) {
			var template=Handlebars.compile(data);
			domElement.html(template({
				"status": status,
				"msg": message
			}));
		});
	}
}