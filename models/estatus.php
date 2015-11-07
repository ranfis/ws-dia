<?php
namespace model;
require_once("models/model.php");


class Estatus extends Model{

    private $id;
    private $name;

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
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



    public function __construct($id = null)
    {
        $this->id = $id;
    }

}
