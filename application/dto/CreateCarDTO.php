<?php

namespace app\application\dto;

class CreateCarDTO
{
    public string $title;
    public string $description;
    public float $price;
    public ?string $photoUrl;
    public string $contacts;
    public ?CarOptionDTO $options;

    public function __construct(
        string $title,
        string $description,
        float $price,
        ?string $photoUrl,
        string $contacts,
        ?CarOptionDTO $options = null
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->price = $price;
        $this->photoUrl = $photoUrl;
        $this->contacts = $contacts;
        $this->options = $options;
    }
}
