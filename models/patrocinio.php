<?php
namespace Model;
require_once("models/model.php");

class Patrocinio extends Model{

    const QUERY_FIND = "SELECT id,nombre,estatus FROM patrocinio";


    private $id;
    private $name;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
    private function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    }


    public function __construct($id = null, $name = null)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setEstatus(new Estatus(1));
    }

    public static function findById($id){
        $sponsor = null;

        $sponsors = self::find();

        foreach($sponsors as $sp){
            if ($sp->getId() == $id){
                $sponsor = $sp;
                break;
            }
        }

        return $sponsor;
    }


    /**
     * Method to find all the sponsors available
    */
    public static function find(){
        if (!self::connectDB()) return null;
        $query = self::QUERY_FIND;

        $query.= " WHERE estatus !=3";
        $results = [];

        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return $results;
        if (!self::$dbManager->executeSql($result)) return $results;

        return self::mappingFromDBResult($result);
    }

    /**
     * Method to mapping from database result
    */
    private static function mappingFromDBResult(&$result){
        $bindResult = [];
        $results = [];
        $result->bind_result($bindResult['id'],$bindResult['name'],$bindResult['estatus']);
        while($result->fetch()){
            $pat = new Patrocinio();
            $pat->setId($bindResult['id']);
            $pat->setName($bindResult['name']);
            $pat->setEstatus(new Estatus($bindResult['estatus']));
            $results[] = $pat;
        }
        return $results;
    }

    /**
     * Method to convert the object to array
    */
    public function toArray(){
        $obj = [];
        $obj['id'] = $this->getId();
        $obj['nombre'] = $this->getName();
        return $obj;
    }

    /**
     * Method to add the sponsor
    */
    public function add(){
        if (!self::connectDB()) return null;
        $query = "INSERT INTO patrocinio (nombre,estatus) VALUES (?,?)";
        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("si",$this->getName(),$this->getEstatus()->getId());
        if (!self::$dbManager->executeSql($result)) return null;

        return $result->affected_rows > 0;
    }

    /**
     * Method to delete the sponsor
     */
    public function delete(){
        if (!$this->getId()) return false;
        if (!self::connectDB()) return false;
        $query = "UPDATE patrocinio SET estatus=3 WHERE ID=?";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return false;
        $result->bind_param("i",$this->getId());
        if (!self::$dbManager->executeSql($result)) return false;
        return true;
    }


    /**
     * Method to add the sponsor
     */
    public function update(){
        if (!$this->getId()) return false;
        if (!self::connectDB()) return false;
        $query = "UPDATE patrocinio SET NOMBRE=? WHERE ID=?";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return false;
        $result->bind_param("si",$this->getName(),$this->getId());
        if (!self::$dbManager->executeSql($result)) return false;
        return true;
    }
}