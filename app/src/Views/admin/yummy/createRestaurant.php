<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Add New Restaurant</h4>
        </div>
        <div class="card-body">
            <form action="/admin/yummy/create" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Restaurant Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cuisine Type</label>
                        <input type="text" name="cuisine" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Image URL</label>
                    <input type="text" name="image_url" class="form-control">
                </div>

                <div class="d-flex justify-content-end">
                    <a href="/admin/yummy" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-success">Save Restaurant</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>