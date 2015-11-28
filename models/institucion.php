<?php
namespace Model;

require_once("models/model.php");
require_once("models/estatus.php");

class Institucion extends Model{

    const QUERY_FIND = "SELECT id_institucion, descripcion, estatus FROM institucion";

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
        $query = "INSERT INTO institucion(descripcion, estatus) VALUES (?,?)";
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

        $query = "UPDATE institucion SET descripcion=? WHERE id_institucion=?";
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

        $query = "UPDATE institucion SET estatus=? WHERE id_institucion=?";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return false;
        $result->bind_param("ii",$this->estatus->getId(),$this->id);
        if (!self::$dbManager->executeSql($result)) return null;

        return true;
    }

    /**
     * Method to find the institution
    */
    public static function findById($id){
        $ins = null;
        $results = self::find($id);
        if (is_array($results) && count($results) == 1)
            $ins = $results[0];

        return $ins;
    }

    /**
     * Method to find the institution
    */
    public static function find($id = null){
        if (!self::connectDB()) return null;
        $results = [];
        $query = self::QUERY_FIND;
        $query.= " WHERE estatus != " . Estatus::ESTATUS_REMOVED;
        if ($id) $query .=" AND id_institucion=?";

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
            $institucion = new Institucion($bindResult['id'],$bindResult['descripcion']);
            $institucion->estatus = new Estatus($bindResult['estatus']);
            $results[] = $institucion;
        }
        return $results;
    }


    /**
     * Method to mapping the object to array
    */
    public function toArray(){
        $result = [];

        $result['id']           = $this->getId();
        $result['name']  = $this->getDescripcion();

        return $result;
    }
}