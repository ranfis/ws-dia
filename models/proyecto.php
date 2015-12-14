<?php
namespace  Model;

require_once("models/model.php");
require_once("models/estadoActual.php");
require_once("models/estatusAplicacion.php");
require_once("models/fondo.php");
require_once("models/institucionProyecto.php");
require_once("models/unidadEjecutoraProyecto.php");

use DatabaseManager;

class Proyecto extends Model{
    const QUERY_FIND = "SELECT p.id_proyecto, p.descripcion, p.fecha_aplicacion, p.fecha_inicio, p.asesor,a.NOMBRE 'asesor_nombre',a.APELLIDO 'asesor_apellido', p.id_estado_actual,e.descripcion 'estado_actual', p.id_estado_aplicacion,ea.descripcion 'estado_aplicacion', p.contrapartida_unibe, p.aporte_unibe, p.moneda,m.simbolo \"moneda_simbolo\", p.monto_total, p.overhead_unibe, p.software, p.patente, p.otro_producto, p.investigador_id,i.NOMBRE 'investigador_nombre',i.APELLIDO 'investigador_apellido', p.estatus, p.creador, p.fecha_creacion FROM proyecto p inner join estado_aplicacion ea ON p.id_estado_aplicacion = ea.id_estado_aplicacion inner join estado_actual e ON p.id_estado_actual = e.id_estado_actual INNER JOIN moneda m on p.moneda = m.id inner join participante i on p.investigador_id=i.ID inner join participante a on p.asesor = a.ID";

    private $id;
    //String
    private $descripcion;
    //Date
    private $fechaAplicacion;
    //Date
    private $fechaInicio;

    //Participante
    private $asesor;

    //Estatus actual del proyecto
    private $estatusActual;

    //Estatus de la aplicacion del proyecto
    private $estatusAplicacion;

    //Double: Contra Partida de la institucion de donde sale la investigacion
    private $contraPartida;

    //String: Aporte de la institucion de donde sale la investigacion
    private $aporte;

    //Moneda: moneda
    private $moneda;

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
     * @return Participante
     */
    public function getAsesor()
    {
        return $this->asesor;
    }

    /**
     * @param Participante $asesor
     */
    public function setAsesor($asesor)
    {
        $this->asesor = $asesor;
    }

    /**
     * @return EstadoActual
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
     * @return EstatusAplicacion
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
     * @return Participante
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
     * @return array(Fondo)
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
     * @return array(UnidadEjecutoraProyecto)
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
     * @return array(Participante)
     */
    public function getCoInvestigadores()
    {
        return $this->coInvestigadores;
    }

    /**
     * @param array(Participante) $coInvestigadores
     */
    public function setCoInvestigadores($coInvestigadores)
    {
        $this->coInvestigadores = $coInvestigadores;
    }

    /**
     * @return Moneda
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * @param Moneda $moneda
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;
    }

    /**
     * @return Proyecto
    */
    public static function findById($id){
        $pro = null;
        $results = self::find($id);

        if ($results && is_array($results) && count($results) == 1)
            $pro = $results[0];
        return $pro;
    }

    /**
     * Method to find the revista publicacion
     * @return array(Proyecto)
     */
    public static function find($id = null,$estatusActual = null,$estadoAplicacion = null,$limit = null,$year = null,$investigador = null){
        if (!self::connectDB()) return null;
        $results = [];
        $query = self::QUERY_FIND;
        $dinParams = [];
        $query.= " WHERE p.estatus != ?";

        $dinParams[] = self::getBindParam("i",Estatus::ESTATUS_REMOVED);
        if ($id) {
            $query .=" AND p.id_proyecto=?";
            $dinParams[] = self::getBindParam("i",$id);
        }

        if ($estatusActual) {
            $query .=" AND p.id_estado_actual=?";
            $dinParams[] = self::getBindParam("i",$estatusActual);
        }

        if ($estadoAplicacion) {
            $query .=" AND p.id_estado_aplicacion=?";
            $dinParams[] = self::getBindParam("i",$estadoAplicacion);
        }

        if ($year){
            $query .=" AND YEAR(p.fecha_aplicacion)=?";
            $dinParams[] = self::getBindParam("s",$year);
        }

        if ($investigador){
            $query .=" AND (p.investigador_id=? OR p.id_proyecto in (SELECT proyecto_id_proyecto FROM proyecto_coinvestigador WHERE participante_id= ?))";
            $dinParams[] = self::getBindParam("i",$investigador);
            $dinParams[] = self::getBindParam("i",$investigador);
        }

        $query.= " ORDER BY fecha_creacion desc";

        if ($limit){
            $query.= " LIMIT " . $limit;
        }

        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return $results;
        self::bindDinParam($result,$dinParams);
        if (!self::$dbManager->executeSql($result)) return $results;

        $results = self::mappingFromDBResult($result);
        return $results;
    }


    public static function mappingFromDBResult(&$result){
        //id_proyecto, descripcion, fecha_aplicacion, fecha_inicio, asesor, id_estado_actual, id_estado_aplicacion,
        // contrapartida_unibe, aporte_unibe, moneda, monto_total, overhead_unibe, software,
        // patente, otro_producto, investigador_id, estatus, creador, fecha_creacion
        $bindResult = [];
        $result->bind_result(
            $bindResult['id'],$bindResult['description'],$bindResult['fecha_aplicacion'],
            $bindResult['fecha_inicio'],
            $bindResult['asesor'],$bindResult['asesor_nombre'],$bindResult['asesor_apellido'],
            $bindResult['estado_actual'],$bindResult['estado_actual_nombre'],
            $bindResult['estado_aplicacion'],$bindResult['estado_aplicacion_nombre'],
            $bindResult['contrapartida'],$bindResult['aporte'],
            $bindResult['moneda'],$bindResult['moneda_simbolo'],
            $bindResult['monto'],$bindResult['overhead'],$bindResult['software'],
            $bindResult['patente'],$bindResult['otro_producto'],
            $bindResult['investigador'],$bindResult['investigador_nombre'],$bindResult['investigador_apellido'],
            $bindResult['estatus'],$bindResult['creador'],$bindResult['fecha_creacion']);

        $results = [];
        while($result->fetch()){
            $pro = new Proyecto();
            $pro->setId($bindResult['id']);
            $pro->setDescripcion($bindResult['description']);
            $pro->setFechaAplicacion($bindResult['fecha_aplicacion']);
            $pro->setFechaInicio($bindResult['fecha_inicio']);

            $asesor = new Participante($bindResult['asesor'],$bindResult['asesor_nombre'],$bindResult['asesor_apellido']);
            $pro->setAsesor($asesor);

            $estadoActual = new EstadoActual($bindResult['estado_actual'],$bindResult['estado_actual_nombre']);
            $pro->setEstatusActual($estadoActual);

            $estadoApl = new EstatusAplicacion($bindResult['estado_aplicacion'],$bindResult['estado_aplicacion_nombre']);
            $pro->setEstatusAplicacion($estadoApl);

            $pro->setContraPartida($bindResult['contrapartida']);
            $pro->setAporte($bindResult['aporte']);

            $moneda = new Moneda($bindResult['moneda'],$bindResult['moneda_simbolo']);
            $pro->setMoneda($moneda);

            $pro->setMontoTotal($bindResult['monto']);
            $pro->setOverhead($bindResult['overhead']);
            $pro->setSoftware($bindResult['software'] ? true : false);
            $pro->setPatente($bindResult['patente'] ? true : false);
            $pro->setOtroProducto($bindResult['otro_producto']);

            $inv = new Participante($bindResult['investigador'],$bindResult['investigador_nombre']);
            $inv->setApellido($bindResult['investigador_apellido']);

            $pro->setInvestigador($inv);

            $estatus = new Estatus($bindResult['estatus']);
            $pro->setEstatus($estatus);

            $creador = new User($bindResult['creador']);
            $pro->setCreador($creador);

            $pro->setFechaCreacion($bindResult['fecha_creacion']);

            //co-researchers
            $coResearchers = Participante::find(null,$bindResult['id']);
            $pro->setCoInvestigadores($coResearchers);

            //funds
            $funds = Fondo::find(null,$bindResult['id']);
            $pro->setFondos($funds);

            //units
            $units = UnidadEjecutoraProyecto::findByProject($bindResult['id']);
            $pro->setUnidadesEjecutora($units);

            //institutions
            $institutions = InstitucionProyecto::findByProject($bindResult['id']);
            $pro->setInstituciones($institutions);

            $results[] = $pro;
        }
        return $results;
    }


    /**
     * Method to convert the object to array
    */
    public function toArray(){
        $result = [];

        $result['id'] = $this->getId();
        $result['description'] = $this->getDescripcion();
        $result['date_application'] = $this->getFechaAplicacion();
        $result['date_start'] = $this->getFechaInicio();

        $result['adviser']= [];
        $result['adviser']['id'] = $this->getAsesor()->getId();

        $result['current_status'] = $this->getEstatusActual()->toArray();
        $result['application_status']= $this->getEstatusAplicacion()->toArray();

        $result['researcher'] = [];
        $result['researcher']['id']         = $this->getInvestigador()->getId();
        $result['researcher']['name']       = $this->getInvestigador()->getNombre();
        $result['researcher']['lastname']   = $this->getInvestigador()->getApellido();

        $result['counterpart']  = $this->getContraPartida() + 0;
        $result['input']        = $this->getAporte();

        $result['currency'] = [];
        $result['currency']['id'] = $this->getMoneda()->getId();

        $result['total_amount'] = $this->getMontoTotal() + 0;

        $result['overhead'] = $this->getOverhead() + 0;

        $result['other_product'] = $this->otroProducto;

        $result['software'] = $this->getSoftware();

        $result['patent'] = $this->getPatente();

        $result['co_researchers'] = [];
        foreach($this->getCoInvestigadores() as $coresearcher)
            $result['co_researchers'][] = $coresearcher->toArray();

        $result['funds'] = [];
        foreach($this->getFondos() as $fondo)
            $result['funds'][] = $fondo->toArray();

        $result['executing_units'] = [];
        foreach($this->getUnidadesEjecutora() as $unit)
            $result['executing_units'][] = $unit->toArray();

        $result['institutions'] = [];
        foreach($this->getInstituciones() as $ins)
            $result['institutions'][] = $ins->toArray();

        return $result;
    }


    /**Metodo para obtener las cantiedades de los proyectos*/
    public static function getCount($estatusActual = null,$estadoAplicacion = null){
        $results=  self::find(null,$estatusActual,$estadoAplicacion);
        return count($results);
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

        $query = "INSERT INTO proyecto(descripcion, fecha_aplicacion, fecha_inicio, asesor, id_estado_actual, id_estado_aplicacion, contrapartida_unibe, aporte_unibe, moneda, monto_total, overhead_unibe, software, patente, otro_producto, investigador_id, estatus, creador, fecha_creacion) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())";
        $query = self::formatQuery($query);

        $dinParams[] = self::getBindParam("s",$this->descripcion);
        $dinParams[] = self::getBindParam("s",$this->fechaAplicacion);
        $dinParams[] = self::getBindParam("s",$this->fechaInicio);
        $dinParams[] = self::getBindParam("i",$this->asesor->getId());
        $dinParams[] = self::getBindParam("i",$this->estatusActual->getId());
        $dinParams[] = self::getBindParam("i",$this->estatusAplicacion->getId());
        $dinParams[] = self::getBindParam("d",$this->contraPartida);
        $dinParams[] = self::getBindParam("d",$this->aporte);
        $dinParams[] = self::getBindParam("i",$this->moneda->getId());
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
            $this->id = $result->insert_id;

            if (!$this->persistInstituciones())                 $error = true;

            if (!$error && !$this->persistsUnidadesEjecutora()) $error = true;

            if (!$error && !$this->persistsFondos())            $error = true;

            if (!$error && !$this->persistCoInvestigadores())   $error = true;

            $ret = !$error;
        }

        if ($ret) DatabaseManager::$link->commit();
        else DatabaseManager::$link->rollback();
        return $ret;
    }

    /**
     * Metodo para actualizar el proyecto
     * @return boolean
    */
    public function update(){
        if (!$this->getId()) return null;
        if (!self::connectDB()) return null;

        DatabaseManager::$link->autocommit(FALSE);

        $dinParams=  [];

        $query = "UPDATE proyecto SET descripcion=?,fecha_aplicacion=?,fecha_inicio=?,asesor=?," .
                 "id_estado_actual=?,id_estado_aplicacion=?,contrapartida_unibe=?,aporte_unibe=?," .
                 "moneda=?,monto_total=?,overhead_unibe=?,software=?,patente=?,otro_producto=?,investigador_id=? WHERE id_proyecto=?";
        $dinParams[] = self::getBindParam("s",$this->descripcion);
        $dinParams[] = self::getBindParam("s",$this->fechaAplicacion);
        $dinParams[] = self::getBindParam("s",$this->fechaInicio);
        $dinParams[] = self::getBindParam("i",$this->getAsesor()->getId());
        $dinParams[] = self::getBindParam("i",$this->getEstatusActual()->getId());
        $dinParams[] = self::getBindParam("i",$this->getEstatusAplicacion()->getId());
        $dinParams[] = self::getBindParam("d",$this->contraPartida);
        $dinParams[] = self::getBindParam("d",$this->aporte);
        $dinParams[] = self::getBindParam("i",$this->getMoneda()->getId());
        $dinParams[] = self::getBindParam("d",$this->getMontoTotal());
        $dinParams[] = self::getBindParam("d",$this->getOverhead());
        $dinParams[] = self::getBindParam("i",$this->getSoftware() ? "1" : "0");
        $dinParams[] = self::getBindParam("i",$this->getPatente() ? "1" : "0");
        $dinParams[] = self::getBindParam("s",$this->getOtroProducto() ? $this->getOtroProducto() : "n/a");
        $dinParams[] = self::getBindParam("i",$this->getInvestigador()->getId());
        $dinParams[] = self::getBindParam("i",$this->getId());

        $query = self::formatQuery($query);

        if (!$result = self::$dbManager->query($query)) return null;
        self::bindDinParam($result,$dinParams);
        if (!self::$dbManager->executeSql($result)) return null;

        $ret = false;

        //remove all the institutes
        $query = "DELETE FROM proyecto_has_institucion WHERE proyectos_id_proyecto=?";
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
        //end: remove all the institutes

        //remove all the units
        $query = "DELETE FROM proyecto_has_unidad_ejecutora WHERE proyecto_id_proyecto=?";
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
        //end: remove all the units

        //remove all the funds
        $query = "DELETE FROM proyecto_has_fondo WHERE id_proyecto=?";
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
        //end: remove all the funds

        //remove all the co-researchers
        $query = "DELETE FROM proyecto_coinvestigador WHERE proyecto_id_proyecto=?";
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
        $error = false;

        //end: remove all the co-researchers
        if (!$this->persistInstituciones())                 $error = true;

        if (!$error && !$this->persistsUnidadesEjecutora()) $error = true;

        if (!$error && !$this->persistsFondos())            $error = true;

        if (!$error && !$this->persistCoInvestigadores())   $error = true;

        if ($error) DatabaseManager::$link->rollback();
        else DatabaseManager::$link->commit();
        $ret = !$error;

        return $ret;
    }

    /**
     * Metodo para persistir las instituciones
     * @return boolean
     */
    private function persistInstituciones(){
        if (!is_array($this->getInstituciones()) || count($this->getInstituciones()) == 0)
            return true;
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
            return true;
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
            return true;
        //add the funds
        foreach($this->getFondos() as $fondo){
            $query = "INSERT INTO proyecto_has_fondo(id_proyecto, id_fondo) VALUES (?,?)";
            $dinParams = [];
            $dinParams[] = self::getBindParam("i",$this->getId());
            $dinParams[] = self::getBindParam("i",$fondo->getId());

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
            return true;
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


    /**
     * Method to delete an object from database
     */
    public function delete(){
        if (!$this->getId()) return false;

        $query = "UPDATE proyecto SET estatus=? WHERE id_proyecto=?";
        $dinParams = [];

        $dinParams[] = self::getBindParam("i",Estatus::ESTATUS_REMOVED);
        $dinParams[] = self::getBindParam("i",$this->getId());

        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return false;
        self::bindDinParam($result,$dinParams);
        if (!self::$dbManager->executeSql($result)) return false;

        return true;
    }


}