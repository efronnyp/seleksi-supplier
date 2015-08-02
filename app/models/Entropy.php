<?php

class Entropy extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id_entropy;

    /**
     *
     * @var integer
     */
    protected $id_kue_krit;

    /**
     *
     * @var double
     */
    protected $entropy_value;

    /**
     *
     * @var double
     */
    protected $dispersi_value;

    /**
     *
     * @var double
     */
    protected $weight_value;

    /**
     * Method to set the value of field id_entropy
     *
     * @param integer $id_entropy
     * @return $this
     */
    public function setIdEntropy($id_entropy)
    {
        $this->id_entropy = $id_entropy;

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
     * Method to set the value of field entropy_value
     *
     * @param double $entropy_value
     * @return $this
     */
    public function setEntropyValue($entropy_value)
    {
        $this->entropy_value = $entropy_value;

        return $this;
    }

    /**
     * Method to set the value of field dispersi_value
     *
     * @param double $dispersi_value
     * @return $this
     */
    public function setDispersiValue($dispersi_value)
    {
        $this->dispersi_value = $dispersi_value;

        return $this;
    }

    /**
     * Method to set the value of field weight_value
     *
     * @param double $weight_value
     * @return $this
     */
    public function setWeightValue($weight_value)
    {
        $this->weight_value = $weight_value;

        return $this;
    }

    /**
     * Returns the value of field id_entropy
     *
     * @return integer
     */
    public function getIdEntropy()
    {
        return $this->id_entropy;
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
     * Returns the value of field entropy_value
     *
     * @return double
     */
    public function getEntropyValue()
    {
        return $this->entropy_value;
    }

    /**
     * Returns the value of field dispersi_value
     *
     * @return double
     */
    public function getDispersiValue()
    {
        return $this->dispersi_value;
    }

    /**
     * Returns the value of field weight_value
     *
     * @return double
     */
    public function getWeightValue()
    {
        return $this->weight_value;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('id_kue_krit', 'KuesionerChain', 'id_kue_krit', array('alias' => 'KuesionerChain'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'entropy';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Entropy[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Entropy
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
