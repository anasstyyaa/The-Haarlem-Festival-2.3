<?php if (isset($_GET['status'])): ?>
    <div class="alert alert-dismissible fade show shadow-sm border-0 
        <?= str_contains($_GET['status'], 'error') ? 'alert-danger' : 'alert-success' ?>" role="alert">
        
        <div class="d-flex align-items-center">
            <i class="bi <?= str_contains($_GET['status'], 'error') ? 'bi-exclamation-octagon' : 'bi-check-circle' ?> me-2 fs-5"></i>
            <div>
                <?php
                switch ($_GET['status']) {
                    case 'sessions_added':
                        echo "<strong>Success!</strong> New sessions have been added to the schedule.";
                        break;
                    case 'deleted':
                        echo "<strong>Deleted!</strong> The item has been removed successfully.";
                        break;
                    case 'updated':
                        echo "<strong>Updated!</strong> Changes have been saved.";
                        break;
                    case 'error':
                        echo "<strong>Error!</strong> Something went wrong. Please try again.";
                        break;
                    default:
                        echo "Action completed successfully.";
                }
                ?>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger shadow-sm border-0" role="alert">
        <h6 class="alert-heading fw-bold"><i class="bi bi-x-circle me-2"></i>Please fix the following:</h6>
        <ul class="mb-0 small">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>