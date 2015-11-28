<?php
namespace  Model;

require_once("models/fondo.php");

class FondoProyecto extends Fondo{
    //Double: monto asignado para proyecto
    private $monto;

    /**
     * @return null
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * @param null $monto
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;
    }

    public function __construct($id = null, $descripcion = null,$monto = null)
    {
        parent::__construct($id, $descripcion);
        $this->monto = $monto;
    }


}