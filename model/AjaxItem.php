<?php

/**
 * Classe che rappresenta un oggetto Ajax
 *
 * @author Mattia Ibba
 */
class AjaxItem {
   
    private $id;
    private $messaggio=false;
    
    public function __construct($id) {
        $this->id = $id;
    }
    
    public function setMessaggio($message){
        $this->messaggio = $message;
    }
    
    public function getMessaggio(){
        return $this->messaggio;
    }
    
    public function getId(){
        return $this->id;
    }
}
