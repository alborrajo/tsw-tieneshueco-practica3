<?php

class Voto implements JsonSerializable {

private $usuario;
private $idEncuesta;
private $fecha;
private $horaInicio;
private $horaFin;

	function __construct($usuario, $idEncuesta, $fecha, $horaInicio, $horaFin)
	{
		$this->usuario = $usuario;
		$this->idEncuesta = $idEncuesta;
		$this->fecha = $fecha;
		$this->horaInicio = $horaInicio;
		$this->horaFin = $horaFin;
	}

	function getUsuario()
	{
		return $this->usuario;
	}

	function getIdEncuesta()
	{
		return $this->idEncuesta;
	}

	function getFecha()
	{
		return $this->fecha;
	}
	function getHoraInicio()
	{
		return $this->horaInicio;
	}

	function getHoraFin()
	{
		return $this->horaFin;
	}

	public function jsonSerialize() {
        return [
			'usuario' => $this->usuario,
			'idEncuesta' => $this->idEncuesta,
			'fecha' => $this->fecha,
			'horaInicio' => $this->horaInicio,
			'horaFin' => $this->horaFin
        ];
    }
}
?>