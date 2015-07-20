<?php
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$json = array();

if(isset($risposta['username']))
    $json["username"] = $risposta['username']->getMessage();

if(isset($risposta['password']))
    $json["password"] = $risposta['password']->getMessage();

if(isset($risposta['name']))
    $json["name"] = $risposta['name']->getMessage();

if(isset($risposta['surname']))
    $json["surname"] = $risposta['surname']->getMessage();

if(isset($risposta['mail']))
    $json["mail"] = $risposta['mail']->getMessage();

if(isset($risposta['creditCard']))
    $json["creditCard"] = $risposta['creditCard']->getMessage();

if(isset($risposta['creditCardNumber']))
    $json["creditCardNumber"] = $risposta['creditCardNumber']->getMessage();

if(isset($risposta['city']))
    $json["city"] = $risposta['city']->getMessage();

if(isset($risposta['cap']))
    $json["cap"] = $risposta['cap']->getMessage();

if(isset($risposta['street']))
    $json["street"] = $risposta['street']->getMessage();

if(isset($risposta['streetNumber']))
    $json["streetNumber"] = $risposta['streetNumber']->getMessage();

echo json_encode($json);
?>
