<form method="POST" action="/admin/elements/store">
    
    <input type="hidden" name="type" value="button">
    <input type="hidden" name="section" value="<?= htmlspecialchars($section) ?>">
    <input type="hidden" name="pageName" value="<?= htmlspecialchars($pageName) ?>">

    <div>
        <label>Button Text:</label><br>
        <input type="text" name="text" required>
    </div>

    <div>
        <label>Button Path (URL):</label><br>
        <input type="text" name="path" placeholder="/some-page" required>
    </div>

    <button type="submit">Create Button</button>
</form>