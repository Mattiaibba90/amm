<?php
include_once 'Settings.php';
?>

<!DOCTYPE html>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <base href="<?= Settings::getApplicationPath() ?>"/>
        <title>Errore</title>
    </head>
    
    <body>
        <h1>Siamo spiacenti, si &egrave; verificato un errore:</h1>
        <br/>
        <h2><?= $titolo ?></h2>
        <p>
            <?=
            $messaggio
            ?>
        </p>
        <?php if (isset($canLogin)) { ?>
            <p>Puoi autenticarti nella seguente pagina di <a href="ibbaMattia/index.php?page=login">login</a></p>
        <?php    } ?>
    </body>
</html>
