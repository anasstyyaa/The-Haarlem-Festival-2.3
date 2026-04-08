<?php 
use App\ViewModels\PageElementViewModel;
use App\Models\TextModel;
use App\Models\ImageModel;
use App\Models\ButtonModel;
/** @var PageElementViewModel $vm */
?>
<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>


<style>
table {
    border-collapse: collapse;
    width: 85%;
    margin: 30px auto;
}

th, td {
    border: 1px solid #aaa;
    padding: 10px;
    text-align: center;
}

th {
    background: #f2f2f2;
}

.actions{
    display:flex;
    justify-content:center;
    gap:8px;
}

.edit-btn{
    background:#4CAF50;
    color:white;
    border:none;
    padding:6px 12px;
}

.delete-btn{
    background:#e53935;
    color:white;
    border:none;
    padding:6px 12px;
}

.add-btn{
    margin:20px auto;
    display:block;
    padding:10px 20px;
}

.section-title{
    text-align:center;
    margin-top:40px;
}
td img {
    max-width: 150px;
    height: auto;
    display: block;
    margin: 0 auto;
}
</style>
</head>
<body>

<h1 style="text-align:center;">Admin Page Manager</h1>

<?php $num = 1; ?>
<?php foreach ($vm->getSections() as $section => $elements): ?>

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-light fw-bold">
        Section: <?= $num++ ?>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="ps-3" style="width: 15%;">Element ID</th>
                    <th style="width: 50%;">Preview</th>
                    <th class="text-center" style="width: 35%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($elements as $element): ?>
                <tr>
                    <td class="ps-3 text-muted small">
                        #<?= htmlspecialchars($element->getId()) ?>
                    </td>

                    <td>
                        <div class="admin-element-preview border rounded bg-light" style="max-height: 120px; overflow: hidden; max-width: 300px;">
                            <?= $element->render(); ?>
                        </div>
                    </td>

                    <td>
                        <div class="d-flex justify-content-center gap-2">

                            <?php if($element instanceof TextModel): ?>
                                <a href="/admin/elements/edit/<?= $element->getId() ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            <?php elseif($element instanceof ImageModel): ?>
                                <a href="/admin/elements/editImg/<?= $element->getId() ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            <?php elseif($element instanceof ButtonModel): ?>
                                <a href="/admin/elements/editButton/<?= $element->getId() ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            <?php endif; ?>

                            <?php if($element instanceof TextModel): ?>
                                <a href="/admin/elements/delete/text/<?= $element->getId() ?>" class="delete-btn">Delete Text</a>
                            <?php elseif($element instanceof ImageModel): ?>
                                <a href="/admin/elements/delete/image/<?= $element->getId() ?>" class="delete-btn">Delete Image</a>
                            <?php elseif($element instanceof ButtonModel): ?>
                                <a href="/admin/elements/delete/button/<?= $element->getId() ?>" class="delete-btn">Delete Button</a>
                            <?php endif; ?>

                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white py-3">
        <form method="GET" action="/admin/elements/createForm" class="row g-2 align-items-center justify-content-center">
            <input type="hidden" name="section" value="<?= htmlspecialchars($section) ?>">
            <input type="hidden" name="pageName" value="home">

            <div class="col-auto">
                <label class="small fw-bold text-muted">Add New:</label>
            </div>

            <div class="col-auto">
                <select name="type" class="form-select form-select-sm">
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                    <option value="button">Button</option>
                </select>
            </div>

            <div class="col-auto">
                <button class="btn btn-sm btn-success">
                    <i class="bi bi-plus-lg"></i> Create Element
                </button>
            </div>
        </form>
    </div>

</div>

<?php endforeach; ?>