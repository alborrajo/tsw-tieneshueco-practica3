<?php
class Fecha {

    //Privado porque no se deben cambiar nunca desde fuera
    private $fecha;

    //Array con las horas para esa fecha
    public $horas;

    function __construct( $fecha, $horas=null) {
        $this->fecha=$fecha;
        $this->horas = $horas;
    }

    function getHoras() {
        return $this->horas;
    }

    function getFecha()
    {
        return $this->fecha;
    }
}
?>