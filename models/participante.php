<?php
namespace Model;

require_once("models/model.php");

class Participante extends Model{

    const QUERY_FIND = "SELECT ID, NOMBRE, APELLIDO FROM participante";

    private $id;
    private $nombre;
    private $apellido;

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
     * @return null
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * @param null $apellido
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }


    public function __construct($id = null, $nombre= null,$apellido = null)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
    }

    /**
     * Method to find the object by id
    */
    public static function findById($id){
        if (!self::connectDB()) return null;
        $results = self::find($id);
        $p = null;
        if (is_array($results)  && count($results) == 1)
            $p = $results[0];
        return $p;
    }

    /**
     * Method to find all participantes, can be filter by id
    */
    public static function find($id = null,$pag = null){
        if (!self::connectDB()) return null;
        $query = self::QUERY_FIND;
        $query = self::formatQuery($query);
        if ($id) $query.= " WHERE ID=?";

        if (!$result = self::$dbManager->query($query)) return null;

        if ($id)
            $result->bind_param("i",$id);
        if (!self::$dbManager->executeSql($result)) return null;

        $results = self::mappingFromDBResult($result);

        return $results;
    }

    /**
     * Method to add participante
    */
    public function add(){
        if (!self::connectDB()) return null;
        $query = "INSERT INTO participante(NOMBRE, APELLIDO) VALUES (?,?)";
        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("ss",$this->nombre,$this->apellido);

        if (!self::$dbManager->executeSql($result)) return null;

        return $result->affected_rows > 0;
    }

    /**
     * Method to mapping
    */
    private function mappingFromDBResult(&$result){
        $bindResult = [];
        $results = [];
        $result->bind_result($bindResult['id'],$bindResult['nombre'],$bindResult['apellido']);
        while($result->fetch()){
            $p = new Participante($bindResult['id'],$bindResult['nombre'],$bindResult['apellido']);
            $results[] = $p;
        }
        return $results;
    }

    /**
     * Method to mapping the list with participante objects
     *
    */
    public static function mappingToArray(&$participantes){
        $results = [];
        $participantes = is_array($participantes) ? $participantes : [];
        foreach($participantes as $loopPar){
            $p = [];
            $p['id'] = $loopPar->id;
            $p['nombre'] = $loopPar->nombre;
            $p['apellido'] = $loopPar->apellido;
            $results[] = $p;
        }
        return $results;
    }


    /**
     * Method to update participante
    */
    public function update(){
        if ($this->getId() == null) return null;
        if (!self::connectDB()) return null;

        $query = "UPDATE participante SET NOMBRE=?,APELLIDO=? WHERE ID=?";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("ssi",$this->nombre,$this->apellido,$this->id);
        if (!self::$dbManager->executeSql($result)) return null;

        return true;
    }

}