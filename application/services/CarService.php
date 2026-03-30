<?php

namespace app\application\services;

use app\application\dto\CarDTO;
use app\application\dto\CarOptionDTO;
use app\application\dto\CreateCarDTO;
use app\domain\models\Car;
use app\domain\models\CarOption;
use app\domain\repositories\CarRepositoryInterface;
use app\domain\services\CarServiceInterface;

class CarService implements CarServiceInterface
{
    private CarRepositoryInterface $carRepository;

    public function __construct(CarRepositoryInterface $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    public function create(Car $car): Car
    {
        return $this->carRepository->save($car);
    }

    public function getById(int $id): ?Car
    {
        return $this->carRepository->findById($id);
    }

    public function getAll(): array
    {
        return $this->carRepository->findAll();
    }

    public function createFromDTO(CreateCarDTO $dto): CarDTO
    {
        $car = new Car(
            null,
            $dto->title,
            $dto->description,
            $dto->price,
            $dto->photoUrl,
            $dto->contacts,
            $this->mapOptionFromDTO($dto->options)
        );

        return $this->mapCarToDTO($this->create($car));
    }

    public function getDTOById(int $id): ?CarDTO
    {
        $car = $this->getById($id);

        return $car === null ? null : $this->mapCarToDTO($car);
    }

    /**
     * @return CarDTO[]
     */
    public function getAllDTO(): array
    {
        return array_map([$this, 'mapCarToDTO'], $this->getAll());
    }

    /**
     * @return array{items: CarDTO[], total: int}
     */
    public function getPaginatedDTO(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;

        return [
            'items' => array_map(
                [$this, 'mapCarToDTO'],
                $this->carRepository->findPaginated($perPage, $offset)
            ),
            'total' => $this->carRepository->countAll(),
        ];
    }

    private function mapCarToDTO(Car $car): CarDTO
    {
        return new CarDTO(
            $car->getId(),
            $car->getTitle(),
            $car->getDescription(),
            $car->getPrice(),
            $car->getPhotoUrl(),
            $car->getContacts(),
            $this->mapOptionToDTO($car->getOptions())
        );
    }

    private function mapOptionToDTO(?CarOption $option): ?CarOptionDTO
    {
        if ($option === null) {
            return null;
        }

        return new CarOptionDTO(
            $option->getId(),
            $option->getBrand(),
            $option->getModel(),
            $option->getYear(),
            $option->getBody(),
            $option->getMileage()
        );
    }

    private function mapOptionFromDTO(?CarOptionDTO $option): ?CarOption
    {
        if ($option === null) {
            return null;
        }

        return new CarOption(
            $option->id,
            $option->brand,
            $option->model,
            $option->year,
            $option->body,
            $option->mileage
        );
    }
}
