<?php

class Kriteria extends \Phalcon\Mvc\Model
{
    
    /**
     *
     * @var integer
     */
    protected $id_kriteria;
    
    /**
     *
     * @var integer
     */
    protected $parent_id;
    
    /**
     *
     * @var string
     */
    protected $kode;
    
    /**
     *
     * @var integer
     */
    protected $active;
    
    /**
     *
     * @var string
     */
    protected $deskripsi;
    
    /**
     * Method to set the value of field id_kriteria
     *
     * @param integer $id_kriteria            
     * @return $this
     */
    public function setIdKriteria($id_kriteria) {
        $this->id_kriteria = $id_kriteria;
        
        return $this;
    }
    
    /**
     * Method to set the value of field parent_id
     *
     * @param integer $parent_id            
     * @return $this
     */
    public function setParentId($parent_id) {
        $this->parent_id = $parent_id;
        
        return $this;
    }
    
    /**
     * Method to set the value of field kode
     *
     * @param string $kode            
     * @return $this
     */
    public function setKode($kode) {
        $this->kode = $kode;
        
        return $this;
    }
    
    /**
     * Method to set the value of field active
     *
     * @param integer $active            
     * @return $this
     */
    public function setActive($active) {
        $this->active = $active;
        
        return $this;
    }
    
    /**
     * Method to set the value of field deskripsi
     *
     * @param string $deskripsi            
     * @return $this
     */
    public function setDeskripsi($deskripsi) {
        $this->deskripsi = $deskripsi;
        
        return $this;
    }
    
    /**
     * Returns the value of field id_kriteria
     *
     * @return integer
     */
    public function getIdKriteria() {
        return $this->id_kriteria;
    }
    
    /**
     * Returns the value of field parent_id
     *
     * @return integer
     */
    public function getParentId() {
        return $this->parent_id;
    }
    
    /**
     * Returns the value of field kode
     *
     * @return string
     */
    public function getKode() {
        return $this->kode;
    }
    
    /**
     * Returns the value of field active
     *
     * @return integer
     */
    public function getActive() {
        return $this->active;
    }
    
    /**
     * Returns the value of field deskripsi
     *
     * @return string
     */
    public function getDeskripsi() {
        return $this->deskripsi;
    }
    
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters            
     * @return Kriteria[]
     */
    public static function find($parameters = null) {
        return parent::find($parameters);
    }
    
    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters            
     * @return Kriteria
     */
    public static function findFirst($parameters = null) {
        return parent::findFirst($parameters);
    }
    
    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource() {
        return 'kriteria';
    }
    
    /**
     * Fetch all active kriteria child record
     *
     * @param Phalcon\Mvc\Model\Query\Builder $qBuilder
     *            $qBuilder
     * @param array $blacklist            
     * @return Phalcon\Mvc\Model\Resultset
     */
    public static function getKriteriaAndSubKriteria($qBuilder, $blacklist = array("")) {
        return $qBuilder->columns(array("c.id_kriteria", "p.deskripsi AS kriteria", "c.deskripsi AS sub_kriteria"))
            ->addFrom("Kriteria", "c")
            ->leftJoin("Kriteria", "c.parent_id = p.id_kriteria", "p")
            ->where("c.parent_id IS NOT NULL AND c.active = true")
            ->notInWhere("c.id_kriteria", $blacklist)
            ->orderBy("kriteria")
            ->getQuery()
            ->execute();
    }
    
    /**
     * Find active kriteria record by child id
     *
     * @param int $id            
     * @param Phalcon\Mvc\Model\Query\Builder $qBuilder            
     * @return Phalcon\Mvc\Model\Resultset\Simple
     */
    public static function getKriteriaAndSubKriteriaById($id, $qBuilder) {
        return $qBuilder->columns(array("c.id_kriteria", "p.deskripsi AS kriteria", "c.deskripsi AS sub_kriteria"))
            ->addFrom("Kriteria", "c")
            ->leftJoin("Kriteria", "c.parent_id = p.id_kriteria", "p")
            ->where("c.id_kriteria = :id: AND c.active = true", array("id" => $id))
            ->getQuery()
            ->getSingleResult();
    }
}
