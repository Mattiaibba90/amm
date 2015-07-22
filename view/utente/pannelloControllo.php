<ul>
        <li>
            <h3>Pannello di controllo di <?php echo $user->getUsername(); ?></h3>
            <ul>
                <li>
                    <a href="utente/modificaDati">Modifica dati personali</a>
                </li>
                <li>
                    <a href="utente/ricaricaCredito">Ricarica credito</a>
                </li>
                <li>
                    <a href="utente/carrello">Carrello</a>
                </li>
                <li>
                    <a href="utente/cronologiaOrdini">Cronologia ordini</a>
                </li>
                <li>
                    <a href="utente/home">Torna alla home</a>
                </li>
                <li>
                    <form method="post" action="utente/logout">
                        <button type="submit" name="cmd" value="logout">Logout</button>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
