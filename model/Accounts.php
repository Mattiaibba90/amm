<?php
include_once 'Utente.php';
include_once 'Amministratore.php';
include_once 'UtenteRegistrato.php';
include_once basename(__DIR__) . '/../Settings.php';

/**
 * Classe Factory
 *
 * @author Mattia Ibba
 */
class Accounts {
    public function __construct() {}
    
    public static function loadUser($username, $password) {
        $mysqli = new mysqli();
        $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);
        if($mysqli->connect_errno != 0){
            $idErrore = $mysqli->connect_errno;
            $message = $mysqli->connect_error;
            error_log("Errore nella connessione al server $idErrore : $message", 0);
            return NULL;
        } //se non si connette, segnala errore
        else{
            $stmt = $mysqli->stmt_init(); //prepared statemant
            $query = "SELECT * FROM utenti WHERE username=? AND password=?";
            $stmt->prepare($query);
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            if($stmt->errno > 0){
                error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
            } //non esegue la query tra gli utenti
            else{
               $stmt->store_result();
               if($stmt->num_rows > 0){
                    $stmt->bind_result($id, $username, $password, $credit, $name, $surname, $city, $street, $streetNumber, $cap, $email, $creditCard, $creditCardNumber);
                    $stmt->fetch();
                    $return = new UtenteRegistrato($username, $password, $name, $surname, $city, $cap, $street, $streetNumber, $creditCard, $creditCardNumber, $email);
                    $return->setId($id);
                } //se è un utente registrato                    
                else{
                    $query = "SELECT * FROM admin WHERE username_admin=? AND password_admin=?";
                    $stmt->prepare($query);
                    $stmt->bind_param("ss", $username, $password);
                    $stmt->execute();
                    if($stmt->errno > 0){
                        error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                    }
                    else{
                        $stmt->store_result();
                        if($stmt->num_rows > 0){
                            $stmt->bind_result($id, $username, $password);
                            $stmt->fetch();
                            $return = new Admin($username, $password);
                            $return->setId($id);
                        }//se è un admin
                    }
                }
            }
        }
        $mysqli->close();
        return $return;
    }
}
        
?>
