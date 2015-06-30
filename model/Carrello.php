<?php
include_once 'UtenteRegistrato.php';
include_once 'Bijou.php';

/**
 * Descrizione del carrello dell'utente
 *
 * @author Mattia Ibba
 */

class Carrello {
    protected $idCart; //id del carrello, che sarà uguale all'id dell'utente
    protected $listObjects;
    protected $numberObjects = 0;
    protected $subTotal = 0;


    public function _construct(){
        $this->listObjects = array();
    }
    
    public function setIdCart($idCart){
        $intVal = filter_var($idCart, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(isset($intVal)){
            $this->idCart = $intVal;
            return true;
        }
        else {
            return false;
        }
    }
    
    public function getIdCart(){
        return $this->idCart;
    }
    
    public function addItemToCart($nameBijou, $material, $typeBijou, $originalPrice, $actualPrice, $quantity, $codeBijou){
        $this->listObjects[$this->numberObjects] = new Bijou($nameBijou, $material, $typeBijou, $originalPrice, $actualPrice, $quantity);
        $this->listObjects[$this->numberObjects]->setCode($codeBijou);
        $this->subTotal += $actualPrice * $quantity;
        $this->numberObjects++;
    }
    
    public function removeItem($position){
        if($position <= $this->numberObjects)
        {
            $priceBijou = $this->listObjects[$position]->getActualPrice();
            $numberSameItems = $this->listObjects[$position]->getAvaibility();
            $this->subTotal -= $priceBijou * $numberSameItems;
            array_splice($this->listObjects, $position, 1);
            $this->numberObjects--;
        }
    }
    
    public function &getList(){
        return $this->listObjects;
    }
    
    public function setTotal($subTotal){
        $this->subTotal = $subTotal;
    }
    
    public function getTotal(){
        return $this->subTotal;
    }
    
    public function getNumberObjects(){
        return $this->numberObjects;
    }
}
