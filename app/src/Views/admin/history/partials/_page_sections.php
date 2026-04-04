<?php
use App\Models\TextModel;
use App\Models\ImageModel;
use App\Models\ButtonModel;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">History Home Page</h3>
</div>

<?php foreach ($vm->getSections() as $section => $elements): ?>

    <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
        <h4 class="h5 mb-0">
            <i class="bi bi-layers-half me-2"></i>Section: <?= htmlspecialchars((string)$section) ?>
        </h4>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3" style="width: 15%;">Element ID</th>
                        <th style="width: 15%;">Type</th>
                        <th style="width: 40%;">Preview</th>
                        <th class="text-center" style="width: 30%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($elements as $element): ?>
                        <tr>
                            <td class="ps-3 text-muted small">
                                #<?= htmlspecialchars((string)$element->getId()) ?>
                            </td>

                            <td>
                                <?php if ($element instanceof TextModel): ?>
                                    Text
                                <?php elseif ($element instanceof ImageModel): ?>
                                    Image
                                <?php elseif ($element instanceof ButtonModel): ?>
                                    Button
                                <?php else: ?>
                                    Element
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="border rounded bg-light p-2" style="max-width: 320px; max-height: 140px; overflow: hidden;">
                                    <?= $element->render(); ?>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <?php if ($element instanceof TextModel): ?>
                                        <a href="/admin/elements/edit/<?= $element->getId() ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    <?php elseif ($element instanceof ImageModel): ?>
                                        <a href="/admin/elements/editImg/<?= $element->getId() ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    <?php elseif ($element instanceof ButtonModel): ?>
                                        <a href="/admin/elements/editButton/<?= $element->getId() ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    <?php endif; ?>

                                    <form method="POST" action="/admin/elements/delete" onsubmit="return confirm('Delete this element?')" class="m-0">
                                        <input type="hidden" name="id" value="<?= $element->getId() ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white py-3">
            <form method="GET" action="/admin/elements/createForm" class="row g-2 align-items-center justify-content-center">
                <input type="hidden" name="section" value="<?= htmlspecialchars((string)$section) ?>">
                <input type="hidden" name="pageName" value="History">

                <div class="col-auto">
                    <label class="small fw-bold text-muted mb-0">Add New:</label>
                </div>

                <div class="col-auto">
                    <select name="type" class="form-select form-select-sm">
                        <option value="text">Text</option>
                        <option value="image">Image</option>
                        <option value="button">Button</option>
                    </select>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-lg"></i> Create Element
                    </button>
                </div>
            </form>
        </div>
    </div>

<?php endforeach; ?>