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

    /**
     * Method to find the institution
     */
    public static function findByProject($id){
        if (!self::connectDB()) return null;
        $results = [];
        $query = "SELECT phu.unidad_ejecutora_id_unidad_ejecutora,u.descripcion 'unidad_ejectura_descripcion', phu.unidad_ejecutora, phu.unidad_supervisora FROM proyecto_has_unidad_ejecutora phu INNER JOIN unidad_ejecutora u ON phu.unidad_ejecutora_id_unidad_ejecutora= u.id_unidad_ejecutora";
        $query.= " WHERE u.estatus != ?";

        $dinParams = [];
        $dinParams[] = self::getBindParam("i",Estatus::ESTATUS_REMOVED);

        if ($id) {
            $query .=" AND phu.proyecto_id_proyecto=?";
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
        //phu.unidad_ejecutora_id_unidad_ejecutora,u.descripcion 'unidad_ejectura_descripcion', phu.unidad_ejecutora, phu.unidad_supervisora
        $result->bind_result($bindResult['id'],$bindResult['descripcion'],$bindResult['has_unidad_ejecutora'],$bindResult['has_unidad_supervisora']);

        $results = [];
        while($result->fetch()){
            $unit= new UnidadEjecutoraProyecto($bindResult['id'],$bindResult['descripcion'],$bindResult['has_unidad_ejecutora'],$bindResult['has_unidad_supervisora']);
            $results[]=  $unit;
        }
        return $results;
    }

    public function toArray(){
        $result = parent::toArray();
        $result['executing_unit'] = $this->isUnidadEjecutora() ? true : false;
        $result['superviser_unit'] = $this->isUnidadSupervisora() ? true : false;
        return $result;
    }
}