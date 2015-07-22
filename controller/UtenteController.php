<?php
include_once 'Controller.php';

/**
 * Controller che gestisce gli input degli utenti
 * @author Mattia Ibba
 */
class UtenteController extends Controller {
    
    const MAX_RIGHE_TABELLA = 5;
    
    public function __construct() {
        parent::__construct();
    }

    public function handleInput(&$request, &$session) {

        $pageContent = new PageContent();

        $pageContent->setPage($request['page']);
        $ajaxMode=0;

        if (!$this->loggedIn()) {
            $this->showLoginPage($pageContent);
        }
        else{
            $user = $session['user'];
            if (isset($request["subpage"])) {
                switch ($request["subpage"]) {                    

                    case 'carrello':
                        $pageContent->setSubPage('carrello');
                        break;
                    
                    case 'ricercaAvanzata':
                        $pageContent->setSubPage('ricercaAvanzata');
                        break;
                    
                    case 'risultatiRicercaAvanzata':
                        $pageContent->setSubPage('risultatiRicercaAvanzata');
                        break; 
                    
                    case 'ordiniPrecedenti':
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
                            $idUser = $user->getId();
                            $query = "select COUNT(*) as numeroRisultati FROM ordini where ordini.id_order=$idUser";
                            $result = $mysqli->query($query);
                            if($mysqli->errno > 0)
                                error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                            else{
                                $row = $result->fetch_object();
                                $numeroMaxRisultati = $row->numeroRisultati;
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
                                
                                $query = "select ordini.id as id_order, ordini.bijoux as id_bijou, bijoux.name_bijou, bijoux.material, bijoux.type_bijou, bijoux.st_price, bijoux.act_price, bijoux.avaibility FROM ordini join bijoux on ordini.bijoux = bijoux.id_bijou where ordini.id_order=$idUtente LIMIT $limiteInferiore , $limiteSuperiore";
                                $result = $mysqli->query($query);
                                if($mysqli->errno > 0)
                                    error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                                else{
                                    $ordiniPrecedenti = array();
                                    while($row = $result->fetch_object()){
                                        $bijoux = array();
                                        $bijou = new Bijou($row->name_bijou, $row->material, $row->type_bijou, $row->st_price, $row->act_price, $row->avaibility);
                                        $bijou->setCode($row->id_bijou);
                                        $bijoux[] = $bijou;
                                        $ordine = new Ordine($idUtente, $row->day, $row->month, $row->year);
                                        $ordine->setContent($bijoux);
                                        $ordine->setId($row->id_order);
                                        $ordiniPrecedenti[] = $ordine;
                                    }
                                }
                                $mysqli->close();
                                $pageContent->setSubPage('ordiniPrecedenti');
                            }
                        }
                        break;

                    case 'pannelloControllo':
                        $pageContent->setSubPage('pannelloControllo');
                        break;
                    
                    case 'ricaricaCredito':
                        $pageContent->setSubPage('ricaricaCredito');
                        break;
                    
                    case 'mostraBijou':
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
                                //non uso i prepared statments poichè l'id viene validato come intero rendendo impossibile l'sql injection
                                $query = "SELECT name_bijou, material, typeBijou, st_price, act_price, avaibility from bijoux where id_bijou=$intId";
                                $result = $mysqli->query($query);
                                if($mysqli->errno > 0)
                                    error_log("Errore nell'esecuzione della query $mysqli->errno : $mysqli->error");
                                else{
                                    if($result->num_rows > 0){
                                        $bijoux = array();
                                        while($row = $result->fetch_object()){
                                            $bijou = new Bijou($row->name_bijou, $row->material, $row->type_bijou, $row->st_price, $row->act_price, $row->avaibility);
                                            $bijou->setCode($intId);
                                            $bijoux[] = $bijou;
                                        }
                                        $mysqli->close();
                                        $pageContent->setSubPage('mostraBijou');
                                    }
                                    else{
                                        $message = array();
                                        $message[] = '<li>Non esiste questo bijou sul sito</li>';
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
            
            if (isset($request["cmd"])) {
                switch ($request["cmd"]) {
                    // logout
                    case 'logout':
                        $this->logout($pageContent);
                        break;
                    
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
                    
                    case 'pannelloControllo':
                        $validi=0;
                        $risposta = array();
                        if(isset($request['name'])){
                            if (!filter_var($request['name'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[a-zA-Z]{3,10}/')))) {
                                $ajaxItem = new AjaxItem('name');
                                $ajaxItem->setMessage('Il nome non e\' valido, inserisci un nome con lunghezza compresa fra 3 e 10 lettere');
                                $risposta['name'] = $AjaxItem;
                            }
                            else
                                $validi++;
                        }
                        if(isset($request['surname'])){
                            if (!filter_var($request['surname'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[a-zA-Z]{3,10}/')))) {
                                $ajaxItem = new AjaxItem('surname');
                                $ajaxItem->setMessage('Il cognome non e\' valido, inserisci un cognome con lunghezza compresa fra 3 e 10 lettere');
                                $risposta['surname'] = $ajaxItem;
                            }
                            else
                                $validi++;
                            
                        }
                        if(isset($request['mail'])){
                            if (!filter_var($request['mail'], FILTER_VALIDATE_EMAIL)) {
                                $ajaxItem = new AjaxItem('mail');
                                $ajaxItem->setMessage('L\'indirizzo e-mail utilizzato non e\' valido');
                                $risposta['mail'] = $ajaxItem;
                            }
                            elseif($this->emailDisponibileUtente($request['mail'], $user->getId()) == 1){
                                $validi++;
                            }
                            elseif($this->emailDisponibileUtente($request['mail'], $user->getId()) == 0){
                                $ajaxItem = new AjaxItem('mail');
                                $ajaxItem->setMessage('L\'indirizzo e-mail scelto non e\' disponibile, scegline un altro');
                                $risposta['mail'] = $ajaxItem;
                            }
                            elseif($this->emailDisponibileUtente($request['mail'], $user->getId()) == -1){
                                $ajaxItem = new AjaxItem('mail');
                                $ajaxItem->setMessage('Si e\' verificato un errore durante l\'operazione, si prega di riprovare');
                                $risposta['mail'] = $ajaxItem;
                            }
                               
                        }
                        if(isset($request['city'])){
                            if (!filter_var($request['city'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[a-zA-Z]{3,15}/')))) {
                                $ajaxItem = new AjaxItem('city');
                                $ajaxItem->setMessage('La città\' non e\' valida, inserisci una città\' con lunghezza compresa fra 3 e 15 lettere');
                                $risposta['city'] = $ajaxItem;
                            }
                            else
                                $validi++;
                        }
                        if(isset($request['cap'])){
                            if (!filter_var($request['cap'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[0-9]{5}/')))) {
                                $ajaxItem = new AjaxItem('cap');
                                $ajaxItem->setMessage('Il cap non e\' valido, inserisci una cap corretto');
                                $risposta['cap'] = $ajaxItem;
                            }
                            else
                                $validi++;
                        }
                        if(isset($request['street'])){
                            if (!filter_var($request['street'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[a-zA-Z]{5,20}/')))) {
                                $ajaxItem = new AjaxItem('street');
                                $ajaxItem->setMessage('La via non e\' valida, inserisci una via con lunghezza compresa fra 5 e 20 lettere');
                                $risposta['street'] = $ajaxItem;
                            }
                            else
                                $validi++;
                        }
                        if(isset($request['streetNumber'])){
                            if (!filter_var($request['streetNumber'], FILTER_VALIDATE_INT)) {
                                $ajaxItem = new AjaxItem('streetNumber');
                                $ajaxItem->setMessage('Il numero civico non puo\' contenere lettere');
                                $risposta['streetNumber'] = $ajaxItem;
                            }
                            else
                                $validi++;
                        }
                        
                        if($validi == 7){
                            $message = array();
                            $this->pannelloControlloUtente($user, $request, $message);
                            $this->creaFeedbackUtente($message, $pageContent, "Informazioni personali aggiornate con successo!");
                            $this->showHomeUtente($pageContent);
                        }
                        else
                            $ajaxMode=1;
                        
                        break;

                    case 'ricaricaCredito':
                        $message = array();
                        $this->ricaricaCredito($user, $request, $message);
                        $this->creaFeedbackUtente($message, $pageContent, "Credito ricaricato con successo!");
                        $this->showHomeUtente($pageContent);
                        break;

                    case 'carrello':
                        $message = array();
                        $this->carrello($user, $request, $message);
                        $this->creaFeedbackUtente($message, $pageContent, "Bijou inserito correttamente nel Carrello!");
                        $this->showHomeUtente($pageContent);
                        break;
                    
                    case 'rimuoviElementoCarrello':
                        $message = array();
                        $intPos = filter_var($request['pos'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                        if(isset($intPos)){
                            if($intPos <= $user->getTotaleElementiCarrello() && $intPos >= 0){
                                $user->removeItem($intPos);
                            }
                            else{
                                $message[] = "<li>La posizione dell'elemento non e' valida</li>";
                            }
                        }
                        else{
                            $message[] = "<li>La posizione dell'elemento deve essere un numero</li>";
                        }
                        $this->creaFeedbackUtente($message, $pageContent, "Elemento rimosso dal carrello con successo!");
                        $this->showHomeUtente($pageContent);
                        break;
                    
                    case 'confermaOrdine':
                        $message = array();
                        $this->confermaOrdine($user, $request, $message);
                        $this->creaFeedbackUtente($message, $pageContent, "Ordine confermato correttamente!");
                        $this->showHomeUtente($pageContent);
                        break;
                    
                    /*case 'ricerca':
                        $message = array();
                        $this->showHomeUtente($pageContent);
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
                include_once basename(__DIR__) . '/../view/ajax/register.php';
                break;
        }
    }

    public function &getSessione(&$request) {
        if (!isset($_SESSION) || !array_key_exists('user', $_SESSION)) {
            return null;
        }
        $user = $_SESSION['user'];
        switch ($user->getTipo()) {

            case "registered_user":
                return $_SESSION;

            default:
                return null;
        }
    }
    
    private function pannelloControlloUtente(&$user, &$request, &$message){
        $validMail = filter_var($request['mail'], FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE);
        $validNumeroCivico = filter_var($request['streetNumber'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $controlloCAP = true;
        if (!filter_var($request['cap'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/[0-9]{5}/'))))
                $controlloCAP = null;
        if(isset($validMail) && isset($validNumeroCivico) && isset($controlloCAP)){
            $mysqli = new mysqli();
            $mysqli->connect(Settings::$db_host, Settings::$db_user, Settings::$db_password, Settings::$db_name);

            if($mysqli->connect_errno != 0){
                $idErrore = $mysqli->connect_errno;
                $messaggio = $mysqli->connect_error;
                error_log("Errore nella connessione al server $idErrore : $messaggio", 0);
                $message[] = '<li>Errore nella connessione al server</li>';
            }
            else{
                $stmt = $mysqli->stmt_init();
                $query = "SELECT * FROM utenti WHERE mail=? AND id != ?";
                $stmt->prepare($query);
                $stmt->bind_param("si", $request['mail'], $user->getId());
                $stmt->execute();
                if($stmt->errno > 0){
                    error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                    $mysqli->close();
                    $message[] = '<li>Si e\' verificato un errore durante la procedura, si prega di contattare un amministratore del sito</li>';
                }
                else{
                    $stmt->store_result();
                    if($stmt->num_rows > 0){
                        $mysqli->close();
                        $message[] = '<li>L\'e-mail da te scelta e\' gia\' utilizzata da un altro utente, si prega di inserire dei valori differenti</li>';
                    }
                    else{
                        $stmt = $mysqli->stmt_init();
                        $query = "UPDATE utenti SET name=? , surname=? , city=? , street=? , streetNumber=?, cap=?, mail=?  WHERE id=?";
                        $stmt->prepare($query);
                        $stmt->bind_param("ssssiisi", $request['name'], $request['surname'], $request['city'], $request['street'], $request['streetNumber'], $request['cap'], $request['mail'], $user->getId());
                        $stmt->execute();
                        if($stmt->errno > 0){
                            error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                            $mysqli->close();
                            $message[] = '<li>Si e\' verificato un errore durante la procedura, si prega di contattare un amministratore</li>';
                        }
                        else{
                            $mysqli->close();
                            $user->setName($request['name']);
                            $user->setSurname($request['surname']);
                            if(!$user->setEmail($request['mail'])){
                                $message[] = '<li>L\'email specificata non &egrave; nel formato corretto</li>';
                            }
                            $user->setCity($request['city']);
                            if(!$user->setCap($request['cap'])){
                                $message[] = '<li>Il CAP specificato non &egrave; nel formato corretto</li>';
                            }
                            $user->setStreet($request['street']);
                            if(!$user->setStreetNumber($request['streetNumber'])){
                                $message[] = '<li>Il Numero Civico specificato non &egrave; nel formato corretto</li>';
                            }
                        }
                    }
                }
            }
        }
        else
            $message[] = '<li>L\'e-mail, il CAP o il Numero Civico specificato non sono nel formato corretto</li>';
    }
    
    private function ricaricaCredito(&$user, &$request, &$message){
        $intImport = filter_var($request['importo_ricarica'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(isset($intImport)) {
            if($intImport > 0){
                if(!$user->recharge($intImport))
                    $message[] = '<li>L\'importo della ricarica non e\' valido</li>';
            }
            else
                $message[] = '<li>L\'importo della ricarica non puo\' essere negativo o nullo</li>';
        }
         else
            $message[] = '<li>L\'importo deve essere un numero</li>';
    }
    
    private function carrello(&$user, &$request, &$message){
        $intQuantita = filter_var($request['quantita'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $intDisponibilita = filter_var($request['avaibility'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $intPrezzo = filter_var($request['act_price'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $intIdBijou = filter_var($request['id_bijou'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if (isset($intQuantita) && isset($intDisponibilita) && isset($intPrezzo) && isset($intIdBijou)) {
            if($request['quantita'] > 0){
                if($request['quantita'] <= $request['avaibility']){
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
                        $stmt = $mysqli->stmt_init();
                        $query = "SELECT bijoux.id_bijou from bijoux where bijoux.id_bijou=?";
                        $stmt->prepare($query);
                        $stmt->bind_param("i", $intIdBijou);
                        $stmt->execute();
                        if($stmt->errno > 0){
                            error_log("Errore nell'esecuzione della query $stmt->errno : $stmt->error");
                        }
                        else{
                            $stmt->store_result();
                            if($stmt->num_rows > 0){
                                $user->addItemToCart($request['name_bijou'], $request['material'], $request['type_bijou'], $request['st_price'], $request['act_price'], $request['quantita'], $intIdBijou);
                                $mysqli->close();
                            }
                            else{
                                $message[] = '<li>L\'id del bijou selezionato non è valido</li>';
                            }
                        }
                    }
                }
                else
                    $message[] = '<li>Non puoi acquistare una quantita\' di uno stesso bijou superiore alla sua disponibilita\' di vendita</li>';
            }
            else
                $message[] = '<li>Non puoi acquistare una quantita\' negativa o nulla di bijoux</li>';
        }
        else
            $message[] = '<li>La quantita\' o la disponibilita\' o il prezzo o l\'id del bijou digitati non sono validi</li>';
    }
    
    private function confermaOrdine(&$user, &$request, &$message){
        if($user->getCredit() >= $user->getTotal()){
            $contenutoCarrello = $user->getContenut();
            $errore = $user->creaOrdine($contenutoCarrello);
            if($errore == -1)
                $message[] = "<li>Si e' verificato un problema con la conferma dell'ordine</li>";
            else if($errore == 1)
                $message[] = "<li>La quantita di qualche elemento nel tuo carrello non e' piu' disponibile, si prega di effettuare il logout e di rieffettuare il login</li>";
        }
        else
            $message[] = '<li>Non hai credito sufficiente per confermare l\'ordine, ti preghiamo di <a href="utente/ricaricaCredito">ricaricare</a></li>';
    }
    
    protected function emailDisponibileUtente($email, $userID){
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
            $query = "SELECT * FROM utenti WHERE mail=? AND id !=?";
            $stmt->prepare($query);
            $stmt->bind_param("si", $email, $userID);
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
