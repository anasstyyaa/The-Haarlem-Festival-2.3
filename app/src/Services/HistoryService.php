<?php

namespace App\Services;

use App\Models\HistoryVenueModel;
use App\Models\HistoryEventModel;
use App\Repositories\Interfaces\IHistoryEventRepository;
use App\Repositories\Interfaces\IHistoryVenueRepository;
use App\Repositories\Interfaces\IImageRepository;
use App\Services\Interfaces\IHistoryService;
use App\Services\Interfaces\IPersonalProgramService;
use App\ViewModels\PageElementViewModel;
use App\Services\Interfaces\IPageElementService;

class HistoryService implements IHistoryService
{
    private IHistoryEventRepository $historyEventRepo;
    private IHistoryVenueRepository $historyVenueRepo;
    private IImageRepository $imageRepository;
    private IPersonalProgramService $programService;
    private IPageElementService $pageService;
    private ImageService $imageService;

    public function __construct(
        IHistoryEventRepository $historyEventRepo,
        IHistoryVenueRepository $historyVenueRepo,
        IImageRepository $imageRepository,
        IPersonalProgramService $programService,
        IPageElementService $pageService
    ) {
        $this->historyEventRepo = $historyEventRepo;
        $this->historyVenueRepo = $historyVenueRepo;
        $this->imageRepository = $imageRepository;
        $this->programService = $programService;
        $this->pageService = $pageService;
        $this->imageService = new ImageService($imageRepository);
    }

    public function getAllSessions(): array
    {
        return $this->historyEventRepo->getAll();
    }

    public function getSessionByEventId(int $eventId): ?HistoryEventModel
    {
        return $this->historyEventRepo->getByEventId($eventId);
    }

    public function createSession(HistoryEventModel $event): bool
    {
        return $this->historyEventRepo->create($event);
    }
    public function createTourFromForm(array $post): HistoryEventModel
    {
        $slotDate = trim($post['slotDate'] ?? '');
        $startTime = trim($post['startTime'] ?? '');
        $language = trim($post['language'] ?? '');
        $duration = (int)($post['duration'] ?? 150);
        $minAge = (int)($post['minAge'] ?? 12);
        $capacity = (int)($post['capacity'] ?? 12);
        $priceIndividual = (float)($post['priceIndividual'] ?? 17.50);
        $priceFamily = (float)($post['priceFamily'] ?? 60.00);

        if ($slotDate === '' || $startTime === '' || $language === '') {
            throw new \InvalidArgumentException('Date, time and language are required.');
        }

        $tour = new HistoryEventModel(
            0,
            0,
            $slotDate,
            $startTime,
            $language,
            $duration,
            $minAge,
            $capacity,
            $priceIndividual,
            $priceFamily
        );

        $success = $this->createSession($tour);

        if (!$success) {
            throw new \RuntimeException('Failed to create tour.');
        }

        return $tour;
    }
    public function updateTourFromForm(array $post): HistoryEventModel
    {
        $eventId = (int)($post['eventId'] ?? 0);
        $historyEventId = (int)($post['historyEventId'] ?? 0);

        $existingTour = $this->getSessionByEventId($eventId);
        if (!$existingTour) {
            throw new \InvalidArgumentException('Tour not found.');
        }

        $slotDate = trim($post['slotDate'] ?? '');
        $startTime = trim($post['startTime'] ?? '');
        $language = trim($post['language'] ?? '');
        $duration = (int)($post['duration'] ?? 150);
        $minAge = (int)($post['minAge'] ?? 12);
        $capacity = (int)($post['capacity'] ?? 12);
        $priceIndividual = (float)($post['priceIndividual'] ?? 17.50);
        $priceFamily = (float)($post['priceFamily'] ?? 60.00);

        if ($slotDate === '' || $startTime === '' || $language === '') {
            throw new \InvalidArgumentException('Date, time and language are required.');
        }

        $tour = new HistoryEventModel(
            $eventId,
            $historyEventId,
            $slotDate,
            $startTime,
            $language,
            $duration,
            $minAge,
            $capacity,
            $priceIndividual,
            $priceFamily
        );

        $success = $this->updateSession($tour);

        if (!$success) {
            throw new \RuntimeException('Failed to update tour.');
        }

        return $tour;
    }

    public function updateSession(HistoryEventModel $event): bool
    {
        return $this->historyEventRepo->update($event);
    }

    public function deleteSession(int $eventId): bool
    {
        return $this->historyEventRepo->delete($eventId);
    }

    public function getAllVenues(): array
    {
        return $this->historyVenueRepo->getAll();
    }

    public function getVenueById(int $venueId): ?HistoryVenueModel
    {
        return $this->historyVenueRepo->getById($venueId);
    }

    public function createVenue(array $post, array $files): HistoryVenueModel
    {
        $venueName = trim($post['venueName'] ?? '');
        $details = trim($post['details'] ?? '');
        $location = trim($post['location'] ?? '');

        if ($venueName === '') {
            throw new \InvalidArgumentException('Venue name is required.');
        }

        $imageId = null;

        $uploadedFileName = $this->imageService->uploadImage('image', 'history', 'history_venue');
        if ($uploadedFileName !== null) {
            $imgURL = '/assets/images/history/' . $uploadedFileName;
            $altText = $venueName;
            $imageId = $this->imageRepository->createImage($imgURL, $altText);
        }

        $venue = new HistoryVenueModel(
            0,
            $venueName,
            $details !== '' ? $details : null,
            $location !== '' ? $location : null,
            $imageId
        );

        $success = $this->historyVenueRepo->create($venue);

        if (!$success) {
            throw new \RuntimeException('Failed to create venue.');
        }

        return $venue;
    }

    public function updateVenue(int $venueId, array $post, array $files): HistoryVenueModel
    {
        $existingVenue = $this->historyVenueRepo->getById($venueId);

        if (!$existingVenue) {
            throw new \InvalidArgumentException('Venue not found.');
        }

        $venueName = trim($post['venueName'] ?? '');
        $details = trim($post['details'] ?? '');
        $location = trim($post['location'] ?? '');

        if ($venueName === '') {
            throw new \InvalidArgumentException('Venue name is required.');
        }

        $imageId = $existingVenue->getImageId();

        $uploadedFileName = $this->imageService->uploadImage('image', 'history', 'history_venue');
        if ($uploadedFileName !== null) {
            $imgURL = '/assets/images/history/' . $uploadedFileName;
            $altText = $venueName;
            $imageId = $this->imageRepository->createImage($imgURL, $altText);
        }

        $venue = new HistoryVenueModel(
            $venueId,
            $venueName,
            $details !== '' ? $details : null,
            $location !== '' ? $location : null,
            $imageId
        );

        $success = $this->historyVenueRepo->update($venue);

        if (!$success) {
            throw new \RuntimeException('Failed to update venue.');
        }

        return $venue;
    }

    public function deleteVenue(int $venueId): bool
    {
        if ($venueId <= 0) {
            throw new \InvalidArgumentException('Invalid venue id.');
        }

        $existingVenue = $this->historyVenueRepo->getById($venueId);

        if (!$existingVenue) {
            throw new \InvalidArgumentException('Venue not found.');
        }

        return $this->historyVenueRepo->delete($venueId);
    }

    public function getStopsByEventId(int $eventId): array
    {
        return $this->historyVenueRepo->getStopsByEventId($eventId);
    }

    public function getIndexPageData(): array
    {
        return [
            'pageVM' => new PageElementViewModel(
                $this->pageService->getPageSections('History')
            ),
            'sessions' => $this->getAllSessions(),
            'venues' => $this->getAllVenues()
        ];
    }

    public function getAdminIndexPageData(): array
    {
        return [
            'pageVM' => new PageElementViewModel(
                $this->pageService->getPageSections('History')
            ),
            'venues' => $this->getAllVenues(),
            'tours' => $this->getAllSessions(),
            'pageName' => 'History'
        ];
    }

    public function getBookingTourPageData(): array
    {
        $sessions = $this->getAllSessions();
        $stops = [];

        foreach ($sessions as $session) {
            $sessionStops = $this->getStopsByEventId($session->getEventId());

            if (!empty($sessionStops)) {
                $stops = $sessionStops;
                break;
            }
        }

        return [
            'sessions' => $sessions,
            'stops' => $stops
        ];
    }

    public function createBookingFromRequest(array $post, ?int $userId): int
    {
        $eventId = (int)($post['eventId'] ?? 0);
        $individualCount = (int)($post['individualCount'] ?? 0);
        $familyCount = (int)($post['familyCount'] ?? 0);

        $numberOfPeople = $individualCount + ($familyCount * 4);

        if ($eventId <= 0 || $numberOfPeople <= 0) {
            throw new \InvalidArgumentException('Please select a valid tour and at least one ticket.');
        }

        if ($userId === null) {
            throw new \InvalidArgumentException('User is not logged in.');
        }

        $this->programService->addTicketToProgram($eventId, $numberOfPeople, $userId);

        return $numberOfPeople;
    }
}
