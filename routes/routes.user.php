<?php
require_once("models/user.php");
require_once("models/session.php");

$app->post(Config\Routes::USER_LOGIN,function() use ($app,$param){
    $ws = new Core\Webservice();
    
    $email = isset($param['email']) ? $param['email'] : null;
    $pass = isset($param['pass']) ? $param['pass'] : null;
    
    if ($email === null || !$email) $ws->generate_error(01,"El Correo es requerido");
	else if (!StringValidator::isEmail($email)) $ws->generate_error(02,"El correo es inv&aacute;lido. Verificar que tenga el formato: usuario@dominio.com");
    else if ($pass === null || !$pass) $ws->generate_error(01,"La clave es requerida");
    
    if ($ws->error) {
		echo $ws->output($app);
		return;
	 }
	 
	 if ($user = Model\User::login($email,$pass)){
		 //generate session
		 $session = Model\Session::addSession($user);
		 
		 $result = [
			"session_id"=> $session->id
		 ];
		 
		 $ws->result = $result;
	 }else{
		$ws->generate_error(03,"Correo o clave inv&aacute;lida");
	}

    echo $ws->output($app);
});
