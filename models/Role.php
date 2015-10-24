<?php
namespace Model;

require_once("models/model.php");

class Role extends Model{

    const ROLE_ADMIN = 1;
    const ROLE_REPORT = 2;

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
}