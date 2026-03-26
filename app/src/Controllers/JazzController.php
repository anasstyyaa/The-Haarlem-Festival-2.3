<?php

namespace App\Controllers;

use App\Services\Interfaces\IArtistService;
use App\Services\Interfaces\IJazzEventService; 
use App\Services\Interfaces\IJazzPassService; 
use App\Models\ArtistModel;
use App\Models\JazzEventModel; 
use App\Models\JazzPassModel;  
use App\Models\Enums\EventTypeEnum;

class JazzController
{
    private IArtistService $artistService;
    private IJazzEventService $jazzEventService;
    private IJazzPassService $jazzPassService;

    public function __construct(IArtistService $artistService, IJazzEventService $jazzEventService, IJazzPassService $jazzPassService){
        $this->artistService = $artistService;
        $this->jazzEventService = $jazzEventService;
        $this->jazzPassService = $jazzPassService;
    }

    public function index()
    {
        $artists = $this->artistService->getJazzArtists();
        $lineup = [];
        $passes = $this->jazzPassService->getAllActivePasses();

        foreach ($artists as $artist) {
            $events = $this->jazzEventService->getEventsForArtist(
                $artist->getId(),
                EventTypeEnum::JazzEvent
            );

            if (!empty($events)) {
                $lineup[] = [
                    'artist' => $artist,
                    'events' => $events
                ];
            }
        }

        include __DIR__ . '/../Views/event/jazz/index.php';
    }

    public function adminIndex()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $artists = $this->artistService->getJazzArtists();
        $events = $this->jazzEventService->getAllJazzEvents();
        $passes = $this->jazzPassService->getAllActivePasses();
        include __DIR__ . '/../Views/admin/jazz/index.php';
    }

    //artist detail 
    public function detail($vars)
    {
        $id = (int)$vars['id'];
        $artist = $this->artistService->getArtistById($id);

        if (!$artist) {
            http_response_code(404);
            echo 'Artist not found';
            return;
        }

        $events = $this->jazzEventService->getEventsForArtist(
            $id,
            EventTypeEnum::JazzEvent
        );
        include __DIR__ . '/../Views/event/jazz/detail.php';
    }

    public function showCreateForm()
    {
        include __DIR__ . '/../Views/admin/jazz/createArtist.php';
    }

    public function showCreateEventForm()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $artists = $this->artistService->getJazzArtists();
        include __DIR__ . '/../Views/admin/jazz/createEvent.php';
    }

    public function showCreatePassForm()
    {
        include __DIR__ . '/../Views/admin/jazz/createPass.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fileName = $this->handleImageUpload('image_file', 'artist', 'artists');

            $artist = new ArtistModel();
            $artist->setName(trim($_POST['name'] ?? ''));
            $artist->setShortDescription(trim($_POST['short_description'] ?? ''));
            $artist->setDescription(trim($_POST['description'] ?? ''));
            $artist->setArtistType('jazz');

            if ($fileName) {
                $artist->setImageUrl('/assets/uploads/jazz/artists/' . $fileName);
            }else{
                $artist->setImageUrl(null); 
            }

            if ($this->artistService->createArtist($artist)) {
                header('Location: /admin/jazz?status=created');
                exit;
            }
        }

        include __DIR__ . '/../Views/admin/jazz/createArtist.php';
    }

    public function storeEvent()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $event = new JazzEventModel();
            $event->setArtistId((int)($_POST['artist_id'] ?? 0));
            $event->setJazzVenueId((int)($_POST['jazz_venue_id'] ?? 0));
            $startDateTime = !empty($_POST['start_datetime'])
            ? date('Y-m-d H:i:s', strtotime($_POST['start_datetime']))
            : '';
            $endDateTime = !empty($_POST['end_datetime'])
                ? date('Y-m-d H:i:s', strtotime($_POST['end_datetime']))
                : null;
            $event->setStartDateTime($startDateTime);
            $event->setEndDateTime($endDateTime);
            $event->setPrice((float)($_POST['price'] ?? 0));

            $capacity = (int)($_POST['capacity'] ?? 0);
            $event->setCapacity($capacity);
            $event->setTicketsLeft($capacity);

            if ($this->jazzEventService->createJazzEvent($event)) {
                header('Location: /admin/jazz?status=created');
                exit;
            }
        }
        //need the artists to show in the dropdown when creating an event. (If it were inside the POST block, Then opening the page normally would give: "Undefined variable: artists", because the view needs artists!)
        $artists = $this->artistService->getJazzArtists();
        include __DIR__ . '/../Views/admin/jazz/createEvent.php';
    }

    public function storePass()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fileName = $this->handleImageUpload('image_file', 'pass', 'passes');

            $pass = new JazzPassModel();
            $pass->setTitle(trim($_POST['title'] ?? ''));
            $pass->setDescription(trim($_POST['description'] ?? ''));
            $pass->setPrice((float)($_POST['price'] ?? 0));
            $pass->setIsActive(isset($_POST['is_active']) && $_POST['is_active'] == '1');

            $capacity = (int)($_POST['capacity'] ?? 0);
            $pass->setCapacity($capacity);
            $pass->setTicketsLeft($capacity);

            if ($fileName) {
                $pass->setImageUrl('/assets/uploads/jazz/passes/' . $fileName);
            }

            if ($this->jazzPassService->createPass($pass)) {
                header('Location: /admin/jazz?status=pass-created');
                exit;
            }
        }

        include __DIR__ . '/../Views/admin/jazz/createPass.php';
    }

    public function showEditForm($vars)
    {
        $id = (int)$vars['id'];
        $artist = $this->artistService->getArtistById($id);

        if (!$artist) {
            header('Location: /admin/jazz?error=notfound');
            exit;
        }

        include __DIR__ . '/../Views/admin/jazz/editArtist.php';
    }

    public function showEditEventForm($vars)
    {
        $id = (int)$vars['id'];
        $event = $this->jazzEventService->getJazzEventById($id);

        if (!$event) {
            header('Location: /admin/jazz?error=notfound');
            exit;
        }

        $artists = $this->artistService->getJazzArtists();
        include __DIR__ . '/../Views/admin/jazz/editEvent.php';
    }

    public function showEditPassForm($vars)
    {
        $id = (int)$vars['id'];
        $pass = $this->jazzPassService->getPassById($id);

        if (!$pass) {
            header('Location: /admin/jazz?error=notfound');
            exit;
        }

        include __DIR__ . '/../Views/admin/jazz/editPass.php';
    }

    public function update($vars)
    {
        $id = (int)$vars['id'];
        $artist = $this->artistService->getArtistById($id);

        if (!$artist) {
            header('Location: /admin/jazz');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $artist->setName(trim($_POST['name'] ?? ''));
            $artist->setShortDescription(trim($_POST['short_description'] ?? ''));
            $artist->setDescription(trim($_POST['description'] ?? ''));
            $artist->setArtistType('jazz');

            $newImage = $this->handleImageUpload('image_file', 'artist', 'artists');
            if ($newImage) {
                $artist->setImageUrl('/assets/uploads/jazz/artists/' . $newImage);
            }

            if ($this->artistService->updateArtist($id, $artist)) {
                header('Location: /admin/jazz?status=updated');
                exit;
            }
        }

        include __DIR__ . '/../Views/admin/jazz/editArtist.php';
    }

    public function updateEvent($vars)
    {
        $id = (int)$vars['id'];
        $event = $this->jazzEventService->getJazzEventById($id);

        if (!$event) {
            header('Location: /admin/jazz');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $event->setArtistId((int)($_POST['artist_id'] ?? 0));
            $event->setJazzVenueId((int)($_POST['jazz_venue_id'] ?? 0));
            $startDateTime = !empty($_POST['start_datetime'])
            ? date('Y-m-d H:i:s', strtotime($_POST['start_datetime']))
            : '';
            $endDateTime = !empty($_POST['end_datetime'])
                ? date('Y-m-d H:i:s', strtotime($_POST['end_datetime']))
                : null;
            $event->setStartDateTime($startDateTime);
            $event->setEndDateTime($endDateTime);
            $event->setPrice((float)($_POST['price'] ?? 0));

            $event->setCapacity((int)($_POST['capacity'] ?? 0));
            $event->setTicketsLeft((int)($_POST['tickets_left'] ?? 0));

            if ($this->jazzEventService->updateJazzEvent($id, $event)) {
                header('Location: /admin/jazz?status=updated');
                exit;
            }
        }

        $artists = $this->artistService->getJazzArtists();
        include __DIR__ . '/../Views/admin/jazz/editEvent.php';
    }

    public function updatePass($vars)
    {
        $id = (int)$vars['id'];
        $pass = $this->jazzPassService->getPassById($id);

        if (!$pass) {
            header('Location: /admin/jazz');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pass->setTitle(trim($_POST['title'] ?? ''));
            $pass->setDescription(trim($_POST['description'] ?? ''));
            $pass->setPrice((float)($_POST['price'] ?? 0));
            $pass->setIsActive(isset($_POST['is_active']) && $_POST['is_active'] == '1');

            $pass->setCapacity((int)($_POST['capacity'] ?? 0));
            $pass->setTicketsLeft((int)($_POST['tickets_left'] ?? 0));

            $newImage = $this->handleImageUpload('image_file', 'pass', 'passes');
            if ($newImage) {
                $pass->setImageUrl('/assets/uploads/jazz/passes/' . $newImage);
            }

            if ($this->jazzPassService->updatePass($id, $pass)) {
                header('Location: /admin/jazz?status=pass-updated');
                exit;
            }
        }

        include __DIR__ . '/../Views/admin/jazz/editPass.php';
    }

    public function delete($vars)
    {
        $id = (int)$vars['id'];
        $this->artistService->deleteArtist($id);
        header('Location: /admin/jazz?status=deleted');
        exit;
    }

    public function deleteEvent($vars)
    {
        $id = (int)$vars['id'];
        $this->jazzEventService->deleteJazzEvent($id);
        header('Location: /admin/jazz?status=deleted');
        exit;
    }

    public function deletePass($vars)
    {
        $id = (int)$vars['id'];
        $this->jazzPassService->deletePass($id);
        header('Location: /admin/jazz?status=pass-deleted');
        exit;
    }

    private function handleImageUpload(string $inputName, string $prefix, string $folder): ?string
    {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/assets/uploads/jazz/' . $folder . '/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid($prefix . '_', true) . '.' . $extension;

        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadDir . $newFileName)) {
            return $newFileName;
        }

        return null;
    }
    
}