<?php

namespace app\application\dto;

class CarDTO
{
    public ?int $id;
    public string $title;
    public string $description;
    public float $price;
    public ?string $photoUrl;
    public string $contacts;
    public ?CarOptionDTO $options;

    public function __construct(
        ?int $id,
        string $title,
        string $description,
        float $price,
        ?string $photoUrl,
        string $contacts,
        ?CarOptionDTO $options = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->price = $price;
        $this->photoUrl = $photoUrl;
        $this->contacts = $contacts;
        $this->options = $options;
    }
}
