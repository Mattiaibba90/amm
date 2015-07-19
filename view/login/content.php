<?php
if($pageContent->getSottoPagina()=='register')
{
        include 'register.php';
}
else
{
?>
<h4>Sei tornato? Accedi qui</h4><br><br>

<input type="text" id="Username" name="Username" maxlength="20" value="Username" style="text-align: center"> <br>
<input type="password" id="Password" name="Password" maxlength="16" value="Password" style="text-align: center"> <br>
<input type="submit" name="Login" value="Login"> <br>
<input type="hidden" name="IE" value="IE">

<h4>Altrimenti inserisci i dati e registrati <a href=register.php>QUI</a>, è facile!</h4>
<?php
}
