// Cargar cookies
email = Cookies.get('email');
password = Cookies.get('password');

// Cargar navbar
Navbar.navbarView($("nav"));

// Evento para cuando cambia el hash
$(window).on('hashchange', function() {
    if(window.location.hash) {
        Encuesta.participarEncuesta(window.location.hash.substring(1));
    } else {
        main();
    }
});

main();

// -------------------
// FUNCTIONS

function main() {
    // Si hay hash (ID Encuesta en la URL)
    if(window.location.hash) {
        // Comprobar si hay cookies
        if(email !== undefined && password !== undefined) {
            Login.login(
                email,
                password,
                function(data) {Navbar.navbarLoggedButtonsView($("#loggedButtons"));}, // Si las cookies logean bien, crear navbar de logeado
                //function(xhr, status, error) {Navbar.navbarView($("nav"));} // Si no, crear navbar normal
            );
        }

        Encuesta.participarEncuesta(window.location.hash.substring(1));
    }

    // Si no hay hash
    else {
        // Comprobar si hay cookies
        if(email !== undefined && password !== undefined) {
            Login.login(
                email,
                password,
                Login.loginSuccessCallback, // Si las cookies logean bien
                function(xhr, status, error) { // Si las cookies son inválidas
                    //Navbar.navbarView($("nav")); // Crear navbar
                    Navbar.navbarLoggedButtonsView($("#loggedButtons")); // Crear navbar
                    Login.loginForm($("main")); // Crear formulario de login
                    Login.loginErrorCallback(xhr, status, error); // Mostrar error
                }
            );
        }
        // Si no hay cookies
        else {
            //Navbar.navbarView($("nav")); // Crear navbar
            Login.loginForm($("main")); // Crear formulario de login
            // Hacer como si nada
        }
    }
}

// http://www.developerdrive.com/2013/04/turning-a-form-element-into-json-and-submiting-it-via-jquery/
function ConvertFormToJSON(domForm){
    var array = domForm.serializeArray();
    var json = {};
    
    jQuery.each(array, function() {
        json[this.name] = this.value || '';
    });
    
    return json;
}