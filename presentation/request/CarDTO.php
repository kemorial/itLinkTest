<?php

namespace app\presentation\request;

use app\application\dto\CarOptionDTO;
use app\application\dto\CreateCarDTO;

class CarDTO
{
    private const OPTION_FIELDS = [
        'brand' => 'string',
        'model' => 'string',
        'year' => 'int',
        'body' => 'string',
        'mileage' => 'int',
    ];

    private array $body;

    public function __construct(array $body)
    {
        $this->body = $body;
    }

    public function validate(): array
    {
        $required = ['title', 'description', 'price', 'contacts'];
        $missing = array_values(array_filter($required, function (string $key): bool {
            return !array_key_exists($key, $this->body) || $this->body[$key] === null || $this->body[$key] === '';
        }));

        if ($missing !== []) {
            return [
                'error' => 'Validation failed',
                'missing' => $missing,
            ];
        }

        if (!is_numeric($this->body['price'])) {
            return [
                'error' => 'Validation failed',
                'field' => 'price',
            ];
        }

        if (
            array_key_exists('photo_url', $this->body)
            && $this->body['photo_url'] !== null
            && $this->body['photo_url'] !== ''
            && !is_string($this->body['photo_url'])
        ) {
            return [
                'error' => 'Validation failed',
                'field' => 'photo_url',
            ];
        }

        if (array_key_exists('options', $this->body) && !is_array($this->body['options'])) {
            return [
                'error' => 'Validation failed',
                'field' => 'options',
            ];
        }

        if (!array_key_exists('options', $this->body) || $this->body['options'] === null) {
            return [];
        }

        $unknownFields = array_diff(array_keys($this->body['options']), array_keys(self::OPTION_FIELDS));
        if ($unknownFields !== []) {
            return [
                'error' => 'Validation failed',
                'field' => 'options.' . array_values($unknownFields)[0],
            ];
        }

        $missingOptionFields = array_values(array_filter(
            array_keys(self::OPTION_FIELDS),
            function (string $field): bool {
                if (!array_key_exists($field, $this->body['options'])) {
                    return true;
                }

                $value = $this->body['options'][$field];

                return $value === null || ($value === '' && self::OPTION_FIELDS[$field] === 'string');
            }
        ));
        if ($missingOptionFields !== []) {
            return [
                'error' => 'Validation failed',
                'missing' => array_map(
                    static fn(string $field): string => 'options.' . $field,
                    $missingOptionFields
                ),
            ];
        }

        foreach (self::OPTION_FIELDS as $field => $type) {
            if ($type === 'string' && !is_string($this->body['options'][$field])) {
                return [
                    'error' => 'Validation failed',
                    'field' => 'options.' . $field,
                ];
            }

            if ($type === 'int' && filter_var($this->body['options'][$field], FILTER_VALIDATE_INT) === false) {
                return [
                    'error' => 'Validation failed',
                    'field' => 'options.' . $field,
                ];
            }
        }

        return [];
    }

    public function toApplicationDTO(): CreateCarDTO
    {
        $options = null;
        if (array_key_exists('options', $this->body) && is_array($this->body['options'])) {
            $options = new CarOptionDTO(
                null,
                array_key_exists('brand', $this->body['options']) ? (string) $this->body['options']['brand'] : null,
                array_key_exists('model', $this->body['options']) ? (string) $this->body['options']['model'] : null,
                array_key_exists('year', $this->body['options']) && $this->body['options']['year'] !== null
                    ? (int) $this->body['options']['year']
                    : null,
                array_key_exists('body', $this->body['options']) ? (string) $this->body['options']['body'] : null,
                array_key_exists('mileage', $this->body['options']) && $this->body['options']['mileage'] !== null
                    ? (int) $this->body['options']['mileage']
                    : null
            );
        }

        return new CreateCarDTO(
            (string) $this->body['title'],
            (string) $this->body['description'],
            (string) $this->body['price'],
            array_key_exists('photo_url', $this->body) && $this->body['photo_url'] !== ''
                ? ($this->body['photo_url'] === null ? null : (string) $this->body['photo_url'])
                : null,
            (string) $this->body['contacts'],
            $options
        );
    }
}
