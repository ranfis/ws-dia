<?php
namespace Model;

require_once("models/unidadEjecutora.php");

class UnidadEjecutoraProyecto extends UnidadEjecutora{

    //boolean:
    private $unidadEjecutora;
    //boolean:
    private $unidadSupervisora;

    /**
     * @return null
     */
    public function isUnidadEjecutora()
    {
        return $this->unidadEjecutora;
    }

    /**
     * @param null $unidadEjecutora
     */
    public function setUnidadEjecutora($unidadEjecutora)
    {
        $this->unidadEjecutora = $unidadEjecutora;
    }

    /**
     * @return mixed
     */
    public function isUnidadSupervisora()
    {
        return $this->unidadSupervisora;
    }

    /**
     * @param mixed $unidadSupervisora
     */
    public function setUnidadSupervisora($unidadSupervisora)
    {
        $this->unidadSupervisora = $unidadSupervisora;
    }

    public function __construct($id = null, $descripcion = null,$unidadEjecutora = false, $unidadSupervisora = false)
    {
        parent::__construct($id, $descripcion);
        $this->unidadEjecutora = $unidadEjecutora;
        $this->unidadSupervisora = $unidadSupervisora;
    }

    /**
     * Method to find the unit by id
     * @param id integer: id
     * @return UnidadEjecutoraProyecto
     */
    public static function findById($id){
        $unit = parent::findById($id);
        if ($unit) $unit = new UnidadEjecutoraProyecto($unit->getId(),$unit->getDescripcion());
        return $unit;
    }


}