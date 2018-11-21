<?php
class Hora implements JsonSerializable {
	private $horaInicio;
	private $horaFin;

	function __construct( $horaInicio, $horaFin)
	{
	$this->horaInicio = $horaInicio;
	$this->horaFin = $horaFin;
	}

	function getHoraInicio() {
		return $this->horaInicio;
	}

	function getHoraFin() {
		return $this->horaFin;
	}

	public function jsonSerialize() {
        return [
            'horaInicio' => $this->getHoraInicio(),
            'horaFin' => $this->getHoraFin()
        ];
    }
}
?>