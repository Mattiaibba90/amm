<?php
switch($pageContent->getSubPage()){
        case 'register':
                include 'register.php';
                break;
        case 'risultatiRicercaAvanzata':
                include 'risultatiRicercaAvanzata.php';
                break;
        default:
?>
<h4>Sei tornato? Accedi qui</h4><br><br>

<form action="login" method="post">
<input type="hidden" name="cmd" value="login">
<input type="text" id="Username" name="Username" maxlength="20" value="Username" style="text-align: center"> <br>
<input type="password" id="Password" name="Password" maxlength="16" value="Password" style="text-align: center"> <br>
<button type="submit">Login</button>
</form>

<h4>Altrimenti inserisci i dati e registrati <a href=register>QUI</a>, è facile!</h4>
<?php
}
