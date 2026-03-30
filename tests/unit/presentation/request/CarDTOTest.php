<?php

namespace tests\unit\presentation\request;

use app\presentation\request\CarDTO;

class CarDTOTest extends \Codeception\Test\Unit
{
    public function testValidateReturnsErrorWhenOptionsContainsUnknownField(): void
    {
        $dto = new CarDTO([
            'title' => 'BMW X5',
            'description' => 'Diesel SUV',
            'price' => '35000.50',
            'photo_url' => 'https://example.com/car.jpg',
            'contacts' => '+1-202-555-0123',
            'options' => [
                'color' => 'black',
            ],
        ]);

        $this->assertSame([
            'error' => 'Validation failed',
            'field' => 'options.color',
        ], $dto->validate());
    }

    public function testValidateReturnsErrorWhenOptionsArePartial(): void
    {
        $dto = new CarDTO([
            'title' => 'BMW X5',
            'description' => 'Diesel SUV',
            'price' => '35000.50',
            'photo_url' => 'https://example.com/car.jpg',
            'contacts' => '+1-202-555-0123',
            'options' => [
                'brand' => 'BMW',
                'model' => 'X5',
            ],
        ]);

        $this->assertSame([
            'error' => 'Validation failed',
            'missing' => ['options.year', 'options.body', 'options.mileage'],
        ], $dto->validate());
    }

    public function testValidateReturnsErrorWhenOptionsIntegerFieldIsInvalid(): void
    {
        $dto = new CarDTO([
            'title' => 'BMW X5',
            'description' => 'Diesel SUV',
            'price' => '35000.50',
            'photo_url' => 'https://example.com/car.jpg',
            'contacts' => '+1-202-555-0123',
            'options' => [
                'brand' => 'BMW',
                'model' => 'X5',
                'year' => 'invalid',
                'body' => 'SUV',
                'mileage' => 80000,
            ],
        ]);

        $this->assertSame([
            'error' => 'Validation failed',
            'field' => 'options.year',
        ], $dto->validate());
    }

    public function testValidateReturnsNoErrorsWhenOptionsAreValid(): void
    {
        $dto = new CarDTO([
            'title' => 'BMW X5',
            'description' => 'Diesel SUV',
            'price' => '35000.50',
            'photo_url' => 'https://example.com/car.jpg',
            'contacts' => '+1-202-555-0123',
            'options' => [
                'brand' => 'BMW',
                'model' => 'X5',
                'year' => 2020,
                'body' => 'SUV',
                'mileage' => 80000,
            ],
        ]);

        $this->assertSame([], $dto->validate());
    }

    public function testValidateReturnsNoErrorsWhenOptionsAreMissing(): void
    {
        $dto = new CarDTO([
            'title' => 'BMW X5',
            'description' => 'Diesel SUV',
            'price' => '35000.50',
            'photo_url' => 'https://example.com/car.jpg',
            'contacts' => '+1-202-555-0123',
        ]);

        $this->assertSame([], $dto->validate());
    }

    public function testValidateReturnsNoErrorsWhenPhotoUrlIsMissing(): void
    {
        $dto = new CarDTO([
            'title' => 'BMW X5',
            'description' => 'Diesel SUV',
            'price' => '35000.50',
            'contacts' => '+1-202-555-0123',
        ]);

        $this->assertSame([], $dto->validate());
    }

    public function testToApplicationDtoSetsNullPhotoUrlWhenItIsMissing(): void
    {
        $dto = new CarDTO([
            'title' => 'BMW X5',
            'description' => 'Diesel SUV',
            'price' => '35000.50',
            'contacts' => '+1-202-555-0123',
        ]);

        $applicationDto = $dto->toApplicationDTO();

        $this->assertNull($applicationDto->photoUrl);
    }
}
