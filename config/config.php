<?php
namespace Config;

require "config/routes.php";

/**
 * Class Configuration
 * @author Fernando Perez
*/
class Config{
	public static $db_host = "localhost";
	public static $db_port = "3301";
	public static $db_name = "diadb";
	public static $db_prefTable = "";
	public static $db_user = "diadbuser";
	public static $db_passwd = "D1aDbUs3r2016**";
	
	public static $param_session_id = "SESSION_ID";

	public static $projectName = "DIA WebService";
	
	public static $formatDate = "d-m-Y";
}

date_default_timezone_set("America/Santo_Domingo");
