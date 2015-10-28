<?php
require 'Slim/Slim.php';
require "config/config.php";
require "core/Webservice.php";
require "lib/StringValidator.php";

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$body = $app->request->getBody();
$param = json_decode($body,true);

// GET route
$app->get('/',function () { echo ""; } );

//include
include "routes/routes.user.php";
include "routes/routes.admin.php";

$app->notFound(function () use($param,$app) {
   $ws = new Core\Webservice(false);
   $ws->generate_error(404,"Pagina no encontrada");
   echo $ws->output($app);
});

$app->error(function (\Exception $e) use($param,$app){
   $ws = new Core\Webservice(false);
   $ws->generate_error(500,"Error interno del servidor");
   echo $ws->output($app);
});

$app->run();
