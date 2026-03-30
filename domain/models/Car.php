<?php

namespace app\domain\models;

class Car
{
    private ?int $id;
    private string $title;
    private string $description;
    private float $price;
    private ?string $photoUrl;
    private string $contacts;
    private ?CarOption $options;

    public function __construct(
        ?int $id,
        string $title,
        string $description,
        float $price,
        ?string $photoUrl,
        string $contacts,
        ?CarOption $options = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->price = $price;
        $this->photoUrl = $photoUrl;
        $this->contacts = $contacts;
        $this->options = $options;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function getContacts(): string
    {
        return $this->contacts;
    }

    public function getOptions(): ?CarOption
    {
        return $this->options;
    }
}
