<span id="confirm_reg" class="form_confirm"> </span>
<ul>
    <li>
        <h2>Utenti registrati:</h2>
        <table  id="tabella_buyers">
            <tr class="rigaPari">
                <th>id</th>
                <th>Username</th>
                <th>Password</th>
                <th>Nome</th>
                <th>Cognome</th>
                <th>e-mail</th>
                <th>Citt&agrave;</th>
                <th>CAP</th>
                <th>Via</th>
                <th>Numero Civico</th>
                <th></th>
            </tr>
            <?php
                $riga = 1;
                foreach($utenti as $utente){
                    if($riga % 2 != 0){
                        echo '<tr>';
                        echo    '<td>' . $utente->getId() . '</td>';
                        echo    '<td>' . $utente->getUsername() . '</td>';
                        echo    '<td>' . $utente->getPassword() . '</td>';
                        echo    '<td>' . $utente->getName() . '</td>';
                        echo    '<td>' . $utente->getSurname() . '</td>';
                        echo    '<td>' . $utente->getEmail() . '</td>';
                        echo    '<td>' . $utente->getCity() . '</td>';
                        echo    '<td>' . $utente->getCap() . '</td>';
                        echo    '<td>' . $utente->getStreet() . '</td>';
                        echo    '<td>' . $utente->getStreetNumber() . '</td>';
                        echo    '<td><a href="admin/modificaUtenti?utenteSelezionato=' . $utente->getId() . '">Modifica</a></td>';
                        echo '</tr>';
                    }
                    else{
                        echo '<tr class="rigaPari">';
                        echo    '<td>' . $utente->getId() . '</td>';
                        echo    '<td>' . $utente->getUsername() . '</td>';
                        echo    '<td>' . $utente->getPassword() . '</td>';
                        echo    '<td>' . $utente->getName() . '</td>';
                        echo    '<td>' . $utente->getSurname() . '</td>';
                        echo    '<td>' . $utente->getEmail() . '</td>';
                        echo    '<td>' . $utente->getCity() . '</td>';
                        echo    '<td>' . $utente->getCap() . '</td>';
                        echo    '<td>' . $utente->getStreet() . '</td>';
                        echo    '<td>' . $utente->getStreetNumber() . '</td>';
                        echo    '<td><a href="admin/modificaUtenti?utenteSelezionato=' . $utente->getId() . '">Modifica</a></td>';
                        echo '</tr>';
                    }
                    $riga++;
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
                echo "<td></td>";
                echo '<td><a href="' . 'admin/amministraUtenti?limiteInferiore=' . $limiteInferiore . '&amp;limiteSuperiore=' . $limiteSuperiore . '&amp;cursore=0' . '">Indietro</a></td>';
                echo '<td><a href="' . 'admin/amministraUtenti?limiteInferiore=' . $limiteInferiore . '&amp;limiteSuperiore=' . $limiteSuperiore . '&amp;cursore=1' . '">Avanti</a></td>';
                echo '</tr>';
            ?>
        </table>
    </li>
    <li>
        <h3>Registra un nuovo utente</h3>
            <p>Compila tutti i campi:</p>
                    <ul class="form_ul">
                        <li>
                            <label for="username">Username:</label>
                            <input type="text" required="required" name="username" id="username">
                            <span id="error_username" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="password">Password:</label>
                            <input type="text" required="required" name="password" id="password">
                            <span id="error_password" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="name">Nome:</label>
                            <input type="text" required="required" name="name" id="name">
                            <span id="error_name" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="surname">Cognome:</label>
                            <input type="text" required="required" name="surname" id="surname">
                            <span id="error_surname" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="mail">e-mail:</label>
                            <input type="text" required="required" name="mail" id="mail">
                            <span id="error_mail" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="creditCard">Circuito della carta di credito:</label>
                            <input type="text" required="required" name="creditCard" id="creditCard">
                            <span id="error_creditCard" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="creditCardNumber">Numero della carta di credito:</label>
                            <input type="text" required="required" name="creditCardNumber" id="creditCardNumber">
                            <span id="error_creditCardNumber" class="form_error"> </span>
                        </li>
                    </ul>
                    <br/>
                </li>
                <li>
                    Indirizzo di fatturazione:
                    <br/>
                    <ul class="form_ul">
                        <li>
                            <label for="city">Citt&agrave; :</label>
                            <input type="text" required="required" name="city" id="city">
                            <span id="error_city" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="cap">CAP :</label>
                            <input type="text" required="required" name="cap" id="cap">
                            <span id="error_cap" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="street">Via:</label>
                            <input type="text" required="required" name="street" id="street">
                            <span id="error_street" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="streetNumber">Numero Civico:</label>
                            <input type="text" required="required" name="streetNumber" id="streetNumber">
                            <span id="error_streetNumber" class="form_error"> </span>
                        </li>
                    </ul>
                    <br/>
                </li>
            </ul>
        <p><button id="submit-registration">Conferma Registrazione</button></p>
