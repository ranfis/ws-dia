<?php
namespace Model;

require_once("models/model.php");
require_once("models/patrocinio.php");

class Congreso extends  \Model\Model{

    const QUERY_FIND = "SELECT C.id_congreso, C.nombre, C.fecha_congreso, C.ponencia, C.lugar, C.patrocinio_id, C.fecha_creacion,P.nombre 'patrocinio' FROM congreso C INNER JOIN patrocinio P ON C.patrocinio_id=P.id";

    private $id;
    private $nombre;
    private $fechaCongreso;
    private $ponencia;
    private $lugar;
    private $patrocinio;
    private $fechaCreacion;

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
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param mixed $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return mixed
     */
    public function getFechaCongreso()
    {
        return $this->fechaCongreso;
    }

    /**
     * @param mixed $fechaCongreso
     */
    public function setFechaCongreso($fechaCongreso)
    {
        $this->fechaCongreso = $fechaCongreso;
    }

    /**
     * @return mixed
     */
    public function getPonencia()
    {
        return $this->ponencia;
    }

    /**
     * @param mixed $ponencia
     */
    public function setPonencia($ponencia)
    {
        $this->ponencia = $ponencia;
    }

    /**
     * @return mixed
     */
    public function getLugar()
    {
        return $this->lugar;
    }

    /**
     * @param mixed $lugar
     */
    public function setLugar($lugar)
    {
        $this->lugar = $lugar;
    }

    /**
     * @return mixed
     */
    public function getPatrocinio()
    {
        return $this->patrocinio;
    }

    /**
     * @param mixed $patrocinio
     */
    public function setPatrocinio($patrocinio)
    {
        $this->patrocinio = $patrocinio;
    }

    /**
     * @return mixed
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * @param mixed $fechaCreacion
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;
    }

    /**
     * Method to find the congress by id
    */
    public static function findById($id){
        $con = null;
        $results = self::find($id);
        if (is_array($results) && count($results) == 1)
            $con = $results[0];
        return $con;
    }


    /**
     * Method to find all the congress
    */
    public static function find($id = null){
        if (!self::connectDB()) return null;
        $query = self::QUERY_FIND;
        $results = [];

        $query = self::formatQuery($query);

        if ($id) $query.= " WHERE ID=?";

        if (!$result = self::$dbManager->query($query)) return $results;

        if ($id) $result->bind_param("i",$id);

        if (!self::$dbManager->executeSql($result)) return $results;

        return self::mappingFromDBResult($result);
    }

    /**
     * Method to mapping from database result
    */
    private static function mappingFromDBResult(&$result){
        $bindResult = [];
        $result->bind_result($bindResult['id'],$bindResult['nombre'],$bindResult['fecha_congreso'],$bindResult['ponencia'],$bindResult['lugar'],$bindResult['patrocinio_id'],$bindResult['fecha_creacion'],$bindResult['patrocinio']);
        $results= [];
        while($result->fetch()){
            $con = new Congreso();
            $con->setId($bindResult['id']);
            $con->setNombre($bindResult['nombre']);
            $con->setFechaCongreso($bindResult['fecha_congreso']);
            $con->setPonencia($bindResult['ponencia']);
            $con->setLugar($bindResult['lugar']);
            $con->setPatrocinio(new Patrocinio($bindResult['patrocinio_id'],$bindResult['patrocinio']));
            $con->setFechaCreacion($bindResult['fecha_creacion']);
            $results[] = $con;
        }
        return $results;
    }

    /**
     * Method to convert the object to array
    */
    public function toArray(){
        $obj = [];

        $obj['id'] = $this->getId();
        $obj['nombre'] = $this->getNombre();
        $obj['fecha_congreso'] = $this->getFechaCongreso();
        $obj['ponencia'] = $this->getPonencia();
        $obj['lugar'] = $this->getLugar();
        if ($this->getPatrocinio()){
            $obj['patrocinio'] = [];
            $obj['patrocinio']["id"] = $this->getPatrocinio()->getId();
            $obj['patrocinio']["nombre"] = $this->getPatrocinio()->getName();
        }
        $obj['fecha_creacion'] = $this->getFechaCreacion();

        return $obj;
    }


    /**
     * Method  to update a congress
    */
    public function update(){
        if (!$this->getId()) return null;
        if (!self::connectDB()) return null;
        $query = "UPDATE congreso SET nombre=?,fecha_congreso=?,ponencia=?,lugar=?,patrocinio_id=? WHERE id_congreso=?";
        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("ssssii",$this->getNombre(),$this->getFechaCongreso(),$this->getPonencia(),$this->getLugar(),$this->getPatrocinio()->getId(),$this->getId());
        if (!self::$dbManager->executeSql($result)) return null;

        return $result->affected_rows > 0;
    }

    /**
     * Method to add congress
    */
    public function add(){
        if (!self::connectDB()) return null;
        $query = "INSERT INTO congreso(nombre, fecha_congreso, ponencia, lugar, patrocinio_id, fecha_creacion) VALUES (?,?,?,?,?,NOW())";
        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("ssssi",$this->getNombre(),$this->getFechaCongreso(),$this->getPonencia(),$this->getLugar(),$this->patrocinio->getId());
        if (!self::$dbManager->executeSql($result)) return null;

        return $result->affected_rows > 0;
    }
}