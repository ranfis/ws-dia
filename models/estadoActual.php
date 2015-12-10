<?php
namespace Model;
/**
 * Created by PhpStorm.
 * User: fernandomanuelperezramos
 * Date: 11/27/15
 * Time: 10:55 PM
 */
class EstadoActual extends Model{
    const ESTADO_ACTUAL_NO_FINALIZADO   = 1;
    const ESTADO_ACTUAL_EN_PROCESO      = 2;
    const ESTADO_ACTUAL_FINALIZADO      = 3;


    const QUERY_FIND = "SELECT id_estado_actual, descripcion,estatus FROM estado_actual";

    private $id;
    private $descripcion;

    //Estatus
    private $estatus;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param mixed $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return mixed
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * @param mixed $estatus
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    }

    public function __construct($id= null,$descripcion = null)
    {
        $this->id =$id;
        $this->descripcion = $descripcion;
    }

    /**
     * Method to find the id
    */
    public static function findById($id){
        $estado = null;

        $results = self::find($id);

        if (is_array($results) && count($results) == 1)
            $estado = $results[0];
        return $estado;
    }


    public static function find($id = null){
        if (!self::connectDB()) return null;
        $results = [];
        //dynamic parameters
        $bindParams = [];
        //SELECT id_estado_actual, descripcion, estatus FROM estado_actual
        $query = self::QUERY_FIND;
        $query.= " WHERE estatus != ?";

        $bindParams[] = self::getBindParam("i",Estatus::ESTATUS_REMOVED);

        if ($id) {
            $query .=" AND id_estado_actual=?";
            $bindParams[] = self::getBindParam("i",$id);
        }
        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return $results;
        self::bindDinParam($result,$bindParams);
        if (!self::$dbManager->executeSql($result)) return $results;

        $results = self::mappingFromDBResult($result);

        return $results;
    }

    /**
     * Metodo para mapear desde el DATABASE Result
     * @param $result
     * @return array
    */
    private static function mappingFromDBResult(&$result){
        $bindResult= [];
        $result->bind_result($bindResult['id'],$bindResult['descripcion'],$bindResult['estatus']);

        $results = [];
        while($result->fetch()){
            $estadoActual = new EstadoActual($bindResult['id'],$bindResult['descripcion']);
            $estadoActual->estatus = new Estatus($bindResult['estatus']);
            $results[] = $estadoActual;
        }
        return $results;
    }

    /**
     * Metodo para convertir objeto a arreglo
     * @return array
    */
    public function toArray(){
        $result = [];
        $result['id'] = $this->getId();
        $result['description'] = $this->getDescripcion();
        return $result;
    }

}