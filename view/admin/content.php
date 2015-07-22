<?php
switch ($pageContent->getSubPage()) {
    
    case 'amministraBijoux':
        include 'amministraBijoux.php';
        break;
    
    case 'risultatiRicercaAvanzata':
        include 'risultatiRicercaAvanzata.php';
        break;

    case 'amministraUtenti':
        include 'amministraUtenti.php';
        break;
    
    case 'modificaBijoux':
        include 'modificaBijoux.php';
        break;
    
    case 'modificaUtenti':
        include 'modificaUtenti.php';
        break;
    
    case 'ricaricaUtenti':
        include 'ricaricaUtenti.php';
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
                                echo '<li><a href="admin/mostraBijou?id_bijou=' . $ultimoArrivo->getId() . '">' . $ultimoArrivo->getNameBijou() . '</a></li>';
                            }
                            ?>
                    </ul>
                </li>
            </ul>
<?php break;}
                
