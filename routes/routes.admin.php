<?php
require_once("models/congreso.php");
require_once("models/participante.php");
require_once("models/publicacion.php");
require_once("models/revistaPublicacion.php");
require_once("models/fondo.php");
require_once("models/institucion.php");
require_once("models/unidadEjecutora.php");


$app->options('/(:name+)', function() use ($app) {
    $app->response()->header('Access-Control-Allow-Origin','*');
    $app->response()->header('Access-Control-Allow-Methods','PUT');
    $app->response()->header('Access-Control-Allow-Headers', 'X-Requested-With, X-authentication, X-client');
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
