<h2>Configurer le cache</h2>
<form action="" method="post">
    <fieldset>
        <legend>&nbsp;Activer le cache du site&nbsp;</legend>
        <table>
            <tr>
                <td><label>OUI</label></td>
                <td><input type="radio" id="1" name="activeCache" value="1" <?= ($activeCache == 1) ? 'checked' : null ?>/></td>
                <td><label>NON</label></td>
                <td><input type="radio" id="0" name="activeCache" value="0" <?= ($activeCache == 0) ? 'checked' : null ?>/></td>
            </tr>
        </table>
    </fieldset>
    <input class="button save" type="submit" value="Enregistrer"/>
    <a href="delete-cache.html" class="button delete">Effacer le cache</a>
</form>