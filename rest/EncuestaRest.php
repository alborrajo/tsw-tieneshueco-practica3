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
		if ($currentLogged == $_SERVER['PHP_AUTH_USER']) {
            try {
                (new PerfilModel())->nuevaEncuesta($data->nombre, $_SERVER['PHP_AUTH_USER']);

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
    
    public function delEncuesta($id) {
		
    }
    
    public function getEncuesta($id) {
		
    }

    public function addFecha($id,$data) {
        $currentLogged = parent::authenticateUser();
        if ($currentLogged == $_SERVER['PHP_AUTH_USER']) {
            try {
                (new EncuestaModel())->addFecha($id, $data->fecha);

                header($_SERVER['SERVER_PROTOCOL'].' 201 Data added');
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
        if ($currentLogged == $_SERVER['PHP_AUTH_USER']) {
            try {
                (new EncuestaModel())->delFecha($id, $fecha);

                header($_SERVER['SERVER_PROTOCOL'].' 201 Data deleted');
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
        if ($currentLogged == $_SERVER['PHP_AUTH_USER']) {
            try {
                (new EncuestaModel())->addHora($id, $fecha, $data->horaInicio, $data->horaFin);

                header($_SERVER['SERVER_PROTOCOL'].' 201 Hour added');
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

    public function delVoto($id,$fecha,$horaInicio,$horaFin,$email) {

        //URL para pruebas http://localhost/rest/encuesta/20d59b95948b67ce4cadaac4f7934b1a/2018-12-05/12:00:00/14:00:00/otropropie@tar.io

    $currentLogged = parent::authenticateUser();
    if($currentLogged == $_SERVER['PHP_AUTH_USER'])
    {
        try
        {
            (new EncuestaModel())->delVoto($id, $_SERVER['PHP_AUTH_USER'], $fecha,$horaInicio,$horaFin);

            header($_SERVER['SERVER_PROTOCOL'].'201 Deleted');
            header($_SERVER['REQUEST_URI']);
        }
        catch(MSGException $e)
        {
            http_response_code(404);
            header('Content-Type: application/json');
            die($e->getMessage());
        }
    }
    else
    {
        http_response_code(401);
        die("El usuario debe identificarse");
    }
		
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
->map("DELETE",	"/encuesta/$1/$2/$3/$4/$5", array($encuestaRest,"delVoto"));

