<?php
namespace Model;

require_once("models/model.php");
require_once("models/estatus.php");

class UnidadEjecutora extends Model{


    const QUERY_FIND = "SELECT id_unidad_ejecutora, descripcion, estatus FROM unidad_ejecutora";

    private $id;
    private $descripcion;
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

    public function __construct($id = null,$descripcion = null)
    {
        $this->id = $id;
        $this->descripcion = $descripcion;
    }


    /**
     * Method to create an article
    */
    public function add(){
        if (!self::connectDB()) return null;

        $this->estatus = new Estatus(Estatus::ESTATUS_ACTIVED);
        $query = "INSERT INTO unidad_ejecutora(descripcion, estatus) VALUES (?,?)";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return null;

        $result->bind_param("si",$this->descripcion,$this->estatus->getId());
        if (!self::$dbManager->executeSql($result)) return null;

        return $result->affected_rows > 0;
    }

    /**
     * Method to update the article
    */

    public function update(){
        if (!$this->id) return null;
        if (!self::connectDB()) return null;

        $query = "UPDATE unida_ejecutora SET descripcion=? WHERE id_unidad_ejecutora=?";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("si",$this->descripcion,$this->id);
        if (!self::$dbManager->executeSql($result)) return null;

        return true;
    }

    /**
     * Method to delete the article
     */

    public function delete(){
        if (!$this->id) return null;
        if (!self::connectDB()) return null;

        $this->estatus = new Estatus(Estatus::ESTATUS_REMOVED);

        $query = "UPDATE unidad_ejecutora SET status=? WHERE id_unidad_ejecutora=?";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return false;
        $result->bind_param("ii",$this->estatus->getId(),$this->id);
        if (!self::$dbManager->executeSql($result)) return null;

        return true;
    }


    /**
     * Method to find the revista publicacion
    */
    public static function find($id = null){
        if (!self::connectDB()) return null;
        $results = [];
        $query = self::QUERY_FIND;
        $query.= " WHERE ESTATUS != " . Estatus::ESTATUS_REMOVED;
        if ($id) $query .=" AND id_unidad_ejecutora=?";

        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return $results;
        if ($id) $result->bind_param("i",$id);
        if (!self::$dbManager->executeSql($result)) return $results;

        $results = self::mappingFromDBResult($result);
        return $results;
    }

    /**
     * Method to mapping from database result
     *
    */
    private static function mappingFromDBResult(&$result){
        $bindResult= [];
        $result->bind_result($bindResult['id'],$bindResult['descripcion'],$bindResult['estatus']);

        $results = [];
        while($result->fetch()){
            $unidad = new UnidadEjecutora($bindResult['id'],$bindResult['descripcion']);
            $unidad->estatus = new Estatus($bindResult['estatus']);
            $results[] = $unidad;
        }
        return $results;
    }


    /**
     * Method to mapping the object to array
    */
    public static function mappingToArray(&$unidad){
        $result = [];

        $result['id']           = $unidad->getId();
        $result['description']  = $unidad->getDescription();

        return $result;
    }
}