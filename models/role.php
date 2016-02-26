<?php
namespace Model;

use Config\Routes;

require_once("models/model.php");

class Role extends Model{

    const ROLE_SUPERADMIN   = 1;
    const ROLE_ADMIN        = 2;
    const ROLE_REPORT       = 3;

    private $id;
    private $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    public function __construct($id = null,$name=  null)
    {
        $this->id = $id;
        $this->name = $name;
    }


    /**
     * Method to get all the privileges available for the web service
     * @return array
    */
    public static function getPrivileges(){

        $privileges = [];

        $allRoles = [Role::ROLE_SUPERADMIN,Role::ROLE_ADMIN,Role::ROLE_REPORT];
        $adminRoles = [Role::ROLE_SUPERADMIN,Role::ROLE_ADMIN];


        $privileges[Routes::USER_LOGIN]     = $allRoles;
        $privileges[Routes::USER_SUMMARY]   = $allRoles;
        $privileges[Routes::USER_LOGOUT]    = $allRoles;

        $privileges[Routes::PROFILE_CHANGE_PASSWORD]   = $allRoles;
        $privileges[Routes::PROFILE_UPDATE_INFO]    = $allRoles;

        $privileges[Routes::ADM_USER_ADD]                   = [Role::ROLE_SUPERADMIN];
        $privileges[Routes::ADM_USER_UPDATE]                = [Role::ROLE_SUPERADMIN];
        $privileges[Routes::ADM_USER_DEL]                   = [Role::ROLE_SUPERADMIN];
        $privileges[Routes::ADM_USER_LIST]                  = [Role::ROLE_SUPERADMIN];
        $privileges[Routes::ADM_USER_CHANGE_PASSWORD]       = [Role::ROLE_SUPERADMIN];


        $privileges[Routes::DASHBOARD]    = $allRoles;

        $privileges[Routes::SPONSOR_ADD]        = $adminRoles;
        $privileges[Routes::SPONSOR_UPDATE]     = $adminRoles;
        $privileges[Routes::SPONSOR_LIST]       = $adminRoles;
        $privileges[Routes::SPONSOR_DEL]        = $adminRoles;


        $privileges[Routes::PARTICIPANTS_ADD]       = $adminRoles;
        $privileges[Routes::PARTICIPANTS_UPDATE]    = $adminRoles;
        $privileges[Routes::PARTICIPANTS_LIST]      = $allRoles;
        $privileges[Routes::PARTICIPANTS_DEL]       = $adminRoles;

        $privileges[Routes::CONGRESS_ADD]       = $adminRoles;
        $privileges[Routes::CONGRESS_UPDATE]    = $adminRoles;
        $privileges[Routes::CONGRESS_LIST]      = $adminRoles;
        $privileges[Routes::CONGRESS_DEL]       = $adminRoles;


        $privileges[Routes::JOURNAL_ADD]       = $adminRoles;
        $privileges[Routes::JOURNAL_UPDATE]    = $adminRoles;
        $privileges[Routes::JOURNAL_DEL]       = $adminRoles;
        $privileges[Routes::JOURNAL_LIST]      = $adminRoles;


        $privileges[Routes::ARTICLE_ADD]       = $adminRoles;
        $privileges[Routes::ARTICLE_UPDATE]    = $adminRoles;
        $privileges[Routes::ARTICLE_DEL]       = $adminRoles;
        $privileges[Routes::ARTICLE_LIST]      = $adminRoles;


        $privileges[Routes::FUND_ADD]      = $adminRoles;
        $privileges[Routes::FUND_UPDATE]   = $adminRoles;
        $privileges[Routes::FUND_DEL]      = $adminRoles;
        $privileges[Routes::FUND_LIST]     = $adminRoles;


        $privileges[Routes::PUBLICATION_ADD]     = $adminRoles;
        $privileges[Routes::PUBLICATION_UPDATE]     = $adminRoles;
        $privileges[Routes::PUBLICATION_DELETE]     = $adminRoles;
        $privileges[Routes::PUBLICATION_LIST]     = $adminRoles;


        $privileges[Routes::EXECUTING_UNIT_ADD]     = $adminRoles;
        $privileges[Routes::EXECUTING_UNIT_UPDATE]     = $adminRoles;
        $privileges[Routes::EXECUTING_UNIT_DEL]     = $adminRoles;
        $privileges[Routes::EXECUTING_UNIT_LIST]     = $adminRoles;


        $privileges[Routes::INSTITUTION_ADD]     = $adminRoles;
        $privileges[Routes::INSTITUTION_UPDATE]     = $adminRoles;
        $privileges[Routes::INSTITUTION_DEL]     = $adminRoles;
        $privileges[Routes::INSTITUTION_LIST]     = $adminRoles;

        $privileges[Routes::CURRENCY_GET]     = $adminRoles;


        $privileges[Routes::PROJECT_ADD]     = $adminRoles;
        $privileges[Routes::PROJECT_UPDATE]     = $adminRoles;
        $privileges[Routes::PROJECT_DEL]     = $adminRoles;
        $privileges[Routes::PROJECT_LIST]     = $adminRoles;


        $privileges[Routes::STATUS_APPLICATION_LIST]     = $adminRoles;
        $privileges[Routes::CURRENT_STATUS_LIST]     = $adminRoles;

        $privileges[Routes::REPORT_PROJECT_EARNINGS]    = $allRoles;
        $privileges[Routes::REPORT_PROJECT_OVERHEAD]    = $allRoles;
        $privileges[Routes::REPORT_PROJECT_TOTALAMOUNT] = $allRoles;
        $privileges[Routes::REPORT_PROJECT_QUANTITY]    = $allRoles;
        $privileges[Routes::REPORT_PUBLICATIONS]        = $allRoles;
        $privileges[Routes::REPORT_CONGRESS]            = $allRoles;
        $privileges[Routes::REPORT_PROJECT]             = $allRoles;
        $privileges[Routes::REPORT_ANNUAL]              = $allRoles;

        return $privileges;
    }


    /**
     * Method to verify if a role has permission
     * @return boolean
     */
    public static function hasPermission($resource,$role){
        if (!$resource || !$role) return false;
        $priv = self::getPrivileges();
        return isset($priv[$resource]) && in_array($role,$priv[$resource]);
    }
}
