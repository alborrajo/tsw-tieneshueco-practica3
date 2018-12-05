<?php

include_once __DIR__."/../../Classes/MSGException.php";

include_once __DIR__."/../../Classes/Encuesta.php";
include_once __DIR__."/../../Classes/Fecha.php";
include_once __DIR__."/../../Classes/Hora.php";
include_once __DIR__."/../../Classes/Voto.php";


class EncuestaModel {
    
    private $dbh;

    function __construct() {
        try {
            $this->dbh = new PDO('mysql:host=localhost;dbname=TIENESHUECO', "tieneshueco", "tieneshueco");
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            //Si no se hace así, se mostrarían todos los datos de la conexión, INCLUYENDO USER Y PASS DE LA BD
            throw new MSGException("DBConnectionError","danger");
        }
    }

    function addFecha($id, $fecha) {
        try {
            $stmt = $this->dbh->prepare("INSERT INTO FECHA (IDENCUESTA, FECHA) VALUES (:id, :fecha)");
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":fecha", $fecha);

            if(!$stmt->execute()) {throw new PDOException();}
        }
        catch (PDOException $e) {
            throw new MSGException("DateAddError","danger");    
        }
    }

    function addHora($id, $fecha, $horaInicio, $horaFin) {
        try {
            $stmt = $this->dbh->prepare("INSERT INTO HORA (IDENCUESTA, FECHA, HORAINICIO, HORAFIN) VALUES (:id, :fecha, :horainicio, :horafin)");
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->bindParam(":horainicio", $horaInicio);
            $stmt->bindParam(":horafin", $horaFin);

            if(!$stmt->execute()) {throw new PDOException();}
        }
        catch (PDOException $e) {
            throw new MSGException("HourAddError","danger");    
        }
    }

    function delFecha($id, $fecha) {
        try {
            $stmt = $this->dbh->prepare("DELETE FROM FECHA WHERE IDENCUESTA = :id AND FECHA = :fecha");
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":fecha", $fecha);

            if(!$stmt->execute()) {throw new PDOException();}
        }
        catch (PDOException $e) {
            throw new MSGException("DateDeleteError","danger");    
        }
    }

    function delHora($id, $fecha, $horaInicio, $horaFin) {
        try {
            $stmt = $this->dbh->prepare("DELETE FROM HORA WHERE IDENCUESTA = :id AND FECHA = :fecha AND HORAINICIO = :horainicio AND HORAFIN = :horafin");
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->bindParam(":horainicio", $horaInicio);
            $stmt->bindParam(":horafin", $horaFin);

            if(!$stmt->execute()) {throw new PDOException();}
        }
        catch (PDOException $e) {
            throw new MSGException("HourDeleteError","danger");    
        }
    }    

    function getEncuesta($id) {
        try {
            //Encuesta
            $stmt = $this->dbh->prepare("SELECT * FROM ENCUESTA WHERE ID = :id");
            $stmt->bindParam(":id", $id);

            if(!$stmt->execute()) {throw new PDOException();}

            if(!$encuesta = $stmt->fetch()) {return null;} //If nothing found, return null
            $toReturn = new Encuesta($encuesta["ID"],$encuesta["NOMBRE"],$encuesta["PROPIETARIO"]);

            //Fechas
            $stmt = $this->dbh->prepare("SELECT * FROM FECHA WHERE IDENCUESTA = :id");
            $stmt->bindParam(":id", $id);

            if(!$stmt->execute()) {throw new PDOException();}

            $fechas = array();
            foreach($stmt->fetchAll() as $fecha) {
                //Por cada Fecha encontrada, añadir al array un nuevo objeto con los datos encontrados
                $fechas[] = new Fecha($fecha["FECHA"]);
            }


            //Horas
            $stmt = $this->dbh->prepare("SELECT * FROM HORA WHERE IDENCUESTA = :id");
            $stmt->bindParam(":id", $id);

            if(!$stmt->execute()) {throw new PDOException();}

            foreach($stmt->fetchAll() as $hora) {
                //Por cada Hora encontrada, añadir al array un nuevo objeto con los datos encontrados
                foreach($fechas as $fecha) {
                    if($fecha->getFecha() == $hora["FECHA"]) {
                        $tmpHora = new Hora($hora["HORAINICIO"],$hora["HORAFIN"]);

                        $tmpHora->votos = $this->getVotosOnHora($id, $fecha->getFecha(), $tmpHora->getHoraInicio(), $tmpHora->getHoraFin());

                        $fecha->horas[] = $tmpHora;
                    }
                }
            }
            
            $toReturn->setFechas($fechas);

            return $toReturn;
        }
        catch (PDOException $e) {
            throw new MSGException("EncuestaGetError","danger");    
        }
    }

    function getVotosOnEncuesta($id) {
        try {

            $toReturn = array();

            $stmt = $this->dbh->prepare("SELECT * FROM VOTA WHERE IDENCUESTA = :id");
            $stmt->bindParam(":id", $id);

            if(!$stmt->execute()) {throw new PDOException();}

            foreach($stmt->fetchAll() as $voto) {
                $toReturn[] = new Voto($voto["CORREOUSUARIO"],$voto["IDENCUESTA"],$voto["FECHA"],$voto["HORAINICIO"],$voto["HORAFIN"]);
            }
            
            return $toReturn;
        }
        catch (PDOException $e) {
            throw new MSGException("Error getting votes","danger");    
        }
    }

    function getVotosOnHora($idEncuesta, $fecha, $horaInicio, $horaFin) {
        try {
            $toReturn = array();

            $stmt = $this->dbh->prepare("SELECT CORREOUSUARIO FROM VOTA WHERE IDENCUESTA = :id AND FECHA = :fecha AND HORAINICIO = :horaInicio AND HORAFIN = :horaFin");
            $stmt->bindParam(":id", $idEncuesta);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->bindParam(":horaInicio", $horaInicio);
            $stmt->bindParam(":horaFin", $horaFin);

            if(!$stmt->execute()) {throw new PDOException();}

            foreach($stmt->fetchAll() as $voto) {
                $toReturn[] = $voto["CORREOUSUARIO"];
            }
            
            return $toReturn;
        }
        catch (PDOException $e) {
            throw new MSGException("Error getting votes","danger");    
        }
    }

    function addVoto($idEncuesta, $idUsuario, $fecha, $horaInicio, $horaFin)
    {
        try {
            $stmt = $this->dbh->prepare("INSERT INTO VOTA (CORREOUSUARIO, IDENCUESTA, FECHA, HORAINICIO, HORAFIN) VALUES (:usuario, :id, :fecha, :horainicio, :horafin)");
            $stmt->bindParam(":usuario", $idUsuario);
            $stmt->bindParam(":id", $idEncuesta);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->bindParam(":horainicio", $horaInicio);
            $stmt->bindParam(":horafin", $horaFin);

            if(!$stmt->execute()) {throw new PDOException();}
        }
        catch (PDOException $e) {
            throw new MSGException("VotoAddError","danger");    
        }

    }

    function delVoto($idEncuesta, $idUsuario, $fecha, $horaInicio, $horaFin)
    {

        try {
            $stmt = $this->dbh->prepare("DELETE FROM VOTA WHERE CORREOUSUARIO = :usuario AND IDENCUESTA = :id 
                AND FECHA = :fecha AND HORAINICIO = :horainicio AND HORAFIN = :horafin");
            $stmt->bindParam(":usuario", $idUsuario);
            $stmt->bindParam(":id", $idEncuesta);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->bindParam(":horainicio", $horaInicio);
            $stmt->bindParam(":horafin", $horaFin);

            if(!$stmt->execute()) {throw new PDOException();}
        }
        catch (PDOException $e) {
            throw new MSGException("VotoDeleteError","danger");    
        }

    }
}

?>