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

    private ?int $detailImageId;
    private ?string $detailImgURL;
    private ?string $detailAltText;

    public function __construct(
        int $venueId,
        string $venueName,
        ?string $details = null,
        ?string $location = null,
        ?int $imageId = null,
        ?string $imgURL = null,
        ?string $altText = null,
        ?int $detailImageId = null,
        ?string $detailImgURL = null,
        ?string $detailAltText = null
    ) {
        $this->venueId = $venueId;
        $this->venueName = $venueName;
        $this->details = $details;
        $this->location = $location;
        $this->imageId = $imageId;
        $this->imgURL = $imgURL;
        $this->altText = $altText;
        $this->detailImageId = $detailImageId;
        $this->detailImgURL = $detailImgURL;
        $this->detailAltText = $detailAltText;
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

    public function getDetailImageId(): ?int
    {
        return $this->detailImageId;
    }

    public function getDetailImgURL(): ?string
    {
        return $this->detailImgURL;
    }

    public function getDetailAltText(): ?string
    {
        return $this->detailAltText;
    }
}