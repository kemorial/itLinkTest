<?php

namespace app\domain\services;

use app\domain\models\Car;

interface CarServiceInterface
{
    public function create(Car $car): Car;

    public function getById(int $id): ?Car;

    /**
     * @return Car[]
     */
    public function getAll(): array;
}
