<?php
namespace Model;

require_once("models/model.php");


class User extends Model{
	
	const QUERY_FIND = "SELECT U.ID, U.CORREO, U.CLAVE, U.NOMBRE_COMPLETO, U.VER_CODIGO, U.FECHA_LOGIN, U.FECHA_CREACION FROM {PREF_TABLE}usuario_aplicacion U";
	
	public $id;
	public $correo;
	private $clave;
	public $nombreCompleto;
	public $verifCodigo;
	public $fechaLogin;
	public $fechaCreacion;
	
	
	public function __construct(){
		
	}
	
	/**
	 * Method to get the user by id
	 */
	public function findById($id){
		if (!self::connectDB()) return null;
		$user = new User();
		$query = User::formatQuery(self::QUERY_FIND);
		$query.= " WHERE U.ID=?";
		if (!$result = self::$dbManager->query($query)) return $user;
		$result->bind_param("i",$id);
		if (!self::$dbManager->executeSql($result)) return $user;
		
		$users = User::mappingFromDBResult($result);
		
		if (is_array($users) && count($users) == 1)
			$user = $users[0];
		return $user;
	}
	
	/**
	 * Method to make login to the system
	 * @param $email 
	 * @param $pass password without encryption
	 */
	public static function login($email,$pass){
		if (!self::connectDB()) return null;
		$user = null;
		$query = User::formatQuery(self::QUERY_FIND);
		$query.= " WHERE U.CORREO=?";
		
		if (!$result = User::$dbManager->query($query)) return $user;
		$result->bind_param("s",$email);
		if (!self::$dbManager->executeSql($result)) return $user;
		
		$users = self::mappingFromDBResult($result);
		
		if (is_array($users) && count($users) == 1){
			$user = $users[0];
			
			if (!password_verify($pass,$user->clave)) $user = null;
	
			//set last login
			User::lastLogin($user);
			//end: set last login
		}
		return $user;
	}
	
	
	/**
	 * Method ot mapping from database result to list users object
	 */
	private static function mappingFromDBResult(&$result,&$users = array()){
		if ($users === null || !$users) $users = array();
			
		//SELECT `ID`, `CORREO`, `CLAVE`, `NOMBRE_COMPLETO`, `VER_CODIGO`, `FECHA_LOGIN`, `FECHA_CREACION` FROM `usuario_aplicacion` WHERE 1
		$result->bind_result($rId,$rCorreo,$rClave,$rNombreCompleto,$rVerCodigo,$rFechaLogin,$rFechaCreacion);
		while($result->fetch()){
			$user =  new User();
			$user->id =$rId;
			$user->correo = $rCorreo;
			$user->clave = $rClave;
			$user->nombreCompleto = $rNombreCompleto;
			$user->verCodigo = $rVerCodigo;
			$user->fechaLogin = $rFechaLogin;
			$user->fechaCreacion = $rFechaCreacion;
			$users[] = $user;
		}
		
		return $users;
	}
	
	
	/**
	 *Method to crypt the password
	 */
	private static function cryptPassword($password){
		return password_hash($password,PASSWORD_BCRYPT);
	}
	
	/**
	 * Method to generate ethe last login of the user
	 */
	private  static function lastLogin($user){
		if (!self::connectDB()) return null;
		$query = User::formatQuery("UPDATE {PREF_TABLE}USUARIO_APLICACION SET FECHA_LOGIN= ? WHERE ID=?");
		$lastLogin = date("Y-m-d H:i:s");
		if (!$result = self::$dbManager->query($query)) return;
		$result->bind_param("si",$lastLogin,$user->id);
		return self::$dbManager->executeSql($result);
	}
	
	
	/**
	 * Method to generate the verification code
	 */
	private function generateVerificationCode($admin = false){
		if ($admin){
			if (!$this->email || !$this->password) return false;
			$this->verCode = base64_encode(base64_encode($this->email) . "|||" . $this->password);
		}else {
			if (!$this->email || !$this->password || !$this->productType || !$this->productType->id) return false;
			$this->verCode = base64_encode(base64_encode($this->email) . "|||" . $this->password . "|||" . base64_encode($this->productType->id));
		}
		return true;
	}
	
	
	/**
	 * Method to mapping the user object to json
	 */
	private function mappingToJson(){
			
	}



}

