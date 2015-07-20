<?php
switch ($pageContent->getSubPage()) {
    
    case 'carrello':
        include 'carrello.php';
        break;
    
    case 'risultatiRicercaAvanzata':
        include 'risultatiRicercaAvanzata.php';
        break;

    case 'cronologiaOrdini':
        include 'cronologiaOrdini.php';
        break;
    
    case 'modificaDati':
        include 'modificaDati.php';
        break;
    
    case 'ricaricaCredito':
        include 'ricaricaCredito.php';
        break;
    
    case 'pannelloControllo':
        include 'pannelloControllo.php';
        break;

    default: ?>
            <ul>
                <li><h3>Ultimi arrivi</h3>
                    <ul class="lista_visiva">
                        <?php 
                            foreach ($ultimiArrivi as $ultimoArrivo) {
                                echo '<li><a href="utente/bijou?id_bijou=' . $ultimoArrivo->getId() . '">' . $ultimoArrivo->getNameBijou() . '</a></li>';
                            }
                            ?>
                    </ul>
                </li>
            </ul>
<?php break;}
                
