<?php
require_once("models/congreso.php");

$app->get(\Config\Routes::CONGRESS_LIST,function() use ($app,$param){
    $ws = new \Core\Webservice();
    $results = \Model\Congreso::find();

    $congresses = [];
    foreach($results as $con)
        $congresses[] = $con->toArray();


    $ws->result = $congresses;
    echo $ws->output($app);
});

$app->post(\Config\Routes::CONGRESS_ADD,function() use($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : $param;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    $nombre = isset($param['nombre']) ? $param['nombre'] : null;
    $fechaCongreso  = isset($param['fecha_congreso']) ? $param['fecha_congreso'] : null;
    $ponencia  = isset($param['ponencia']) ? $param['ponencia'] : null;
    $lugar  = isset($param['lugar']) ? $param['lugar'] : null;
    $patrocinio  = isset($param['patrocinio']) ? $param['patrocinio'] : null;

    if ($nombre === null || !$nombre) $ws->generate_error(00,"El nombre del congreso es requerido");
    else if ($fechaCongreso === null || !$fechaCongreso) $ws->generate_error(00,"La fecha del congreso es requerida");
    else if (!StringValidator::isDate($fechaCongreso)) $ws->generate_error(00,"La fecha del congreso es inv&aacute;lida, el formato debe ser: YYYY-MM-DD. Ejemplo: 2015-10-25");
    else if ($ponencia === null || !$ponencia) $ws->generate_error(00,"La ponencia es requerida");
    else if ($lugar === null || !$lugar) $ws->generate_error(00,"El lugar es requerido");
    else if ($patrocinio === null || !$patrocinio) $ws->generate_error(00,"El patrocinio es requerido");
    else if (!StringValidator::isInteger($patrocinio)) $ws->generate_error(00,"El patrocinio es inv&aacute;lido");
    else if (!$pat = \Model\Patrocinio::findById($patrocinio)) $ws->generate_error(00,"Patrocinador no encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $con = new \Model\Congreso();
    $con->setNombre($nombre);
    $con->setFechaCongreso($fechaCongreso);
    $con->setPonencia($ponencia);
    $con->setLugar($lugar);
    $con->setPatrocinio($pat);

    if (!$con->add()) $ws->generate_error(00,"Error agregando el congreso");

    echo $ws->output($app);
});

$app->put(\Config\Routes::CONGRESS_UPDATE,function() use($app,$param){

});


$app->get(\Config\Routes::SPONSOR_LIST,function() use ($app,$param){
    $ws = new \Core\Webservice();
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


    if ($name === null || !$name) $ws->generate_error(00,"El nombre del patrocinador es requerido");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $pat = new \Model\Patrocinio();
    $pat->setName($name);

    if (!$pat->add()) $ws->generate_error(00,"Error agregando patrocinador, intente nuevamente");

    echo $ws->output($app);
});


$app->put(\Config\Routes::SPONSOR_UPDATE,function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;
    $name = isset($param['nombre']) ? $param['nombre'] : null;

    if ($id === null || !$id) $ws->generate_error(00,"El id del patrocinador es requerido");
    else if (!StringValidator::isInteger($id)) $ws->generate_error(00,"Id del patrocinador invalido");
    else if ($name === null || !$name) $ws->generate_error(00,"El nombre del patrocinador es requerido");
    else if (!$pat = \Model\Patrocinio::findById($id)) $ws->generate_error(00,"Patrocinador no encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $pat->setName($name);

    if (!$pat->update()) $ws->generate_error(00,"Error actualizando el patrocinador");

    echo $ws->output($app);
});