<?php

include_once __DIR__."/../../Classes/MSGException.php";
include_once __DIR__."/../../Classes/Encuesta.php";

class PerfilModel {
    
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


    //Retorna el ID de la encuesta insertada
    //Tira MSGException si falla
    function nuevaEncuesta($nombre, $propietario) {
        try {
            $id = md5(uniqid($propietario, true));

            $stmt = $this->dbh->prepare("INSERT INTO ENCUESTA (ID, NOMBRE, PROPIETARIO) VALUES (:id, :nombre, :propietario)");
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":propietario", $propietario);

            if(!$stmt->execute()) {throw new PDOException();}

            return $id;
        }
        catch (PDOException $e) {
            throw new MSGException("EncuestaAddError","danger");    
        }
    }


    //Retorna EMAIL del propietario de la encuesta ID
    //Tira MSGException si falla
    function getPropietarioEncuesta($id) {
        try {
            $stmt = $this->dbh->prepare("SELECT PROPIETARIO FROM ENCUESTA WHERE ID = :id");
            $stmt->bindParam(":id", $id);

            if(!$stmt->execute()) {throw new PDOException();}

            return $stmt->fetch()["PROPIETARIO"];
        }
        catch (PDOException $e) {
            throw new MSGException("EncuestaGetError","danger");    
        }
    }


    //Tira MSGException si falla
    function delEncuesta($id) {
        try {
            $stmt = $this->dbh->prepare("DELETE FROM ENCUESTA WHERE ID = :id");
            $stmt->bindParam(":id", $id);

            if(!$stmt->execute()) {throw new PDOException();}
            
        }
        catch (PDOException $e) {
            throw new MSGException("EncuestaDeleteError","danger");    
        }
    }


    function getEncuestas($email) {
        try {
            $toReturn = array();

            //Encuestas propias
            $stmt = $this->dbh->prepare("SELECT * FROM ENCUESTA WHERE PROPIETARIO = :email");
            $stmt->bindParam(":email", $email);

            if(!$stmt->execute()) {throw new PDOException();}

            foreach($stmt->fetchAll() as $encuesta) {
                //Por cada Encuesta encontrada, añadir al array un nuevo objeto con los datos encontrados
                $toReturn["encuestas"][] = new Encuesta($encuesta["ID"],$encuesta["NOMBRE"],$encuesta["PROPIETARIO"]);
            }

            //Encuestas compartidas
            $stmt = $this->dbh->prepare("SELECT DISTINCT e.ID, e.NOMBRE, e.PROPIETARIO FROM ENCUESTA e, VOTA v WHERE PROPIETARIO <> :email AND CORREOUSUARIO = :email AND ID = IDENCUESTA");
            $stmt->bindParam(":email", $email);

            if(!$stmt->execute()) {throw new PDOException();}

            foreach($stmt->fetchAll() as $encuesta) {
                //Por cada Encuesta Compartida encontrada, añadir al array un nuevo objeto con los datos encontrados
                $toReturn["encuestasCompartidas"][] = new Encuesta($encuesta["ID"],$encuesta["NOMBRE"],$encuesta["PROPIETARIO"]);
            }

            return $toReturn;
        }
        catch (PDOException $e) {
            throw new MSGException("EncuestasGetError","danger");    
        }
    }

}
?>