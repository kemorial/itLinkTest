<?php

namespace app\domain\models;

use DateTimeImmutable;

class Car
{
    private ?int $id;
    private string $title;
    private string $description;
    private string $price;
    private ?string $photoUrl;
    private string $contacts;
    private DateTimeImmutable $createdAt;
    private ?CarOption $options;

    public function __construct(
        ?int $id,
        string $title,
        string $description,
        string $price,
        ?string $photoUrl,
        string $contacts,
        DateTimeImmutable $createdAt,
        ?CarOption $options = null
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

    public function getPrice(): string
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getOptions(): ?CarOption
    {
        return $this->options;
    }
}
