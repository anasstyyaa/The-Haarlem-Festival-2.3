<form method="POST" action="/admin/elements/editButton/<?= $button->getId() ?>">

    <div>
        <label>Button Text:</label><br>
        <input type="text" name="text" value="<?= htmlspecialchars($button->getText()) ?>" required>
    </div>

    <div>
        <label>Button Path:</label><br>
        <input type="text" name="path" value="<?= htmlspecialchars($button->getPath()) ?>" required>
    </div>

    <button type="submit">Save Changes</button>
</form>