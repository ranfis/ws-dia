<?php
namespace Model;

require_once("models/model.php");

class Session extends Model{
	
	const SESSION_DATE_EXPIRE = 30; //minutes
	
	//INSERT INTO `sesion`(`SESION_ID`, `OPCION_ADICIONAL`, `FECHA_EXPIRACION`, `FECHA_CREACION`, `USUARIO_ID`) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5])
	
	public $id;
	public $opcionAdicional;
	public $fechaExpiracion;
	public $fechaCreacion;
	public $user;
	
	/**
	 * Method to generate session
	 */
	private static function generateSessionId(){
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}
	
	/**
		 * Method to generate session
		 */
		public static function addSession(&$user){
			if ($user == null || $user->id == null) return null;
			if (!self::connectDB()) return null;
			
			$query = "INSERT INTO sesion(SESION_ID, OPCION_ADICIONAL, FECHA_EXPIRACION, FECHA_CREACION, USUARIO_ID) VALUES (?,?,NOW() + INTERVAL " . self::SESSION_DATE_EXPIRE . " MINUTE,NOW(),?)";
			$query = Session::formatQuery($query);
			
			if (!$result = self::$dbManager->query($query)) return null;

			$session = new Session();
			$session->id = Session::generateSessionId();
			//$session->opcionAdicional = $aditionalInfo;
			$session->opcionAdicional = $session->opcionAdicional ? json_encode($session->opcionAdicional) : null;
			
			$result->bind_param("ssi",$session->id,$session->opcionAdicional,$user->id);
			if (!self::$dbManager->executeSql($result)) return null;
			if ($result->affected_rows <= 0) $session = null;
			return $session;
		}


		/**
		 * Method to logout session
		 */
		public static function logoutSession($sessionId){
			if ($userId == null) return null;
			if (!self::connectDB()) return null;
			$query = "DELETE FROM " . Session::$prefTable . "sesion WHERE SESION_ID=?;";
			if (!$result = self::$dbManager->query($query)) return null;

			$result->bind_param("s",$sessionId);

			if (!self::$dbManager->executeSql($result)) return null;
			return $result->affected_rows > 0;
		}

}

