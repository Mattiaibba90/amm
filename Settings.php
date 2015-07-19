<?php

// Classe con le impostazioni per il database

class Settings {

    const BIJOUX_HOME = 6;
    
    private static $appPath;
    public static $db_host = 'localhost';
    public static $db_user = 'ibbaMattia';
    public static $db_password = 'upupa551';
    public static $db_name = 'ibbaMattia';

    // gestisco il path per l'applicazione, che sia in locale o sul server pubblico
    
    public static function getApplicationPath() {
        if (!isset(self::$appPath)) {
            // restituisce il server corrente
            switch ($_SERVER['HTTP_HOST']) {
                case 'localhost':
                    // configurazione locale
                    self::$appPath = 'http://' . $_SERVER['HTTP_HOST'] . '/PhpProjectAmm';
                    break;
                case 'spano.sc.unica.it':
                    // configurazione pubblica
                    self::$appPath = 'http://' . $_SERVER['HTTP_HOST'] . '/amm2015/ibbaMattia';
                    break;

                default:
                    self::$appPath = '';
                    break;
            }
        }
        
        return self::$appPath;
    }

}
