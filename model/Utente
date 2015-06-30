<?php
include_once 'Bijou.php';

/**
 * Descrizione dell'utente generico, la superclasse
 *
 * @author Mattia Ibba
 */

class Utente {
    
    protected $id = 0; //user id
    protected $typeUser = "not_registered";  //il tipo di utente
    protected $username; //username dell'utente
    protected $password; //password dell'utente
    protected $credit = 0;
    
    
    public function _construct($typeUser, $username, $password){
        $this->typeUser = $typeUser;
        $this->username = $username;
        $this->password = $password;
    }
    
    public function setId($id){
        $intVal = filter_var($id, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(isset($intVal)){
            $this->id = $id;
            return true;
        }
        return false;
    }
    
    public function getId(){
        return $this->id;
    }
    
    public function setType($typeUser){
        $this->typeUser = $typeUser;
    }
    
    public function getType(){
        return $this->typeUser;
    }
    
    public function setUsername($username){
        $this->username = $username;
    }
    
    public function getUsername(){
        return $this->username;
    }
    
    public function setPassword($password){
        $this->password = $password;
    }
    
    public function getPassword(){
        return $this->password;
    }
    
    //funzione di ricarica credito per entrambi gli utenti
    public function recharge($import, $registerDB){
        $registerDB = true;
        $intVal = filter_var($import, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if (isset($intVal)) {
            if($registerDB){
                $mysqli = new mysqli();
                $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);
                if($mysqli->connect_errno != 0){
                    $idError = $mysqli->connect_errno;
                    $messagge = $mysqli->connect_error;
                    error_log("Errore nella connessione al server $idError : $messagge", 0);
                    return false;
                }
                else{
                    switch($this->typeUser){
                        case "registered_user" : $query = "SELECT credit FROM utenti WHERE id=$this->id"; 
                            break;
                        case "admin" : $query = "SELECT credit_admin FROM admin WHERE id_admin=$this->id"; 
                            break;
                    }
                    $result = $mysqli->query($query);
                    if($mysqli->errno > 0){
                        error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                        return false;
                    }
                    else{
                        $row = $result->fetch_object();
                        $credit = $row->credit;
                        $newCredit = $credit + $intVal;
                        switch($this->tipo){
                            case "registered_user" : $query = "UPDATE utenti SET credit=$newCredit WHERE id=$this->id"; 
                                break;
                            case "admin" : $query = "UPDATE admin SET credit_admin=$newCredit WHERE id_admin=$this->id"; 
                                break;
                        }
                        $result = $mysqli->query($query);
                        if($mysqli->errno > 0){
                            error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                            return false;
                        }
                        else{
                            $this->credit += $intVal;
                            $mysqli->close();
                            return true;
                        }
                    }
                }
            }
            else{
                $this->credit += $intVal;
                return true;
            }
        }
        else{
            return false;
        }
    }
    
    public function getCredit() {
        return $this->credit;
    }    
}
