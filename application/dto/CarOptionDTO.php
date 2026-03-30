<?php

namespace app\application\dto;

class CarOptionDTO
{
    public ?int $id;
    public ?string $brand;
    public ?string $model;
    public ?int $year;
    public ?string $body;
    public ?int $mileage;

    public function __construct(
        ?int $id,
        ?string $brand,
        ?string $model,
        ?int $year,
        ?string $body,
        ?int $mileage
    ) {
        $this->id = $id;
        $this->brand = $brand;
        $this->model = $model;
        $this->year = $year;
        $this->body = $body;
        $this->mileage = $mileage;
    }
}
