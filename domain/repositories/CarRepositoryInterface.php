<?php

namespace app\domain\repositories;

use app\domain\models\Car;

interface CarRepositoryInterface
{
    public function save(Car $car): Car;

    public function findById(int $id): ?Car;

    /**
     * @return Car[]
     */
    public function findAll(): array;

    /**
     * @return Car[]
     */
    public function findPaginated(int $limit, int $offset): array;

    public function countAll(): int;
}
