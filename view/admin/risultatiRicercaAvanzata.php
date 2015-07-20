<ul>
    <li><h3>Categoria selezionata</h3>
        <?php if(count($risultatiRicerca) > 0){ ?>
        <ul class="lista_visiva">
            <?php
                foreach ($risultatiRicerca as $trovato) {
                    echo '<li>'.$trovato->getNameBijou().'</li>';
                }
            ?>
        </ul>
        <div class="clear"></div>
        <ul class="lista_visiva">
        <?php
        echo '<li><a href="' . 'login?cmd=ricerca_avanzata' . $parametriPost . '&amp;ric_limiteInferiore=' . $ric_limiteInferiore . '&amp;ric_limiteSuperiore=' . $ric_limiteSuperiore . '&amp;ric_cursore=0' . '">Indietro</a></li>';
        echo '<li><a href="' . 'login?cmd=ricerca_avanzata' . $parametriPost . '&amp;ric_limiteInferiore=' . $ric_limiteInferiore . '&amp;ric_limiteSuperiore=' . $ric_limiteSuperiore . '&amp;ric_cursore=1' . '">Avanti</a></li>';
        echo '</ul>';
        }//end if ?>
        </ul>
</ul>

