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


$app->get(\Config\Routes::REPORT_PROJECT_EARNINGS,function($sessionId) use($app,$param){
    $param = $_GET ? $_GET : [];
    $param['session_id'] = $sessionId;
    if (!validateSessionFile($param)) return true;

    $headers = ["ID","Descripcion","Fecha Aplicacion","Fecha Final","Investigador","Moneda","Monto Total"];

    $year = isset($param['year']) ? $param['year'] : null;

    $projects = \Model\Proyecto::find(null,null,\Model\EstatusAplicacion::ESTATUS_APP_ACEPTADA,null,$year);

    $rows= [];

    $total = 0;

    foreach($projects as $project){
        $row = [];
        $row[] = $project->getId();
        $row[] = $project->getDescripcion();
        $row[] = substr($project->getFechaAplicacion(),0,4);
        $row[] = substr($project->getFechaInicio(),0,4);
        $row[] = $project->getInvestigador()->getNombre() . " " . $project->getInvestigador()->getApellido();
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


$app->get(\Config\Routes::REPORT_PROJECT_OVERHEAD,function($sessionId) use($app,$param){
    $param = $_GET ? $_GET : [];
    $param['session_id'] = $sessionId;
    if (!validateSessionFile($param)) return true;

    $headers = ["ID","Descripcion","Fecha Aplicacion","Fecha Final","Investigador","Moneda","Overhead"];

    $year = isset($param['year']) ? $param['year'] : null;

    $projects = \Model\Proyecto::find(null,null,\Model\EstatusAplicacion::ESTATUS_APP_ACEPTADA,null,$year);

    $rows= [];

    $total = 0;

    foreach($projects as $project){
        $row = [];
        $row[] = $project->getId();
        $row[] = $project->getDescripcion();
        $row[] = substr($project->getFechaAplicacion(),0,4);
        $row[] = substr($project->getFechaInicio(),0,4);
        $row[] = $project->getInvestigador()->getNombre() . " " . $project->getInvestigador()->getApellido();
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


$app->get(\Config\Routes::REPORT_PROJECT_TOTALAMOUNT,function($sessionId) use($app,$param){
    $param = $_GET ? $_GET : [];
    $param['session_id'] = $sessionId;
    if (!validateSessionFile($param)) return true;

    $headers = ["ID","Descripcion","Fecha Aplicacion","Fecha Final","Investigador","Moneda","Monto Total","Overhead"];

    $year = isset($param['year']) ? $param['year'] : null;

    $projects = \Model\Proyecto::find(null,null,\Model\EstatusAplicacion::ESTATUS_APP_ACEPTADA,null,$year);

    $rows= [];

    $total_amount = 0;
    $total_overhead = 0;

    foreach($projects as $project){
        $row = [];
        $row[] = $project->getId();
        $row[] = $project->getDescripcion();
        $row[] = substr($project->getFechaAplicacion(),0,4);
        $row[] = substr($project->getFechaInicio(),0,4);
        $row[] = $project->getInvestigador()->getNombre() . " " . $project->getInvestigador()->getApellido();
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


$app->get(\Config\Routes::REPORT_PROJECT_QUANTITY,function($sessionId) use($app,$param){
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


$app->get(\Config\Routes::REPORT_PUBLICATIONS,function($sessionId) use($app,$param){
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
        $row[] = substr($publication->getFecha(),0,4);
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

$app->get(\Config\Routes::REPORT_CONGRESS,function($sessionId) use($app,$param){
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
        $row[] = substr($c->getFechaCongreso(),0,4);
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


$app->get(\Config\Routes::REPORT_PROJECT,function($sessionId) use($app,$param){
    $param = $_GET ? $_GET : [];
    $param['session_id'] = $sessionId;
    if (!validateSessionFile($param)) return true;

    $headers = ["ID","Descripcion","Fecha Aplicacion","Fecha Final",
        "Asesor","Estado Actual","Estado Aplicacion","Investigador",
        "Contrapartida","Aporte","Moneda",
        "Monto Total","Overhead","Software","Patente",
        "Co-Investigadores","Fondos","Unidades Ejecutoras","Instituciones","Aprobación Comite Etica"];

    $year = isset($param['year']) ? $param['year'] : null;
    $estatusAplication = isset($param['application_status']) ? $param['application_status'] : null;
    $investigador = isset($param['researcher']) ? $param['researcher'] : null;

    $projects = \Model\Proyecto::find(null,null,$estatusAplication,null,$year,$investigador);

    $rows= [];

    foreach($projects as $project){
        $row = [];
        $row[] = $project->getId();
        $row[] = $project->getDescripcion() ? $project->getDescripcion() : "";
        $row[] = substr($project->getFechaAplicacion(),0,4);
        $row[] = substr($project->getFechaInicio(),0,4);
        $row[] = $project->getAsesor() ? $project->getAsesor()->getNombre() : "";
        $row[] = $project->getEstatusActual()->getDescripcion();
        $row[] = $project->getEstatusAplicacion()->getDescripcion();
        $row[] = $project->getInvestigador()->getNombre() . " " . $project->getInvestigador()->getApellido();
        $row[] = $project->getContraPartida() ? $project->getContraPartida() : "";
        $row[] = $project->getAporte() ? $project->getAporte() : "";
        $row[] = $project->getMoneda()->getSimbolo() ? $project->getMoneda()->getSimbolo() : "";
        $row[] = $project->getMontoTotal() ? $project->getMontoTotal() : "";
        $row[] = $project->getOverhead() ? $project->getOverhead() : "";
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
        $row[] = $project->getNumeroAprobacionEtica() ? $project->getNumeroAprobacionEtica() : "" ;

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

$app->get(\Config\Routes::REPORT_ANNUAL,function($sessionId) use($app,$param){
    $param = $_GET ? $_GET : [];
    $param['session_id'] = $sessionId;
    if (!validateSessionFile($param)) return true;

    $headers = [""];

    $year = isset($param['year']) ? $param['year'] : null;

    $projects = \Model\Proyecto::find(null,null,null,null,$year);

    $rows= [];

    $rows[] = ["Proyectos",$year ? $year : ""];
    $rows[] = ["ID","Descripcion","Investigador","Estado Actual","Estado Aplicacion","Moneda","Monto Total","Overhead"];

    foreach($projects as $project){
        $row = [];
        $row[] = $project->getId();
        $row[] = $project->getDescripcion();
        $row[] = $project->getInvestigador()->getNombre() . " " . $project->getInvestigador()->getApellido();
        $row[] = $project->getEstatusActual()->getDescripcion();
        $row[] = $project->getEstatusAplicacion()->getDescripcion();
        $row[] = $project->getMoneda()->getSimbolo();
        $row[] = $project->getMontoTotal();
        $row[] = $project->getOverhead();
        $rows[] = $row;
    }
    $rows[] = ["Total Proyectos",count($projects)];
    $rows[] = [""];
    $rows[] = [""];

    $rows[] = ["Congresos"];
    $rows[] = ["ID","Nombre","Tema","Lugar","Patrocinio","Autor"];

    $congress = \Model\Congreso::find(null,null,$year);

    foreach($congress as $c){
        $row = [];
        $row[] = $c->getId();
        $row[] = $c->getNombre();
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

    $rows[] = ["Total Congresos",count($congress)];
    $rows[] = [""];
    $rows[] = [""];


    $rows[] = ["Publicaciones"];
    $rows[] = ["ID","Nombre","Revista","Autores","Propiedad Intelectual"];

    $publications = \Model\Publicacion::find(null,null,$year);

    foreach($publications as $publication){
        $row = [];
        $row[] = $publication->getId();
        $row[] = $publication->getDescripcion();
        $row[] = $publication->getRevista()->getDescripcion();

        $authors = "";
        foreach($publication->getParticipantes() as $author){
            $authors.= $author->getNombre() .  " " . $author->getApellido() . "\n";
        }
        $row[] = $authors;
        $row[] = $publication->hasPropiedadIntelectual() ? "SI": "NO";
        $rows[] = $row;
    }
    $rows[] = ["Total Publicaciones",count($publications)];

    $filename = "memoria-anual";

    if ($year)
        $filename.= "-$year";

    $report = new ReportFileManager($filename);
    $report->setHeader($headers);
    foreach($rows as $row)
        $report->addRow($row);

    $filename = $report->generateFile();
});