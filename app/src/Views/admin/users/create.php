<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Create New User</h5>
            </div>
            <div class="card-body">
                <form action="/admin/users/create" method="POST">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="fullName" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Username</label>
                            <input type="text" name="userName" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number</label>
                                <input type="text" name="phoneNumber" class="form-control" placeholder="+31 6 12345678">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Role</label>
                                <select name="role" class="form-select">
                                    <option value="User">User</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>
                        </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="/admin/users" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>