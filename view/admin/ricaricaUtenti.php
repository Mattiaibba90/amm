<ul>
    <li>
        <h3>Ricarica il credito di un utente</h3>
        <p>Circuito: <?= $user->getCreditCard(); ?></p>
        <p>Numero: <?= $user->getCreditCardNumber(); ?></p>
        <p>Credito: <?= $user->getCredit(); ?></p>
        <form action="utente/home" method="post">
            <br/>
                <ul class="form_ul">
                    <li>
                        <label for="marca">Circuito:</label>
                        <input type="text" required="required" readonly="readonly" name="marca" value="<?= $user->getCreditCard(); ?>" id="marca">
                    </li>
                    <li>
                        <label for="numero_carta">Numero di carta:</label>
                        <input type="text" required="required" readonly="readonly" name="numero_carta" value="<?= $user->getCreditCardNumber(); ?>" id="numero_carta">
                    </li>
                    <li>
                        <label for="importo_ricarica">Importo:</label>
                        <input type="text" required="required" name="importo_ricarica" id="importo_ricarica">
                    </li>
                </ul>
            <br/>
        <p><button type="submit" name="cmd" value="ricaricaUtente">Conferma Ricarica</button></p>
        </form>
    </li>
</ul>
