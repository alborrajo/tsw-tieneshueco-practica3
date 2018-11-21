<?php
class Fecha implements JsonSerializable {

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

    public function jsonSerialize() {
        return [
            'fecha' => $this->getFecha(),
            'horas' => $this->getHoras()
        ];
    }
}
?>