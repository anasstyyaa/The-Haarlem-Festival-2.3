<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Edit Profile</h2>
        <a href="/profile" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php
        $flashError = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);
    ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!empty($flashError)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="/profile/edit" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input class="form-control" name="email" value="<?= htmlspecialchars($user->getEmail()) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input class="form-control" name="userName" value="<?= htmlspecialchars($user->getUserName()) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input class="form-control" name="fullName" value="<?= htmlspecialchars($user->getFullName()) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input class="form-control" name="phoneNumber" value="<?= htmlspecialchars($user->getPhoneNumber()) ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Profile Picture (optional)</label>
                        <input class="form-control" type="file" name="profilePicture" accept="image/png,image/jpeg,image/webp">
                    </div>

                    <div class="col-12">
                        <hr>
                        <h5 class="mb-2">Change Password (optional)</h5>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">New Password</label>
                        <input class="form-control" type="password" name="newPassword">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input class="form-control" type="password" name="confirmPassword">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-dark">Save Changes</button>
                    <a href="/profile" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>