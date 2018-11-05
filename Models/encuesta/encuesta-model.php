<?php

include_once __DIR__."/../../Classes/MSGException.php";


class EncuestaModel {
    
    private $dbh;
    private $strings;

    function __construct() {
        include "Locale/en.php";
        if(isset($_SESSION["locale"])) {
            include "Locale/".$_SESSION["locale"].".php";
        }

        $this->strings = $strings;

        try {
            $this->dbh = new PDO('mysql:host=localhost;dbname=TIENESHUECO', "tieneshueco", "tieneshueco");
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            //Si no se hace así, se mostrarían todos los datos de la conexión, INCLUYENDO USER Y PASS DE LA BD
            throw new MSGException($this->strings["DBConnectionError"],"danger");
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
            throw new MSGException($this->strings["DateAddError"],"danger");    
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
            throw new MSGException($this->strings["HourAddError"],"danger");    
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
            throw new MSGException($this->strings["DateDeleteError"],"danger");    
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
            throw new MSGException($this->strings["HourDeleteError"],"danger");    
        }
    }    

    function getEncuesta($id) {
        try {
            //Encuesta
            $stmt = $this->dbh->prepare("SELECT * FROM ENCUESTA WHERE ID = :id");
            $stmt->bindParam(":id", $id);

            if(!$stmt->execute()) {throw new PDOException();}

            $encuesta = $stmt->fetch();
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
                        $fecha->horas[] = new Hora($hora["HORAINICIO"],$hora["HORAFIN"]);
                    }
                }
            }
            
            $toReturn->setFechas($fechas);

            return $toReturn;
        }
        catch (PDOException $e) {
            throw new MSGException($this->strings["EncuestaGetError"],"danger");    
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
            throw new MSGException($this->strings["VotosGetError"],"danger");    
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
            throw new MSGException($this->strings["VotoAddError"],"danger");    
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
            throw new MSGException($this->strings["VotoDeleteError"],"danger");    
        }

    }
}

?>