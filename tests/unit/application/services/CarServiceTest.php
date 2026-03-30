<?php

namespace tests\unit\application\services;

use app\application\dto\CarOptionDTO;
use app\application\dto\CreateCarDTO;
use app\application\services\CarService;
use app\domain\models\Car;
use app\domain\models\CarOption;
use app\domain\repositories\CarRepositoryInterface;
use DateTimeImmutable;

class CarServiceTest extends \Codeception\Test\Unit
{
    public function testCreateFromDtoCreatesCarAndReturnsApplicationDto(): void
    {
        $repository = $this->createMock(CarRepositoryInterface::class);
        $service = new CarService($repository);

        $inputDto = new CreateCarDTO(
            'BMW X5',
            'Diesel SUV',
            '35000.50',
            'https://example.com/car.jpg',
            '+1-202-555-0123',
            new CarOptionDTO(
                null,
                'BMW',
                'X5',
                2020,
                'SUV',
                80000
            )
        );

        $repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Car $car): bool {
                $this->assertNull($car->getId());
                $this->assertSame('BMW X5', $car->getTitle());
                $this->assertSame('Diesel SUV', $car->getDescription());
                $this->assertSame('35000.50', $car->getPrice());
                $this->assertSame('https://example.com/car.jpg', $car->getPhotoUrl());
                $this->assertSame('+1-202-555-0123', $car->getContacts());
                $this->assertInstanceOf(DateTimeImmutable::class, $car->getCreatedAt());
                $this->assertInstanceOf(CarOption::class, $car->getOptions());
                $this->assertSame('BMW', $car->getOptions()->getBrand());
                $this->assertSame('X5', $car->getOptions()->getModel());
                $this->assertSame(2020, $car->getOptions()->getYear());
                $this->assertSame('SUV', $car->getOptions()->getBody());
                $this->assertSame(80000, $car->getOptions()->getMileage());

                return true;
            }))
            ->willReturn(new Car(
                10,
                'BMW X5',
                'Diesel SUV',
                '35000.50',
                'https://example.com/car.jpg',
                '+1-202-555-0123',
                new DateTimeImmutable('2025-01-10T12:30:00+00:00'),
                new CarOption(
                    15,
                    'BMW',
                    'X5',
                    2020,
                    'SUV',
                    80000
                )
            ));

        $result = $service->createFromDTO($inputDto);

        $this->assertSame(10, $result->id);
        $this->assertSame('BMW X5', $result->title);
        $this->assertSame('Diesel SUV', $result->description);
        $this->assertSame('35000.50', $result->price);
        $this->assertSame('https://example.com/car.jpg', $result->photoUrl);
        $this->assertSame('+1-202-555-0123', $result->contacts);
        $this->assertSame('2025-01-10T12:30:00+00:00', $result->createdAt->format(DATE_ATOM));
        $this->assertNotNull($result->options);
        $this->assertSame(15, $result->options->id);
        $this->assertSame('BMW', $result->options->brand);
        $this->assertSame('X5', $result->options->model);
        $this->assertSame(2020, $result->options->year);
        $this->assertSame('SUV', $result->options->body);
        $this->assertSame(80000, $result->options->mileage);
    }

    public function testGetPaginatedDtoUsesRepositoryPagination(): void
    {
        $repository = $this->createMock(CarRepositoryInterface::class);
        $service = new CarService($repository);

        $repository
            ->expects($this->once())
            ->method('findPaginated')
            ->with(5, 5)
            ->willReturn([
                new Car(
                    10,
                    'BMW X5',
                    'Diesel SUV',
                    '35000.50',
                    'https://example.com/car.jpg',
                    '+1-202-555-0123',
                    new DateTimeImmutable('2025-01-10T12:30:00+00:00'),
                    null
                ),
            ]);

        $repository
            ->expects($this->once())
            ->method('countAll')
            ->willReturn(12);

        $result = $service->getPaginatedDTO(2, 5);

        $this->assertSame(12, $result['total']);
        $this->assertCount(1, $result['items']);
        $this->assertSame(10, $result['items'][0]->id);
        $this->assertSame('BMW X5', $result['items'][0]->title);
    }

    public function testCreateFromDtoAllowsNullPhotoUrl(): void
    {
        $repository = $this->createMock(CarRepositoryInterface::class);
        $service = new CarService($repository);

        $inputDto = new CreateCarDTO(
            'BMW X5',
            'Diesel SUV',
            '35000.50',
            null,
            '+1-202-555-0123'
        );

        $repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Car $car): bool {
                $this->assertNull($car->getPhotoUrl());

                return true;
            }))
            ->willReturn(new Car(
                10,
                'BMW X5',
                'Diesel SUV',
                '35000.50',
                null,
                '+1-202-555-0123',
                new DateTimeImmutable('2025-01-10T12:30:00+00:00'),
                null
            ));

        $result = $service->createFromDTO($inputDto);

        $this->assertNull($result->photoUrl);
    }
}
