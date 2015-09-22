<?php
include_once basename(__DIR__) . '/../model/Utente.php';
include_once basename(__DIR__) . '/../model/Accounts.php';
include_once basename(__DIR__) . '/../view/PageContent.php';
include_once basename(__DIR__) . '/../model/AjaxItem.php';

// Controller generico

class Controller {
    public function __construct() {}

    public function handleInput(&$request, &$session) {
        
// creo il descrittore della vista
        $pageContent = new PageContent();
        $ajaxMode = 0;
        
        // imposto la pagina
        $pageContent->setPage($request['page']);
        
        if (isset($request["subpage"])) {
                switch ($request["subpage"]) {
                    case 'register':
                        $pageContent->setSubPage('register');
                        break;
                    
                    /*case 'ricercaAvanzata':
                        $pageContent->setSubPage('ricercaAvanzata');
                        break;*/
                    
                    case 'risultatiRicercaAvanzata':
                        $pageContent->setSubPage('risultatiRicercaAvanzata');
                        break;                    
                    
                     default:
                         $pageContent->setSubPage('home');
                }
        }
        
        
        if (isset($request["cmd"])) {
            switch ($request["cmd"]) {
                case 'login':
                    if(isset ($request['username']))
                        $username = $request['username'];
                    else
                        $username = '';
                    if(isset($request['password']))
                        $password = $request['password'];
                    else
                        $password = '';
                    $this->login($pageContent, $username, $password);
                    if ($this->loggedIn())
                        $user = $_SESSION['user'];
                    break;
                /*case 'ricerca':
                    $message = array();
                    $this->showLoginPage($pageContent);
                    $risultati = $this->ricerca($pageContent, $user, $request, $message);
                    $risultatiRicerca = $risultati['risultatiRicerca'];
                    $ric_limiteSuperiore = $risultati['limiteSuperiore'];
                    $ric_limiteInferiore = $risultati['limiteInferiore'];
                    $ric_cursore = $risultati['cursore'];
                    $pattern = urlencode($risultati['pattern']);
                    if(count($risultatiRicerca) == 0)
                        $message[] = '<li>La ricerca non ha prodotto risultati</li>';
                    $pageContent->setSubPage('ricerca');
                    $this->creaFeedbackUtente($message, $pageContent, "Ricerca effettuata con successo!");
                    break;*/
                    
                    case 'ricerca_avanzata':
                    $msg = array();
                    $this->showLoginPage($pageContent);
                    $risultati = $this->ricercaAvanzata($pageContent, $user, $request, $msg);
                    $risultatiRicerca = $risultati['risultatiRicerca'];
                    $ric_limiteSuperiore = $risultati['limiteSuperiore'];
                    $ric_limiteInferiore = $risultati['limiteInferiore'];
                    $ric_cursore = $risultati['cursore'];
                    $parametriPost = $risultati['parametriPost'];
                    if(count($risultatiRicerca) == 0)
                        $msg[] = '<li>La ricerca non ha prodotto risultati</li>';
                    $pageContent->setSubPage('risultatiRicercaAvanzata');
                    $this->creaFeedbackUtente($msg, $pageContent, "Ricerca effettuata con successo!");
                    break;
                    
                    case 'register':
                        
                        $validi=0;
                        $answer = array();
                        if(isset($request['username'])){
                            if (!filter_var($request['username'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[a-zA-Z0-9]{5,10}/')))) {
                                $ajaxItem = new ajaxItem('username');
                                $ajaxItem->setMessage('L\'username non e\' valido, inserisci un username con lunghezza compresa fra 5 e 10 caratteri (non simboli)');
                                $answer['username'] = $ajaxItem;
                            }
                            elseif($this->usernameDisponibile($request['username']) == 1){
                                $validi++;
                            }
                            elseif($this->usernameDisponibile($request['username']) == 0){
                                $ajaxItem = new ajaxItem('username');
                                $ajaxItem->setMessage('Questo username non e\' disponibile, scegline un altro');
                                $answer['username'] = $ajaxItem;
                            }
                            elseif($this->usernameDisponibile($request['username']) == -1){
                                $ajaxItem = new AjaxItem('username');
                                $ajaxItem->setMessage('Si e\' verificato un errore durante l\'operazione, si prega di riprovare');
                                $answer['username'] = $ajaxItem;
                            }
                            
                        }
                        if(isset($request['password'])){
                            if (!filter_var($request['password'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[a-zA-Z0-9]{7,14}/')))) {
                                $ajaxItem = new AjaxItem('password');
                                $ajaxItem->setMessage('La password non e\' valida, inserisci una password con lunghezza compresa fra 7 e 14 caratteri (non simboli)');
                                $answer['password'] = $ajaxItem;
                            }
                            else
                                $validi++;
                        }
                        if(isset($request['name'])){
                            if (!filter_var($request['name'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[a-zA-Z]{3,10}/')))) {
                                $ajaxItem = new AjaxItem('name');
                                $ajaxItem->setMessage('Il nome non e\' valido, inserisci un nome con lunghezza compresa fra 3 e 10 lettere');
                                $answer['name'] = $ajaxItem;
                            }
                            else
                                $validi++;
                        }
                        if(isset($request['surname'])){
                            if (!filter_var($request['surname'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[a-zA-Z]{3,10}/')))) {
                                $ajaxItem = new AjaxItem('surname');
                                $ajaxItem->setMessage('Il cognome non e\' valido, inserisci un cognome con lunghezza compresa fra 3 e 10 lettere');
                                $answer['surname'] = $ajaxItem;
                            }
                            else
                                $validi++;
                            
                        }
                        if(isset($request['mail'])){
                            if (!filter_var($request['mail'], FILTER_VALIDATE_EMAIL)) {
                                $ajaxItem = new AjaxItem('mail');
                                $ajaxItem->setMessage('L\'indirizzo e-mail utilizzato non e\' valido');
                                $answer['mail'] = $ajaxItem;
                            }
                            elseif($this->emailDisponibileUtente($request['mail']) == 1){
                                $validi++;
                            }
                            elseif($this->emailDisponibileUtente($request['mail']) == 0){
                                $ajaxItem = new AjaxItem('mail');
                                $ajaxItem->setMessage('L\'indirizzo e-mail scelto non e\' disponibile, scegline un altro');
                                $answer['mail'] = $ajaxItem;
                            }
                            elseif($this->emailDisponibileUtente($request['mail']) == -1){
                                $ajaxItem = new AjaxItem('mail');
                                $ajaxItem->setMessage('Si e\' verificato un errore durante l\'operazione, si prega di riprovare');
                                $answer['mail'] = $ajaxItem;
                            }
                               
                        }
                        if(isset($request['creditCard'])){
                            if (!filter_var($request['creditCard'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[a-zA-Z]{4,15}/')))) {
                                $ajaxItem = new AjaxItem('creditCard');
                                $ajaxItem->setMessage('Inserisci una marca con lunghezza compresa fra 4 e 15 lettere');
                                $answer['creditCard'] = $ajaxItem;
                            }
                            else
                                $validi++;
                        }
                        if(isset($request['creditCardNumber'])){
                            if (!filter_var($request['creditCardNumber'], FILTER_VALIDATE_INT)) {
                                $ajaxItem = new AjaxItem('creditCardNumber');
                                $ajaxItem->setMessage('Il numero della carta di credito non e\' valido');
                                $answer['creditCardNumber'] = $ajaxItem;
                            }
                            else
                                $validi++;
                        }
                        if(isset($request['city'])){
                            if (!filter_var($request['city'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[a-zA-Z]{3,20}/')))) {
                                $ajaxItem = new AjaxItem('city');
                                $ajaxItem->setMessage('La citta\' non e\' valida, inserisci una citta\' con lunghezza compresa fra 3 e 20 lettere');
                                $answer['city'] = $ajaxItem;
                            }
                            else
                                $validi++;
                        }
                        if(isset($request['cap'])){
                            if (!filter_var($request['cap'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[0-9]{5}/')))) {
                                $ajaxItem = new AjaxItem('cap');
                                $ajaxItem->setMessage('Il cap non e\' valido, inserisci una cap corretto');
                                $answer['cap'] = $ajaxItem;
                            }
                            else
                                $validi++;
                        }
                        if(isset($request['street'])){
                            if (!filter_var($request['street'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[a-zA-Z]{5,20}/')))) {
                                $ajaxItem = new AjaxItem('street');
                                $ajaxItem->setMessage('La via non e\' valida, inserisci una via con lunghezza compresa fra 5 e 20 lettere');
                                $answer['street'] = $ajaxItem;
                            }
                            else
                                $validi++;
                        }
                        if(isset($request['streetNumber'])){
                            if (!filter_var($request['streetNumber'], FILTER_VALIDATE_INT)) {
                                $ajaxItem = new AjaxItem('streetNumber');
                                $ajaxItem->setMessage('Il numero civico non puo\' contenere lettere');
                                $answer['streetNumber'] = $ajaxItem;
                            }
                            else
                                $validi++;
                        }
                        
                        if($validi == 11){
                            $message = array();
                            $this->registraUtente($request, $message);
                            $this->creaFeedbackUtente($message, $pageContent, "Utente registrato con successo!");
                            $this->creaFeedbackUtente($message, $pageContent, "Puoi gia' da ora accedere con le tue credenziali");
                            $this->showLoginPage($pageContent);
                        }
                        else
                            $ajaxMode=1;
                        
                        break;
                    
                default : $this->showLoginPage($pageContent);
            }
        }
        else{
            if($this->loggedIn()) {
                $user = $_SESSION['user'];
                $this->showHomeUtente($pageContent);
            }
            else{
                $this->showLoginPage($pageContent);// utente non autenticato
            }
        }
        
        $ultimiArrivi = Controller::loadUltimiArrivi();
        switch($ajaxMode){
            case 0:
                require basename(__DIR__) . '/../view/master.php';
                break;
            case 1:
                include_once basename(__DIR__) . '/../view/ajax/register.php';
                break;
        }
    }

    public function &getSessione() {
        return $_SESSION;
    }

    protected function loggedIn() {
        $autenticato = false;
        if(isset($_SESSION) && array_key_exists('user', $_SESSION))
            $autenticato = true;
        return $autenticato;
    }

    protected function showLoginPage($pageContent) {
        //caricamento di tutti i singoli pezzi della pagina
        $pageContent->setTitle("I bijoux filati di Mimi");
        $pageContent->setHeader(basename(__DIR__) . '/../view/login/header.php');
        $pageContent->setSideBar(basename(__DIR__) . '/../view/login/sidebar.php');
        $pageContent->setContent(basename(__DIR__) . '/../view/login/content.php');
    }

    protected function showHomeUtente($pageContent) {
        //caricamento di tutti i singoli pezzi della pagina
        $pageContent->setTitle("I bijoux filati di Mimi");
        $pageContent->setHeader(basename(__DIR__) . '/../view/utente/header.php');
        $pageContent->setSideBar(basename(__DIR__) . '/../view/utente/sidebar.php');
        $pageContent->setContent(basename(__DIR__) . '/../view/utente/content.php');
    }

    protected function showHomeAdmin($pageContent) {
        //caricamento di tutti i singoli pezzi della pagina
        $pageContent->setTitle("I bijoux filati di Mimi");
        $pageContent->setHeader(basename(__DIR__) . '/../view/admin/header.php');
        $pageContent->setSideBar(basename(__DIR__) . '/../view/admin/sidebar.php');
        $pageContent->setContent(basename(__DIR__) . '/../view/admin/content.php');
    }

     /**
     * Seleziona quale pagina mostrare in base al tipo dell'utente corrente
     */
    protected function showHome($pageContent) {
        $user = $_SESSION['user'];
        switch ($user->getType()) {
            case "registered_user":
                $this->showHomeUtente($pageContent);
                break;

            case "admin":
                $this->showHomeAdmin($pageContent);
                break;
        }
    }

    /**
     * Procedura di autenticazione 
     */
    protected function login($pageContent, $username, $password) {
        //caricamento dati dell'utente
        $user = Accounts::loadUser($username, $password);
        if (isset($user)) {
            // utente autenticato
            $_SESSION['user'] = $user;
            $this->showHomeUtente($pageContent);
        }
        else{
            $pageContent->setErrorMessage("Username o password errati");
            $this->showLoginPage($pageContent);
        }
    }

    /**
     * Procedura di logout
     */
    protected function logout($pageContent) {
        // reset array $_SESSION
        $_SESSION = array();
        // termino la validita' del cookie di sessione
        if (session_id() != '' || isset($_COOKIE[session_name()])) {
            // imposto il termine di validita' del cookie a 15gg fa
            setcookie(session_name(), '', time() - 1296000, '/');
        }
        // distruggo il file di sessione
        session_destroy();
        $this->showLoginPage($pageContent);
    }

    /**
     * Crea un messaggio di feedback per l'utente 
     */
    protected function creaFeedbackUtente(&$msg, $pageContent, $okMessage) {
        if (count($msg) > 0){
            $error = "Si sono verificati i seguenti errori \n<ul>\n";
            foreach ($msg as $message) {
                $error = $error . $message . "\n";
            }
            $error = $error . "\n</ul>\n";
            $pageContent->setErrorMessage($error);
        }//end if
        else
            $pageContent->setConfirmMessage($okMessage);
    }
    
    protected function loadUltimiArrivi(){
        $mysqli = new mysqli();
        $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

        if($mysqli->connect_errno != 0){
            $idErrore = $mysqli->connect_errno;
            $messaggio = $mysqli->connect_error;
            error_log("Errore nella connessione al server $idErrore : $messaggio", 0);
        }
        else{
            $query = "SELECT COUNT(distinct(bijoux.id_bijou)) as numeroBijouPresenti FROM bijoux";
            $result = $mysqli->query($query);
            if($mysqli->errno > 0){
                error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                $mysqli->close();
            }
            else{
                $row = $result->fetch_object();
                $numeroBijouPresenti = $row->numeroBijouPresenti;
                $limiteSuperiore = $numeroBijouPresenti;
                $limiteInferiore = $limiteSuperiore - Settings::BIJOUX_HOME;
                if($limiteInferiore < 0)
                    $limiteInferiore = 0;
                $query = 'SELECT distinct(bijoux.id_bijou), bijoux.* FROM bijoux LIMIT ' . $limiteInferiore . ',' . $limiteSuperiore;
                $result = $mysqli->query($query);
                if($mysqli->errno > 0){
                    error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                    $mysqli->close();
                }
                else{
                    $result = $mysqli->query($query);
                    $ultimiArrivi = array();
                    while ($row = $result->fetch_object()) {
                        $bijou = new Bijou($row->name_bijou, $row->material, $row->type_bijou, $row->st_price, $row->actual_price, $row->avaibility);
                        $bijou->setCode($row->id_bijou);
                        $ultimiArrivi[] = $bijou;
                    }
                    $mysqli->close();
                    return $ultimiArrivi;
                }
            }
        }
    }
    
    
    protected function &ricercaAvanzata(&$pageContent, &$user, &$request, &$msg){
        
        $mysqli = new mysqli();
        $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);
        
        if($mysqli->connect_errno != 0){
            $idErrore = $mysqli->connect_errno;
            $msg = $mysqli->connect_error;
            error_log("Errore nella connessione al server $idErrore : $msg", 0);
            $msg[] = '<li>Errore nella connessione al server</li>';
        }
        else{
            $stmt = $mysqli->stmt_init();
            $condizioni = "";
            $tipi = "";
            $numeroCondizioni = 0;
            $parametri = array();
            $parametriPost = "";
             
            if(isset($request['typeBijou']) && ($request['typeBijou'] != "")){
                $parametri[] = "%" . $request['typeBijou'] . "%";
                $parametriPost = $parametriPost . '&amp;typeBijou=' . urlencode($request['typeBijou']);
                $condizioni = $condizioni . "bijoux.typeBijou LIKE ? ";
                $tipi = $tipi . "s";
                $numeroCondizioni++;
            }  
            
            if($numeroCondizioni == 0){
                $mysqli->close();
                $msg[] = '<li>Non puoi effettuare la ricerca se non specifichi nulla!</li>';
            }
            else{
                $query = "SELECT COUNT(distinct(bijoux.idBijou)) as numeroMaxRisultati FROM bijoux WHERE " . $condizioni;
                $stmt->prepare($query);
                $stmt->bind_param($tipi, $parametri[0]);                
                $stmt->execute();
                
                if($stmt->errno > 0){
                    error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                    $mysqli->close();
                    $msg[] = '<li>Si e\' verificato un errore nella ricerca</li>';
                }
                else{
                   $stmt->store_result();
                   if($stmt->num_rows > 0){
                        $stmt->bind_result($numeroMaxRisultati);
                        $stmt->fetch();

                        $intLimiteInferiore = filter_var($request['ric_limiteInferiore'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                        $intLimiteSuperiore = filter_var($request['ric_limiteSuperiore'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                        $intCursore = filter_var($request['ric_cursore'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                        if(isset($intLimiteInferiore) && isset($intLimiteSuperiore)){
                            if(isset($intCursore)){
                                if($intCursore == 0){
                                    $limiteInferiore = $intLimiteInferiore - UtenteController::MAX_RIGHE_TABELLA;
                                    $limiteSuperiore = $intLimiteSuperiore - UtenteController::MAX_RIGHE_TABELLA;
                                }
                                else{
                                    $limiteInferiore = $intLimiteInferiore + UtenteController::MAX_RIGHE_TABELLA;
                                    $limiteSuperiore = $intLimiteSuperiore + UtenteController::MAX_RIGHE_TABELLA;
                                }
                            }
                            else{
                                $limiteInferiore = $intLimiteInferiore + UtenteController::MAX_RIGHE_TABELLA;
                                $limiteSuperiore = $intLimiteSuperiore + UtenteController::MAX_RIGHE_TABELLA;
                            }
                        }
                        else{
                            $limiteInferiore = 0;
                            $limiteSuperiore = UtenteController::MAX_RIGHE_TABELLA;
                        }
                        if($limiteSuperiore < UtenteController::MAX_RIGHE_TABELLA)
                            $limiteSuperiore = UtenteController::MAX_RIGHE_TABELLA;
                        if($limiteInferiore < 0)
                            $limiteInferiore = 0;
                        if($limiteSuperiore > $numeroMaxRisultati)
                            $limiteSuperiore = $numeroMaxRisultati;
                        if($limiteInferiore > $numeroMaxRisultati)
                            $limiteInferiore = $numeroMaxRisultati - UtenteController::MAX_RIGHE_TABELLA;
                        if(($limiteSuperiore - $limiteInferiore) != UtenteController::MAX_RIGHE_TABELLA)
                            $limiteInferiore = $limiteSuperiore - UtenteController::MAX_RIGHE_TABELLA;
                        if($limiteInferiore < 0)
                            $limiteInferiore = 0;
                        $stmt = $mysqli->stmt_init();
                        $query = "SELECT distinct(bijoux.idBijou), bijoux.nameBijou, bijoux.material, bijoux.typeBijou, bijoux.avaibility FROM bijoux WHERE " . $condizioni . "LIMIT ?,?";
                        $stmt->prepare($query);
                        
                        $tipi = $tipi . "ii";
                        $stmt->bind_param($tipi, $parametri[0], $limiteInferiore, $limiteSuperiore);
                        
                        $stmt->execute();
                        if($stmt->errno > 0){
                            error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                            $mysqli->close();
                            $msg[] = '<li>Si e\' verificato un errore nella ricerca</li>';
                        }
                        else{
                            $stmt->store_result();
                            $stmt->bind_result($idBijou, $name_bijou, $material, $typeBijou, $avaibility);
                            
                            $risultatiRicerca = array();
                            while($stmt->fetch()){
                                $bijou = new Bijou($name_bijou, $material, $typeBijou, 0, 0, $avaibility);
                                $bijou->setId($idBijou);
                                $bijouGiaPresente = false;
                                foreach($risultatiRicerca as $bijouTrovato){
                                    if($bijouTrovato->getId() == $bijou->getId())
                                        $bijouGiaPresente = true;
                                }
                                if(!$bijouGiaPresente)
                                    $risultatiRicerca[] = $bijou;
                           }//end while
                            $mysqli->close();
                            $return = array();
                            $return['risultatiRicerca'] = $risultatiRicerca;
                            $return['limiteInferiore'] = $limiteInferiore;
                            $return['limiteSuperiore'] = $limiteSuperiore;
                            $return['cursore'] = $intCursore;
                            $return['parametriPost'] = $parametriPost;
                            return $return;
                        }//end else
                   }
                   else
                        $msg[] = '<li>La ricerca non ha prodotto risultati</li>';
                }
            }
        }
    }    
    
    private function registraUtente(&$request, &$message){
        if (filter_var($request['mail'], FILTER_VALIDATE_EMAIL)) {
            if(filter_var($request['cap'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[0-9]{5}/')))){
                $intStreetNumber = filter_var($request['streetNumber'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                if(isset($intStreetNumber)){
                    $intCreditCardNumber = filter_var($request['creditCardNumber'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    if(isset($intCreditCardNumber)){
                        $mysqli = new mysqli();
                        $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

                        if($mysqli->connect_errno != 0){
                            $idErrore = $mysqli->connect_errno;
                            $message = $mysqli->connect_error;
                            error_log("Errore nella connessione al server $idErrore : $message", 0);
                            $message[] = '<li>Errore nella connessione al server</li>';
                        }
                        else{
                            $stmt = $mysqli->stmt_init();
                            $query = "SELECT * FROM utenti WHERE username=? AND email=? ";
                            $stmt->prepare($query);
                            $stmt->bind_param("ss", $request['username'], $request['mail']);
                            $stmt->execute();
                            if($stmt->errno > 0){
                                error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                                $message[] = '<li>Si e\' verificato un errore durante la procedura, si prega di contattare un amministratore del sito</li>';
                            }
                            else{
                                $stmt->store_result();
                                if($stmt->num_rows > 0){
                                    $mysqli->close();
                                    $message[] = '<li>L\'username o l\'e-mail da te scelti sono gia\' utilizzati da un altro utente, si prega di inserire dei valori differenti</li>';
                                }
                                else{
                                    $stmt = $mysqli->stmt_init();
                                    $query = "INSERT INTO utenti (`username`, `password`, `name`, `surname`, `city`, `street`, `streetNumber`, 'cap', 'mail', 'creditCard', 'creditCardNumber') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                    $stmt->prepare($query);
                                    $stmt->bind_param("ssssssiissi", $request['username'], $request['password'], $request['name'], $request['surname'], $request['city'], $request['street'], $intStreetNumber, $request['cap'], $request['mail'], $request['creditCard'], $intCreditCardNumber);
                                    $stmt->execute();
                                    if($stmt->errno > 0){
                                        error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                                        $mysqli->close();
                                        $message[] = "<li>Si e\' verificato un errore durante la procedura di registrazione</li>";
                                    }
                                    else{
                                        $stmt->fetch();
                                        $mysqli->close();
                                    }
                                }
                            }
                        }
                    }
                    else
                        $message[] = '<li>Il numero della carta di credito non &egrave; nel formato corretto</li>';
                }
                else
                    $message[] = '<li>Il numero civico non &egrave; nel formato corretto</li>';
            }
            else
                $message[] = '<li>Il CAP specificato non &egrave; nel formato corretto</li>';
        }
        else
            $message[] = '<li>L\'email specificata non &egrave; nel formato corretto</li>';
    }

    protected function usernameDisponibile($username){
        $mysqli = new mysqli();
        $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

        if($mysqli->connect_errno != 0){
            $idErrore = $mysqli->connect_errno;
            $message = $mysqli->connect_error;
            error_log("Errore nella connessione al server $idErrore : $message", 0);
            return -1;
        }
        else{
            $stmt = $mysqli->stmt_init();
            $query = "SELECT * FROM utenti WHERE username=?";
            $stmt->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            if($stmt->errno > 0){
                error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                return -1;
            }
            else{
                $stmt->store_result();
                if($stmt->num_rows > 0){
                    $mysqli->close();
                    return 0;
                }
                else{
                    $mysqli->close();
                    return 1;
                }
            }
        }          
    }
    
    protected function emailDisponibileUtente($email){
        $mysqli = new mysqli();
        $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

        if($mysqli->connect_errno != 0){
            $idErrore = $mysqli->connect_errno;
            $message = $mysqli->connect_error;
            error_log("Errore nella connessione al server $idErrore : $message", 0);
            return -1;
        }
        else{
            $stmt = $mysqli->stmt_init();
            $query = "SELECT * FROM utenti WHERE mail=?";
            $stmt->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if($stmt->errno > 0){
                error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                return -1;
            }
            else{
                $stmt->store_result();
                if($stmt->num_rows > 0){
                    $mysqli->close();
                    return 0;
                }
                else{
                    $mysqli->close();
                    return 1;
                }
            }
        }
    }
}
