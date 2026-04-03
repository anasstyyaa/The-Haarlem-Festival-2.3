<?php require_once __DIR__ . '../../partials/header.php'; ?>

<div class="container">
    <div class="row align-items-center mb-5">
        <div class="col-md-8">
            <h1 class="fw-bold">My Dashboard</h1>
            <p class="text-muted">Welcome back, <?= htmlspecialchars($user->getFullName()) ?>. Manage your account and view your tickets.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="/profile/edit" class="btn btn-dark shadow-sm px-4">
                <i class="bi bi-pencil-square me-2"></i>Edit Profile
            </a>
        </div>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-center">
                    <?php
                        $pic = $user->getProfilePicture();
                        $img = !empty($pic) ? $pic : "https://ui-avatars.com/api/?name=" . urlencode($user->getFullName()) . "&size=150&background=random";
                    ?>
                    <img src="<?= htmlspecialchars($img) ?>" class="rounded-circle border p-1 mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    
                    <h5 class="fw-bold mb-0"><?= htmlspecialchars($user->getFullName()) ?></h5>
                    <p class="text-muted small mb-4">@<?= htmlspecialchars($user->getUserName()) ?></p>

                    <div class="text-start small">
                        <div class="mb-3">
                            <label class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Email Address</label>
                            <div class="fw-medium"><?= htmlspecialchars($user->getEmail()) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Phone Number</label>
                            <div class="fw-medium"><?= htmlspecialchars($user->getPhoneNumber() ?: 'Not linked') ?></div>
                        </div>
                    </div>

                    <hr class="my-4 text-light">

                    <form method="POST" action="/profile/delete" onsubmit="return confirm('Are you sure? This cannot be undone.');">
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 border-0">
                            <i class="bi bi-trash3 me-2"></i>Delete Account
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 mt-2"><i class="bi bi-ticket-perforated me-2 text-primary"></i>My Purchased Tickets</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($tickets)): ?>
                        <div class="text-center py-5">
                            <div class="display-1 text-light mb-3"><i class="bi bi-calendar2-x"></i></div>
                            <p class="text-muted">You haven't bought any tickets yet.</p>
                            <a href="/events" class="btn btn-primary btn-sm px-4">Go to Festival Program</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="ps-4 border-0">Event</th>
                                        <th class="border-0">Date & Time</th>
                                        <th class="border-0">Venue / Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tickets as $ticket): 
                                        $event = $ticket->getEvent();
                                        $details = $event->getDetails();
                                        $type = $event->getEventType()->value;
                                        
                                        $startTime = 'TBD';
                                        $venue = 'Festival Grounds';
                                        $title = 'Festival Event';

                                        if ($type === 'jazzpass' && $details instanceof \App\Models\JazzPassModel) {
                                            $title = $details->getTitle();
                                            $venue = "All Jazz Venues";
                                            $startTime = "Festival Period"; 
                                        } elseif ($type === 'jazz' && is_array($details)) {
                                            $jazzEvent = $details['jazzEvent'] ?? null;
                                            $artist = $details['artist'] ?? null;
                                            $venueInfo = $details['venueInfo'] ?? $details['venue'] ?? null;

                                            if ($jazzEvent instanceof \App\Models\JazzEventModel) {
                                                $startTime = $jazzEvent->getStartDateTime();
                                            }
                                            
                                            if ($artist instanceof \App\Models\ArtistModel) {
                                                $title = $artist->getName();
                                            }
                   
                                            if (is_array($venueInfo)) {
                                                $venue = $venueInfo['name'] ?? $venueInfo['address'] ?? 'Jazz Venue';
                                            } elseif (is_object($venueInfo) && method_exists($venueInfo, 'getName')) {
                                                $venue = $venueInfo->getName();
                                            }
                                        } elseif ($type === 'reservation' && $details instanceof \App\Models\Yummy\RestaurantModel) {
                                            $startTime = $details->getSessionData()->getStartTime();
                                            $venue = $details->getLocation();
                                            $title = $details->getName();
                                        } elseif ($type === 'tour' && $details instanceof \App\Models\HistoryEventModel) {
                                            $startTime = $details->getStartTime();
                                            $venue = $details->getVenue()->getLocation();
                                            $title = "History Tour";
                                        } elseif ($type === 'kids' && $details instanceof \App\Models\KidsEventModel) {
                                            $title = $details->getType(); 
                                            $venue = $details->getLocation();                                                                
                                            $startTime = $details->getDay() . ' ' . $details->getStartTime();
                                        }
                                    ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold"><?= htmlspecialchars($title) ?></div>
                                            <span class="badge rounded-pill bg-light text-dark border small fw-normal">
                                                <?= ucfirst($type) ?> × <?= $ticket->getNumberOfPeople() ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-medium small"><?= ($startTime !== 'TBD') ? date('D, M j, Y', strtotime($startTime)) : 'TBD' ?></div>
                                            <div class="text-muted small"><?= ($startTime !== 'TBD') ? date('H:i', strtotime($startTime)) : '' ?></div>
                                        </td>
                                        <td>
                                            <div class="text-muted small">
                                                <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($venue) ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '../../partials/footer.php'; ?>