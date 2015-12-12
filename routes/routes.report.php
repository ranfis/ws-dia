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

    $headers = ["ID","Descripcion","Fecha Aplicacion","Fecha Final","Moneda","Monto Total"];

    $projects = \Model\Proyecto::find();

    $rows= [];

    $total = 0;

    foreach($projects as $project){
        $row = [];
        $row[] = $project->getId();
        $row[] = $project->getDescripcion();
        $row[] = date("Y",strtotime($project->getFechaAplicacion()));
        $row[] = date("Y",strtotime($project->getFechaInicio()));
        $row[] = $project->getMoneda()->getSimbolo();
        $row[] = $project->getMontoTotal();
        $total = $total + $project->getMontoTotal();
        $rows[] = $row;
    }

    $rows[] = ["","","","","",""];
    $rows[] = ["","","","","",$total];


    $report = new ReportFileManager("ganacias-proyectos");
    $report->setHeader($headers);
    foreach($rows as $row)
        $report->addRow($row);

    $filename = $report->generateFile();
});


$app->get("/report/projects/earnings/overhead/:s",function($sessionId) use($app,$param){
    $param = $_GET ? $_GET : [];
    $param['session_id'] = $sessionId;
    if (!validateSessionFile($param)) return true;

    $headers = ["ID","Descripcion","Fecha Aplicacion","Fecha Final","Moneda","Overhead"];

    $projects = \Model\Proyecto::find();

    $rows= [];

    $total = 0;

    foreach($projects as $project){
        $row = [];
        $row[] = $project->getId();
        $row[] = $project->getDescripcion();
        $row[] = date("Y",strtotime($project->getFechaAplicacion()));
        $row[] = date("Y",strtotime($project->getFechaInicio()));
        $row[] = $project->getMoneda()->getSimbolo();
        $row[] = $project->getOverhead();
        $total = $total + $project->getOverhead();
        $rows[] = $row;
    }

    $rows[] = ["","","","","",""];
    $rows[] = ["","","","","",$total];


    $report = new ReportFileManager("ganancias-proyectos-overhead");
    $report->setHeader($headers);
    foreach($rows as $row)
        $report->addRow($row);

    $filename = $report->generateFile();
});