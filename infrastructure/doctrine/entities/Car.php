<?php

namespace app\infrastructure\doctrine\entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'cars')]
class Car
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[Column(type: 'string', length: 255, nullable: false)]
    private string $title;

    #[Column(type: 'text', nullable: false)]
    private string $description;

    #[Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    private float $price;

    #[Column(name: 'photo_url', type: 'string', length: 500, nullable: true)]
    private ?string $photoUrl = null;

    #[Column(type: 'string', length: 255, nullable: false)]
    private string $contacts;

    #[OneToOne(mappedBy: 'car', targetEntity: CarOption::class, cascade: ['persist', 'remove'])]
    private ?CarOption $options = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(?string $photoUrl): self
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }

    public function getContacts(): string
    {
        return $this->contacts;
    }

    public function setContacts(string $contacts): self
    {
        $this->contacts = $contacts;

        return $this;
    }

    public function getOptions(): ?CarOption
    {
        return $this->options;
    }

    public function setOptions(?CarOption $options): self
    {
        $this->options = $options;
        if ($options !== null && $options->getCar() !== $this) {
            $options->setCar($this);
        }

        return $this;
    }
}
