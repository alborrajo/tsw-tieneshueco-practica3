// Cargar cookies e intentar logear
email = Cookies.get('email');
password = Cookies.get('password');
if(email !== undefined && password !== undefined) {
	Login.login(
		Cookies.get('email'),
		Cookies.get('password')
	);
}

// Crear navbar
Navbar.navbarView($("nav"));

// Crear formulario de login
Login.loginForm($("main"));



// -------------------
// FUNCTIONS

// http://www.developerdrive.com/2013/04/turning-a-form-element-into-json-and-submiting-it-via-jquery/
function ConvertFormToJSON(domForm){
    var array = domForm.serializeArray();
    var json = {};
    
    jQuery.each(array, function() {
        json[this.name] = this.value || '';
    });
    
    return json;
}