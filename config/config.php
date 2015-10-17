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
	public static $db_user = "dia_db_user";
	public static $db_passwd = "diadb_pass_2015";
	
	public static $param_session_id = "SESSION_ID";

	public static $projectName = "DIA WebService";
	
	public static $formatDate = "d-m-Y";
}
