<?php
namespace Model;

require_once("models/model.php");
require_once("models/estatus.php");
require_once("models/revistaPublicacion.php");
require_once("models/participante.php");

use DatabaseManager;

class Publicacion extends Model{
    const JSON_FIELD_ID = "id";
    const JSON_FIELD_DESCRIPTION = "description";
    const JSON_FIELD_DATE = "date";
    const JSON_FIELD_JOURNAL = "journal";
    const JSON_FIELD_VOLUME = "volume";
    const JSON_FIELD_PAGES  = "pages";
    const JSON_FIELD_HAS_INTELLECTUAL_PROP = "has_intellectual_prop";
    const JSON_FIELD_PARTICIPANTS = "participants";


    const QUERY_FIND = "SELECT p.id_publicacion, p.descripcion, p.fecha, p.id_revista_publicacion, rp.descripcion 'revista_publicacion', p.volumen, p.pagina, p.propiedad_intelectual, p.estatus FROM publicacion p inner join revista_publicacion rp on p.id_revista_publicacion = rp.id_revista_publicacion";

    private $id;
    private $descripcion;
    private $fecha;
    private $revista;
    private $volumen;
    private $pagina;
    private $propiedadIntelectual;
    private $participantes;
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
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param mixed $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * @return mixed
     */
    public function getRevista()
    {
        return $this->revista;
    }

    /**
     * @param mixed $revista
     */
    public function setRevista($revista)
    {
        $this->revista = $revista;
    }

    /**
     * @return mixed
     */
    public function getVolumen()
    {
        return $this->volumen;
    }

    /**
     * @param mixed $volumen
     */
    public function setVolumen($volumen)
    {
        $this->volumen = $volumen;
    }

    /**
     * @return mixed
     */
    public function getPagina()
    {
        return $this->pagina;
    }

    /**
     * @param mixed $pagina
     */
    public function setPagina($pagina)
    {
        $this->pagina = $pagina;
    }

    /**
     * @return mixed
     */
    public function hasPropiedadIntelectual()
    {
        return $this->propiedadIntelectual;
    }

    /**
     * @param mixed $propiedadIntelectual
     */
    public function setPropiedadIntelectual($propiedadIntelectual)
    {
        $this->propiedadIntelectual = $propiedadIntelectual;
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

    public function __construct($id = null,$descripcion = null)
    {
        $this->id = $id;
        $this->descripcion = $descripcion;
    }


    /**
     * method to add the publication
    */
    public function add(){
        if (!self::connectDB()) return null;
        $this->estatus = new Estatus(Estatus::ESTATUS_ACTIVED);

        $query = "INSERT INTO publicacion(descripcion, fecha, id_revista_publicacion, volumen, pagina, propiedad_intelectual, estatus) VALUES (?,?,?,?,?,?,?)";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return null;

        $result->bind_param("ssissii",$this->descripcion,$this->fecha,$this->getRevista()->getId(),$this->getVolumen(),$this->getPagina(),$this->hasPropiedadIntelectual(),$this->estatus->getId());
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
            $query = "INSERT INTO publicacion_autor(publicacion_id_publicacion,participante_id) VALUES (?,?)";
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


    /**
     * Method to update the publication
    */
    public function update(){
        if (!$this->id) return null;
        if (!self::connectDB()) return null;
        $query = "UPDATE publicacion SET descripcion=?,fecha=?,id_revista_publicacion=?,volumen=?,pagina=?,propiedad_intelectual=?,estatus=? WHERE id_publicacion=?";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("ssisssii",$this->getDescripcion(),$this->getFecha(),$this->getRevista()->getId(),$this->getVolumen(),$this->getPagina(),$this->hasPropiedadIntelectual(),$this->getEstatus()->getId(),$this->getId());
        if (!self::$dbManager->executeSql($result)) return null;
        return true;
    }


    /**
     * Method to delete the publication
    */
    public function delete(){
        if (!$this->id) return null;
        if (!self::connectDB()) return null;

        $this->estatus = new Estatus(Estatus::ESTATUS_REMOVED);

        $query = "UPDATE publicacion SET estatus=? WHERE id_publicacion=?";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return false;
        $result->bind_param("ii",$this->estatus->getId(),$this->id);
        if (!self::$dbManager->executeSql($result)) return null;

        return true;
    }

    /**
     * Method to find the publication by id
    */
    public static function findById($id){
        $obj = null;
        $results = self::find($id);
        if (is_array($results) && count($results) == 1)
            $obj = $results[0];

        return $obj;

    }

    /**
     * Method to find the revista publicacion
     */
    public static function find($id = null){
        if (!self::connectDB()) return null;
        $results = [];
        $query = self::QUERY_FIND;
        $query.= " WHERE p.estatus != " . Estatus::ESTATUS_REMOVED;
        if ($id) $query .=" AND p.id_publicacion=?";

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

        $result->bind_result($bindResult['id'],$bindResult['descripcion'],$bindResult['fecha'],$bindResult['id_revista'],$bindResult['revista'],$bindResult['volumen'],$bindResult['pagina'],$bindResult['propiedad_intelectual'],$bindResult['estatus']);

        $results = [];
        while($result->fetch()){
            $publicacion  = new Publicacion($bindResult['id'],$bindResult['descripcion']);
            $publicacion->setFecha($bindResult['fecha']);
            $publicacion->setRevista(new RevistaPublicacion($bindResult['id_revista'],$bindResult['revista']));
            $publicacion->setVolumen($bindResult['volumen']);
            $publicacion->setPagina($bindResult['pagina']);
            $publicacion->setPropiedadIntelectual($bindResult['propiedad_intelectual'] ? true : false);
            $publicacion->setEstatus(new Estatus($bindResult['estatus']));
            $publicacion->setParticipantes(Participante::findByPublication($bindResult['id']));

            $results[] = $publicacion;
        }
        return $results;
    }

    public function toArray(){
        return self::mappingToArray($this);
    }


    /**
     * Method to mapping the object to array
     */
    public static function mappingToArray(&$publicacion){
        $result = [];

        $result[self::JSON_FIELD_ID]               = $publicacion->getId();
        $result[self::JSON_FIELD_DESCRIPTION]      = $publicacion->getDescripcion();
        $result[self::JSON_FIELD_DATE]             = $publicacion->getFecha();
        $result[self::JSON_FIELD_JOURNAL]          = [];
        $result[self::JSON_FIELD_JOURNAL]['id']    = $publicacion->getRevista()->getId();
        $result[self::JSON_FIELD_JOURNAL]['name']    = $publicacion->getRevista()->getDescripcion();
        $result[self::JSON_FIELD_VOLUME]           = $publicacion->getVolumen();
        $result[self::JSON_FIELD_PAGES]            = $publicacion->getPagina();
        $result[self::JSON_FIELD_HAS_INTELLECTUAL_PROP]    = $publicacion->hasPropiedadIntelectual();

        $result["participantes"] = [];
        foreach($publicacion->getParticipantes() as $par){
            $objPar = [];
            $objPar['id'] = $par->getId();
            $objPar['nombre'] = $par->getNombre();
            $objPar['apellido'] = $par->getApellido();
            $result["participantes"][] = $objPar;
        }
        return $result;
    }
}
