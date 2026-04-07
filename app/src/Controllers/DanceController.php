<?php

namespace App\Controllers;

use App\Services\Interfaces\IArtistService;
use App\Services\Interfaces\IDanceEventService;
use App\Models\Enums\EventTypeEnum;

use App\Services\ArtistService;
use App\Services\DanceEventService;
use App\Repositories\ArtistRepository;
use App\Repositories\DanceEventRepository;
use App\Models\ArtistModel;

class DanceController
{
    // Service to handle all artist (DJ) data
    private IArtistService $artistService;

    // Service to handle all dance event data
    private IDanceEventService $danceEventService;

    /**
     * Constructor
     * Since the router creates the controller without arguments,
     * we create the repository and service objects manually here.
     */
    public function __construct()
    {
        $this->artistService = new ArtistService(new ArtistRepository());
        $this->danceEventService = new DanceEventService(new DanceEventRepository());
    }

    /**
     * /dance
     * Shows the Dance homepage with all DJs and their events
     */
    public function index()
    {
        // Get all artists from database
        // On this page we treat them as DJs
        $artists = $this->artistService->getDanceArtists();

        // This array will store only artists who have dance events
        $lineup = [];

        // Loop through each artist
        foreach ($artists as $dj) {

            // Get only this DJ's dance events
            $events = $this->danceEventService->getEventsForArtist(
                $dj->getId(),
                EventTypeEnum::DanceEvent
            );

            // Only include DJs who actually have dance events
            if (!empty($events)) {
                $lineup[] = [
                    'dj' => $dj,
                    'events' => $events
                ];
            }
        }

        // Load Dance homepage view
        include __DIR__ . '/../Views/admin/Dance/index.php';
    }

public function adminIndex()
{
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }

    $artists = $this->artistService->getDanceArtists();

    $events = $this->danceEventService->getAllDanceEvents();

    include __DIR__ . '/../Views/admin/Dance/adminIndex.php';
}
    /**
     * /dance/{id}
     * Shows one DJ detail page
     */
   public function detail($vars)
{
    // Get id from the URL
    $id = (int)$vars['id'];

    // Get the clicked DJ from the shared Artist table
    $dj = $this->artistService->getArtistById($id);

    // If no DJ exists with that ID, show 404
    if (!$dj) {
        http_response_code(404);
        echo 'DJ not found';
        return;
    }

    // Get all dance events for this DJ
    $events = $this->danceEventService->getEventsForArtist(
        $id,
        \App\Models\Enums\EventTypeEnum::DanceEvent
    );

    // Load the correct detail page
   include __DIR__ . '/../Views/admin/dance/detail.php';
}
public function showCreateForm()
{
    // Open the Dance create artist form page
    // This loads the file where the admin can add a new dance artist
    include __DIR__ . '/../Views/admin/Dance/createArtist.php';
}


public function store()
{
    // Check if the request is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Upload image if provided
        $fileName = $this->handleImageUpload('image_file', 'artist');

        // Create new artist model
        $artist = new \App\Models\ArtistModel();

        // Basic artist fields from form
        $artist->setName(trim($_POST['name'] ?? ''));
        $artist->setShortDescription(trim($_POST['short_description'] ?? ''));
        $artist->setDescription(trim($_POST['description'] ?? ''));

        // Very important: make sure this artist belongs to Dance
        $artist->setArtistType('dance');

        // Save uploaded image path
        if ($fileName) {
            $artist->setImageUrl('/assets/uploads/dance/artists/' . $fileName);
        }

        // Save artist
        if ($this->artistService->createArtist($artist)) {
            header('Location: /admin/dance?status=created');
            exit;
        }
    }

    // If form not submitted or save fails, show form again
    include __DIR__ . '/../Views/admin/dance/createArtist.php';
}

public function showEditForm($vars)
{
    //  Get the ID from the URL (e.g. /admin/dance/edit/5)
    $id = (int)$vars['id'];

    //  Get the artist from the database using the ID
    $artist = $this->artistService->getArtistById($id);

    //  If no artist is found, redirect back with an error
    if (!$artist) {
        header('Location: /admin/dance?error=notfound');
        exit;
    }

    //  Load the edit form page
    // The $artist will be available inside this view
    include __DIR__ . '/../Views/admin/Dance/editArtist.php';
}

//this is the save edited artist part.
//gets the artist by ID
//checks if that artist exists
//reads the edited form values
//uploads a new image if one was chosen
//updates the artist in the database
//redirects back to Dance admin page

public function update($vars)
{
    $id = (int)$vars['id'];

    $artist = $this->artistService->getArtistById($id);

    if (!$artist) {
        header('Location: /admin/dance');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $artist->setName(trim($_POST['name'] ?? ''));
        $artist->setShortDescription(trim($_POST['short_description'] ?? ''));
        $artist->setDescription(trim($_POST['description'] ?? ''));

        // Keep this artist in the Dance category
        $artist->setArtistType('dance');

        $newImage = $this->handleImageUpload('image_file', 'artist');

        if ($newImage) {
            $artist->setImageUrl('/assets/uploads/dance/artists/' . $newImage);
        }

        if ($this->artistService->updateArtist($id, $artist)) {
            header('Location: /admin/dance?status=updated');
            exit;
        }
    }

    include __DIR__ . '/../Views/admin/Dance/editArtist.php';
}

public function delete($vars)
{
    //  Get the artist ID from the URL
    $id = (int)$vars['id'];

    //  Delete the artist from the database
    $this->artistService->deleteArtist($id);

    //  Redirect back to the Dance admin page with a success message
    header('Location: /admin/dance?status=deleted');
    exit;
}

private function handleImageUpload(string $inputName, string $type = 'artist'): ?string
{
    if (!isset($_FILES[$inputName])) {
        return null;
    }

    $file = $_FILES[$inputName];

    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        return null;
    }

    $uploadDir = __DIR__ . '/../../public/assets/uploads/dance/artists/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $safeName = preg_replace('/[^A-Za-z0-9_-]/', '-', $originalName);
    $newFileName = $safeName . '-' . time() . '.' . $extension;

    $targetPath = $uploadDir . $newFileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return null;
    }

    return $newFileName;
}
public function showCreateEventForm()
{
    $artists = $this->artistService->getDanceArtists();

    include __DIR__ . '/../Views/admin/dance/createEvent.php';
}

public function storeEvent()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $event = new \App\Models\DanceEventModel();

        // Convert HTML datetime-local format to SQL Server datetime format
        $startDateTime = !empty($_POST['start_datetime'])
            ? date('Y-m-d H:i:s', strtotime($_POST['start_datetime']))
            : '';

        $endDateTime = !empty($_POST['end_datetime'])
            ? date('Y-m-d H:i:s', strtotime($_POST['end_datetime']))
            : null;

        $event->setArtistId((int)($_POST['artist_id'] ?? 0));
        $event->setDanceVenueId((int)($_POST['venue_id'] ?? 0));
        $event->setStartDateTime($startDateTime);
        $event->setEndDateTime($endDateTime);
        $event->setPrice((float)($_POST['price'] ?? 0));
        $event->setCapacity((int)($_POST['capacity'] ?? 0));
        $event->setDisplayTitle(trim($_POST['title'] ?? ''));

        if ($this->danceEventService->createDanceEvent($event)) {
            header('Location: /admin/dance?status=event-created');
            exit;
        }
    }

    $artists = $this->artistService->getDanceArtists();
    include __DIR__ . '/../Views/admin/dance/createEvent.php';
}
public function showEditEventForm($vars)
{
    $id = (int)$vars['id'];

    $event = $this->danceEventService->getDanceEventById($id);

    if (!$event) {
        header('Location: /admin/dance?error=event-notfound');
        exit;
    }

    $artists = $this->artistService->getDanceArtists();

    include __DIR__ . '/../Views/admin/dance/editEvent.php';
}
public function deleteEvent($vars)
{
    $id = (int)$vars['id'];

    $this->danceEventService->deleteDanceEvent($id);

    header('Location: /admin/dance?status=event-deleted');
    exit;
}

public function updateEvent($vars)
{
    // Get the event ID from the URL
    $id = (int)$vars['id'];

    // Find the event in the database
    $event = $this->danceEventService->getDanceEventById($id);

    // If event does not exist, go back
    if (!$event) {
        header('Location: /admin/dance?error=event-notfound');
        exit;
    }

    // Check if the form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Convert HTML datetime-local to SQL format
        $startDateTime = !empty($_POST['start_datetime'])
            ? date('Y-m-d H:i:s', strtotime($_POST['start_datetime']))
            : '';

        $endDateTime = !empty($_POST['end_datetime'])
            ? date('Y-m-d H:i:s', strtotime($_POST['end_datetime']))
            : null;

        // Update event values from the form
        $event->setArtistId((int)($_POST['artist_id'] ?? 0));
        $event->setDanceVenueId((int)($_POST['venue_id'] ?? 0));
        $event->setDisplayTitle(trim($_POST['title'] ?? ''));
        $event->setStartDateTime($startDateTime);
        $event->setEndDateTime($endDateTime);
        $event->setPrice((float)($_POST['price'] ?? 0));
        $event->setCapacity((int)($_POST['capacity'] ?? 0));

        // Save updated event
        if ($this->danceEventService->updateDanceEvent($id, $event)) {
            header('Location: /admin/dance?status=event-updated');
            exit;
        }
    }

    // Reload form if first open or update fails
    $artists = $this->artistService->getDanceArtists();
    include __DIR__ . '/../Views/admin/dance/editEvent.php';
}

}