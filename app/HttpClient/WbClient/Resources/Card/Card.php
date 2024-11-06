<?php

namespace App\HttpClient\WbClient\Resources\Card;

use Illuminate\Support\Collection;

class Card
{
    protected int $nm_id;
    protected string $vendor_code;
    protected int $subject_id;
    protected string $subject_name;
    protected Collection $photos;
    protected ?string $video;
    protected Collection $sizes;
    protected int $dimensions_length;
    protected int $dimensions_width;
    protected int $dimensions_height;
    protected bool $dimensions_isValid;
    protected Collection $characteristics;
    protected string $created_at;
    protected string $updated_at;

    public function __construct(Collection $card)
    {
        $this->nm_id = $card->get('nmID');
        $this->vendor_code = $card->get('vendorCode');
        $this->subject_id = $card->get('subjectID');
        $this->subject_name = $card->get('subjectName');
        $this->photos = $card->get('photos');
        $this->video = $card->get('video');
        $this->sizes = $card->get('sizes');
        $this->dimensions_length = $card->get('dimensions')->get('length');
        $this->dimensions_width = $card->get('dimensions')->get('width');
        $this->dimensions_height = $card->get('dimensions')->get('height');
        $this->dimensions_isValid = $card->get('dimensions')->get('isValid');
        $this->characteristics = $card->get('characteristics');
        $this->created_at = $card->get('createdAt');
        $this->updated_at = $card->get('updatedAt');
    }

    public function getNmId(): int
    {
        return $this->nm_id;
    }

    public function getVendorCode(): string
    {
        return $this->vendor_code;
    }

    public function getSubjectId(): int
    {
        return $this->subject_id;
    }

    public function getSubjectName(): string
    {
        return $this->subject_name;
    }

    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function getSizes(): Collection
    {
        return $this->sizes;
    }

    public function getDimensionsLength(): int
    {
        return $this->dimensions_length;
    }

    public function getDimensionsWidth(): int
    {
        return $this->dimensions_width;
    }

    public function getDimensionsHeight(): int
    {
        return $this->dimensions_height;
    }

    public function isDimensionsIsValid(): bool
    {
        return $this->dimensions_isValid;
    }

    public function getCharacteristics(): Collection
    {
        return $this->characteristics;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

}
