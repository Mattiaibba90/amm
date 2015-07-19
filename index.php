<?php 
include_once 'controller/Controller.php';
include_once 'controller/UtenteController.php';
include_once 'controller/AdminController.php';

// punto unico di accesso all'applicazione
MainController::dispatch($_REQUEST);

// Classe che controlla il punto unico di accesso tramite un controller principale

class MainController {

    public static function dispatch(&$request) {
        //inizializzazione della sessione
        session_start();
        $request["page"] = 'login';
        if(isset($request["page"])) {
            switch ($request["page"]) {
                case "login":
                    // Pagina iniziale per tutti gli utenti
                    $controller = new Controller();
                    $controller->handleInput($request, $_SESSION);
                    break;
                
                case 'utente':
                    // la pagina per gli utenti registrati
                    $controller = new UtenteController();
                    $sessione = &$controller->getSessione($request);
                    
                    if (!isset($sessione)) {
                        self::write403();
                    }
                    
                    $controller->handleInput($request, $sessione);
                    break;

                case 'admin':
                    // la pagina per gli admin
                    $controller = new AdminController();
                    $sessione = &$controller->getSessione($request);
                    
                    if (!isset($sessione)) {
                        self::write403();
                    }
                    
                    $controller->handleInput($request, $sessione);
                    break;

                default:
                    self::write404();
                    break;
            }
        }
        else
            self::write404();
    }

    // 404 error: file not found

    public static function write404() {
        header('HTTP/1.0 404 Not Found');
        $titolo = "File non trovato!";
        $messaggio = "La pagina che hai richiesto non &egrave; momentaneamente disponibile";
        include_once('not-found.php');
        exit();
    }

    // 404 error: forbidden access
    
    public static function write403() {
        // impostiamo il codice della risposta http a 404 (file not found)
        header('HTTP/1.0 403 Forbidden');
        $titolo = "Accesso negato";
        $messaggio = "Non hai i diritti necessari per poter accedere a questa pagina";
        $canLogin = true;
        include_once('not-found.php');
        exit();
    }

}


?>
