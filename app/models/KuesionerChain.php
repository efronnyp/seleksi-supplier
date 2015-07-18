<?php

class KuesionerChain extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id_kue_krit;

    /**
     *
     * @var integer
     */
    protected $id_kuesioner;

    /**
     *
     * @var integer
     */
    protected $id_kriteria;

    /**
     *
     * @var integer
     */
    protected $sequence_no;

    /**
     *
     * @var integer
     */
    protected $active;

    /**
     * Method to set the value of field id_kue_krit
     *
     * @param integer $id_kue_krit
     * @return $this
     */
    public function setIdKueKrit($id_kue_krit)
    {
        $this->id_kue_krit = $id_kue_krit;

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
     * Method to set the value of field id_kriteria
     *
     * @param integer $id_kriteria
     * @return $this
     */
    public function setIdKriteria($id_kriteria)
    {
        $this->id_kriteria = $id_kriteria;

        return $this;
    }

    /**
     * Method to set the value of field sequence_no
     *
     * @param integer $sequence_no
     * @return $this
     */
    public function setSequenceNo($sequence_no)
    {
        $this->sequence_no = $sequence_no;

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
     * Returns the value of field id_kue_krit
     *
     * @return integer
     */
    public function getIdKueKrit()
    {
        return $this->id_kue_krit;
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
     * Returns the value of field id_kriteria
     *
     * @return integer
     */
    public function getIdKriteria()
    {
        return $this->id_kriteria;
    }

    /**
     * Returns the value of field sequence_no
     *
     * @return integer
     */
    public function getSequenceNo()
    {
        return $this->sequence_no;
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
    public function initialize()
    {
        $this->belongsTo('id_kuesioner', 'KuesionerHead', 'id_kuesioner', array('alias' => 'KuesionerHead'));
        $this->belongsTo('id_kriteria', 'Kriteria', 'id_kriteria', array('alias' => 'Kriteria'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'kuesioner_chain';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return KuesionerChain[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return KuesionerChain
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
