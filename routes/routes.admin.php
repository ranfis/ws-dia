<?php
require_once("models/congreso.php");
require_once("models/participante.php");

$app->get(\Config\Routes::CONGRESS_LIST,function() use ($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;
    $results = \Model\Congreso::find();

    $congresses = [];
    foreach($results as $con)
        $congresses[] = $con->toArray();

    $ws->result = $congresses;
    echo $ws->output($app);
});


function validateCongress(&$ws,&$app,&$param,$update = false){

    $id             = isset($param['id']) ? $param['id'] : null;
    $nombre         = isset($param['nombre']) ? $param['nombre'] : null;
    $fechaCongreso  = isset($param['fecha_congreso']) ? $param['fecha_congreso'] : null;
    $ponencia       = isset($param['ponencia']) ? $param['ponencia'] : null;
    $lugar          = isset($param['lugar']) ? $param['lugar'] : null;
    $patrocinio     = isset($param['patrocinio']) ? $param['patrocinio'] : null;
    $participantes  = isset($param['participantes']) ? $param['participantes'] : null;

    if ($update && ($id === null || !$id)) $ws->generate_error(01,"El congreso es inv&&acute;lido");
    if ($nombre === null || !$nombre) $ws->generate_error(01,"El nombre del congreso es requerido");
    else if ($fechaCongreso === null || !$fechaCongreso) $ws->generate_error(01,"La fecha del congreso es requerida");
    else if (!StringValidator::isDate($fechaCongreso)) $ws->generate_error(01,"La fecha del congreso es inv&aacute;lida, el formato debe ser: YYYY-MM-DD. Ejemplo: 2015-10-25");
    else if ($ponencia === null || !$ponencia) $ws->generate_error(01,"La ponencia es requerida");
    else if ($lugar === null || !$lugar) $ws->generate_error(01,"El lugar es requerido");
    else if ($patrocinio === null || !$patrocinio) $ws->generate_error(01,"El patrocinio es requerido");
    else if (!StringValidator::isInteger($patrocinio)) $ws->generate_error(01,"El patrocinio es inv&aacute;lido");
    else if (!$pat = \Model\Patrocinio::findById($patrocinio)) $ws->generate_error(01,"Patrocinador no encontrado");
    else if ($participantes === null || !is_array($participantes) || count($participantes) == 0) $ws->generate_error(01,"Participantes inv&aacute;lido");
    else if ($update && (!$con = \Model\Congreso::findById($id))) $ws->generate_error(01,"Congreso no encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return false;
    }

    //verify if any of the participans is invalid
    foreach($participantes as $k=>$p){
        $pos = $k+1;
        if (!StringValidator::isInteger($p)){
            $ws->generate_error(01,"El participante con posici&oacute;n #{$pos} es inv&aacute;lido");
            break;
        }

        if (!\Model\Participante::findById($p)){
            $ws->generate_error(01,"El participante con posici&oacute;n #{$pos} es inv&aacute;lido");
            break;
        }
    }
    //end: verify if any of the participans is invalid

    if ($ws->error){
        echo $ws->output($app);
        return false;
    }

    $con = isset($con) && $con ? $con :  new \Model\Congreso();
    $con->setNombre($nombre);
    $con->setFechaCongreso($fechaCongreso);
    $con->setPonencia($ponencia);

    $parsToObject = [];
    foreach($participantes as $parId){
        $par = new \Model\Participante($parId);
        $parsToObject[] = $par;
    }
    $con->setParticipantes($parsToObject);
    $con->setLugar($lugar);
    $con->setPatrocinio($pat);

    return $con;
}


$app->post(\Config\Routes::CONGRESS_ADD,function() use($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : $param;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    //validate the param
    if (!$con = validateCongress($ws,$app,$param)) return;
    //validate

    if (!$con->add()) $ws->generate_error(01,"Error agregando el congreso");

    echo $ws->output($app);
});



$app->put(\Config\Routes::CONGRESS_UPDATE,function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    //validate the param
    if (!$con = validateCongress($ws,$app,$param,true)) return;
    //validate

    if (!$con->update()) $ws->generate_error(01,"Error actualizando el congreso");
    echo $ws->output($app);
});


$app->get(\Config\Routes::SPONSOR_LIST,function() use ($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_GET,$param,$app)) return null;
    $results = \Model\Patrocinio::find();

    $sponsors = [];
    foreach($results as $pat)
        $sponsors[] = $pat->toArray();


    $ws->result = $sponsors;
    echo $ws->output($app);
});


$app->post(\Config\Routes::SPONSOR_ADD,function() use($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : $param;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    $name = isset($param['nombre']) ? $param['nombre'] : null;


    if ($name === null || !$name) $ws->generate_error(01,"El nombre del patrocinador es requerido");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $pat = new \Model\Patrocinio();
    $pat->setName($name);

    if (!$pat->add()) $ws->generate_error(01,"Error agregando patrocinador, intente nuevamente");

    echo $ws->output($app);
});


$app->put(\Config\Routes::SPONSOR_UPDATE,function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;
    $name = isset($param['nombre']) ? $param['nombre'] : null;

    if ($id === null || !$id) $ws->generate_error(01,"El id del patrocinador es requerido");
    else if (!StringValidator::isInteger($id)) $ws->generate_error(01,"Id del patrocinador invalido");
    else if ($name === null || !$name) $ws->generate_error(01,"El nombre del patrocinador es requerido");
    else if (!$pat = \Model\Patrocinio::findById($id)) $ws->generate_error(01,"Patrocinador no encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $pat->setName($name);

    if (!$pat->update()) $ws->generate_error(01,"Error actualizando el patrocinador");

    echo $ws->output($app);
});

$app->get(\Config\Routes::PARTICIPANTS_LIST,function() use($app){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_GET,$param,$app)) return null;

    $participantes = \Model\Participante::find();

    $participantes = \Model\Participante::mappingToArray($participantes);
    $ws->result = $participantes;
    echo $ws->output($app);
});


$app->put(\Config\Routes::PARTICIPANTS_UPDATE, function() use($param,$app){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id     = isset($param['id']) ? $param['id'] : null;
    $nombre     = isset($param['nombre']) ? $param['nombre'] : null;
    $apellido   = isset($param['apellido']) ? $param['apellido'] : null;

    if ($id === null || !StringValidator::isInteger($id)) $ws->generate_error(01,"El id del participante");
    else if ($nombre === null || !$nombre) $ws->generate_error(01,"El nombre es requerido");
    else if ($apellido === null || !$apellido) $ws->generate_error(01,"El apellido es requerido");
    else if (!\Model\Participante::findById($id)) $ws->generate_error("01","El participante no fue encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $p = new \Model\Participante();
    $p->setId($id);
    $p->setNombre($nombre);
    $p->setApellido($apellido);

    if (!$p->update()) $ws->generate_error(01,"Error actualizando el participante");
    echo $ws->output($app);
});


$app->post(\Config\Routes::PARTICIPANTS_ADD, function() use($param,$app){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    $nombre     = isset($param['nombre']) ? $param['nombre'] : null;
    $apellido   = isset($param['apellido']) ? $param['apellido'] : null;

    if ($nombre === null || !$nombre) $ws->generate_error(01,"El nombre es requerido");
    else if ($apellido === null || !$apellido) $ws->generate_error(01,"El apellido es requerido");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $p = new \Model\Participante();
    $p->setNombre($nombre);
    $p->setApellido($apellido);

    if (!$p->add()) $ws->generate_error(01,"Error agregando el participante");

    echo $ws->output($app);
});