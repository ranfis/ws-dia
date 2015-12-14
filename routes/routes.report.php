<?php

/**
 * @return boolean true: the session is valid, otherwise is invalid
*/
function validateSessionFile($param){
    if ($param['session_id'])
        $param['session_id'] = base64_decode($param['session_id']);

    if (!\Core\SessionManager::isValidSession($param['session_id'])){
        echo "Sesion Invalida";
        return false;
    }

    if (!\Core\SessionManager::hasSessionExpired($param['session_id'])){
        echo "Sesión Expirada";
        return false;
    }
    return true;
}


$app->get("/report/projects/earnings/:s",function($sessionId) use($app,$param){
    $param = $_GET ? $_GET : [];
    $param['session_id'] = $sessionId;
    if (!validateSessionFile($param)) return true;

    $headers = ["ID","Descripcion","Fecha Aplicacion","Fecha Final","Investigador","Moneda","Monto Total"];

    $year = isset($param['year']) ? $param['year'] : null;

    $projects = \Model\Proyecto::find(null,null,null,null,$year);

    $rows= [];

    $total = 0;

    foreach($projects as $project){
        $row = [];
        $row[] = $project->getId();
        $row[] = $project->getDescripcion();
        $row[] = date("Y",strtotime($project->getFechaAplicacion()));
        $row[] = date("Y",strtotime($project->getFechaInicio()));
        $row[] = $project->getInvestigador()->getNombre();
        $row[] = $project->getMoneda()->getSimbolo();
        $row[] = $project->getMontoTotal();
        $total = $total + $project->getMontoTotal();
        $rows[] = $row;
    }

    $rows[] = ["","","","","",""];
    $rows[] = ["","","","","",$total];


    $filename = "ganacias-proyectos";
    if ($year)
        $filename.= "-$year";

    $report = new ReportFileManager($filename);
    $report->setHeader($headers);
    foreach($rows as $row)
        $report->addRow($row);

    $filename = $report->generateFile();
});


$app->get("/report/projects/earnings/overhead/:s",function($sessionId) use($app,$param){
    $param = $_GET ? $_GET : [];
    $param['session_id'] = $sessionId;
    if (!validateSessionFile($param)) return true;

    $headers = ["ID","Descripcion","Fecha Aplicacion","Fecha Final","Investigador","Moneda","Overhead"];

    $year = isset($param['year']) ? $param['year'] : null;

    $projects = \Model\Proyecto::find(null,null,null,null,$year);

    $rows= [];

    $total = 0;

    foreach($projects as $project){
        $row = [];
        $row[] = $project->getId();
        $row[] = $project->getDescripcion();
        $row[] = date("Y",strtotime($project->getFechaAplicacion()));
        $row[] = date("Y",strtotime($project->getFechaInicio()));
        $row[] = $project->getInvestigador()->getNombre();
        $row[] = $project->getMoneda()->getSimbolo();
        $row[] = $project->getOverhead();
        $total = $total + $project->getOverhead();
        $rows[] = $row;
    }

    $rows[] = ["","","","","",""];
    $rows[] = ["","","","","",$total];

    $filename = "ganancias-proyectos-overhead";

    if ($year)
        $filename.= "-$year";

    $report = new ReportFileManager($filename);
    $report->setHeader($headers);
    foreach($rows as $row)
        $report->addRow($row);

    $filename = $report->generateFile();
});


$app->get("/report/projects/earnings/total-amount-overhead/:s",function($sessionId) use($app,$param){
    $param = $_GET ? $_GET : [];
    $param['session_id'] = $sessionId;
    if (!validateSessionFile($param)) return true;

    $headers = ["ID","Descripcion","Fecha Aplicacion","Fecha Final","Investigador","Moneda","Monto Total","Overhead"];

    $year = isset($param['year']) ? $param['year'] : null;

    $projects = \Model\Proyecto::find(null,null,null,null,$year);

    $rows= [];

    $total_amount = 0;
    $total_overhead = 0;

    foreach($projects as $project){
        $row = [];
        $row[] = $project->getId();
        $row[] = $project->getDescripcion();
        $row[] = date("Y",strtotime($project->getFechaAplicacion()));
        $row[] = date("Y",strtotime($project->getFechaInicio()));
        $row[] = $project->getInvestigador()->getNombre();
        $row[] = $project->getMoneda()->getSimbolo();
        $row[] = $project->getMontoTotal();
        $row[] = $project->getOverhead();
        $total_amount   = $total_amount + $project->getMontoTotal();
        $total_overhead = $total_overhead + $project->getOverhead();
        $rows[] = $row;
    }

    $rows[] = ["","","","","","","",""];
    $rows[] = ["","","","","","",$total_amount,$total_overhead];

    $filename = "ganancias-proyectos-total-monto-overhead";

    if ($year)
        $filename.= "-$year";

    $report = new ReportFileManager($filename);
    $report->setHeader($headers);
    foreach($rows as $row)
        $report->addRow($row);

    $filename = $report->generateFile();
});


$app->get("/report/projects/quantity/:s",function($sessionId) use($app,$param){
    $param = $_GET ? $_GET : [];
    $param['session_id'] = $sessionId;
    if (!validateSessionFile($param)) return true;

    $headers = ["Estado","Cantidad"];

    $year = isset($param['year']) ? $param['year'] : null;

    $rows= [];
    $rows[] = ["En Proceso",\Model\Proyecto::getCount(\Model\EstadoActual::ESTADO_ACTUAL_EN_PROCESO)];
    $rows[] = ["Finalizado",\Model\Proyecto::getCount(\Model\EstadoActual::ESTADO_ACTUAL_FINALIZADO)];
    $rows[] = ["No Finalizado",\Model\Proyecto::getCount(\Model\EstadoActual::ESTADO_ACTUAL_NO_FINALIZADO)];
    $rows[] = ["Aceptados",\Model\Proyecto::getCount(null,\Model\EstatusAplicacion::ESTATUS_APP_ACEPTADA)];
    $rows[] = ["En Revision",\Model\Proyecto::getCount(null,\Model\EstatusAplicacion::ESTATUS_APP_EN_REVISION)];
    $rows[] = ["Rechazados",\Model\Proyecto::getCount(null,\Model\EstatusAplicacion::ESTATUS_APP_RECHAZADA)];

    $filename = "resumen-proyectos-cantidad";

    if ($year)
        $filename.= "-$year";

    $report = new ReportFileManager($filename);
    $report->setHeader($headers);
    foreach($rows as $row)
        $report->addRow($row);

    $filename = $report->generateFile();
});


$app->get("/report/publications/:s",function($sessionId) use($app,$param){
    $param = $_GET ? $_GET : [];
    $param['session_id'] = $sessionId;
    if (!validateSessionFile($param)) return true;

    //Nombre, Fecha, Revista, autores y propiedad intelectual.
    $headers = ["ID","Nombre","Fecha","Revista","Autores","Propiedad Intelectual"];

    $year = isset($param['year']) ? $param['year'] : null;

    $publications = \Model\Publicacion::find(null,null,$year);

    $rows= [];

    foreach($publications as $publication){
        $row = [];
        $row[] = $publication->getId();
        $row[] = $publication->getDescripcion();
        $row[] = date("Y",strtotime($publication->getFecha()));
        $row[] = $publication->getRevista()->getDescripcion();

        $authors = "";
        foreach($publication->getParticipantes() as $author){
            $authors.= $author->getNombre() .  " " . $author->getApellido() . "\n";
        }
        $row[] = $authors;
        $row[] = $publication->hasPropiedadIntelectual() ? "SI": "NO";
        $rows[] = $row;
    }


    $filename = "publicaciones";

    if ($year)
        $filename.= "-$year";

    $report = new ReportFileManager($filename);
    $report->setHeader($headers);
    foreach($rows as $row)
        $report->addRow($row);

    $filename = $report->generateFile();
});

$app->get("/report/congress/:s",function($sessionId) use($app,$param){
    $param = $_GET ? $_GET : [];
    $param['session_id'] = $sessionId;
    if (!validateSessionFile($param)) return true;

    //Nombre, Fecha, Tema, Lugar, Patrocinio y autor.
    $headers = ["ID","Nombre","Fecha","Tema","Lugar","Patrocinio","Autor"];

    $year = isset($param['year']) ? $param['year'] : null;

    $congress = \Model\Congreso::find(null,null,$year);

    $rows= [];

    foreach($congress as $c){
        $row = [];
        $row[] = $c->getId();
        $row[] = $c->getNombre();
        $row[] = date("Y",strtotime($c->getFechaCongreso()));
        $row[] = $c->getPonencia();
        $row[] = $c->getLugar();
        $row[] = $c->getPatrocinio()->getName();

        $authors = "";
        foreach($c->getParticipantes() as $author){
            $authors.= $author->getNombre() .  " " . $author->getApellido() . "\n";
        }
        $row[] = $authors;
        $rows[] = $row;
    }

    $filename = "congresos";

    if ($year)
        $filename.= "-$year";

    $report = new ReportFileManager($filename);
    $report->setHeader($headers);
    foreach($rows as $row)
        $report->addRow($row);

    $filename = $report->generateFile();
});


$app->get("/report/projects/:s",function($sessionId) use($app,$param){
    $param = $_GET ? $_GET : [];
    $param['session_id'] = $sessionId;
    if (!validateSessionFile($param)) return true;

    $headers = ["ID","Descripcion","Fecha Aplicacion","Fecha Final",
                "Asesor","Estado Actual","Estado Aplicacion","Investigador",
                "Contrapartida","Aporte","Moneda",
                "Monto Total","Overhead","Software","Patente",
                "Co-Investigadores","Fondos","Unidades Ejecutoras","Instituciones"];

    $year = isset($param['year']) ? $param['year'] : null;
    $estatusAplication = isset($param['application_status']) ? $param['application_status'] : null;
    $investigador = isset($param['researcher']) ? $param['researcher'] : null;

    $projects = \Model\Proyecto::find(null,null,$estatusAplication,null,$year,$investigador);

    $rows= [];

    foreach($projects as $project){
        $row = [];
        $row[] = $project->getId();
        $row[] = $project->getDescripcion();
        $row[] = date("Y",strtotime($project->getFechaAplicacion()));
        $row[] = date("Y",strtotime($project->getFechaInicio()));
        $row[] = $project->getAsesor()->getNombre();
        $row[] = $project->getEstatusActual()->getDescripcion();
        $row[] = $project->getEstatusAplicacion()->getDescripcion();
        $row[] = $project->getInvestigador()->getNombre();
        $row[] = $project->getContraPartida();
        $row[] = $project->getAporte();
        $row[] = $project->getMoneda()->getSimbolo();
        $row[] = $project->getMontoTotal();
        $row[] = $project->getOverhead();
        $row[] = $project->getSoftware() ? "SI" : "NO";
        $row[] = $project->getPatente() ? "SI" : "NO";

        $coresearchers = "";
        foreach($project->getCoInvestigadores() as $coresearch){
            $coresearchers.= $coresearch->getNombre() .  " " . $coresearch->getApellido() . "\n";
        }
        $row[] = $coresearchers;

        $funds = "";
        foreach($project->getFondos() as $fondo){
            $funds.= $fondo->getDescripcion();
        }
        $row[] = $funds;

        $units = "";
        foreach($project->getUnidadesEjecutora() as $unit){
            $units.= $unit->getDescripcion();
            $units.= $unit->isUnidadEjecutora() ? "(Unidad Ejecutora)" : "";
            $units.= $unit->isUnidadSupervisora() ? "(Unidad Supervisora)" : "";
            $units.= "\n";
        }
        $row[] = $units;

        $institutions = "";
        foreach($project->getInstituciones() as $ins){
            $institutions.= $ins->getDescripcion();
            $institutions.= $ins->isPrincipal() ? "(Principal)" : "";
            $institutions.= "\n";
        }
        $row[] = $institutions;

        $rows[] = $row;
    }

    $filename = "proyectos";


    if ($estatusAplication){
        $estatusAplication = \Model\EstatusAplicacion::findById($estatusAplication);
        if ($estatusAplication){
            $name = $estatusAplication->getDescripcion();
            $name = str_replace(" ","",$name);
            $filename.= "-$name";
        }
    }

    if ($investigador){
        $investigador = \Model\Participante::findById($investigador);
        if ($investigador){
            $name = $investigador->getNombre() . " " . $investigador->getApellido();
            $name = str_replace(" ","",$name);
            $filename.= "-$name";
        }
    }

    if ($year)
        $filename.= "-$year";

    $report = new ReportFileManager($filename);
    $report->setHeader($headers);
    foreach($rows as $row)
        $report->addRow($row);

    $filename = $report->generateFile();
});