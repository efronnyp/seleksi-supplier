<?php

class KuesionerHead extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id_kuesioner;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $status;

    /**
     *
     * @var integer
     */
    protected $active;

    /**
     * Method to set the value of field id_kuesioner
     *
     * @param integer $id_kuesioner
     * @return $this
     */
    public function setIdKuesioner($id_kuesioner)
    {
        $this->id_kuesioner = $id_kuesioner;

        return $this;
    }

    /**
     * Method to set the value of field name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Method to set the value of field status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Method to set the value of field active
     *
     * @param integer $active
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Returns the value of field id_kuesioner
     *
     * @return integer
     */
    public function getIdKuesioner()
    {
        return $this->id_kuesioner;
    }

    /**
     * Returns the value of field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value of field status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the value of field active
     *
     * @return integer
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->hasMany('id_kuesioner', 'KuesionerChain', 'id_kuesioner', array('alias' => 'KuesionerChain'));
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters            
     * @return KuesionerHead[]
     */
    public static function find($parameters = null) {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters            
     * @return KuesionerHead
     */
    public static function findFirst($parameters = null) {
        return parent::findFirst($parameters);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'kuesioner_head';
    }

}
