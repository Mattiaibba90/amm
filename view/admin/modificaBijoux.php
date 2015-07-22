<h3>Modifica Bijou</h3>
    <p>Compila il form:</p>
            <form action="admin/home" method="post">
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
        <p><button type="submit" name="cmd" value="modificaBijoux">Conferma</button></p>
    </form>
