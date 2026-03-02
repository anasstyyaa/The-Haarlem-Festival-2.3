<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">My Profile</h2>
        <a href="/profile/edit" class="btn btn-outline-dark">Edit Profile</a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body d-flex gap-4">
            <div style="width:140px;">
                <?php
                    $pic = $user->getProfilePicture();
                    $img = !empty($pic) ? $pic : "https://via.placeholder.com/140?text=User";
                ?>
                <img src="<?= htmlspecialchars($img) ?>" class="img-fluid rounded" alt="Profile picture">
            </div>

            <div class="flex-grow-1">
                <p class="mb-1"><strong>Username:</strong> <?= htmlspecialchars($user->getUserName()) ?></p>
                <p class="mb-1"><strong>Full name:</strong> <?= htmlspecialchars($user->getFullName()) ?></p>
                <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($user->getEmail()) ?></p>
                <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($user->getPhoneNumber()) ?></p>

                <hr>

                <form method="POST" action="/profile/delete" onsubmit="return confirm('Are you sure you want to delete your account?');">
                    <button type="submit" class="btn btn-danger">Delete my account</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>