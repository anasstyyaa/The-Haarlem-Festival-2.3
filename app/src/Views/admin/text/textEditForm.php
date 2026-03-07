 <?php
use App\Models\TextModel;
/** @var TextModel $text */
?>
<?php
include_once 'partials/header.php';
?>

<form action="/admin/text/edit/<?= $text->getId() ?>" method="POST" enctype="multipart/form-data">
<textarea name="long_description" class="form-control wysiwyg-editor" rows="12">
                                <?= $text->getContent() ?>
                            </textarea>
</form>