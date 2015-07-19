<ul>
    <li>
        <h2>Bijoux attualmente in Vendita</h2>
        <table  id="tabella_bijoux">
            <tr class="rigaPari">
                <th>ID</th>
                <th>Nome</th>
                <th>Materiale</th>
                <th>Categoria</th>
                <th>Disponibilita'</th>
                <th>Prezzo</th>
                <th></th>
            </tr>
            <?php
                $righe = 0;
                foreach($bijoux as $bijou){
                    if($righe < UtenteController::MAX_RIGHE_TABELLA){
                        if(($righe+1) % 2 == 0)
                            echo '<tr class="rigaPari">';
                        else
                            echo '<tr>';
                        echo    '<td>' . $bijou->getId() . '</td>';
                        echo    '<td>' . $bijou->getNameBijou() . '</td>';
                        echo    '<td>' . $bijou->getMaterial() . '</td>';
                        echo    '<td>' . $bijou->getTypeBijou() . '</td>';
                        echo    '<td>' . $bijou->getAvaibility() . '</td>';
                        echo    '<td>' . $bijou->getActualPrice() . '</td>';
                        echo    '<td><a href="admin/modificaBijoux?id_bijou=' . $bijou->getId() . '">Modifica</a></td>';
                        echo '</tr>';
                        $righe++;
                    }
                }
                echo "<tr>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo '<td><a href="' . 'admin/amministraBijoux?limiteInferiore=' . $limiteInferiore . '&amp;limiteSuperiore=' . $limiteSuperiore . '&amp;cursore=0' . '">Indietro</a></td>';
                echo '<td><a href="' . 'admin/amministraBijoux?limiteInferiore=' . $limiteInferiore . '&amp;limiteSuperiore=' . $limiteSuperiore . '&amp;cursore=1' . '">Avanti</a></td>';
                echo '</tr>';
             ?>
        </table>
    </li>
    <li><h3>Aggiungi un bijou in vendita</h3>
                <li>
                    <p>Compila il form:</p>
                    <ul class="form_ul">
                        <li>
                            <label for="name">Nome:</label>
                            <input type="text" required="required" name="name" id="name">
                        </li>
                        <li>
                            <label for="material">Materiale:</label>
                            <input type="text" required="required" name="material" id="material">
                        </li>
                        <li>
                            <p>
                                Tipo di bijou:
                                <select name="typeBijou" required="required">
                                    <option></option>
                                    <option value="Amigurumi">Amigurumi</option>
                                    <option value="Gioielli">Gioielli</option>
                                    <option value="Parure">Parure</option>
                                </select>
                            </p>
                        </li>
                        <li>
                            <label for="avaibility">Disponibilità:</label>
                            <input type="text" required="required" name="avaibility" id="avaibility">
                        </li>
                        <li>
                            <label for="actualPrice">Prezzo:</label>
                            <input type="text" required="required" name="actualPrice" id="actualPrice">
                        </li>
                    </ul>
                </li>
            </ul>
            <p><button type="submit" name="cmd" value="vendiBijou">Conferma</button></p>
