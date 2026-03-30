<?php

namespace App\Models;

class HistoryVenueModel
{
    private int $venueId;
    private string $venueName;
    private ?string $details;
    private ?string $location;
    private ?int $imageId;
    private ?string $imgURL;
    private ?string $altText;

    public function __construct(
        int $venueId,
        string $venueName,
        ?string $details,
        ?string $location,
        ?int $imageId,
        ?string $imgURL = null,
        ?string $altText = null
    ) {
        $this->venueId = $venueId;
        $this->venueName = $venueName;
        $this->details = $details;
        $this->location = $location;
        $this->imageId = $imageId;
        $this->imgURL = $imgURL;
        $this->altText = $altText;
    }

    public function getVenueId(): int
    {
        return $this->venueId;
    }

    public function getVenueName(): string
    {
        return $this->venueName;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getImageId(): ?int
    {
        return $this->imageId;
    }

    public function getImgURL(): ?string
    {
        return $this->imgURL;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }
}