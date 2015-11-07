<?php
namespace Model;

require_once("models/model.php");
require_once("models/patrocinio.php");
require_once("models/participante.php");
require_once("models/estatus.php");

use DatabaseManager;

class Congreso extends  \Model\Model{

    const QUERY_FIND = "SELECT C.id_congreso, C.nombre, C.fecha_congreso, C.ponencia, C.lugar, C.patrocinio_id, C.fecha_creacion,P.nombre 'patrocinio',C.estatus FROM congreso C INNER JOIN patrocinio P ON C.patrocinio_id=P.id";

    private $id;
    private $nombre;
    private $fechaCongreso;
    private $ponencia;
    private $participantes;
    private $lugar;
    private $patrocinio;
    private $fechaCreacion;
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
     * @return mixed
     */
    public function getParticipantes()
    {
        return $this->participantes;
    }

    /**
     * @param mixed $participantes
     */
    public function setParticipantes($participantes)
    {
        $this->participantes = $participantes;
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

        $query.= " WHERE C.estatus != 3";
        if ($id) $query.= " AND C.id_congreso=?";

        $query = self::formatQuery($query);

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
        $result->bind_result($bindResult['id'],$bindResult['nombre'],$bindResult['fecha_congreso'],$bindResult['ponencia'],$bindResult['lugar'],$bindResult['patrocinio_id'],$bindResult['fecha_creacion'],$bindResult['patrocinio'],$bindResult['estatus']);
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
            $con->setEstatus(new Estatus($bindResult['estatus']));

            $participantes = Participante::findByCongress($con->getId());
            $con->setParticipantes($participantes);

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

        $obj['participante'] = [];
        foreach($this->getParticipantes() as $par){
            $objPar = [];
            $objPar['id'] = $par->getId();
            $objPar['nombre'] = $par->getNombre();
            $objPar['apellido'] = $par->getApellido();

            $obj['participante'][] = $objPar;
        }
        return $obj;
    }


    /**
     * Method to delete an object from database
    */
    public function delete(){
        if (!$this->getId()) return false;

        $query = "UPDATE congreso SET estatus=3 WHERE id_congreso=?";
        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return false;
        $result->bind_param("i",$this->getId());
        if (!self::$dbManager->executeSql($result)) return false;

        return $result->affected_rows > 0;
    }

    /**
     * Method  to update a congress
    */
    public function update(){
        if (!$this->getId()) return null;
        if (!self::connectDB()) return null;

        DatabaseManager::$link->autocommit(FALSE);

        $query = "UPDATE congreso SET nombre=?,fecha_congreso=?,ponencia=?,lugar=?,patrocinio_id=? WHERE id_congreso=?";
        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("ssssii",$this->getNombre(),$this->getFechaCongreso(),$this->getPonencia(),$this->getLugar(),$this->getPatrocinio()->getId(),$this->getId());
        if (!self::$dbManager->executeSql($result)) return null;

        $ret = null;

        //remove all authors
        $query = "DELETE FROM congreso_autor WHERE congreso_id_congreso=?";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) {
            DatabaseManager::$link->rollback();
            return null;
        }
        $result->bind_param("i",$this->getId());
        if (!self::$dbManager->executeSql($result)) {
            DatabaseManager::$link->rollback();
            return null;
        }
        //end: remove all authors

        if ($this->persistAuthors($result)){
            DatabaseManager::$link->commit();
            $ret = true;
        }
        else
            DatabaseManager::$link->rollback();
        return $ret;
    }

    /**
     * Method to add congress
    */
    public function add(){
        if (!self::connectDB()) return null;
        DatabaseManager::$link->autocommit(FALSE);

        $this->setEstatus(new Estatus(1));

        $query = "INSERT INTO congreso(nombre, fecha_congreso, ponencia, lugar, patrocinio_id, estatus, fecha_creacion) VALUES (?,?,?,?,?,?,NOW())";
        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("ssssii",$this->getNombre(),$this->getFechaCongreso(),$this->getPonencia(),$this->getLugar(),$this->patrocinio->getId(),$this->estatus->getId());
        if (!self::$dbManager->executeSql($result)) return null;

        $ret = false;
        if ($result->affected_rows > 0){
            $this->setId($result->insert_id);
            if ($this->persistAuthors($result)) {
                DatabaseManager::$link->commit();
                $ret = true;
            }
            else
                DatabaseManager::$link->rollback();
        }
        return $ret;
    }

    /**
     * Method to persists Authors
    */
    private function persistAuthors(&$result){
        if (!is_array($this->getParticipantes()) || count($this->getParticipantes()) == 0)
            return false;
        //add the participants
        foreach($this->getParticipantes() as $par){
            $query = "INSERT INTO congreso_autor(congreso_id_congreso,participante_ID,estatus) VALUES (?,?)";
            $query = self::formatQuery($query);
            if (!$result = self::$dbManager->query($query)) {
                DatabaseManager::$link->rollback();
                return false;
            }

            $result->bind_param("ii",$this->getId(),$par->getId());
            if (!self::$dbManager->executeSql($result)) {
                DatabaseManager::$link->rollback();
                return false;
            }
        }
        return true;
    }
}