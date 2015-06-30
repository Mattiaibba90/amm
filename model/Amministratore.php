<?php
include_once 'Utente.php';
include_once 'UtenteRegistrato.php';

/**
 * Descrizione della classe Amministratore
 *
 * @author Mattia Ibba
 */
class Amministratore extends Utente{
    
    public function _construct($username, $password){
        parent::_construct("admin", $username, $password);
    }
    
    //funzioni sql
    public function modifyUser($id, $username, $password, $name, $surname, $city, $street, $streetNumber, $cap, $email){
        $mysqli = new mysqli();
        $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);
        if($mysqli->connect_errno != 0){
            $idError = $mysqli->connect_errno;
            $message = $mysqli->connect_error;
            error_log("Errore nella connessione al server $idError : $message", 0);
            $errore = '<li>Errore nella connessione al server</li>';
            return $errore;
        }//errore nella connessione del db
        else{
            $stmt = $mysqli->stmt_init();
            $query = "SELECT * FROM utenti WHERE mail=? AND id != ?";
            $stmt->prepare($query);
            $stmt->bind_param("si", $email, $id);
            $stmt->execute();
            if($stmt->errno > 0){
                error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                $mysqli->close();
                $errore = '<li>Si e\' verificato un errore durante la procedura, si prega di contattare un amministratore</li>';
                return $errore;
            }
            else{
                $stmt->store_result();
                if($stmt->num_rows > 0){
                    $mysqli->close();
                    $errore = '<li>L\'e-mail scelta e\' gia\' utilizzata.</li>';
                    return $errore;
                }
                else{
                    $stmt = $mysqli->stmt_init();
                    $query = "UPDATE utenti SET username=? , password=? , name=? , surname=?, city=?, street=?, streetNumber=?, cap=?, mail=?  WHERE id=?";
                    $stmt->prepare($query);
                    $stmt->bind_param("ssssssiisi", $username, $password, $name, $surname, $city, $street, $streetNumber, $cap, $email, $id);
                    $stmt->execute();
                    if($stmt->errno > 0){
                        error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                        $mysqli->close();
                        $errore = '<li>Si e\' verificato un errore durante la procedura, si prega di contattare un amministratore</li>';
                        return $errore;
                     }
                    else{
                        $mysqli->close();
                        return NULL;
                    }
                }
            }
        }
    }
    
    //funzione per modificare un bijou
    public function modifyBijou($codeBijou, $nameBijou, $material, $typeBijou, $st_price, $act_price, $avaibility){
        $mysqli = new mysqli();
        $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);
        if($mysqli->connect_errno != 0){
            $idErrore = $mysqli->connect_errno;
            $msg = $mysqli->connect_error;
            error_log("Errore nella connessione al server $idErrore : $msg", 0);
            $return = '<li>Errore nella connessione al server</li>';
            return $return;
        }//errore di connessione
        else{
            $stmt = $mysqli->stmt_init();
            $query = "SELECT avaibility FROM bijoux WHERE id_bijou = ?";
            $stmt->prepare($query); //seleziono la quantità di orecchini disponibile
            $stmt->bind_param("i", $codeBijou);
            $stmt->execute();
            if($stmt->errno > 0){
                error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                $mysqli->rollback();
                $mysqli->close();
                $return = '<li>Si e\' verificato un errore durante la procedura, si prega di contattare un amministratore del sito</li>';
                return $return;
            }
            else{
                $stmt->bind_result($oldAvaibility);
                $stmt->fetch();
                $diffAvaibility = $avaibility - $oldAvaibility;
                $stmt = $mysqli->stmt_init();
                $query = "UPDATE bijoux SET avaibility=?, price=? WHERE id_bijou = ?";
                $stmt->prepare($query); //aggiorno la sua disponibilità
                $stmt->bind_param("ifi", $avaibility, $act_price, $codeBijou);
                $stmt->execute();
                if($stmt->errno > 0){
                    error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                    $mysqli->rollback();
                    $mysqli->close();
                    $return = '<li>Si e\' verificato un errore durante la procedura, si prega di contattare un amministratore</li>';
                    return $return;
                }
                else{
                    $stmt = $mysqli->stmt_init();
                    $query = "UPDATE bijou SET nameBijou = ?, material = ?, typeBijou = ?, st_price = ?, act_price = ?, avaibility = (avaibility + ?)  WHERE id_bijou = ?";
                    $stmt->prepare($query);
                    $stmt->bind_param("sssffii", $nameBijou, $material, $typeBijou, $st_price, $act_price, $diffAvaibility, $codeBijou);
                    $stmt->execute();//aggiorno il bijou selezionato
                    if($stmt->errno > 0){
                        error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                        $mysqli->rollback();
                        $mysqli->close();
                        $return = '<li>Si e\' verificato un errore durante la procedura, si prega di contattare un amministratore</li>';
                        return $return;
                    }
                    else{
                        $mysqli->commit();
                        $mysqli->autocommit(true);
                        $mysqli->close();
                        return null;
                    }
                }
            }
        }
    }
    
    public function addBijou($codeBijou, $nameBijou, $material, $typeBijou, $st_price, $act_price, $avaibility){
        $newBijou = false;
        $mysqli = new mysqli();
        $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

        if($mysqli->connect_errno != 0){
            $idErrore = $mysqli->connect_errno;
            $msg = $mysqli->connect_error;
            error_log("Errore nella connessione al server $idErrore : $msg", 0);
            $return = '<li>Errore nella connessione al server</li>';
            return $return;
        }
        else{
            if($newBijou){
                if($codeBijou){
                    //inizio una transazione
                    $mysqli->autocommit(false);
                    $stmt = $mysqli->stmt_init();
                    $query = "SELECT * FROM bijoux WHERE id_bijou = ?";
                    $stmt->prepare($query);
                    $stmt->bind_param("i", $codeBijou);
                    $stmt->execute();
                    if($stmt->errno > 0){
                        error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                        $mysqli->close();
                        $return = '<li>Si e\' verificato un errore durante la procedura, si prega di contattare un amministratore</li>';
                        return $return;
                    }
                    else{
                        $stmt->store_result();
                        if($stmt->num_rows < 1){
                            $mysqli->close();
                            $return = '<li>Non esiste bijou con questo id</li>';
                            return $return;
                        }
                        else{
                            $stmt = $mysqli->stmt_init();
                            $query = "INSERT INTO bijoux (id_bijou, name_bijou, material, type_bijou, st_price, act_price, avaibility) VALUES (?,?,?,?,?,?,?)";
                            $stmt->prepare($query);
                            $stmt->bind_param("isssffi", $codeBijou, $nameBijou, $material, $typeBijou, $st_price, $act_price, $avaibility);
                            $stmt->execute();
                            if($stmt->errno > 0){
                                error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                                $mysqli->rollback();
                                $mysqli->close();
                                $return = '<li>Si e\' verificato un errore durante la procedura, si prega di contattare un amministratore</li>';
                                return $return;            
                            }
                            else{
                                $mysqli->commit();
                                $mysqli->autocommit(true);
                                $mysqli->close();
                                return null;
                            }
                        }
                    }
                }
            }
        }
    }
}
