<?php
if($pageContent->getSubPage()=='register')
{
        include 'register.php';
}
else
{
    if($pageContent->getSubPage()=='risultatiRicercaAvanzata')
    {
        include 'risultatiRicercaAvanzata.php';
    }
    else
{
?>
<h4>Sei tornato? Accedi qui</h4><br><br>

<form action="login" method="post">
<input type="hidden" name="cmd" value="login">
<input type="text" id="Username" name="Username" maxlength="20" value="Username" style="text-align: center"> <br>
<input type="password" id="Password" name="Password" maxlength="16" value="Password" style="text-align: center"> <br>
<button type="submit">Login</button>
</form>

<h4>Altrimenti inserisci i dati e registrati <a href=ibbaMattia/view/index.php?page=login?subPage=register>QUI</a>, è facile!</h4>
<?php
}
}
