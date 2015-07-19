<?php
include_once 'Controller.php';

/**
 * Controller che gestisce l'amministratore dell'applicazione
 * @author Mattia Ibba
 */
class AdminController extends Controller {
    
    public function __construct() {
        parent::__construct();
    }

    public function handleInput(&$request, &$session) {

        $pageContent = new PageContent();

        $pageContent->setPage($request['page']);
        $ajaxMode = 0;

        // gestion dei comandi
        // tutte le variabili che vengono create senza essere utilizzate 
        // direttamente in questo switch, sono quelle che vengono poi lette
        // dalla vista, ed utilizzano le classi del modello
        if (!$this->loggedIn()) {
            //l'utente non e' autenticato, viene rimandato alla home
            $this->showLoginPage($pageContent);
        }//end if 
        else{
            $user = $session['user'];
            
            // verifico quale sia la sottopagina da servire ed imposto 
            // il descrittore della vista per caricare i "pezzi" delle pagine corretti
            // tutte le variabili che vengono create senza essere utilizzate 
            // direttamente in questo switch, sono quelle che vengono poi lette
            // dalla vista, ed utilizzano le classi del modello
            if (isset($request["subpage"])) {
                switch ($request["subpage"]) {
                    
                    case 'credito':
                        $pageContent->setSubPage('credito');
                        break;
                    
                    //pagina dove poter eseguire tutte le operazioni possibili sui clienti
                    case 'listaUtenti':
                        $mysqli = new mysqli();
                        $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

                        if($mysqli->connect_errno != 0){
                            $idErrore = $mysqli->connect_errno;
                            $messaggio = $mysqli->connect_error;
                            error_log("Errore nella connessione al server $idErrore : $messaggio", 0);
                            $message = array();
                            $message[] = "<li>Errore nella connessione $messaggio</li>";
                            $this->creaFeedbackUtente($message, $pageContent, "");
                        }
                        else{
                            $query = "SELECT * FROM utenti";
                            $result = $mysqli->query($query);
                            if($mysqli->errno > 0)
                                error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                            else{
                                if($result->num_rows > 0){
                                    $numeroMaxRisultati = $result->num_rows;

                                    $intLimiteInferiore = filter_var($request['limiteInferiore'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                                    $intLimiteSuperiore = filter_var($request['limiteSuperiore'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                                    $intCursore = filter_var($request['cursore'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
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
                                    $query = "SELECT * FROM clienti LIMIT $limiteInferiore, $limiteSuperiore";
                                    $result = $mysqli->query($query);
                                    if($mysqli->errno > 0)
                                        error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                                    else{
                                        $clienti = array();
                                        while($row = $result->fetch_object()){
                                            $utente = new Utente($row->username, $row->password, $row->nome, $row->cognome, $row->citta, $row->cap, $row->street, $row->streetNumber, $row->creditCard, $row->numeroCartaCredito, $row->email);
                                            $utente->setId($row->id);
                                            $utente->ricarica($row->credito, false);
                                            $clienti[] = $utente;
                                        }
                                        $mysqli->close();
                                        $pageContent->setSubPage('listaUtenti');
                                    }
                                }
                                else{
                                    $message = array();
                                    $message[] = "<li>Non sono presenti utenti registrati sul sito</li>";
                                    $this->creaFeedbackUtente($message, $pageContent, "");
                                }
                            }
                        }//end else errore connessione
                    break;
                    
                    case 'modificaUtente':
                        $intIdUtente = filter_var($request['utenteSelezionato'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                        if(isset($intIdUtente)){
                            $mysqli = new mysqli();
                            $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

                            if($mysqli->connect_errno != 0){
                                $idErrore = $mysqli->connect_errno;
                                $messaggio = $mysqli->connect_error;
                                error_log("Errore nella connessione al server $idErrore : $messaggio", 0);
                                $message = array();
                                $message[] = "<li>Errore nella connessione $messaggio</li>";
                                $this->creaFeedbackUtente($message, $pageContent, "");
                            }
                            else{
                                $query = "SELECT * FROM utenti where id=$intIdUtente";
                                $result = $mysqli->query($query);
                                if($mysqli->errno > 0)
                                    error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                                else{
                                    if($result->num_rows > 0){
                                        $row = $result->fetch_object();
                                        $utente = new UtenteRegistrato($row->username, $row->password, $row->name, $row->surname, $row->city, $row->street, $row->streetNumber, $row->cap, $row->mail, $row->creditCard, $row->creditCardNumber);
                                        $utente->setId($row->id);
                                        $utente->recharge($row->credit, false);
                                        $mysqli->close();
                                        $pageContent->setSubPage('modificaUtente');
                                    }
                                    else{
                                        $message = array();
                                        $message[] = "<li>L'id utilizzato non corrisponde a nessun utente registrato sul sito</li>";
                                        $this->creaFeedbackUtente($message, $pageContent, "");
                                    }
                                }
                            }
                        }
                        else{
                            $message = array();
                            $message[] = '<li>L\'id utilizzato deve essere un numero</li>';
                            $this->creaFeedbackUtente($message, $pageContent, "");
                        }
                        break;
                    
                    case 'listaBijoux':
                        $mysqli = new mysqli();
                        $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

                        if($mysqli->connect_errno != 0){
                            $idErrore = $mysqli->connect_errno;
                            $messaggio = $mysqli->connect_error;
                            error_log("Errore nella connessione al server $idErrore : $messaggio", 0);
                            $message = array();
                            $message[] = "<li>Errore nella connessione $messaggio</li>";
                            $this->creaFeedbackUtente($message, $pageContent, "");
                        }
                        else{
                            $query = "SELECT * from bijoux ORDER BY bijoux.id_bijou";
                            $result = $mysqli->query($query);
                            if($mysqli->errno > 0)
                                error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                            else{
                                if($result->num_rows > 0){
                                    $numeroMaxRisultati = $result->num_rows;

                                    $intLimiteInferiore = filter_var($request['limiteInferiore'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                                    $intLimiteSuperiore = filter_var($request['limiteSuperiore'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                                    $intCursore = filter_var($request['cursore'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
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
                                    $query = "SELECT * from bijoux LIMIT $limiteInferiore, $limiteSuperiore";
                                    $result = $mysqli->query($query);
                                    if($mysqli->errno > 0)
                                        error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                                    else{
                                        $bijoux = array();
                                        while($row = $result->fetch_object()){
                                            $bijou = new Bijou($row->name_bijou, $row->material, $row->type_bijou, $row->st_price, $row->act_price, $row->avaibility);
                                            $bijou->setCode($row->id_bijou);
                                            $bijoux[] = $bijou;
                                        }
                                                $mysqli->close();
                                                $pageContent->setSubPage('listaBijoux');
                                            }
                                        }
                                else{
                                    $message = array();
                                    $message[] = "<li>Non sono presenti bijoux in vendita da visualizzare!</li>";
                                    $this->creaFeedbackUtente($message, $pageContent, "");
                                }
                            }
                        }//end else errore connessione
                    break;
                    
                    case 'modificaBijou':
                        $intId = filter_var($request['id_bijou'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                        if(isset($intId)){
                            $mysqli = new mysqli();
                            $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

                            if($mysqli->connect_errno != 0){
                                $idErrore = $mysqli->connect_errno;
                                $messaggio = $mysqli->connect_error;
                                error_log("Errore nella connessione al server $idErrore : $messaggio", 0);
                                $message = array();
                                $message[] = "<li>Errore nella connessione $messaggio</li>";
                                $this->creaFeedbackUtente($message, $pageContent, "");
                            }
                            else{
                                $query = "SELECT * from bijoux where bijoux.id_bijou=?";
                                $stmt = $mysqli->stmt_init();
                                $stmt->prepare($query);
                                $stmt->bind_param("i", $intId);
                                $stmt->execute();
                                if($stmt->errno > 0)
                                    error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                                else{
                                    $stmt->store_result();
                                    if($stmt->num_rows > 0){
                                        $stmt->bind_result($name_bijou, $material, $type_bijou, $st_price, $act_price, $avaibility);
                                        $stmt->fetch();
                                        $bijou = new Bijou($name_bijou, $material, $type_bijou, $st_price, $act_price, $avaibility);
                                        $bijou->setCode($intId);
                                        $mysqli->close();
                                        $pageContent->setSubPage('modificaBijou');
                                    }
                                }
                            }
                        }
   
                        else{
                            $message = array();
                            $message[] = '<li>L\'id selezionato non corrisponde a nessun bijou in vendita sul sito</li>';
                            $this->creaFeedbackUtente($message, $pageContent, "");
                        }
                    break;
                        
                    case 'ricaricaUtente':
                        $intIdUtente = filter_var($request['id'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                        if(isset($intIdUtente)){
                            $mysqli = new mysqli();
                            $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

                            if($mysqli->connect_errno != 0){
                                $idErrore = $mysqli->connect_errno;
                                $messaggio = $mysqli->connect_error;
                                error_log("Errore nella connessione al server $idErrore : $messaggio", 0);
                                $message = array();
                                $message[] = "<li>Errore nella connessione $messaggio</li>";
                                $this->creaFeedbackUtente($message, $pageContent, "");
                            }
                            else{
                                $query = "SELECT * FROM utenti where id=$intIdUtente";
                                $result = $mysqli->query($query);
                                if($mysqli->errno > 0)
                                    error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                                else{
                                    if($result->num_rows > 0){
                                        $row = $result->fetch_object();
                                        $utente = new UtenteRegistrato($row->username, $row->password, $row->name, $row->surname, $row->city, $row->street, $row->streetNumber, $row->cap, $row->mail, $row->creditCard, $row->creditCardNumber);
                                        $utente->setId($row->id);
                                        $utente->recharge($row->credit, false);
                                        $mysqli->close();
                                        $pageContent->setSubPage('ricaricaUtente');
                                    }
                                    else{
                                        $message = array();
                                        $message[] = "<li>L'id utilizzato non corrisponde a nessun utente registrato sul sito</li>";
                                        $this->creaFeedbackUtente($message, $pageContent, "");
                                    }
                                }
                            }
                        }
                        else{
                            $message = array();
                            $message[] = '<li>L\'id utilizzato deve essere un numero</li>';
                            $this->creaFeedbackUtente($message, $pageContent, "");
                        }
                        break;
                    
                    default:
                        $ultimiArrivi = Controller::loadUltimiArrivi();
                        $pageContent->setSubPage('home');
                        break;
                }
            }
            
            // gestione dei comandi inviati dall'amministratore
            if (isset($request["cmd"])) {
                switch ($request["cmd"]) {
                    // logout
                    case 'logout':
                        $this->logout($pageContent);
                        break;
                    
                    case 'ricerca':
                        $message = array();
                        $this->showHomeAdmin($pageContent);
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
                    break;
                    
                    //aggiornamento informazioni dell'utente
                    case 'modificaUtente':

                        $message = array();
                        $this->modificaUtente($user, $request, $message);
                        $this->creaFeedbackUtente($message, $pageContent, "Informazioni dell'utente aggiornate con successo!");
                        $this->showHomeUtente($pageContent);
                        break;
                    
                    //registrazione di un nuovo utente
                    case 'registrazione':
                        
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
                            if (!filter_var($request['creditCard'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[a-zA-Z]{5,15}/')))) {
                                $ajaxItem = new AjaxItem('creditCard');
                                $ajaxItem->setMessage('Inserisci una marca con lunghezza compresa fra 5 e 15 lettere');
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
                            if (!filter_var($request['city'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[a-zA-Z]{3,15}/')))) {
                                $ajaxItem = new AjaxItem('city');
                                $ajaxItem->setMessage('La citta\' non e\' valida, inserisci una citta\' con lunghezza compresa fra 3 e 15 lettere');
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
                    
                    
                    //messa in vendita di un bijou
                    case 'vendiBijou':

                        // in questo array inserisco i messaggi di 
                        // cio' che non viene validato
                        $message = array();
                        $this->vendiBijou($user, $request, $message);
                        $this->creaFeedbackUtente($message, $pageContent, "Bijou posto in vendita con successo!");
                        $this->showHomeUtente($pageContent);
                        break;
                    
                    //modifica di un bijou già caricato
                    case 'modificaBijou':

                        // in questo array inserisco i messaggi di 
                        // cio' che non viene validato
                        $message = array();
                        $this->modificaBijou($user, $request, $message);
                        $this->creaFeedbackUtente($message, $pageContent, "Informazioni del bijou modificate con successo!");
                        $this->showHomeUtente($pageContent);
                        break;
                    
                    //ricarica della carta di credito dell'utente selezionato
                    case 'ricaricaUtente':
                        // in questo array inserisco i messaggi di 
                        // cio' che non viene validato
                        $message = array();
                        $this->ricaricaUtente($user, $request, $message);
                        $this->creaFeedbackUtente($message, $pageContent, "Credito ricaricato con successo!");
                        $this->showHomeUtente($pageContent);
                        break;
                    
                    default : $this->showHomeUtente($pageContent);
                }
            } 
            else{
                // nessun comando
                $user = $session['user'];
                $ultimiArrivi = Controller::loadUltimiArrivi();
                $this->showHomeUtente($pageContent);
            }//end else
        }

        switch($ajaxMode){
            case 0:
                require basename(__DIR__) . '/../view/master.php';
                break;
            case 1:
                include_once basename(__DIR__) . '/../view/login/registrazione.php';
                break;
        }
    }
    /**
     * Restituisce l'array contentente la sessione per l'utente corrente
     * @return array
     */
    public function &getSessione(&$request) {
        if (!isset($_SESSION) || !array_key_exists('user', $_SESSION)) {
            // la sessione deve essere inizializzata
            return null;
        }

        // verifico chi sia l'utente correntemente autenticato
        $user = $_SESSION['user'];

        // controllo degli accessi
        switch ($user->getTipo()) {
            // l'utente e' un admin, consentiamo l'accesso
            case Utente::FLAG_ADMIN:
                return $_SESSION;

            default:
                return null;
        }
    }
    
    /**
     * Consente di modificare un cliente
     */
    private function modificaUtente(&$user, &$request, &$message){
        if (filter_var($request['mail_mod'], FILTER_VALIDATE_EMAIL)) {
            if(filter_var($request['cap_mod'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[0-9]{5}/')))){
                $intNumeroCivico_mod = filter_var($request['streetNumber_mod'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                if(isset($intNumeroCivico_mod)){
                    $idUtente = filter_var($request['utenteSelezionato'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    if(isset($idUtente)){
                        $errore = $user->modificaUtente($idUtente, $request['username_mod'], $request['password_mod'], $request['name_mod'], $request['surname_mod'], $request['city_mod'], $request['street_mod'], $intNumeroCivico_mod, $request['mail_mod'], $request['cap_mod']);
                          if(isset($errore))
                             $message[] = $errore;
                    }
                    else
                        $message[] = '<li>L\'id specificato non &egrave; nel formato corretto</li>';
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
    
/**
     * Consente di registrare un cliente
     */
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

    
     /**
     * Messa in vendita di un bijou
     */
    private function vendiBijou(&$user, &$request, &$message){
       $intIdBijou = filter_var($request['id_bijou'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(isset($intIdBijou)){
            $intDisponibilita = filter_var($request['avaibility'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            if (isset($intDisponibilita) && $intDisponibilita >= 0) {
                $intPrezzo = filter_var($request['prezzo'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                if (isset($intPrezzo) && $intPrezzo > 0) {
                    $mysqli = new mysqli();
                    $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

                    if($mysqli->connect_errno != 0){
                        $idErrore = $mysqli->connect_errno;
                        $messaggio = $mysqli->connect_error;
                        error_log("Errore nella connessione al server $idErrore : $messaggio", 0);
                        $message[] = "<li>Errore nella connessione $messaggio</li>";
                    }
                    else{
                        $stmt = $mysqli->stmt_init();
                        $query = "SELECT * FROM bijoux WHERE bijoux.id_bijou = ?";
                        $stmt->prepare($query);
                        $stmt->bind_param("i", $intIdBijou);
                        $stmt->execute();
                        if($stmt->errno > 0){
                            error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                            $message[] = "<li>Si e' verificato un errore durante la procedura, si prega di contattare un amministratore</li>";
                            $mysqli->close();
                        }
                        else{
                            $stmt->store_result();
                            if($stmt->num_rows > 0){
                                $message[] = "<li>Il bijou è già in vendita</li>";
                                $mysqli->close();
                            }
                            else{
                                $error = $user->aggiungiBijouVendita("", "", "", $intPrezzo, $intPrezzo, $intDisponibilita, $intIdBijou);
                                if(isset($error))
                                    $message[] = $error;
                                $mysqli->close();
                            }
                        }
                    }
                }
                else
                    $message[] = '<li>Il prezzo specificato non ha un formato valido o e\' negativo!</li>';
            }
            else
                $message[] = '<li>La disponibilita\' specificata non ha un formato valido o e\' negativa!</li>';
        }
        else{
            $intDisponibilita = filter_var($request['avaibility'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            if (isset($intDisponibilita) && $intDisponibilita >= 0) {
                $intPrezzo = filter_var($request['prezzo'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                if (isset($intPrezzo) && $intPrezzo > 0) {
                    $mysqli = new mysqli();
                    $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

                    if($mysqli->connect_errno != 0){
                        $idErrore = $mysqli->connect_errno;
                        $messaggio = $mysqli->connect_error;
                        error_log("Errore nella connessione al server $idErrore : $messaggio", 0);
                        $message[] = "<li>Errore nella connessione $messaggio</li>";
                    }
                    else{
                        $stmt = $mysqli->stmt_init();
                        $query = "SELECT * FROM bijoux WHERE name_bijou = ?";
                        $stmt->prepare($query);
                        $stmt->bind_param("s", $request['name_bijou']);
                        $stmt->execute();
                        if($stmt->errno > 0){
                            error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                            $message[] = "<li>Si e' verificato un errore durante la procedura, si prega di contattare un amministratore del sito</li>";
                            $mysqli->close();
                        }
                        else{
                            $stmt->store_result();
                            if($stmt->num_rows > 0){
                                $message[] = "Il bijou è già in vendita";
                                $mysqli->close();
                            }
                            else{
                                $error = $user->aggiungiBijouVendita($request['name_bijou'], $request['material'], $request['type_bijou'], $intPrezzo, $intPrezzo, $intDisponibilita);
                                if(isset($error))
                                    $message[] = $error;
                            }
                        }
                    }
                }
                else
                    $message[] = '<li>Il prezzo specificato non ha un formato valido o e\' negativo!</li>';
            }
            else
                $message[] = '<li>La disponibilita\' specificata non ha un formato valido o e\' negativa!</li>';
        }
    }
    
     /**
     * Consente di modificare un bijou già esistente
     */
    private function modificaBijou(&$user, &$request, &$message){
       $intIdBijou = filter_var($request['id_bijou'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
       if(isset($intIdBijou)){
                 $intDisponibilita = filter_var($request['avaibility'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                 if (isset($intDisponibilita) && $intDisponibilita >= 0) {
                     $intPrezzo = filter_var($request['prezzo'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                     if (isset($intPrezzo) && $intPrezzo > 0) {
                         $error = $user->modificaBijou($request['id_bijou'], $request['name_bijou'], $request['material'], $request['type_bijou'], $intPrezzo, $intPrezzo, $intDisponibilita);
                         if(isset($error))
                             $message[] = $error;
                     }
                     else
                         $message[] = '<li>Il prezzo specificato non ha un formato valido o e\' negativo!</li>';
                 }
                 else
                     $message[] = '<li>La disponibiita\' specificata non ha un formato valido o e\' negativa!</li>';
       }
       else
           $message[] = '<li>L\'id specificato non ha un formato valido!</li>';
    }
    
    
    private function ricaricaUtente(&$user, &$request, &$message){
        $intImporto = filter_var($request['importo_ricarica'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(isset($intImporto)) {
            if($intImporto > 0){
                $intIdUtente = filter_var($request['id'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                if(isset($intIdUtente)){
                    $mysqli = new mysqli();
                    $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

                    if($mysqli->connect_errno != 0){
                        $idErrore = $mysqli->connect_errno;
                        $messaggio = $mysqli->connect_error;
                        error_log("Errore nella connessione al server $idErrore : $messaggio", 0);
                        $message[] = "<li>Errore nella connessione al server: $messaggio</li>";
                    }
                    else{
                        $stmt = $mysqli->stmt_init();
                        $query = "SELECT credit FROM utenti WHERE id=?";
                        $stmt->prepare($query);
                        $stmt->bind_param("i", $intIdUtente);
                        $stmt->execute();
                        if($stmt->errno > 0){
                            error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                            $mysqli->close();
                            $message[] = '<li>Si e\' verificato un errore durante la procedura, si prega di contattare un amministratore</li>';
                        }
                        else{
                            $stmt->store_result();
                            if($stmt->num_rows > 0){
                                $stmt = $mysqli->stmt_init();
                                $query = "UPDATE utenti SET credit = (credit + ?) WHERE id=?";
                                $stmt->prepare($query);
                                $stmt->bind_param("ii", $intImporto, $intIdUtente);
                                $stmt->execute();
                                if($stmt->errno > 0){
                                    error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                                    $mysqli->close();
                                    $message[] = '<li>Si e\' verificato un errore durante la procedura, si prega di contattare un amministratore</li>';
                                }
                                else{
                                    $mysqli->close();
                                }
                            }
                            else{
                                $mysqli->close();
                                $message[] = "<li>L\'id dell'utente selezionato non corrisponde a nessun utente registrato sul sito</li>";
                            }     
                        }
                    }
                }
                else
                    $message[] = '<li>l\'id dell\'utente deve essere un numero/li>';
            }
            else
                $message[] = '<li>L\'importo della ricarica non puo\' essere negativo o nullo</li>';
        }
         else
            $message[] = '<li>L\'importo deve essere un numero</li>';
    }
    

}

?>
