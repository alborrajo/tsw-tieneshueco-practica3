<?php

require_once(__DIR__."/../Models/perfil/perfil-model.php");
require_once(__DIR__."/../Models/encuesta/encuesta-model.php");

require_once(__DIR__."/BaseRest.php");

/**
* Class UsuarioRest
*
* Operaciones para registrar usuario y obtener las encuestas del usuario autenticado
* Methods gives responses following Restful standards. Methods of this class
* are intended to be mapped as callbacks using the URIDispatcher class.
*
*/
class EncuestaRest extends BaseRest {

	public function __construct() {
		parent::__construct();
	}

	public function nuevaEncuesta($data) {
        $currentLogged = parent::authenticateUser();
		if ($currentLogged != false) { //Si currentLogged no es false entonces el usuario existe y está logeado
            try {
                $idEncuesta = (new PerfilModel())->nuevaEncuesta($data->nombre, $_SERVER['PHP_AUTH_USER']);

                echo json_encode(["id" => $idEncuesta, "nombre" => $data->nombre]);

                header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
                exit;
            } catch(MSGException $e) {
                http_response_code(404);
                header('Content-Type: application/json');
                die(json_encode($e));
            }
        } else {
            http_response_code(401);
            die("El usuario debe identificarse");
        }
    }
    
    public function getEncuesta($id) {
        try {
            $encuesta = (new EncuestaModel())->getEncuesta($id);

            //Respuesta si no se ha encontrado la encuesta
            if($encuesta == null) {
                http_response_code(404);
                header('Content-Type: application/json');
                exit;
            }

            //Respuesta
            header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
            header('Content-Type: application/json');
            echo(json_encode($encuesta));
        }
        catch (MSGException $e) {
            //Si falla algo, Internal Server Error
            http_response_code(500);
            header('Content-Type: application/json');
            die(json_encode($e));
        }   
    }

    public function delEncuesta($id) {
		$currentLogged = parent::authenticateUser();
		if ($currentLogged == (new PerfilModel)->getPropietarioEncuesta($id)) {
            try {
                (new PerfilModel())->delEncuesta($id);

                header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
            } catch(MSGException $e) {
                http_response_code(404);
                header('Content-Type: application/json');
                die(json_encode($e));
            }
        } else {
            http_response_code(403);
            die("El usuario debe identificarse");
        } 
    }

    public function addFecha($id,$data) {
        $currentLogged = parent::authenticateUser();
        if ($currentLogged == (new PerfilModel)->getPropietarioEncuesta($id)) {
            try {
                (new EncuestaModel())->addFecha($id, $data->fecha);

                header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
            } catch(MSGException $e) {
                http_response_code(404);
                header('Content-Type: application/json');
                die($e->getMessage());
            }
        } else {
            http_response_code(401);
            die("El usuario debe identificarse");
        }
		
    }

    public function delFecha($id,$fecha) {
		$currentLogged = parent::authenticateUser();
        if ($currentLogged == (new PerfilModel)->getPropietarioEncuesta($id)) {
            try {
                (new EncuestaModel())->delFecha($id, $fecha);

                header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
            } catch(MSGException $e) {
                http_response_code(404);
                header('Content-Type: application/json');
                die($e->getMessage());
            }
        } else {
            http_response_code(401);
            die("El usuario debe identificarse");
        }
    }

    public function addHora($id,$fecha,$data) {
		$currentLogged = parent::authenticateUser();
        if ($currentLogged == (new PerfilModel)->getPropietarioEncuesta($id)) {
            try {
                (new EncuestaModel())->addHora($id, $fecha, $data->horaInicio, $data->horaFin);

                header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
            } catch(MSGException $e) {
                http_response_code(404);
                header('Content-Type: application/json');
                die($e->getMessage());
            }
        } else {
            http_response_code(401);
            die("El usuario debe identificarse");
        }
    }

    public function delHora($id,$fecha,$hora) {
		
    }
    
    public function addVoto($id,$fecha,$hora,$data) {
		
    }

    public function delVoto($id,$fecha,$hora,$email) {
		
    }

}

// URI-MAPPING for this Rest endpoint
$encuestaRest = new EncuestaRest();
URIDispatcher::getInstance()
->map("POST", "/encuesta", array($encuestaRest,"nuevaEncuesta"))
->map("GET",	"/encuesta/$1", array($encuestaRest,"getEncuesta"))
->map("DELETE",	"/encuesta/$1", array($encuestaRest,"delEncuesta"))
->map("POST",	"/encuesta/$1", array($encuestaRest,"addFecha"))
->map("DELETE",	"/encuesta/$1/$2", array($encuestaRest,"delFecha"))
->map("POST",	"/encuesta/$1/$2", array($encuestaRest,"addHora"))
->map("DELETE",	"/encuesta/$1/$2/$3", array($encuestaRest,"delHora"))
->map("POST",	"/encuesta/$1/$2/$3", array($encuestaRest,"addVoto"))
->map("DELETE",	"/encuesta/$1/$2/$3/$4", array($encuestaRest,"delVoto"));

