<ul>
    <li>
        <h3>Carrello</h3>
        <?php 
            if($user->getNumberItems() <= 0){
                echo '<p>Non sono presenti elementi nel tuo carrello</p>';
                echo '</li>';
            }
            else {
        ?>
                <table  id="tabella_carrello">
                    <tr class="rigaPari">
                        <th>Nome bijou</th>
                        <th>Materiale</th>
                        <th>Categoria</th>
                        <th>Disponibilità</th>
                        <th>Prezzo</th>
                        <th></th>
                    </tr>
                    <?php
                        $riga=1;
                        $contenutoCarrello = $user->getList();
                        foreach($contenutoCarrello as $itemCarrello){
                            if($riga % 2 == 0)
                                echo '<tr class="rigaPari">';
                            else
                                echo '<tr>';
                            echo '<td>' . $itemCarrello->getNameBijou() . '</td>';
                            echo '<td>' . $itemCarrello->getMaterial() . '</td>';
                            echo '<td>' . $itemCarrello->getTypeBijou() . '</td>';
                            echo '<td>' . $itemCarrello->getAvaibility() . '</td>';
                            echo '<td>' . $itemCarrello->getActualPrice() . '</td>';
                            echo '<td><a href="utente/carrello?cmd=rimuoviElementoCarrello&amp;pos=' . ($riga-1) . '">Rimuovi</a></td>';
                            echo '</tr>';
                            $riga++;
                        }
                    ?>
                    <!-- Riga del Totale -->
                    <tr>
                        <th>Totale:</th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?= $user->getTotal(); ?></td>
                        <td></td>
                    </tr>
                </table>
            </li>
            <li>
                <h3>Riepilogo</h3>
                <p>Gli oggetti saranno recapitati a:</p>
                <p>Nome: <?= $user->getName(); ?></p>
                <p>Cognome: <?= $user->getSurname(); ?></p>
                <p>Citt&agrave;: <?= $user->getCity(); ?></p>
                <p>CAP: <?= $user->getCap(); ?></p>
                <p>Indirizzo: <?= $user->getStreet() ?></p>
            </li>
            <li>
                <form action="utente/home" method="post">
                    <button type="submit" name="cmd" value="confermaOrdine">Conferma ordine</button>
                </form>
            </li>
    <?php
            }
    ?>
</ul>
