<?php
class Encuesta implements JsonSerializable {

    //Privado porque no se deben cambiar nunca desde fuera
    private $ID;
    private $nombre;
    private $propietario;

    public $fechas; //Array de objetos Fecha

    function __construct($id, $n, $p, $fechas=null) {
        $this->ID = $id;
        $this->nombre = $n;
        $this->propietario = $p;
        $this->fechas = $fechas;
    }

    function getID() {
        return $this->ID;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getPropietario() {
        return $this->propietario;
    }

    function setFechas($fechas) {
        $this->fechas = $fechas;
    }

    function getFechas() {
        return $this->fechas;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->ID,
            'propietario' => $this->propietario,
            'fechas' => $this->fechas
        ];
    }

}
?>