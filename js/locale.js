var localeData; // GLOBAL Variable where the locale object will be stored

// Read cookie locale
var locale = Cookies.get('locale');

// If it hasn't been set, set it to 'en' (default value)
if(typeof locale  == 'undefined') {
	setLocale('en');
}
// Else, use the cookie value
else {
	setLocale(locale);
}


function setLocale(locale) {
	//Set cookie
	Cookies.set('locale',locale);

	// Load json file with the specified locale
	$.ajax(
		{
			'url': "/locale/"+locale+".json",
			'dataType': "json",
			'success': function (data) {
				// Change HTML to fit localeData
				localeData = data;
				for(var key in localeData) {
					$("."+key).text(localeData[key]);
				}
			}
		}
	);

}