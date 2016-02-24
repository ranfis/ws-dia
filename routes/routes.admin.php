<?php
require_once("models/congreso.php");
require_once("models/participante.php");
require_once("models/publicacion.php");
require_once("models/revistaPublicacion.php");
require_once("models/fondo.php");
require_once("models/institucion.php");
require_once("models/unidadEjecutora.php");
require_once("models/proyecto.php");
require_once("models/moneda.php");
require_once("models/user.php");
require_once("core/ReportFileManager.php");

$app->options('/(:name+)', function() use ($app) {
    $app->response()->header('Access-Control-Allow-Origin','*');
    $app->response()->header('Access-Control-Allow-Methods','PUT');
    $app->response()->header('Access-Control-Allow-Headers', 'X-Requested-With, X-authentication, X-client');
});


$app->get(\Config\Routes::DASHBOARD, function() use ($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_GET,$param,$app)) return null;
    $result = [
        "congress"=>0,
        "projects_total"=>0,
        "projects_no_finished"=>0,
        "projects_processing"=>0,
        "projects_finished"=>0,
        "projects_denied"=>0,
        "projects_review"=>0,
        "projects_accepted"=>0,
        "participants"=>0,
        "publications"=>0,
        "last_publications"=>[],
        "last_congress"=>[]
    ];

    $result['congress'] = \Model\Congreso::getCount();
    $result['projects_total'] = \Model\Proyecto::getCount();
    $result['projects_no_finished'] = \Model\Proyecto::getCount(\Model\EstadoActual::ESTADO_ACTUAL_NO_FINALIZADO);
    $result['projects_processing'] = \Model\Proyecto::getCount(\Model\EstadoActual::ESTADO_ACTUAL_EN_PROCESO);
    $result['projects_finished'] = \Model\Proyecto::getCount(\Model\EstadoActual::ESTADO_ACTUAL_FINALIZADO);
    $result['projects_denied'] = \Model\Proyecto::getCount(null,\Model\EstatusAplicacion::ESTATUS_APP_RECHAZADA);
    $result['projects_review'] = \Model\Proyecto::getCount(null,\Model\EstatusAplicacion::ESTATUS_APP_EN_REVISION);
    $result['projects_accepted'] = \Model\Proyecto::getCount(null,\Model\EstatusAplicacion::ESTATUS_APP_ACEPTADA);
    $result['participants'] = \Model\Participante::getCount();
    $result['publications'] = \Model\Publicacion::getCount();
    $resultFounds = \Model\Publicacion::find(null,10);
    foreach($resultFounds as $pub){
        $result['last_publications'][] = $pub->toArray();
    }

    $resultFounds = \Model\Congreso::find(null,10);
    foreach($resultFounds as $con){
        $result['last_congress'][] = $con->toArray();
    }

    $ws->result = $result;
    echo $ws->output($app);
});


$app->get(\Config\Routes::CONGRESS_LIST,function() use ($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_GET,$param,$app)) return null;
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

    $participantes = is_array($participantes) ? $participantes : [];

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
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    //validate the param
    if (!$con = validateCongress($ws,$app,$param,true)) return;
    //validate

    if (!$con->update()) $ws->generate_error(01,"Error actualizando el congreso");
    echo $ws->output($app);
});

$app->put(\Config\Routes::CONGRESS_DEL, function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;

    if ($id === null || !$id) $ws->generate_error(01,"El congreso a eliminar es requerido");
    else if (!StringValidator::isInteger($id)) $ws->generate_error(01,"El congreso es inv&aacute;lido");
    else if (!$congress = \Model\Congreso::findById($id)) $ws->generate_error(01,"El congreso no fue encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }
    if (!$congress->delete()) $ws->generate_error(01,"No se pudo eliminar el congreo, intente mas tarde");
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

$app->put(\Config\Routes::SPONSOR_DEL, function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;

    if ($id === null || !$id) $ws->generate_error(01,"El patrocinador a eliminar es requerido");
    else if (!StringValidator::isInteger($id)) $ws->generate_error(01,"El patrocinador es inv&aacute;lido");
    else if (!$sponsor = \Model\Patrocinio::findById($id)) $ws->generate_error(01,"El patrocinador no fue encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }
    if (!$sponsor->delete()) $ws->generate_error(01,"No se pudo eliminar el patrocinador, intente mas tarde");
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

$app->put(\Config\Routes::PARTICIPANTS_DEL, function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;

    if ($id === null || !$id) $ws->generate_error(01,"El participante a eliminar es requerido");
    else if (!StringValidator::isInteger($id)) $ws->generate_error(01,"El participante es inv&aacute;lido");
    else if (!$sponsor = \Model\Participante::findById($id)) $ws->generate_error(01,"El participante no fue encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }
    if (!$sponsor->delete()) $ws->generate_error(01,"No se pudo eliminar el participante, intente mas tarde");
    echo $ws->output($app);
});


$app->post(\Config\Routes::JOURNAL_ADD, function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    $name = isset($param['description']) ? $param['description'] : null;
    if ($name === null || !$name) $ws->generate_error(01,"La descripcion es requerida");
    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $revista = new \Model\RevistaPublicacion(null,$name);
    if (!$revista->add()) $ws->generate_error(01,"Error agregando la revista");
    echo $ws->output($app);
});


$app->put(\Config\Routes::JOURNAL_UPDATE, function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;
    $name = isset($param['description']) ? $param['description'] : null;

    if ($id === null || !$id) $ws->generate_error(01,"El id es requerido");
    else if ($name === null || !$name) $ws->generate_error(01,"La descripcion es requerida");
    else if (!$revista = \Model\RevistaPublicacion::findById($id)) $ws->generate_error(01,"Revista de publicacion invalida");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $revista->setDescripcion($name);

    if (!$revista->update()) $ws->generate_error(01,"Error Actualizando la revista");

    echo $ws->output($app);
});


$app->put(\Config\Routes::JOURNAL_DEL, function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;

    if ($id === null || !$id) $ws->generate_error(01,"El id es requerido");
    else if (!$revista = \Model\RevistaPublicacion::findById($id)) $ws->generate_error(01,"Revista de publicacion invalida");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }


    if (!$revista->delete()) $ws->generate_error(01,"Error Eliminando la revista de la publicaci&oacute;n");

    echo $ws->output($app);
});

$app->get(\Config\Routes::JOURNAL_LIST, function() use($app){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : [];
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;
    $result = \Model\RevistaPublicacion::find();


    $results = [];
    foreach($result as $revista){
        $results[] = \Model\RevistaPublicacion::mappingToArray($revista);
    }

    $ws->result = $results;
    echo $ws->output($app);
});

function validatePublication(&$ws,&$app,&$param,$update = false){
    $id             = isset($param[\Model\Publicacion::JSON_FIELD_ID]) ? $param[\Model\Publicacion::JSON_FIELD_ID] : null;
    $descripcion    = isset($param[\Model\Publicacion::JSON_FIELD_DESCRIPTION]) ? $param[\Model\Publicacion::JSON_FIELD_DESCRIPTION] : null;
    $fecha          = isset($param[\Model\Publicacion::JSON_FIELD_DATE]) ? $param[\Model\Publicacion::JSON_FIELD_DATE] : null;
    $revista        = isset($param[\Model\Publicacion::JSON_FIELD_JOURNAL]) ? $param[\Model\Publicacion::JSON_FIELD_JOURNAL] : null;
    $volumen        = isset($param[\Model\Publicacion::JSON_FIELD_VOLUME]) ? $param[\Model\Publicacion::JSON_FIELD_VOLUME] : null;
    $pagina         = isset($param[\Model\Publicacion::JSON_FIELD_PAGES]) ? $param[\Model\Publicacion::JSON_FIELD_PAGES] : null;
    $propiedadIntel = isset($param[\Model\Publicacion::JSON_FIELD_HAS_INTELLECTUAL_PROP]) ? $param[\Model\Publicacion::JSON_FIELD_HAS_INTELLECTUAL_PROP] : null;
    $participantes  = isset($param[\Model\Publicacion::JSON_FIELD_PARTICIPANTS]) ? $param[\Model\Publicacion::JSON_FIELD_PARTICIPANTS] : null;

    if ($update && ($id === null || !$id)) $ws->generate_error(01,"La publicaci&oacute;n es inv&&acute;lida");
    else if ($descripcion === null || !$descripcion) $ws->generate_error(01,"La descripci&oacute;n de la publicaci&oacute;n es requerida");
    else if ($fecha === null || !$fecha) $ws->generate_error(01,"La fecha de la publicaci&oacute;n es requerida");
    else if (!StringValidator::isDate($fecha)) $ws->generate_error(01,"La fecha de la publicaci&oacute;n es inv&aacute;lida, el formato debe ser: YYYY-MM-DD. Ejemplo: 2015-10-25");
    else if ($revista === null || !$revista) $ws->generate_error(01,"La revista es requerida");
    else if (!StringValidator::isInteger($revista)) $ws->generate_error(01,"La revista es inv&aacute;lida");
    else if ($volumen === null || !$volumen) $ws->generate_error(01,"El volumen de la publicaci&oacute;n es requerido");
    else if ($pagina === null || !$pagina) $ws->generate_error(01,"La cantidad de paginas de la publicaci&oacute;n es requerida");
    else if ($propiedadIntel === null) $ws->generate_error(01,"Debe de indicar si la publicaci&oacute;n tiene Propiedad Intelectual o no");

    else if (!$objRevista = \Model\RevistaPublicacion::findById($revista)) $ws->generate_error(01,"Revista no encontrada");
    else if ($participantes === null || !is_array($participantes) || count($participantes) == 0) $ws->generate_error(01,"Participantes inv&aacute;lido");
    else if ($update && (!$pub = \Model\Publicacion::findById($id))) $ws->generate_error(01,"Publicaci&oacute;n no encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return false;
    }


    $participantes = is_array($participantes)  ? $participantes : [];

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

    $pub = isset($pub) && $pub ? $pub : new \Model\Publicacion();
    $pub->setDescripcion($descripcion);
    $pub->setFecha($fecha);
    $pub->setVolumen($volumen);
    $pub->setRevista($objRevista);
    $pub->setPagina($pagina);
    $pub->setPropiedadIntelectual($propiedadIntel);

    $parsToObject = [];
    foreach($participantes as $parId){
        $par = new \Model\Participante($parId);
        $parsToObject[] = $par;
    }
    $pub->setParticipantes($parsToObject);

    return $pub;
}


$app->post(\Config\Routes::PUBLICATION_ADD,function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    //validate the param
    if (!$con = validatePublication($ws,$app,$param)) return;
    //end: validate the param

    if (!$con->add()) $ws->generate_error(01,"Error agregando la publicaci&oacute;n");

    echo $ws->output($app);
});



$app->put(\Config\Routes::PUBLICATION_UPDATE,function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    //validate the param
    if (!$pub = validatePublication($ws,$app,$param,true)) return;
    //end: validate the param

    if (!$pub->update()) $ws->generate_error(01,"Error actualizando la publicaci&oacute;n");
    echo $ws->output($app);
});

$app->put(\Config\Routes::PUBLICATION_DELETE, function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;

    if ($id === null || !$id) $ws->generate_error(01,"La publicaci&oacute;n a eliminar es requerida");
    else if (!StringValidator::isInteger($id)) $ws->generate_error(01,"La publicaci&oacute;n es inv&aacute;lida");
    else if (!$pub = \Model\Publicacion::findById($id)) $ws->generate_error(01,"La publicaci&oacute;n no fue encontrada");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }
    if (!$pub->delete()) $ws->generate_error(01,"No se pudo eliminar la publicaci&oacute;n, intente mas tarde");
    echo $ws->output($app);
});


$app->get(\Config\Routes::PUBLICATION_LIST,function() use ($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_GET,$param,$app)) return null;
    $results = \Model\Publicacion::find();

    $publications = [];
    foreach($results as $pat)
        $publications[] = $pat->toArray();

    $ws->result = $publications;
    echo $ws->output($app);
});


$app->get(\Config\Routes::FUND_LIST,function() use ($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_GET,$param,$app)) return null;
    $results = \Model\Fondo::find();

    $funds = [];
    foreach($results as $fund)
        $funds[] = $fund->toArray();

    $ws->result = $funds;
    echo $ws->output($app);
});


$app->post(\Config\Routes::FUND_ADD,function() use($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : $param;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    $name = isset($param['name']) ? $param['name'] : null;


    if ($name === null || !$name) $ws->generate_error(01,"El nombre del fondo es requerido");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $fund = new \Model\Fondo();
    $fund->setDescripcion($name);

    if (!$fund->add()) $ws->generate_error(01,"Error agregando el fondo, intente nuevamente");

    echo $ws->output($app);
});


$app->put(\Config\Routes::FUND_UPDATE,function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;
    $name = isset($param['name']) ? $param['name'] : null;

    if ($id === null || !$id) $ws->generate_error(01,"El id del fondo es requerido");
    else if (!StringValidator::isInteger($id)) $ws->generate_error(01,"Id del fondo invalido");
    else if ($name === null || !$name) $ws->generate_error(01,"El nombre del fondo es requerido");
    else if (!$fund = \Model\Fondo::findById($id)) $ws->generate_error(01,"Fondo no encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $fund->setDescripcion($name);

    if (!$fund->update()) $ws->generate_error(01,"Error actualizando el fondo");

    echo $ws->output($app);
});

$app->put(\Config\Routes::FUND_DEL, function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;

    if ($id === null || !$id) $ws->generate_error(01,"El fondo a eliminar es requerido");
    else if (!StringValidator::isInteger($id)) $ws->generate_error(01,"El fondo es inv&aacute;lido");
    else if (!$fund = \Model\Fondo::findById($id)) $ws->generate_error(01,"El fondo no fue encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }
    if (!$fund->delete()) $ws->generate_error(01,"No se pudo eliminar el fondo, intente mas tarde");
    echo $ws->output($app);
});


$app->get(\Config\Routes::INSTITUTION_LIST,function() use ($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_GET,$param,$app)) return null;
    $results = \Model\Institucion::find();

    $institutions = [];
    foreach($results as $ins)
        $institutions[] = $ins->toArray();

    $ws->result = $institutions;
    echo $ws->output($app);
});


$app->post(\Config\Routes::INSTITUTION_ADD,function() use($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : $param;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    $name = isset($param['name']) ? $param['name'] : null;


    if ($name === null || !$name) $ws->generate_error(01,"El nombre de la institucion es requerido");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $ins = new \Model\Institucion();
    $ins->setDescripcion($name);

    if (!$ins->add()) $ws->generate_error(01,"Error agregando la institucion, intente nuevamente");

    echo $ws->output($app);
});


$app->put(\Config\Routes::INSTITUTION_UPDATE,function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;
    $name = isset($param['name']) ? $param['name'] : null;

    if ($id === null || !$id) $ws->generate_error(01,"El id de la insituci&oacute;n es requerido");
    else if (!StringValidator::isInteger($id)) $ws->generate_error(01,"Id de la instituci&oacute;n invalido");
    else if ($name === null || !$name) $ws->generate_error(01,"El nombre de la instituci&oacute;n es requerido");
    else if (!$ins = \Model\Institucion::findById($id)) $ws->generate_error(01,"Instituci&oacute;n no encontrada");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $ins->setDescripcion($name);

    if (!$ins->update()) $ws->generate_error(01,"Error actualizando la instituci&oacute;n");

    echo $ws->output($app);
});

$app->put(\Config\Routes::INSTITUTION_DEL, function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;

    if ($id === null || !$id) $ws->generate_error(01,"La instituci&oacute;n a eliminar es requerida");
    else if (!StringValidator::isInteger($id)) $ws->generate_error(01,"La instituci&oacute;n es inv&aacute;lida");
    else if (!$ins = \Model\Institucion::findById($id)) $ws->generate_error(01,"La instituci&oacute;n no fue encontrada");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }
    if (!$ins->delete()) $ws->generate_error(01,"No se pudo eliminar la instituci&oacute;n, intente mas tarde");
    echo $ws->output($app);
});
//EXECUTING UNIT


$app->get(\Config\Routes::EXECUTING_UNIT_LIST,function() use ($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_GET,$param,$app)) return null;
    $results = \Model\UnidadEjecutora::find();

    $units = [];
    foreach($results as $unit)
        $units[] = $unit->toArray();

    $ws->result = $units;
    echo $ws->output($app);
});


$app->post(\Config\Routes::EXECUTING_UNIT_ADD,function() use($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : $param;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    $name = isset($param['name']) ? $param['name'] : null;


    if ($name === null || !$name) $ws->generate_error(01,"El nombre de la unidad es requerido");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $unit = new \Model\UnidadEjecutora();
    $unit->setDescripcion($name);

    if (!$unit->add()) $ws->generate_error(01,"Error agregando la unidad, intente nuevamente");

    echo $ws->output($app);
});


$app->put(\Config\Routes::EXECUTING_UNIT_UPDATE,function() use($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;
    $name = isset($param['name']) ? $param['name'] : null;

    $unit = null;

    if ($id === null || !$id) $ws->generate_error(01,"El id de la unidad es requerido");
    else if (!StringValidator::isInteger($id)) $ws->generate_error(01,"Id de la unidad invalido");
    else if ($name === null || !$name) $ws->generate_error(01,"El nombre de la unidad es requerido");
    else if (!$unit = \Model\UnidadEjecutora::findById($id)) $ws->generate_error(01,"Unidad no encontrada");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $unit->setDescripcion($name);

    if (!$unit->update()) $ws->generate_error(01,"Error actualizando la unidad");

    echo $ws->output($app);
});

$app->put(\Config\Routes::EXECUTING_UNIT_DEL, function() use($app,$param){
    $ws = new \Core\Webservice();
    $unit = null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;

    if ($id === null || !$id) $ws->generate_error(01,"La unidad a eliminar es requerida");
    else if (!StringValidator::isInteger($id)) $ws->generate_error(01,"La unidad es inv&aacute;lida");
    else if (!$unit = \Model\UnidadEjecutora::findById($id)) $ws->generate_error(01,"La unidad no fue encontrada");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }
    if (!$unit->delete()) $ws->generate_error(01,"No se pudo eliminar la unidad, intente mas tarde");
    echo $ws->output($app);
});


$app->get(\Config\Routes::CURRENCY_GET, function() use($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : [];
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_GET,$param,$app)) return null;

    $currencies = \Model\Moneda::find();

    $results = [];
    foreach($currencies as $currency){
        $results[] = $currency->toArray();
    }
    $ws->result = $results;
    echo $ws->output($app);
});

function validateProject(&$ws,&$app,&$param,$update = false){
    $id                 = isset($param['id']) ? $param['id'] : null;
    $descripcion        = isset($param['description']) ? $param['description'] : null;
    $fechaAplicacion    = isset($param['date_application']) ? $param['date_application'] : null;
    $fechaInicio        = isset($param['date_start']) ? $param['date_start'] : null;
    $estatusActual      = isset($param['current_status']) ? $param['current_status'] : null;
    $estatusAplicacion  = isset($param['application_status']) ? $param['application_status'] : null;
    $asesor             = isset($param['adviser']) ? $param['adviser'] : null;
    $contrapartida      = isset($param['counterpart']) ? $param['counterpart'] : null;
    $aporte             = isset($param['input']) ? $param['input'] : null;
    $moneda             = isset($param['currency']) ? $param['currency'] : null;
    $montoTotal         = isset($param['total_amount']) ? $param['total_amount'] : null;
    $overhead           = isset($param['overhead']) ? $param['overhead'] : null;
    $software           = isset($param['software']) ? $param['software'] : null;
    $patente            = isset($param['patent']) ? $param['patent'] : null;
    $otroProducto       = isset($param['other_product']) ? $param['other_product'] : null;
    $investigador       = isset($param['researcher']) ? $param['researcher'] : null;

    $instituciones      = isset($param['institutions']) ? $param['institutions'] : null;
    $unidadesEjecutora  = isset($param['executing_units']) ? $param['executing_units'] : null;
    $coInvestigadores   = isset($param['co_researchers']) ? $param['co_researchers'] : null;
    $fondos             = isset($param['funds']) ? $param['funds'] : null;

    //Nombre, año inicio, año final, Investigador principal y el estado de aplicación
    if ($update && ($id === null || !$id)) $ws->generate_error(01,"El id del proyecto es requerido");
    if ($descripcion === null || !$descripcion) $ws->generate_error(01,"La descripcion es requerida");
    else if ($fechaAplicacion === null || !$fechaAplicacion) $ws->generate_error(01,"La fecha de aplicaci&oacute;n del proyecto es requerida");
    else if (!StringValidator::isDate($fechaAplicacion)) $ws->generate_error(01,"La fecha de la publicaci&oacute;n es inv&aacute;lida, el formato debe ser: YYYY-MM-DD. Ejemplo: 2015-10-25");
    else if ($fechaInicio === null || !$fechaInicio) $ws->generate_error(01,"La fecha de inicio del proyecto es requerida");
    else if (!StringValidator::isDate($fechaInicio)) $ws->generate_error(01,"La fecha de la publicaci&oacute;n es inv&aacute;lida, el formato debe ser: YYYY-MM-DD. Ejemplo: 2015-10-25");
    else if ($asesor && !StringValidator::isInteger($asesor)) $ws->generate_error(01,"Asesor invalido");
    //else if ($contrapartida === null || !$contrapartida) $ws->generate_error(01,"La contrapartida es requerida");
    //else if ($aporte === null || !$aporte) $ws->generate_error(01,"El aporte es requerido");
    else if ($moneda && !StringValidator::isInteger($moneda)) $ws->generate_error(01,"La moneda es inv&aacute;lida");
    else if ($moneda && !$moneda = \Model\Moneda::findById($moneda)) $ws->generate_error(01,"Moneda no encontrada");
    //else if ($montoTotal === null || !$montoTotal) $ws->generate_error(01,"El monto total es requerido");
    //else if ($overhead === null || !$overhead) $ws->generate_error(01,"El overhead es requerido");
    //else if ($software === null) $ws->generate_error(01,"Determine si el proyeto contiene o no contiene software");
    //else if ($patente === null) $ws->generate_error(01,"Determine si el proyeto contiene o no contiene patente");
    else if (($estatusActual || $update) && ($estatusActual === null || !$estatusActual)) $ws->generate_error(01,"El estatus actual es requerido");
    else if (($estatusActual || $update) && !StringValidator::isInteger($estatusActual)) $ws->generate_error(01,"El estatus actual es inv&aacute;lido");
    else if ($estatusAplicacion === null || !$estatusAplicacion) $ws->generate_error(01,"El estatus de la aplicaci&oacute;n es requerido");
    else if (!StringValidator::isInteger($estatusAplicacion)) $ws->generate_error(01,"El estatus de la aplicaci&oacute;n es inv&aacute;lido");
    else if ($investigador === null || !$investigador) $ws->generate_error(01,"El investigador es requerido");
    else if (!StringValidator::isInteger($investigador)) $ws->generate_error(01,"El investigador es inv&aacute;lido");
    else if (($estatusActual || $update) && !$estatusActual = \Model\EstadoActual::findById($estatusActual)) $ws->generate_error(01,"El estado actual no fue encontrado");
    else if (!$estatusAplicacion = \Model\EstatusAplicacion::findById($estatusAplicacion)) $ws->generate_error(01,"El estatus de la aplicaci&oacute;n no fue encontrado");
    else if ($asesor && !$asesor = \Model\Participante::findById($asesor)) $ws->generate_error("01","Asesor no encontrado");
    else if (!$investigador = \Model\Participante::findById($investigador)) $ws->generate_error("01","Investigador no encontrado");
    else if ($coInvestigadores && (!is_array($coInvestigadores) || count($coInvestigadores) == 0)) $ws->generate_error(01,"Co Investigadores inv&aacute;lido");
    else if ($fondos && (!is_array($fondos) || count($fondos) == 0)) $ws->generate_error(01,"Los fondos son inv&aacute;lidos");
    else if ($unidadesEjecutora && (!is_array($unidadesEjecutora) || count($unidadesEjecutora) == 0)) $ws->generate_error(01,"Las unidades ejecutoras son inv&aacute;lidos");
    else if ($instituciones && (!is_array($instituciones) || count($instituciones) == 0)) $ws->generate_error(01,"Las instituciones son inv&aacute;lidos");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $tmpCoInvestigadores = $tmpFondos = $tmpInstituciones = $tmpUnidadesEjecutora  = [];


    $coInvestigadores = is_array($coInvestigadores) ? $coInvestigadores : [];
    $instituciones = is_array($instituciones) ? $instituciones : [];
    $fondos = is_array($fondos) ? $fondos : [];
    $unidadesEjecutora = is_array($unidadesEjecutora) ? $unidadesEjecutora : [];

    //verify if any of the co researchers is invalid
    foreach($coInvestigadores as $k=>$p){
        $pos = $k+1;
        if (!StringValidator::isInteger($p)){
            $ws->generate_error(01,"El co-investigador con posici&oacute;n #{$pos} es inv&aacute;lido");
            break;
        }

        if (!$tmpCoInvestigadores[$k] = \Model\Participante::findById($p)){
            $ws->generate_error(01,"El co-investigador con posici&oacute;n #{$pos} no fue encontrado");
            break;
        }
    }
    $coInvestigadores = $tmpCoInvestigadores;
    //end: verify if any of the co researchers is invalid

    if (!$ws->error)
        //verify if any of the funds is invalid
        foreach($fondos as $k=>$p){
            $pos = $k+1;
            if (!StringValidator::isInteger($p)){
                $ws->generate_error(01,"El fondo con posici&oacute;n #{$pos} es inv&aacute;lido");
                break;
            }

            if (!$tmpFondos[$k] = \Model\Fondo::findById($p)){
                $ws->generate_error(01,"El fondo con posici&oacute;n #{$pos} no fue encontrado");
                break;
            }
        }
    $fondos = $tmpFondos;
    //end: verify if any of the funds is invalid

    if (!$ws->error)
        //verify if any of the institutions is invalid
        foreach($instituciones as $k=>$p){
            $pos = $k+1;
            $ins = is_array($p) ? $p: [];
            $ins['id'] = isset($ins['id']) ? $ins['id'] : null;
            $ins['principal'] = isset($ins['principal']) && $ins['principal'];

            if (!isset($ins['id']) && !StringValidator::isInteger($ins['id'])){
                $ws->generate_error(01,"La instituci&oacute;n con posici&oacute;n #{$pos} es inv&aacute;lido");
                break;
            }

            if (!$tmpInstituciones[$k] = \Model\InstitucionProyecto::findById($ins['id'])){
                $ws->generate_error(01,"La instituci&oacute;n con posici&oacute;n #{$pos} no fue encontrado");
                break;
            }else{
                $tmpInstituciones[$k]->setPrincipal($ins['principal']);
            }
        }
    //end: verify if any of the institutions is invalid
    $instituciones = $tmpInstituciones ;

    if (!$ws->error)
        //verify if any of the unit is invalid
        foreach($unidadesEjecutora as $k=>$p){
            $pos = $k+1;
            $unit = is_array($p) ? $p: [];
            $unit['id'] = isset($unit['id']) ? $unit['id'] : null;
            $unit['executing_unit'] = isset($unit['executing_unit']) && $unit['executing_unit'];
            $unit['superviser_unit'] = isset($unit['superviser_unit']) && $unit['superviser_unit'];

            if (!isset($unit['id']) && !StringValidator::isInteger($unit['id'])){
                $ws->generate_error(01,"La unidad con posici&oacute;n #{$pos} es inv&aacute;lido");
                break;
            }

            if (!$tmpUnidadesEjecutora[$k] = \Model\UnidadEjecutoraProyecto::findById($unit['id'])){
                $ws->generate_error(01,"La unidad con posici&oacute;n #{$pos} no fue encontrado");
                break;
            }else{
                $tmpUnidadesEjecutora[$k]->setUnidadEjecutora($unit['executing_unit']);
                $tmpUnidadesEjecutora[$k]->setUnidadSupervisora($unit['superviser_unit']);
            }
        }
    $unidadesEjecutora = $tmpUnidadesEjecutora;
    //end: verify if any of the unit is invalid

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $tmpCoInvestigadores = $tmpFondos = $tmpInstituciones = $tmpUnidadesEjecutora  = null;

    $proyecto = new \Model\Proyecto();

    if ($update) $proyecto->setId($id);
    $proyecto->setDescripcion($descripcion);
    $proyecto->setFechaAplicacion($fechaAplicacion);
    $proyecto->setFechaInicio($fechaInicio);
    $proyecto->setAsesor($asesor);

    if (!$estatusActual)
        $estatusActual = new \Model\EstadoActual(\Model\EstadoActual::ESTADO_ACTUAL_NO_FINALIZADO);

    $proyecto->setEstatusActual($estatusActual);

    //$estatusAplicacion = new \Model\EstatusAplicacion(\Model\EstatusAplicacion::ESTATUS_APP_EN_REVISION);
    $proyecto->setEstatusAplicacion($estatusAplicacion);

    $proyecto->setContraPartida($contrapartida);
    $proyecto->setAporte($aporte);
    $proyecto->setMontoTotal($montoTotal);
    $proyecto->setOverhead($overhead);
    $proyecto->setMoneda($moneda);
    $proyecto->setOtroProducto($otroProducto);
    $proyecto->setSoftware($software ? true : false);
    $proyecto->setPatente($patente ? true : false);
    $proyecto->setInvestigador($investigador);

    $proyecto->setCoInvestigadores($coInvestigadores);
    $proyecto->setFondos($fondos);
    $proyecto->setInstituciones($instituciones);
    $proyecto->setUnidadesEjecutora($unidadesEjecutora);

    $creador = \Core\SessionManager::getSession()->user;
    $proyecto->setCreador($creador);

    return $proyecto;
}

$app->post(\Config\Routes::PROJECT_ADD,function() use($app,$param){
    $ws = new \Core\Webservice();
    $unit = null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    if (!$proyecto = validateProject($ws,$app,$param,false)){
        return true;
    }
    if (!$proyecto->add())
        $ws->generate_error(01,"Error agregando el proyecto");
    echo $ws->output($app);
});

$app->put(\Config\Routes::PROJECT_UPDATE,function() use($app,$param){
    $ws = new \Core\Webservice();
    $unit = null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    if (!$proyecto = validateProject($ws,$app,$param,true)){
        return true;
    }
    if (!$proyecto->update())
        $ws->generate_error(01,"Error actualizando el proyecto");
    echo $ws->output($app);
});


$app->put(\Config\Routes::PROJECT_DEL, function() use($app,$param){
    $ws = new \Core\Webservice();
    $unit = null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_PUT,$param,$app)) return null;

    $id = isset($param['id']) ? $param['id'] : null;

    if ($id === null || !$id) $ws->generate_error(01,"El proyecto a eliminar es requerido");
    else if (!StringValidator::isInteger($id)) $ws->generate_error(01,"El proyecto a eliminar es inv&aacute;lido");
    else if (!$pro = \Model\Proyecto::findById($id)) $ws->generate_error(01,"El proyecto no fue encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }
    if (!$pro->delete()) $ws->generate_error(01,"No se pudo eliminar el proyecto, intente mas tarde");
    echo $ws->output($app);
});


$app->get(\Config\Routes::PROJECT_LIST,function() use ($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_GET,$param,$app)) return null;
    $results = \Model\Proyecto::find();

    $projects = [];
    foreach($results as $pro)
        $projects[] = $pro->toArray();

    $ws->result = $projects;
    echo $ws->output($app);
});



$app->get(\Config\Routes::STATUS_APPLICATION_LIST,function() use ($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_GET,$param,$app)) return null;
    $results = \Model\EstatusAplicacion::find();

    $statuses = [];
    foreach($results as $status)
        $statuses[] = $status->toArray();

    $ws->result = $statuses;
    echo $ws->output($app);
});


$app->get(\Config\Routes::CURRENT_STATUS_LIST,function() use ($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET ? $_GET : null;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_GET,$param,$app)) return null;
    $results = \Model\EstadoActual::find();

    $statuses = [];
    foreach($results as $status)
        $statuses[] = $status->toArray();

    $ws->result = $statuses;
    echo $ws->output($app);
});


$app->get(\Config\Routes::ADM_USER_LIST,function() use ($app,$param){
    $ws = new \Core\Webservice();
    $param = $_GET;
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_GET,$param,$app)) return null;
    $userSession = \Core\SessionManager::getUser();
    $q = isset($param['q']) ? $param['q'] : null;
    $userSession = \Core\SessionManager::getSession()->user;
    $users = \Model\User::find(null,$userSession->id,null,$q);
    $ws->result= $users;
    echo $ws->output($app);
});


$app->post(\Config\Routes::ADM_USER_ADD,function() use ($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    $userSession = \Core\SessionManager::getUser();

    $correo     = isset($param['email'])   ? $param['email']  : null;
    $clave      = isset($param['password'])    ? $param['password']   : null;
    $nombre     = isset($param['fullname'])   ? $param['fullname']  : null;
    $role       = isset($param['role'])   ? $param['role']  : null;

    if ($correo === null || !$correo) $ws->generate_error(01,"El correo es requerido");
    else if (!StringValidator::isEmail($correo)) $ws->generate_error(01,"El correo es inv&aacute;lido");
    else if ($clave === null || !$clave) $ws->generate_error(01,"La clave de acceso es requerido");
    else if ($nombre === null || !$nombre) $ws->generate_error(01,"El nombre es requerido");
    else if ($role === null || !$role) $ws->generate_error(01,"El role es requerido");
    else if (!StringValidator::isInteger($role)) $ws->generate_error(01,"El role es inv&aacute;lido");
    else if ($user = \Model\User::findByEmail($correo,$userSession->id)) $ws->generate_error(01,"Existe un usuario registrado con este correo");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $user = new \Model\User(null,$correo,$clave);
    $user->setRole(new \Model\Role($role));
    $user->nombreCompleto = $nombre;

    if (!$user->add()) $ws->generate_error(01,"Error agregando el usuario");
    else{
        $result = [];
        $result['id'] = $user->id;
        $ws->result = $result;
    }

    echo $ws->output($app);
});

$app->post(\Config\Routes::ADM_USER_UPDATE,function() use ($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    $userSession = \Core\SessionManager::getUser();

    $id         = isset($param['id']) ? $param['id'] : null;
    $nombre     = isset($param['fullname'])   ? $param['fullname']  : null;
    $role     = isset($param['role'])   ? $param['role']  : null;

    if ($id === null || !$id) $ws->generate_error(01,"El ID del usuario es requerido");
    else if ($nombre === null || !$nombre) $ws->generate_error(01,"El nombre es requerido");
    else if ($role === null || !$role) $ws->generate_error(01,"El role es requerido");
    else if (!StringValidator::isInteger($role)) $ws->generate_error(01,"El role es inv&aacute;lido");
    else if ($id == $userSession->id) $ws->generate_error(01,"No puede actualizar los datos de su mismo usuario");
    else if (!$user = \Model\User::findById($id,$userSession->id)) $ws->generate_error(01,"Usuario no encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    $user->nombreCompleto = $nombre;

    if (!$user->update()){
        $ws->generate_error(01,"Error actualizando el usuario");
    }

    echo $ws->output($app);
});


$app->post(\Config\Routes::ADM_USER_DEL,function() use ($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    $userSession = \Core\SessionManager::getUser();
    $id = isset($param['id']) ? $param['id'] : null;
    if ($id == $userSession->id) $ws->generate_error(01,"No puede remover su mismo usuario");
    else if (!$user = \Model\User::findById($id,$userSession->id)) $ws->generate_error(01,"Usuario no encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    if (!$user->remove()){
        $ws->generate_error(01,"Error eliminando el usuario");
    }

    echo $ws->output($app);
});


$app->post(\Config\Routes::ADM_USER_CHANGE_PASSWORD,function() use ($app,$param){
    $ws = new \Core\Webservice();
    if (!$ws->prepareRequest(\Core\Webservice::METHOD_POST,$param,$app)) return null;

    $userSession = \Core\SessionManager::getUser();
    $id = isset($param['id']) ? $param['id'] : null;
    if ($id == $userSession->id) $ws->generate_error(01,"No puede cambiar la contraseña a su mismo usuario");
    else if (!$user = \Model\User::findById($id,$userSession->id)) $ws->generate_error(01,"Usuario no encontrado");

    if ($ws->error){
        echo $ws->output($app);
        return;
    }

    if (!$user->changePassword()){
        $ws->generate_error(01,"Error cambiando la contraseña del usuario");
    }

    echo $ws->output($app);
});