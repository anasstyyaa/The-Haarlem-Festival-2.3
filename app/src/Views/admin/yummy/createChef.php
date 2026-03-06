<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="/admin/yummy" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0">Add New Chef</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Chef Details</h5>
            </div>
            
            <div class="card-body p-4">
                <form action="/admin/chefs/create" method="POST" enctype="multipart/form-data">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Chef Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Gordon Ramsay" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Years of Experience</label>
                        <input type="number" name="experience_years" class="form-control" placeholder="e.g. 15" min="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Biography (WYSIWYG)</label>
                        <textarea name="description" class="form-control wysiwyg-editor" rows="10" placeholder="Tell the story of the chef, their career, and specialties..."></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Profile Image</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                            <div class="form-text">Select a professional portrait (JPG, PNG, or WebP).</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="/admin/yummy" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-success px-4">Save Chef</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>