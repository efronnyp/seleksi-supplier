<?php

class Permissions extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id_permission;

    /**
     *
     * @var integer
     */
    protected $granted_role;

    /**
     *
     * @var string
     */
    protected $resource;

    /**
     *
     * @var string
     */
    protected $action;

    /**
     * Method to set the value of field id_permission
     *
     * @param integer $id_permission
     * @return $this
     */
    public function setIdPermission($id_permission)
    {
        $this->id_permission = $id_permission;

        return $this;
    }

    /**
     * Method to set the value of field granted_role
     *
     * @param integer $granted_role
     * @return $this
     */
    public function setGrantedRole($granted_role)
    {
        $this->granted_role = $granted_role;

        return $this;
    }

    /**
     * Method to set the value of field resource
     *
     * @param string $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Method to set the value of field action
     *
     * @param string $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Returns the value of field id_permission
     *
     * @return integer
     */
    public function getIdPermission()
    {
        return $this->id_permission;
    }

    /**
     * Returns the value of field granted_role
     *
     * @return integer
     */
    public function getGrantedRole()
    {
        return $this->granted_role;
    }

    /**
     * Returns the value of field resource
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Returns the value of field action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'permissions';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Permissions[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Permissions
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
