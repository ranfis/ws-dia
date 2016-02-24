<?php
namespace Model;
require_once("models/model.php");
require_once("models/role.php");

class User extends Model{
	
	const QUERY_FIND = "SELECT U.ID, U.CORREO, U.CLAVE, U.NOMBRE_COMPLETO, U.VER_CODIGO, U.FECHA_LOGIN, U.FECHA_CREACION,U.ROLE_ID,R.NOMBRE 'ROLE_NOMBRE' FROM usuario_aplicacion U INNER JOIN role R on U.ROLE_ID=R.ID";
	
	public  $id;
	public  $correo;
	private $clave;
	public  $nombreCompleto;
	public  $verifCodigo;
	public  $fechaLogin;
	public  $fechaCreacion;

    //@var Estatus
	public  $estatus;

    //@var Role
    private $role;

    /**
     * @param Role $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }



	public function __construct($id= null,$correo = null,$clave = null){
        $this->id       = $id;
        $this->correo   = $correo;
        $this->clave    = $clave;
	}

	/**
	 * Method to get the user by id
     * @return User
	 */
	public static function findById($id,$me = null){
        $users = self::find($id,$me);
        $user = null;
		if (is_array($users) && count($users) == 1)
			$user = $users[0];
		return $user;
	}

    /**
     * Method to get the user by id
     * @return User
     */
    public static function findByEmail($email,$me = null){
        $users = self::find(null,$me,$email);

        $user = null;
        if (is_array($users) && count($users) == 1)
            $user = $users[0];
        return $user;
    }


    /**
     * Method to find the user (with filter)
     * @return array(User)
    */
    public static function find($id = null, $me = null, $email = null,$q = null){

        if (!self::connectDB()) return null;
        $users = null;
        $query = User::QUERY_FIND;

        $dinParams = [];
        $where = "";

        if ($id){
            if ($where) $where.= " AND ";
            $where.= "U.ID=?";
            $dinParams[] = self::getBindParam("i",$id);
        }

        if ($me){
            if ($where) $where.= " AND ";
            $where.= "U.ID!=?";
            $dinParams[] = self::getBindParam("i",$me);
        }

        if ($email){
            if ($where) $where.= " AND ";
            $where.= "U.CORREO=?";
            $dinParams[] = self::getBindParam("s",$email);
        }


        if ($q){
            if ($where) $where.= " AND ";
            if (\StringValidator::isInteger($q)) {
                $where.= "U.ID LIKE ?";
                $dinParams[] = self::getBindParam("i","%$q%");
            }else{
                $where.= "(U.NOMBRE_COMPLETO LIKE ? OR U.CORREO LIKE ?)";
                $dinParams[] = self::getBindParam("i","%$q%");
                $dinParams[] = self::getBindParam("i","%$q%");
            }
        }

        if ($where) $query.= " WHERE $where";
        $query = User::formatQuery($query);


        if (!$result = self::$dbManager->query($query)) return $users;
        self::bindDinParam($result,$dinParams);
        if (!self::$dbManager->executeSql($result)) return $users;

        $users = User::mappingFromDBResult($result);

        return $users;
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
		$bindResult = [];
        $result->bind_result($bindResult['id'],$bindResult['correo'],$bindResult['clave'],$bindResult['nombre_completo'],$bindResult['codigo_verificacion'],$bindResult['fecha_login'],$bindResult['fecha_creacion'],$bindResult['role_id'],$bindResult['role_name']);
		while($result->fetch()){
			$user =  new User();
			$user->id =$bindResult['id'];
			$user->correo = $bindResult['correo'];
			$user->clave = $bindResult['clave'];
			$user->nombreCompleto = $bindResult['nombre_completo'];
			$user->verCodigo = $bindResult['codigo_verificacion'];
			$user->fechaLogin = $bindResult['fecha_login'];
			$user->fechaCreacion = $bindResult['fecha_creacion'];
            $user->role = new Role($bindResult['role_id'],$bindResult['role_name']);
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
		$query = User::formatQuery("UPDATE {PREF_TABLE}usuario_aplicacion SET FECHA_LOGIN= ? WHERE ID=?");
		$lastLogin = date("Y-m-d H:i:s");
		if (!$result = self::$dbManager->query($query)) return;
		$result->bind_param("si",$lastLogin,$user->id);
		return self::$dbManager->executeSql($result);
	}
	
	
	/**
	 * Method to generate the verification code
	 */
	private function generateVerificationCode($admin = false){
        if (!$this->correo || !$this->clave || !$this->role) return false;
        $this->verCode = base64_encode(base64_encode($this->email) . "|||" . $this->password) . "|||"  . base64_encode($this->role->getId());
		return true;
	}
	
	
	/**
	 * Method to mapping the user object to json
	 */
	public function toJson(){
		$result = [];

        /*public $id;
        public $correo;
        private $clave;
        public $nombreCompleto;
        public $verifCodigo;
        public $fechaLogin;
        public $fechaCreacion;*/

        $result['email']  = $this->correo;
        $result['nombre_completo']  = $this->nombreCompleto;
        $result['role'] = [
            "id"=> $this->role->getId(),
            "name"=>$this->role->getName()
        ];

        return $result;
	}

    /**
     * Method to add an user
     * @return boolean if the user is added
    */
    public function add(){
        if (!self::connectDB()) return null;

        $this->estatus = new Estatus(Estatus::ESTATUS_ACTIVED);
        $this->clave = User::cryptPassword($this->clave);

        $query = "INSERT INTO usuario_aplicacion(CORREO, CLAVE, NOMBRE_COMPLETO, ROLE_ID, ESTATUS) VALUES (?,?,?,?,?)";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return null;

        $dinParams = [];
        $dinParams[] = self::getBindParam("s",$this->correo);
        $dinParams[] = self::getBindParam("s",$this->clave);
        $dinParams[] = self::getBindParam("s",$this->nombreCompleto);
        $dinParams[] = self::getBindParam("i",$this->role->getId());
        $dinParams[] = self::getBindParam("i",$this->estatus->getId());

        self::bindDinParam($result,$dinParams);
        if (!self::$dbManager->executeSql($result)) return null;

        $ret = $result->affected_rows > 0;
        if ($ret) $this->id = $result->insert_id;
        return $ret;
    }


    /**
     * Method to update an user
     * @return boolean if the user is updated
     */
    public function update(){
        if (!self::connectDB()) return null;

        $this->estatus = new Estatus(Estatus::ESTATUS_ACTIVED);
        $this->role = new Role(Role::ROLE_REPORT);
        $query = "UPDATE usuario_aplicacion SET NOMBRE_COMPLETO=?,ROLE_ID=? WHERE ID=?";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return null;

        $dinParams = [];
        $dinParams[] = self::getBindParam("s",$this->nombreCompleto);
        $dinParams[] = self::getBindParam("i",$this->getRole()->getId());
        $dinParams[] = self::getBindParam("i",$this->id);

        self::bindDinParam($result,$dinParams);
        if (!self::$dbManager->executeSql($result)) return null;

        return true;

    }

    /**
     * Method to remove an user
     * @return boolean if the user is removed
     */
    public function remove(){
        if (!self::connectDB()) return null;

        $this->estatus = new Estatus(Estatus::ESTATUS_REMOVED);
        $this->role = new Role(Role::ROLE_REPORT);
        $query = "UPDATE usuario_aplicacion SET ESTATUS=? WHERE ID=?";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return null;

        $dinParams = [];
        $dinParams[] = self::getBindParam("i",$this->estatus->getId());
        $dinParams[] = self::getBindParam("i",$this->id);

        self::bindDinParam($result,$dinParams);
        if (!self::$dbManager->executeSql($result)) return null;

        return true;
    }


    /**
     * Method to remove an user
     * @return boolean if the user is removed
     */
    public function changePassword(){
        if (!self::connectDB()) return null;

        $query = "UPDATE usuario_aplicacion SET CLAVE=? WHERE ID=?";
        $query = self::formatQuery($query);
        if (!$result = self::$dbManager->query($query)) return null;

        $this->clave = User::cryptPassword($this->clave);

        $dinParams = [];
        $dinParams[] = self::getBindParam("s",$this->clave);
        $dinParams[] = self::getBindParam("i",$this->id);

        self::bindDinParam($result,$dinParams);
        if (!self::$dbManager->executeSql($result)) return null;

        return true;
    }
}

