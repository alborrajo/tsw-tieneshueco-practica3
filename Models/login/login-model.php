<?php

include_once __DIR__."/../../Classes/MSGException.php";


class LoginModel {
    
	private $dbh;
	

//Constructor de la clase

function __construct(){

	try {
            $this->dbh = new PDO('mysql:host=localhost;dbname=TIENESHUECO', "tieneshueco", "tieneshueco");
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            //Si no se hace así, se mostrarían todos los datos de la conexión, INCLUYENDO USER Y PASS DE LA BD
            throw new MSGException("DBConnectionError","danger");
        }
}


function login($email, $password){
	try {
            $stmt = $this->dbh->prepare("SELECT count(CORREO) FROM USUARIO WHERE CORREO = :email AND PASS = :password ");
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);

            if(!$stmt->execute()) {throw new PDOException();}

           	if ($stmt->fetchColumn() == 1) {
           		 return $email;
           	}
           	else{
           		return false;
           	}


           
        }
        catch (PDOException $e) {
            throw new MSGException("UserGetError","danger");    
        }
}//fin metodo login

//
function Register($email){

	try{

		$stmt =$this->dbh->prepare("SELECT count(CORREO) FROM USUARIO WHERE correo = :email");
		$stmt->bindParam(":email", $email);

		if(!$stmt->execute()) {throw new PDOException();}

        if ($stmt->fetchColumn() == 1) { //Hay alguien con ese email registrado
           	 return false;
        }
        else{//No hay nadie registrado con ese email
           	return true;
        }
	}	
	catch (PDOException $e) {
            throw new MSGException("UserGetError","danger");    
        }
		

}

function registrar($email, $password, $nombre){
	try {
            
			$stmt = $this->dbh->prepare("INSERT INTO USUARIO (CORREO, PASS, NOMBRE) VALUES (:email, :password, :nombre)");
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":nombre", $nombre);

            if(!$stmt->execute()) {throw new PDOException();}
        }
        catch (PDOException $e) {
            throw new MSGException("UserRegisterError","danger");    
        }
}

}//fin de clase

?> 