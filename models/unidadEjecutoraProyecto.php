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

    public function __construct($id = null, $descripcion = null,$unidadEjecutora = null, $unidadSupervisora)
    {
        parent::__construct($id, $descripcion);
        $this->unidadEjecutora = $unidadEjecutora;
        $this->unidadSupervisora = $unidadSupervisora;
    }


}