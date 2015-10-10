<?php
namespace Model;

require_once("config/config.php");
require_once("core/database/MysqlManager.php");

use Config\Config;

use MysqlManager;

class Model{
	public static $dbManager;
	
	protected static $prefTable;
	
	/**
	 * Method to connect the database
	 */
	public static function connectDB(){
		if (self::$prefTable == null)
			self::$prefTable = Config::$db_prefTable;
		self::$dbManager = new MysqlManager(Config::$db_host,Config::$db_user,Config::$db_passwd,Config::$db_name);
		return self::$dbManager->connectDb();
	}
	
	/**
	 * Method to format query
	 */
	protected static function formatQuery($query){
		if (self::$prefTable == null) self::$prefTable = Config::$db_prefTable;
		$query = str_replace("{PREF_TABLE}",self::$prefTable,$query);
		return $query;
	}
	
}
?>
