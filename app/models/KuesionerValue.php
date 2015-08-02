<?php

class KuesionerValue extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id_kue_value;

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
     * @var integer
     */
    protected $weight;

    /**
     * Method to set the value of field id_kue_value
     *
     * @param integer $id_kue_value
     * @return $this
     */
    public function setIdKueValue($id_kue_value)
    {
        $this->id_kue_value = $id_kue_value;

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
     * Method to set the value of field weight
     *
     * @param integer $weight
     * @return $this
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Returns the value of field id_kue_value
     *
     * @return integer
     */
    public function getIdKueValue()
    {
        return $this->id_kue_value;
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
     * Returns the value of field weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
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
        return 'kuesioner_value';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return KuesionerValue[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return KuesionerValue
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
