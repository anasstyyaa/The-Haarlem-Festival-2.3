<form method="POST" action="/admin/elements/store" enctype="multipart/form-data">
     <input type="hidden" name="type" value="image">
    <input type="hidden" name="section" value="<?= $section ?>">
    <input type="hidden" name="pageName" value="<?= $pageName ?>">
    <input type="file" name="image">
    <input type="text" name="altText">
    <button type="submit">Upload</button>
</form>