<?php

/**
 * Descrizione di un bijou inserito nel sito
 *
 * @author Mattia Ibba
 */
class Bijou {
    private $codeBijou = 0;
    private $nameBijou;
    private $material;
    private $typeBijou;
    private $originalPrice;
    private $actualPrice;
    private $avaibility;
    
    public function _construct($nameBijou, $material, $typeBijou, $originalPrice, $actualPrice, $avaibility){
        $this->nameBijou = $nameBijou;
        $this->material = $material;
        $this->typeBijou = $typeBijou;
        $this->originalPrice = $originalPrice;
        $this->actualPrice = $actualPrice;
        $this->avaibility = $avaibility;
    }
    
    public function setCode($codeBijou){
        $intVal = filter_var($codeBijou, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(isset($intVal)){
            $this->id = $intVal;
            return true;
        }
        else {
            return false;
        }
    }
    
    public function getCode(){
        return $this->codeBijou;
    }
    
    public function setNameBijou($nameBijou){
        $this->nameBijou = $nameBijou;
    }
    
    public function getNameBijou(){
        return $this->nameBijou;
    }
    
    public function setMaterial($material){
        $this->material = $material;
    }
    
    public function getMaterial(){
        return $this->material;
    }
    
    public function setTypeBijou($typeBijou){
        $this->typeBijou = $typeBijou;
    }
    
    public function getTypeBijou(){
        return $this->typeBijou;
    }
    
    public function setAvaibility($avaibility){
        $intVal = filter_var($avaibility, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if (isset($intVal) && $avaibility >= 0) {
            $this->avaibility = $intVal;
            return true;
        }
        else {
            return false;
        }
    }
    
    public function getAvaibility(){
        return $this->avaibility;
    }
    
    public function setStandardPrice($originalPrice){
        $intVal = filter_var($originalPrice, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if (isset($intVal) && $originalPrice > 0) {
            $this->originalPrice = $intVal;
            return true;
        }
        return false;
    }
    
    public function getStandardPrice(){
        return $this->originalPrice;
    }
    
    public function setActualPrice($actualPrice){
        $intVal = filter_var($actualPrice, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if (isset($intVal) && $actualPrice > 0) {
            $this->actualPrice = $intVal;
            return true;
        }
        return false;
    }
    
    public function getActualPrice(){
        return $this->actualPrice;
    }
}
