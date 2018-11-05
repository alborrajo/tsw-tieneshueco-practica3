<?php
class MSGException extends Exception {

    /*
    Status: Cambia el color de la alerta (Bootstrap)
        primary     -   Azul
        secondary   -   Gris
        success     -   Verde
        danger      -   Rojo
        warning     -   Amarillo
        info        -   Azul claro
        light       -   Blanco
        dark        -   Gris otra vez
    */
    private $status;

    public function __construct($message, $status) {
        parent::__construct($message);
        $this->status = $status;        
    }

    public function getStatus() {
        return $this->status;
    }

    //Métodos para guardar un mensaje temporal en el SESSION que solo se pueda mostrar una única vez
    public static function setTemporalMessage($msgException) {
        $_SESSION["msg"] = $msgException;
    }

    public static function getTemporalMessage() {
        if(isset($_SESSION["msg"])) {
            $msg = $_SESSION["msg"];
            unset($_SESSION["msg"]);
        }
        else {
            $msg = null;
        }
        return $msg;
    }


}
?>