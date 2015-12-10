<?php
namespace Model;

require_once("models/institucionProyecto.php");

class InstitucionProyecto extends Institucion{
    //boolean
    private $principal;

    /**
     * @return boolean
     */
    public function isPrincipal()
    {
        return $this->principal;
    }

    /**
     * @param boolean $principal
     */
    public function setPrincipal($principal)
    {
        $this->principal = $principal;
    }


    public function __construct($id = null, $descripcion = null,$principal = false)
    {
        parent::__construct($id, $descripcion);
        $this->principal = $principal;
    }

    /**
     * Method to find the institution
     */
    public static function findById($id){
        $ins = parent::findById($id);
        if ($ins){
            $ins = new InstitucionProyecto($ins->getId(),$ins->getDescripcion());
        }
        return $ins;
    }


}