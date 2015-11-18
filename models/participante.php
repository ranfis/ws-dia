<?php
namespace Model;

require_once("models/model.php");

class Participante extends Model{

    const QUERY_FIND = "SELECT ID, NOMBRE, APELLIDO,estatus FROM participante";

    private $id;
    private $nombre;
    private $apellido;
    private $status;

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

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    private function setStatus($status)
    {
        $this->status = $status;
    }

    public function __construct($id = null, $nombre= null,$apellido = null)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->setStatus(new Estatus(1));
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
    */
    public static function findByCongress($congressId){
        if (!self::connectDB()) return null;

        $query = "SELECT P.ID,P.NOMBRE,P.APELLIDO,P.estatus FROM congreso_autor CA INNER JOIN participante P ON CA.participante_ID=P.ID";
        $query.= " WHERE CA.congreso_id_congreso=? AND P.estatus != 3";

        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("i",$congressId);
        if (!self::$dbManager->executeSql($result)) return null;

        $results = self::mappingFromDBResult($result);
        return $results;
    }


    /**
     * Method to get participants by publicacition
     */
    public static function findByPublication($pubId){
        if (!self::connectDB()) return null;

        $query = "SELECT P.ID,P.NOMBRE,P.APELLIDO,P.estatus FROM publicacion_autor PA INNER JOIN participante P ON PA.participante_id=P.ID";
        $query.= " WHERE PA.publicacion_id_publicacion=? AND P.estatus != 3";

        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("i",$pubId);
        if (!self::$dbManager->executeSql($result)) return null;

        $results = self::mappingFromDBResult($result);
        return $results;
    }


    /**
     * Method to find all participantes, can be filter by id
    */
    public static function find($id = null,$pag = null){
        if (!self::connectDB()) return null;
        $query = self::QUERY_FIND;

        $query.= " WHERE estatus != 3";
        if ($id) $query.= " AND ID=?";

        $query = self::formatQuery($query);

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

        $query = "INSERT INTO participante(NOMBRE, APELLIDO,estatus) VALUES (?,?,?)";
        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("ssi",$this->nombre,$this->apellido,$this->getStatus()->getId());
        if (!self::$dbManager->executeSql($result)) return null;

        return $result->affected_rows > 0;
    }

    /**
     * Method to mapping
    */
    private function mappingFromDBResult(&$result){
        $bindResult = [];
        $results = [];
        $result->bind_result($bindResult['id'],$bindResult['nombre'],$bindResult['apellido'],$bindResult['estatus']);
        while($result->fetch()){
            $p = new Participante($bindResult['id'],$bindResult['nombre'],$bindResult['apellido']);
            $p->setStatus(new Estatus($bindResult['estatus']));
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
     * Method to delete the participante
    */
    public function delete(){
        if ($this->getId() == null) return false;
        if (!self::connectDB()) return false;

        $query = "UPDATE participante SET estatus=3 WHERE ID=?";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return false;
        $result->bind_param("i",$this->id);
        if (!self::$dbManager->executeSql($result)) return false;

        return true;
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