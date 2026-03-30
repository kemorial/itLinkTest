<?php

namespace app\infrastructure\doctrine\repositories;

use app\domain\models\Car as DomainCar;
use app\domain\models\CarOption as DomainCarOption;
use app\domain\repositories\CarRepositoryInterface;
use app\infrastructure\doctrine\entities\Car;
use app\infrastructure\doctrine\entities\CarOption;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCarRepository implements CarRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(DomainCar $car): DomainCar
    {
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $entity = $car->getId() === null
                ? new Car()
                : $this->entityManager->find(Car::class, $car->getId());

            if ($entity === null) {
                $entity = new Car();
            }

            $entity
                ->setTitle($car->getTitle())
                ->setDescription($car->getDescription())
                ->setPrice($car->getPrice())
                ->setPhotoUrl($car->getPhotoUrl())
                ->setContacts($car->getContacts())
                ->setCreatedAt($car->getCreatedAt());

            $option = $car->getOptions();
            if ($option !== null) {
                $optionEntity = $entity->getOptions() ?? new CarOption();
                $optionEntity
                    ->setCar($entity)
                    ->setBrand($option->getBrand())
                    ->setModel($option->getModel())
                    ->setYear($option->getYear())
                    ->setBody($option->getBody())
                    ->setMileage($option->getMileage());
                $entity->setOptions($optionEntity);
            }

            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $connection->commit();

            return $this->mapToDomain($entity);
        } catch (\Throwable $exception) {
            $connection->rollBack();

            throw $exception;
        }
    }

    public function findById(int $id): ?DomainCar
    {
        $entity = $this->entityManager->find(Car::class, $id);

        return $entity === null ? null : $this->mapToDomain($entity);
    }

    public function findAll(): array
    {
        $entities = $this->entityManager->getRepository(Car::class)->findAll();

        return array_map([$this, 'mapToDomain'], $entities);
    }

    public function findPaginated(int $limit, int $offset): array
    {
        $entities = $this->entityManager
            ->getRepository(Car::class)
            ->createQueryBuilder('car')
            ->leftJoin('car.options', 'options')
            ->addSelect('options')
            ->orderBy('car.createdAt', 'DESC')
            ->addOrderBy('car.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return array_map([$this, 'mapToDomain'], $entities);
    }

    public function countAll(): int
    {
        return (int) $this->entityManager
            ->getRepository(Car::class)
            ->createQueryBuilder('car')
            ->select('COUNT(car.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function mapToDomain(Car $entity): DomainCar
    {
        $option = $entity->getOptions();

        return new DomainCar(
            $entity->getId(),
            $entity->getTitle(),
            $entity->getDescription(),
            $entity->getPrice(),
            $entity->getPhotoUrl(),
            $entity->getContacts(),
            $entity->getCreatedAt(),
            $option === null ? null : new DomainCarOption(
                $option->getId(),
                $option->getBrand(),
                $option->getModel(),
                $option->getYear(),
                $option->getBody(),
                $option->getMileage()
            )
        );
    }
}
