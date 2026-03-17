 <?php
use App\Models\TextModel;
/** @var TextModel $text */
?>
<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>


<form action="/admin/elements/edit/<?= $text->getId() ?>" method="POST" enctype="multipart/form-data">
<textarea name="newText" class="form-control wysiwyg-editor" rows="12">
                                <?= $text->getContent() ?>
                            </textarea>
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
</form>