<?php

class KuesionerAccess extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id_kue_access;

    /**
     *
     * @var integer
     */
    protected $id_kuesioner;

    /**
     *
     * @var integer
     */
    protected $id_responden;

    /**
     *
     * @var string
     */
    protected $status;

    /**
     * Method to set the value of field id_kue_access
     *
     * @param integer $id_kue_access
     * @return $this
     */
    public function setIdKueAccess($id_kue_access)
    {
        $this->id_kue_access = $id_kue_access;

        return $this;
    }

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
     * Method to set the value of field id_responden
     *
     * @param integer $id_responden
     * @return $this
     */
    public function setIdResponden($id_responden)
    {
        $this->id_responden = $id_responden;

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
     * Returns the value of field id_kue_access
     *
     * @return integer
     */
    public function getIdKueAccess()
    {
        return $this->id_kue_access;
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
     * Returns the value of field id_responden
     *
     * @return integer
     */
    public function getIdResponden()
    {
        return $this->id_responden;
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
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('id_kuesioner', 'KuesionerHead', 'id_kuesioner', array('alias' => 'KuesionerHead'));
        $this->belongsTo('id_responden', 'Users', 'id_user', array('alias' => 'Users'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'kuesioner_access';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return KuesionerAccess[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return KuesionerAccess
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
