<?php
include_once 'Utente.php';
include_once 'Carrello.php';
include_once 'Ordine.php';

/**
 * Descrizione di un utente registrato
 *
 * @author Mattia Ibba
 */

class UtenteRegistrato extends Utente{
    //dati personali dell'utente
    
    protected $name;
    protected $surname;
    protected $city;
    protected $cap;
    protected $street;
    protected $streetNumber;
    private $email; //email dell'utente
    
    //dati carta di credito
    
    protected $creditCard;
    protected $creditCardNumber;
    private $cart;
    
    public function _construct($username, $password, $name, $surname, $city, $cap, $street, $streetNumber, $email, $creditCard, $creditCardNumber){
        parent::_construct("registered_user", $username, $password);
        $this->name = $name;
        $this->surname = $surname;
        $this->city = $city;
        $this->cap = $cap;
        $this->street = $street;
        $this->streetNumber = $streetNumber;
        $this->email = $email;                
        $this->creditCard = $creditCard;
        $this->creditCardNumber = $creditCardNumber;
        $this->cart = new Carrello();
    }
    
    public function setName($name){
        $this->name = $name;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function setSurname($surname){
        $this->surname = $surname;
    }
    
    public function getSurname(){
        return $this->surname;
    }
    
    public function setCity($city){
        $this->city = $city;
    }
    
    public function getCity($city){
        return $this->city;
    }
    
    public function setCap($cap){
        if (filter_var($cap, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[0-9]{5}/')))) {
            $this->cap = $cap;
            return true;
        }
        else {
            return false;
        }
    }
    
    public function getCap(){
        return $this->cap;
    }
    
    public function setStreet($street){
        $this->street = $street;
    }
    
    public function getStreet(){
        return $this->street;
    }
    
    public function setStreetNumber($streetNumber){
        $intVal = filter_var($streetNumber, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(isset($intVal)){
            $this->streetNumber = $streetNumber;
            return true;
        }
        return false;
    }
    
    public function getStreetNumber(){
        return $this->streetNumber;
    }
    
    public function changeCreditCard($creditCard, $creditCardNumber){
        if (filter_var($creditCardNumber, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[0-9]{16}/')))) {
            $this->creditCardNumber = $creditCardNumber;
            $this->creditCard = $creditCard;
            return true;
        }
        else {
            return false;
        }
    }
    
    public function getCreditCard(){
        return $this->creditCard;
    }
    
    public function getCreditCardNumber(){
        return $this->creditCardNumber;
    }
    
    public function setEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $this->email = $email;
        return true;
    }
    
    function getEmail() {
        return $this->email;
    }
    
    public function addItemToCart($nameBijou, $material, $typeBijou, $quantity, $originalPrice, $actualPrice, $codeBijou){
        $this->cart->addItemToCart($nameBijou, $material, $typeBijou, $quantity, $originalPrice, $actualPrice, $codeBijou);
    }
    
    public function removeItem($position){
        $this->cart->removeItem($position);
    }
    
    public function getTotal(){
        $this->cart->getTotal();
    }
    
    public function getNumberItems(){
        $this->cart->getNumberObjects();
    }
    
    public function &getList(){
        $list = $this->cart->getList();
        return $list;
    }
    
    public function createOrder($content){
        //data dell'ordine
        $today = getdate();
        $day = $today["mday"];
        $month = $today["mon"];
        $year = $today["year"];
        $mysqli = new mysqli();
        $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

        if($mysqli->connect_errno != 0){
            $idErrore = $mysqli->connect_errno;
            $messaggio = $mysqli->connect_error;
            error_log("Errore nella connessione al server $idErrore : $messaggio", 0);
            return -1;
        }
        else{
            foreach($content as $bijou){
                $idBijou = $bijou->getCode();
                $quantity = $bijou->getAvaibility();
                $price = $bijou->getActualPrice();
                $query = "SELECT avaibility from bijoux WHERE id_bijou=$idBijou";
                $result = $mysqli->query($query);
                if($mysqli->errno > 0){
                    error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                    $mysqli->rollback();
                    $mysqli->close();
                    return -1;
                }
                else{
                    $row = $result->fetch_object();
                    $avaibility = $row->avaibility;
                    if($quantity > $avaibility){
                        $mysqli->rollback();
                        $mysqli->close();
                        return 1;
                    }
                    $idBijou = $row->id_bijou;
                    $query = "UPDATE bijoux SET avaibility -= $pezzi WHERE id_bijou=$idBijou";
                    $result = $mysqli->query($query);
                    if($mysqli->errno > 0){
                        error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                        $mysqli->rollback();
                        $mysqli->close();
                        return -1;
                    }
                    else{
                        $query = "INSERT INTO ordini (`id_order`, 'day', 'month', 'year', `bijoux`) VALUES ($this->id, $day, $month, $year, $idBijou)";
                        $result = $mysqli->query($query);
                        if($mysqli->errno > 0){
                            error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                            $mysqli->rollback();
                            $mysqli->close();
                            return -1;
                        }
                        else{
                            $row = $result->fetch_object();
                            $guadagno = $price * $quantity;
                            $query = "UPDATE admin SET credit_admin = (credit_admin + $guadagno)";
                            $result = $mysqli->query($query);
                            if($mysqli->errno > 0){
                                error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                                $mysqli->rollback();
                                $mysqli->close();
                                return -1;
                            }
                        }
                    }
                }
            }//end foreach
            $mysqli->commit();
            $mysqli->autocommit(true);
            $mysqli->close();
                        
            $this->recharge((-1) * ($this->getTotal()));
            //reset del carrello
            $this->cart->setContent(array());
            $this->cart->setTotal(0);
            $this->cart->setNumberObjects(0);
            return 0;
        }
    }
}
