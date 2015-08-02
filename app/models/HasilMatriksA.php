<?php

class HasilMatriksA extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id_matriks_a;

    /**
     *
     * @var integer
     */
    protected $id_kue_krit;

    /**
     *
     * @var integer
     */
    protected $id_responden;

    /**
     *
     * @var double
     */
    protected $value;

    /**
     * Method to set the value of field id_matriks_a
     *
     * @param integer $id_matriks_a
     * @return $this
     */
    public function setIdMatriksA($id_matriks_a)
    {
        $this->id_matriks_a = $id_matriks_a;

        return $this;
    }

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
     * Method to set the value of field value
     *
     * @param double $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Returns the value of field id_matriks_a
     *
     * @return integer
     */
    public function getIdMatriksA()
    {
        return $this->id_matriks_a;
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
     * Returns the value of field id_responden
     *
     * @return integer
     */
    public function getIdResponden()
    {
        return $this->id_responden;
    }

    /**
     * Returns the value of field value
     *
     * @return double
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('id_kue_krit', 'KuesionerChain', 'id_kue_krit', array('alias' => 'KuesionerChain'));
        $this->belongsTo('id_responden', 'Users', 'id_user', array('alias' => 'Users'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'hasil_matriks_a';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return HasilMatriksA[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return HasilMatriksA
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
