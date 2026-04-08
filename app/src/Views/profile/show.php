<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="festival-dark-page">
    <div class="container py-5 mt-5 program-container">
        
        <div class="row align-items-center mb-5 p-4 border-b-peach">
            <div class="col-md-8">
                <h1 class="hero-overlay-title text-light-peach">My Dashboard</h1>
                <p class="text-light-peach opacity-80">Welcome back, <?= htmlspecialchars($user->getFullName()) ?>. Manage your festival experience.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="/profile/edit" class="more-btn d-inline-block w-auto px-4">
                    <i class="bi bi-pencil-square me-2"></i>Edit Profile
                </a>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-lg-4">
                <div class="festival-dark-card text-center h-100">
                    <?php
                        $pic = $user->getProfilePicture();
                        $img = !empty($pic) ? $pic : "https://ui-avatars.com/api/?name=" . urlencode($user->getFullName()) . "&size=150&background=F4C7B8&color=3B0B00";
                    ?>
                    <div class="avatar-wrapper mb-3 d-inline-block">
                        <img src="<?= htmlspecialchars($img) ?>" class="profile-avatar-styled">
                    </div>
                    
                    <h4 class="fw-bold text-light-peach mb-0"><?= htmlspecialchars($user->getFullName()) ?></h4>
                    <p class="text-peach x-small mb-4">@<?= htmlspecialchars($user->getUserName()) ?></p>

                    <div class="text-start info-list pt-3 border-t-peach">
                        <div class="info-item mb-3">
                            <label class="section-label">Email Address</label>
                            <div class="fw-medium text-light-peach small"><?= htmlspecialchars($user->getEmail()) ?></div>
                        </div>
                        <div class="info-item mb-3">
                            <label class="section-label">Phone Number</label>
                            <div class="fw-medium text-light-peach small"><?= htmlspecialchars($user->getPhoneNumber() ?: 'Not linked') ?></div>
                        </div>
                    </div>

                    <div class="d-grid mt-5">
                        <form method="POST" action="/profile/delete" onsubmit="return confirm('Are you sure? This cannot be undone.');">
                            <button type="submit" class="btn btn-delete-custom btn-sm w-100">
                                <i class="bi bi-trash3 me-2"></i>Delete Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="festival-dark-card ticket-container-card p-0 overflow-hidden h-100">
                    <div class="card-header-peach p-4">
                        <h4 class="fw-bold mb-0 text-dark-brown">
                            <i class="bi bi-ticket-perforated me-2"></i>Purchased Tickets
                        </h4>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table custom-dark-table align-middle mb-0">
                            <thead>
                                <tr>
                                   <th class="ps-4 text-peach border-0">Event</th>
                                    <th class="text-peach border-0">Location</th> 
                                    <th class="text-peach border-0">Schedule</th>
                                    <th class="text-center text-peach border-0">Guests</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($tickets)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-peach opacity-50">
                                            No tickets found. <br>
                                            <a href="/program" class="text-light-peach small">Browse events?</a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($tickets as $ticket): ?>
                                        <<tr class="border-b-peach-light"> <?php include __DIR__ . '/../partials/programItem.php'; ?>
                                     </tr> <?php endforeach; ?>                                   
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (isset($totalPages) && $totalPages > 1): ?>
                        <div class="p-3">
                            <?php 
                                $paginationTheme = 'peach'; 
                                include __DIR__ . '/../partials/pagination.php'; 
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div> 
        </div> 
    </div> 
</div> 

<?php require_once __DIR__ . '/../partials/footer.php'; ?>