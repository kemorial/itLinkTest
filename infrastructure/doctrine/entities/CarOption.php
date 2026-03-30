<?php

namespace app\infrastructure\doctrine\entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'car_options')]
class CarOption
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[OneToOne(targetEntity: Car::class, inversedBy: 'options')]
    #[JoinColumn(name: 'car_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Car $car;

    #[Column(type: 'string', length: 255, nullable: false)]
    private ?string $brand = null;

    #[Column(type: 'string', length: 255, nullable: false)]
    private ?string $model = null;

    #[Column(type: 'integer', nullable: false)]
    private ?int $year = null;

    #[Column(type: 'string', length: 255, nullable: false)]
    private ?string $body = null;

    #[Column(type: 'integer', nullable: false)]
    private ?int $mileage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCar(): Car
    {
        return $this->car;
    }

    public function setCar(Car $car): self
    {
        $this->car = $car;
        if ($car->getOptions() !== $this) {
            $car->setOptions($this);
        }

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(?int $mileage): self
    {
        $this->mileage = $mileage;

        return $this;
    }
}
