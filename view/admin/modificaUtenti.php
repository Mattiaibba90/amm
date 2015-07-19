<span id="confirm_mod" class="form_confirm"> </span>
<ul>
    <li>
        <h3>Modifica un altro utente:</h3>
        <ul>
            <li>
                Dati personali:
                <br/>
                    <ul class="form_ul">
                        <li>
                            <label for="name">Nome:</label>
                            <input type="text" required="required" name="name" value="<?= $user->getName(); ?>" id="name">
                            <span id="error_name" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="surname">Cognome:</label>
                            <input type="text" required="required" name="surname" value="<?= $user->getSurname(); ?>" id="surname">
                            <span id="error_surname" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="mail">e-mail:</label>
                            <input type="text" required="required" name="mail" value="<?= $user->getEmail(); ?>" id="mail">
                            <span id="error_mail" class="form_error"> </span>
                        </li>
                    </ul>
                <br/>
            </li>
            <li>
                
                Indirizzo di recapito della merce acquistata:
                <br/>
                    <ul class="form_ul">
                        <li>
                            <label for="city">Citt&agrave;:</label>
                            <input type="text" required="required" name="city" value="<?= $user->getCity(); ?>" id="city">
                            <span id="error_city" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="cap">CAP :</label>
                            <input type="text" required="required" name="cap" value="<?= $user->getCap(); ?>" id="cap">
                            <span id="error_cap" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="street">Via:</label>
                            <input type="text" required="required" name="street" value="<?= $user->getStreet(); ?>" id="street">
                            <span id="error_street" class="form_error"> </span>
                        </li>
                        <li>
                            <label for="streetNumber">Numero Civico:</label>
                            <input type="text" required="required" name="streetNumber" value="<?= $user->getStreetNumber(); ?>" id="streetNumber">
                            <span id="error_streetNumber" class="form_error"> </span>
                        </li>
                    </ul>
                <br/>
            </li>
        </ul>
        <p><button id="pannelloControllo">Conferma i nuovi Dati</button></p>
    </li>
</ul>
