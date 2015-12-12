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

    /**
     * Method to find the institution
     */
    public static function findByProject($id){
        if (!self::connectDB()) return null;
        $results = [];
        $query = "SELECT phi.proyectos_id_proyecto,i.descripcion 'institucion_nombre',i.estatus,phi.principal from proyecto_has_institucion phi INNER JOIN institucion i ON phi.instituciones_id_institucion = i.id_institucion";
        $query.= " WHERE i.estatus != ?";
        $dinParams = [];

        $dinParams[] = self::getBindParam("i",Estatus::ESTATUS_REMOVED);
        if ($id) {
            $query .=" AND phi.proyectos_id_proyecto=?";
            $dinParams[] = self::getBindParam("i",$id);
        }

        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return $results;
        self::bindDinParam($result,$dinParams);
        if (!self::$dbManager->executeSql($result)) return $results;

        $results = self::mappingFromDBResult($result);
        return $results;
    }

    /**
     * Method to mapping from database result
     *
     */
    protected static function mappingFromDBResult(&$result){
        $bindResult= [];
        $result->bind_result($bindResult['id'],$bindResult['descripcion'],$bindResult['estatus'],$bindResult['principal']);

        $results = [];
        while($result->fetch()){
            $institucion = new InstitucionProyecto($bindResult['id'],$bindResult['descripcion']);
            $institucion->setEstatus(new Estatus($bindResult['estatus']));
            $institucion->setPrincipal($bindResult['principal'] ? true : false);
            $results[] = $institucion;
        }
        return $results;
    }


    public function toArray(){
        $result = parent::toArray();
        $result['principal'] = $this->isPrincipal() ? true : false;
        return $result;
    }


}