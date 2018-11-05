<?php

require_once(__DIR__."/../Models/login/login-model.php");
require_once(__DIR__."/../Models/perfil/perfil-model.php");
require_once(__DIR__."/BaseRest.php");

/**
* Class UsuarioRest
*
* Operaciones para registrar usuario y obtener las encuestas del usuario autenticado
* Methods gives responses following Restful standards. Methods of this class
* are intended to be mapped as callbacks using the URIDispatcher class.
*
*/
class UserRest extends BaseRest {

	public function __construct() {
		parent::__construct();
	}

	public function register($data) {
		try {
			$user->checkIsValidForRegister();

			(new LoginModel())->registrar($data->email, $data->password, $data->nombre);

			header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
			header("Location: ".$_SERVER['REQUEST_URI']."/".$data->email);
		}catch(MSGException $e) {
			http_response_code(400);
			header('Content-Type: application/json');
			echo(json_encode($e->getMessage()));
		}
	}

	public function login($email) {
		$currentLogged = parent::authenticateUser();
		if ($currentLogged != $email) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("You are not authorized to login as anyone but you");
		} else {
			header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');

			// Perfil
			$encuestas = (new PerfilModel())->getEncuestas($email);
			echo(json_encode($encuestas));
		}
	}
}

// URI-MAPPING for this Rest endpoint
$userRest = new UserRest();
URIDispatcher::getInstance()
->map("GET",	"/usuario/$1", array($userRest,"login"))
->map("POST", "/usuario", array($userRest,"register"));
