<?php

namespace app\application\dto;

use DateTimeImmutable;

class CarDTO
{
    public ?int $id;
    public string $title;
    public string $description;
    public string $price;
    public ?string $photoUrl;
    public string $contacts;
    public DateTimeImmutable $createdAt;
    public ?CarOptionDTO $options;

    public function __construct(
        ?int $id,
        string $title,
        string $description,
        string $price,
        ?string $photoUrl,
        string $contacts,
        DateTimeImmutable $createdAt,
        ?CarOptionDTO $options = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->price = $price;
        $this->photoUrl = $photoUrl;
        $this->contacts = $contacts;
        $this->createdAt = $createdAt;
        $this->options = $options;
    }
}
