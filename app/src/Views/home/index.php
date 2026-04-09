<?php use App\ViewModels\PageElementViewModel;
/** @var PageElementViewModel $vm */ ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

 <?php foreach ($vm->getSections() as $section => $elements): ?>

    <?php
        $viewFile = __DIR__ . "/sections/section{$section}.php";

        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            require __DIR__ . "/sections/default.php";
        }
    ?>

<?php endforeach; ?>


<?php require __DIR__ . '/../partials/footer.php'; ?> 