<?php

namespace app\presentation\response;

use app\application\dto\CarDTO as ApplicationCarDTO;

class CarDTO
{
    private ApplicationCarDTO $dto;

    public function __construct(ApplicationCarDTO $dto)
    {
        $this->dto = $dto;
    }

    public function toArray(): array
    {
        $options = $this->dto->options;

        return [
            'id' => $this->dto->id,
            'title' => $this->dto->title,
            'description' => $this->dto->description,
            'price' => $this->dto->price,
            'photo_url' => $this->dto->photoUrl,
            'contacts' => $this->dto->contacts,
            'created_at' => $this->dto->createdAt->format(DATE_ATOM),
            'options' => $options === null ? null : [
                'brand' => $options->brand,
                'model' => $options->model,
                'year' => $options->year,
                'body' => $options->body,
                'mileage' => $options->mileage,
            ],
        ];
    }
}
