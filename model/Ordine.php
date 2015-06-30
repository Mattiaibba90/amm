<?php
include_once 'Carrello.php';

/**
 * Descrizione di un ordine
 *
 * @author Mattia Ibba
 */

class Ordine extends Carrello {
    
    private $id; //quando impostato avrà lo stesso id del carrello
    
    private $date; //data dell'ordine
    
    public function __construct($id, $day, $month, $year) {
        parent::__construct();
        $this->setDate($day, $month, $year);
        $this->id = $id;
    }
    
    public function setDate($day, $month, $year){
        $intDay = filter_var($day, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(isset($intDay) && $intDay <= 31 && $intDay > 0){
            $intMonth = filter_var($month, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            if(isset($intMonth) && $intMonth <= 12 && $month > 0){
                $intYear = filter_var($year, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                $today = getdate();
                $currentYear = $today["year"];
                if(isset($intYear) && $intYear <= $currentYear && $intYear > 0){
                    $this->date = $intDay . "-" . $intMonth . "-" . $intYear;
                    return true;
                }
            }
        }
        else{
            return false;
        }
    }
    
    //restituisce la data sotto forma di stringa
    public function getDate(){
        return $this->date;
    }
    
    public function setId($id){
        $intVal = filter_var($id, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(!isset($intVal)){
            return false;
        }
        $this->id = $intVal;
        return true;
    }
    
    public function getId(){
        return $this->id;
    }
    
}
