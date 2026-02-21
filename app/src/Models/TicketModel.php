<?php
namespace App\Models;
use App\Models\EventModel;
use App\Models\UserModel;

class TicketModel
{
    private int $id;
    private EventModel $event;
    private ?UserModel $user;
    private int $numberOfPeople;
    private ?int $programItemId = null;

public function setProgramItemId(int $id): void
{
    $this->programItemId = $id;
}

public function getProgramItemId(): ?int
{
    return $this->programItemId;
}

    public function __construct(
        int $id,
        EventModel $event,
        ?UserModel $user,
        int $numberOfPeople
    ) {
        $this->id = $id;
        $this->event = $event;
        $this->user = $user;
        $this->setNumberOfPeople($numberOfPeople);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEvent(): EventModel
    {
        return $this->event;
    }

    public function getUser(): UserModel
    {
        return $this->user;
    }

    public function getNumberOfPeople(): int
    {
        return $this->numberOfPeople;
    }

    // public function getPrice(): float
    // {
    //     return $this->numberOfPeople * $this->event->getPrice();
    // }

    public function setNumberOfPeople(int $numberOfPeople): void
    {
        $this->numberOfPeople = $numberOfPeople;
    }

    public function setEvent(EventModel $event): void
    {
        $this->event = $event;
    }

    public function setUser(UserModel $user): void
    {
        $this->user = $user;
    }

   
}
