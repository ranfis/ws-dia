<?php
namespace Model;

require_once("models/model.php");

class Moneda extends \Model\Model{

    const QUERY_FIND = "SELECT id, simbolo, descripcion, estatus FROM moneda";

    private $id;
    private $simbolo;
    private $nombre;
    private $estatus;

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getSimbolo()
    {
        return $this->simbolo;
    }

    /**
     * @param null $simbolo
     */
    public function setSimbolo($simbolo)
    {
        $this->simbolo = $simbolo;
    }

    /**
     * @return null
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param null $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
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



    public function __construct($id = null,$symbol = null,$name = null)
    {
        $this->id       = $id;
        $this->simbolo   = $symbol;
        $this->nombre     = $name;
    }

    public function toArray(){
        $result = [];
        $result['id']       = $this->id;
        $result['symbol']   = $this->simbolo;
        $result['name']     = $this->nombre;

        return $result;
    }

    /**
     * Method to find the currency by id
    */
    public static function findById($id){
        $currency = null;
        $results = [];

        $results = self::find($id);
        if (is_array($results) && count($results) == 1)
            $currency = $results[0];
        return $currency;
    }

    /**
     * Method to get the currencies
     * @return array
    */
    public static function find($id = null){
        if (!self::connectDB()) return null;
        $results = [];
        $query = self::QUERY_FIND;
        $dinParams = [];
        $query.= " WHERE estatus != " . Estatus::ESTATUS_REMOVED;

        if ($id) {
            $query.= " AND id=?";
            $dinParams[] = self::getBindParam("i",$id);
        }

        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return $results;
        if ($dinParams){
            self::bindDinParam($result,$dinParams);
        }

        if (!self::$dbManager->executeSql($result)) return $results;

        $results = self::mappingFromDBResult($result);
        return $results;
    }


    public static function mappingFromDBResult(&$result){
        $bindResult= [];
        $result->bind_result($bindResult['id'],$bindResult['simbolo'],$bindResult['nombre'],$bindResult['estatus']);
        $results = [];
        while($result->fetch()){
            $moneda = new Moneda($bindResult['id'],$bindResult['simbolo'],$bindResult['nombre']);
            $moneda->setEstatus($bindResult['estatus']);
            $results[] = $moneda;
        }
        return $results;
    }
}