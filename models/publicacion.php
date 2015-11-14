<?php
namespace Model;

require_once("models/model.php");
require_once("models/estatus.php");
require_once("models/revistaPublicacion.php");


class Publicacion extends Model{
    const QUERY_FIND = "SELECT id_publicacion, descripcion, fecha, id_revista_publicacion, volumen, pagina, propiedad_intelectual, estatus FROM publicacion";

    private $id;
    private $descripcion;
    private $fecha;
    private $revista;
    private $volumen;
    private $pagina;
    private $propiedadIntelectual;
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

    public function __construct($id = null,$descripcion = null)
    {
        $this->id = $id;
        $this->descripcion = $descripcion;
    }


    public function add(){
        if (!self::connectDB()) return null;
        $this->estatus = new Estatus(Estatus::ESTATUS_ACTIVED);

        $query = "INSERT INTO publicacion(descripcion, fecha, id_revista_publicacion, volumen, pagina, propiedad_intelectual, estatus) VALUES (?,?,?,?,?,?,?)";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return null;

        $result->bind_param("ssissii",$this->descripcion,$this->fecha,$this->getRevista()->getId(),$this->estatus->getId());
        if (!self::$dbManager->executeSql($result)) return null;

        return $result->affected_rows > 0;
    }


    public function update(){
        if (!$this->id) return null;
        if (!self::connectDB()) return null;

        $query = "";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return null;
        $result->bind_param("si",$this->descripcion,$this->id);
        if (!self::$dbManager->executeSql($result)) return null;

        return true;
    }


    public function delete(){
        if (!$this->id) return null;
        if (!self::connectDB()) return null;

        $this->estatus = new Estatus(Estatus::ESTATUS_REMOVED);

        $query = "UPDATE publicacion SET status=? WHERE id_publicacion=?";
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
        if ($id) $query .=" AND id_publicacion=?";

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

        $result->bind_result($bindResult['id'],$bindResult['descripcion'],$bindResult['fecha'],$bindResult['revista'],$bindResult['volumen'],$bindResult['pagina'],$bindResult['propiedad_intelectual'],$bindResult['estatus']);

        $results = [];
        while($result->fetch()){
            $publicacion  = new Publicacion($bindResult['id'],$bindResult['descripcion']);
            $publicacion->setFecha($bindResult['fecha']);
            $publicacion->setRevista(new RevistaPublicacion($bindResult['revista']));
            $publicacion->setVolumen($bindResult['volumen']);
            $publicacion->setPagina($bindResult['pagina']);
            $publicacion->setPropiedadIntelectual($bindResult['propiedad_intelectual'] ? true : false);
            $publicacion->setEstatus(new Estatus($bindResult['estatus']));
            $results[] = $publicacion;
        }
        return $results;
    }


    /**
     * Method to mapping the object to array
     */
    public static function mappingToArray(&$publicacion){
        $result = [];

        $result['id']               = $publicacion->getId();
        $result['description']      = $publicacion->getDescription();
        $result['date']             = $publicacion->getFecha();
        $result['journal']          = [];
        $result['journal']['id']    = $publicacion->getRevista()->getId();
        $result['volume']           = $publicacion->getVolumen();
        $result['pages']            = $publicacion->getPagina();
        $result['intellectual_prop']    = $publicacion->hasPropiedadIntelectual();

        return $result;
    }
}
