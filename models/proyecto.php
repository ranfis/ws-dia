<?php
namespace  Model;

require_once("models/model.php");
require_once("models/estadoActual.php");
require_once("models/estatusAplicacion.php");
require_once("models/fondoProyecto.php");
require_once("models/institucionProyecto.php");
require_once("models/unidadEjecutoraProyecto.php");

use DatabaseManager;

class Proyecto extends Model{
    const QUERY_FIND = "";

    private $id;
    //String
    private $descripcion;
    //Date
    private $fechaAplicacion;
    //Date
    private $fechaInicio;

    //String
    private $asesor;

    //Estatus actual del proyecto
    private $estatusActual;

    //Estatus de la aplicacion del proyecto
    private $estatusAplicacion;

    //Double: Contra Partida de la institucion de donde sale la investigacion
    private $contraPartida;

    //String: Aporte de la institucion de donde sale la investigacion
    private $aporte;

    //Double: Monto total del proyecto
    private $montoTotal;

    //Double: Monto total del overhead
    private $overhead;

    //boolean: determina si el proyecto usa software o no
    private $software;

    //boolean: determina si el proyecto tiene patente o no
    private $patente;

    //String:
    private $otroProducto;

    //Participante: Investigado del proyecto
    private $investigador;

    private $estatus;

    //User: El usuario que creó el proyecto
    private $creador;

    //Date: Fecha en la que se creó el registro
    private $fechaCreacion;


    //[Institucion]
    private $instituciones;

    //[Fondo]
    private $fondos;

    //[UnidadEjecutora]
    private $unidadesEjecutora;

    //[Co Investigadores]
    private $coInvestigadores;

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
    public function getFechaAplicacion()
    {
        return $this->fechaAplicacion;
    }

    /**
     * @param mixed $fechaAplicacion
     */
    public function setFechaAplicacion($fechaAplicacion)
    {
        $this->fechaAplicacion = $fechaAplicacion;
    }

    /**
     * @return mixed
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * @param mixed $fechaInicio
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    }

    /**
     * @return mixed
     */
    public function getAsesor()
    {
        return $this->asesor;
    }

    /**
     * @param mixed $asesor
     */
    public function setAsesor($asesor)
    {
        $this->asesor = $asesor;
    }

    /**
     * @return mixed
     */
    public function getEstatusActual()
    {
        return $this->estatusActual;
    }

    /**
     * @param mixed $estatusActual
     */
    public function setEstatusActual($estatusActual)
    {
        $this->estatusActual = $estatusActual;
    }

    /**
     * @return mixed
     */
    public function getEstatusAplicacion()
    {
        return $this->estatusAplicacion;
    }

    /**
     * @param mixed $estatusAplicacion
     */
    public function setEstatusAplicacion($estatusAplicacion)
    {
        $this->estatusAplicacion = $estatusAplicacion;
    }

    /**
     * @return mixed
     */
    public function getContraPartida()
    {
        return $this->contraPartida;
    }

    /**
     * @param mixed $contraPartida
     */
    public function setContraPartida($contraPartida)
    {
        $this->contraPartida = $contraPartida;
    }

    /**
     * @return mixed
     */
    public function getAporte()
    {
        return $this->aporte;
    }

    /**
     * @param mixed $aporte
     */
    public function setAporte($aporte)
    {
        $this->aporte = $aporte;
    }

    /**
     * @return mixed
     */
    public function getMontoTotal()
    {
        return $this->montoTotal;
    }

    /**
     * @param mixed $montoTotal
     */
    public function setMontoTotal($montoTotal)
    {
        $this->montoTotal = $montoTotal;
    }

    /**
     * @return mixed
     */
    public function getOverhead()
    {
        return $this->overhead;
    }

    /**
     * @param mixed $overhead
     */
    public function setOverhead($overhead)
    {
        $this->overhead = $overhead;
    }

    /**
     * @return mixed
     */
    public function getSoftware()
    {
        return $this->software;
    }

    /**
     * @param mixed $software
     */
    public function setSoftware($software)
    {
        $this->software = $software;
    }

    /**
     * @return mixed
     */
    public function getPatente()
    {
        return $this->patente;
    }

    /**
     * @param mixed $patente
     */
    public function setPatente($patente)
    {
        $this->patente = $patente;
    }

    /**
     * @return mixed
     */
    public function getOtroProducto()
    {
        return $this->otroProducto;
    }

    /**
     * @param mixed $otroProducto
     */
    public function setOtroProducto($otroProducto)
    {
        $this->otroProducto = $otroProducto;
    }

    /**
     * @return mixed
     */
    public function getInvestigador()
    {
        return $this->investigador;
    }

    /**
     * @param mixed $investigador
     */
    public function setInvestigador($investigador)
    {
        $this->investigador = $investigador;
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
    public function getCreador()
    {
        return $this->creador;
    }

    /**
     * @param mixed $creador
     */
    public function setCreador($creador)
    {
        $this->creador = $creador;
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
    public function getInstituciones()
    {
        return $this->instituciones;
    }

    /**
     * @param mixed $instituciones
     */
    public function setInstituciones($instituciones)
    {
        $this->instituciones = $instituciones;
    }

    /**
     * @return [FondoProyecto]
     */
    public function getFondos()
    {
        return $this->fondos;
    }

    /**
     * @param mixed $fondos
     */
    public function setFondos($fondos)
    {
        $this->fondos = $fondos;
    }

    /**
     * @return mixed
     */
    public function getUnidadesEjecutora()
    {
        return $this->unidadesEjecutora;
    }

    /**
     * @param mixed $unidadesEjecutora
     */
    public function setUnidadesEjecutora($unidadesEjecutora)
    {
        $this->unidadesEjecutora = $unidadesEjecutora;
    }

    /**
     * @return mixed
     */
    public function getCoInvestigadores()
    {
        return $this->coInvestigadores;
    }

    /**
     * @param mixed $coInvestigadores
     */
    public function setCoInvestigadores($coInvestigadores)
    {
        $this->coInvestigadores = $coInvestigadores;
    }

    /**
     * Metodo para agregar el proyecto
     * @return boolean
    */
    public function add(){
        if (!self::connectDB()) return null;

        $this->estatus = new Estatus(Estatus::ESTATUS_ACTIVED);

        //dinamic parameters for query
        $dinParams = [];

        DatabaseManager::$link->autocommit(FALSE);

        $query = "INSERT INTO proyecto(descripcion, fecha_aplicacion, fecha_inicio, asesor, id_estado_actual, id_estado_aplicacion, contrapartida_unibe, aporte_unibe, monto_total, overhead_unibe, software, patente, otro_producto, investigador_id, estatus, creador, fecha_creacion) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())";
        $query = self::formatQuery($query);
        //SELECT id_proyecto, descripcion, fecha_aplicacion, fecha_inicio, asesor,
        // id_estado_actual, id_estado_aplicacion, contrapartida_unibe, aporte_unibe,
        // monto_total, overhead_unibe, software, patente, otro_producto,
        // investigador_id, estatus, creador, fecha_creacion FROM proyecto WHERE 1

        $dinParams[] = self::getBindParam("s",$this->descripcion);
        $dinParams[] = self::getBindParam("s",$this->fechaAplicacion);
        $dinParams[] = self::getBindParam("s",$this->fechaInicio);
        $dinParams[] = self::getBindParam("s",$this->asesor);
        $dinParams[] = self::getBindParam("i",$this->estatusActual->getId());
        $dinParams[] = self::getBindParam("i",$this->estatusAplicacion->getId());
        $dinParams[] = self::getBindParam("d",$this->contraPartida);
        $dinParams[] = self::getBindParam("d",$this->aporte);
        $dinParams[] = self::getBindParam("d",$this->montoTotal);
        $dinParams[] = self::getBindParam("d",$this->overhead);
        $dinParams[] = self::getBindParam("i",$this->software ? 1: 0);
        $dinParams[] = self::getBindParam("i",$this->patente ? 1: 0);
        $dinParams[] = self::getBindParam("s",$this->otroProducto ? $this->otroProducto : "");
        $dinParams[] = self::getBindParam("i",$this->investigador->getId());
        $dinParams[] = self::getBindParam("i",$this->estatus->getId());
        $dinParams[] = self::getBindParam("i",$this->creador->id);
        if (!$result = self::$dbManager->query($query)) return null;
        self::bindDinParam($result,$dinParams);
        if (!self::$dbManager->executeSql($result)) return null;

        $ret = false;
        $error = false;
        if ($result->affected_rows > 0){
            $ret = true;

            /*if (!$this->persistInstituciones())                 $error = true;

            if (!$error && !$this->persistsUnidadesEjecutora()) $error = true;

            if (!$error && !$this->persistsFondos())            $error = true;

            if (!$error && !$this->persistCoInvestigadores())   $error = true;

            $ret = !$error;*/
        }

        if ($ret) DatabaseManager::$link->commit();
        else DatabaseManager::$link->rollback();
        return $ret;
    }

    /**
     * Metodo para actualizar el proyecto
     * @return boolean
    */
    public function update(){ }

    /**
     * Metodo para persistir las instituciones
     * @return boolean
     */
    private function persistInstituciones(){
        if (!is_array($this->getInstituciones()) || count($this->getInstituciones()) == 0)
            return false;
        //add the participants
        foreach($this->getInstituciones() as $ins){
            $query = "INSERT INTO proyecto_has_institucion(proyectos_id_proyecto, instituciones_id_institucion, principal) VALUES (?,?,?)";
            $dinParams = [];
            $dinParams[] = self::getBindParam("i",$this->getId());
            $dinParams[] = self::getBindParam("i",$ins->getId());
            $dinParams[] = self::getBindParam("i",$ins->isPrincipal() ? 1 : 0);

            $query = self::formatQuery($query);
            if (!$result = self::$dbManager->query($query))
                return false;
            self::bindDinParam($result,$dinParams);
            if (!self::$dbManager->executeSql($result))
                return false;

        }
        return true;

    }

    /**
     * Metodo para persistir las unidades Ejecutoras
     * @return boolean
     */
    private function persistsUnidadesEjecutora(){
        if (!is_array($this->getUnidadesEjecutora()) || count($this->getUnidadesEjecutora()) == 0)
            return false;
        //add the participants
        foreach($this->getUnidadesEjecutora() as $unidad){
            $query = "INSERT INTO proyecto_has_unidad_ejecutora(proyecto_id_proyecto, unidad_ejecutora_id_unidad_ejecutora, unidad_ejecutora, unidad_supervisora) VALUES (?,?,?,?)";
            $dinParams = [];
            $dinParams[] = self::getBindParam("i",$this->getId());
            $dinParams[] = self::getBindParam("i",$unidad->getId());
            $dinParams[] = self::getBindParam("i",$unidad->isUnidadEjecutora() ? 1 : 0);
            $dinParams[] = self::getBindParam("i",$unidad->isUnidadSupervisora() ? 1 : 0);

            $query = self::formatQuery($query);
            if (!$result = self::$dbManager->query($query))
                return false;
            self::bindDinParam($result,$dinParams);
            if (!self::$dbManager->executeSql($result))
                return false;

        }
        return true;
    }

    /**
     * Metodo para persistir los fondos
     * @return boolean
     */
    private function persistsFondos(){
        if (!is_array($this->getFondos()) || count($this->getFondos()) == 0)
            return false;
        //add the participants
        foreach($this->getFondos() as $fondoProyecto){
            $query = "INSERT INTO proyecto_has_fondo(id_proyecto, id_fondo, monto) VALUES (?,?,?)";
            $dinParams = [];
            $dinParams[] = self::getBindParam("i",$this->getId());
            $dinParams[] = self::getBindParam("i",$fondoProyecto->getId());
            $dinParams[] = self::getBindParam("d",$fondoProyecto->getMonto());

            $query = self::formatQuery($query);
            if (!$result = self::$dbManager->query($query))
                return false;
            self::bindDinParam($result,$dinParams);
            if (!self::$dbManager->executeSql($result))
                return false;

        }
        return true;
    }

    /**
     * Metodo para persistir los co investigadores
     * @return boolean
     */
    private function persistCoInvestigadores(){
        if (!is_array($this->getCoInvestigadores()) || count($this->getCoInvestigadores()) == 0)
            return false;
        //add the participants
        foreach($this->getCoInvestigadores() as $co){
            $query = "INSERT INTO proyecto_coinvestigador(proyecto_id_proyecto, participante_id) VALUES (?,?)";
            $dinParams = [];
            $dinParams[] = self::getBindParam("i",$this->getId());
            $dinParams[] = self::getBindParam("i",$co->getId());

            $query = self::formatQuery($query);
            if (!$result = self::$dbManager->query($query))
                return false;
            self::bindDinParam($result,$dinParams);
            if (!self::$dbManager->executeSql($result))
                return false;

        }
        return true;
    }


}