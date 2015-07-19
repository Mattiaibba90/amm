<ul>
    <li>
        <h3>Cronologia ordini</h3>
        <?php 
            if(count($ordiniPrecedenti) == 0){
                echo '<p>Non sono presenti ordini precedenti!</p>';
                if(isset($intCursore)){ ?>
                <table  id="tabella_ordini_precedenti">
                    <tr>
                        <th>ID ordine</th>
                        <th>ID bijou</th>
                        <th>Data dell'acquisto</th>
                        <th>Nome bijou</th>
                        <th>Materiale</th>
                        <th>Categoria</th>
                        <th>Quantita'</th>
                        <th>Prezzo singolo</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <?php
                        echo '<td><a href="' . 'utente/cronologiaOrdini?limiteInferiore=0&amp;limiteSuperiore=' . UtenteController::MAX_RIGHE_TABELLA . '&amp;cursore=0' . '">Indietro</a></td>';
                        echo '<td><a href="' . 'utente/cronologiaOrdini?limiteInferiore=0&amp;limiteSuperiore=' . UtenteController::MAX_RIGHE_TABELLA . '&amp;cursore=1' . '">Avanti</a></td>';
                        ?>
                    </tr>
                </table>
        <?php
                }
            }
            else
                {
        ?>
        <table  id="tabella_ordini_precedenti">
            <tr class="rigaPari">
                <th>ID ordine</th>
                <th>ID bijou</th>
                <th>Data dell'acquisto</th>
                <th>Nome bijou</th>
                <th>Materiale</th>
                <th>Categoria</th>
                <th>Quantita'</th>
                <th>Prezzo singolo</th>
            </tr>
            <?php
                $righe = 0;
                foreach($ordiniPrecedenti as $ordinePrecedente){
                    $contenutoOrdine = $ordinePrecedente->getList();
                    foreach($contenutoOrdine as $bijou){
                        if($righe < UtenteController::MAX_RIGHE_TABELLA){
                            if(($righe+1) % 2 == 0)
                                echo '<tr class="rigaPari">';
                            else
                                echo '<tr>';
                            echo    '<td>' . $ordinePrecedente->getId() . '</td>';
                            echo    '<td>' . $bijou->getIdBijou() . '</td>';
                            echo    '<td>' . $ordinePrecedente->getDate() . '</td>';
                            echo    '<td>' . $bijou->getNameBijou() . '</td>';
                            echo    '<td>' . $bijou->getMaterial() . '</td>';
                            echo    '<td>' . $bijou->getTypeBijou() . '</td>';
                            echo    '<td>' . $bijou->getAvaibility() . '</td>';
                            echo    '<td>' . $bijou->getActualPrice() . '</td>';
                            echo '</tr>';
                            $righe++;
                        }
                    }
                }
                echo "<tr>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo '<td><a href="' . 'utente/cronologiaOrdini?limiteInferiore=' . $limiteInferiore . '&amp;limiteSuperiore=' . $limiteSuperiore . '&amp;cursore=0' . '">Indietro</a></td>';
                echo '<td><a href="' . 'utente/cronologiaOrdini?limiteInferiore=' . $limiteInferiore . '&amp;limiteSuperiore=' . $limiteSuperiore . '&amp;cursore=1' . '">Avanti</a></td>';
                echo '</tr>';
            ?>
        </table>
        <?php
                }
        ?>
    </li>
</ul>
