<?php
namespace Model;
require_once("models/model.php");

class Patrocinio extends Model{

    const QUERY_FIND = "SELECT id,nombre FROM patrocinio";


    private $id;
    private $name;

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

    public function __construct($id = null, $name = null)
    {
        $this->setId($id);
        $this->setName($name);
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
        $result->bind_result($bindResult['id'],$bindResult['name']);
        while($result->fetch()){
            $pat = new Patrocinio();
            $pat->setId($bindResult['id']);
            $pat->setName($bindResult['name']);
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
        $obj['name'] = $this->getName();
        return $obj;
    }

    /**
     * Method to add the sponsor
    */
    public function add(){
        if (!self::connectDB()) return null;
        $query = "INSERT INTO patrocinio (nombre) VALUES (?)";
        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("s",$this->getName());
        if (!self::$dbManager->executeSql($result)) return null;

        return $result->affected_rows > 0;
    }


    /**
     * Method to add the sponsor
     */
    public function update(){
        if (!$this->getId()) return null;
        if (!self::connectDB()) return null;
        $query = "UPDATE patrocinio SET NOMBRE=? WHERE ID=?";
        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("si",$this->getName(),$this->getId());
        if (!self::$dbManager->executeSql($result)) return null;

        return $result->affected_rows > 0;
    }
}