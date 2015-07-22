<ul>
    <li>
        <h3><?= $bijou->getNameBijou(); ?></h3>
    </li>
    <li><p>Materiale: <?= $bijou->getMaterial(); ?></p></li>
    <li><p>Categoria: <?= $bijou->getTypeBijou(); ?></p></li>
    <li><p>Disponibilit&agrave;: <?= $bijou->getAvaibility(); ?></p></li>
    <li><p>Prezzo: <?= $bijou->getActualPrice(); ?></p></li>
    <li>
        <form method="post" action="utente/home">
            <input type="hidden" name="idBijou" value="<?= $bijou->getId(); ?>"/>
            <input type="hidden" name="name_bijou" value="<?= $bijou->getNameBijou() ?>"/>
            <input type="hidden" name="materiale" value="<?= $bijou->getMaterial() ?>"/>
            <input type="hidden" name="categoria" value="<?= $bijou->getTypeBijou() ?>"/>
            <label for="quantita">Quanti ne vuoi porre nel carrello? </label>
            <input type="number" name="quantita" id="quantita"/>
            <input type="hidden" name="disponibilita" value="<?= $bijou->getAvaibility() ?>"/>
            <input type="hidden" name="prezzo" value="<?= $bijou->getActualPrice() ?>"/>
            <button type="submit" name="cmd" value="carrello">Aggiungi al Carrello</button>
        </form>
    </li>
</ul>
