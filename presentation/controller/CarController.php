<?php

namespace app\presentation\controller;

use app\domain\entities\Car;
use app\domain\entities\CarOption;
use Doctrine\ORM\EntityManagerInterface;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CarController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'view' => ['get'],
                    'list' => ['get'],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $body = Yii::$app->request->bodyParams;

        $required = ['title', 'description', 'price', 'photo_url', 'contacts'];
        $missing = array_values(array_filter($required, static function ($key) use ($body) {
            return !array_key_exists($key, $body) || $body[$key] === null || $body[$key] === '';
        }));

        if (!empty($missing)) {
            Yii::$app->response->statusCode = 422;
            return [
                'error' => 'Validation failed',
                'missing' => $missing,
            ];
        }

        if (!is_numeric($body['price'])) {
            Yii::$app->response->statusCode = 422;
            return [
                'error' => 'Validation failed',
                'field' => 'price',
            ];
        }

        $em = Yii::$container->get(EntityManagerInterface::class);

        $car = new Car();
        $car->setTitle((string) $body['title'])
            ->setDescription((string) $body['description'])
            ->setPrice((float) $body['price'])
            ->setContacts((string) $body['contacts'])
            ->setPhotoUrl((string) $body['photo_url']);

        if (array_key_exists('options', $body)) {
            if (!is_array($body['options'])) {
                Yii::$app->response->statusCode = 422;
                return [
                    'error' => 'Validation failed',
                    'field' => 'options',
                ];
            }
            $options = $this->hydrateOptions($body['options'], $car);
            $car->setOptions($options);
        }

        $em->persist($car);
        $em->flush();

        Yii::$app->response->statusCode = 201;
        return $this->serializeCar($car);
    }

    public function actionView(int $id)
    {
        $em = Yii::$container->get(EntityManagerInterface::class);
        $car = $em->find(Car::class, $id);

        if ($car === null) {
            throw new NotFoundHttpException('Car not found.');
        }

        return $this->serializeCar($car);
    }

    public function actionList()
    {
        $em = Yii::$container->get(EntityManagerInterface::class);
        $cars = $em->getRepository(Car::class)->findAll();

        return array_map([$this, 'serializeCar'], $cars);
    }

    private function serializeCar(Car $car): array
    {
        $options = $car->getOptions();
        $optionsData = null;
        if ($options !== null) {
            $optionsData = [
                'id' => $options->getId(),
                'brand' => $options->getBrand(),
                'model' => $options->getModel(),
                'year' => $options->getYear(),
                'body' => $options->getBody(),
                'mileage' => $options->getMileage(),
            ];
        }

        return [
            'id' => $car->getId(),
            'title' => $car->getTitle(),
            'description' => $car->getDescription(),
            'price' => $car->getPrice(),
            'photo_url' => $car->getPhotoUrl(),
            'contacts' => $car->getContacts(),
            'options' => $optionsData,
        ];
    }

    private function hydrateOptions(array $data, Car $car): CarOption
    {
        $options = new CarOption();
        $options->setCar($car)
            ->setBrand(array_key_exists('brand', $data) ? (string) $data['brand'] : null)
            ->setModel(array_key_exists('model', $data) ? (string) $data['model'] : null)
            ->setBody(array_key_exists('body', $data) ? (string) $data['body'] : null);

        if (array_key_exists('year', $data)) {
            $options->setYear($data['year'] !== null ? (int) $data['year'] : null);
        }
        if (array_key_exists('mileage', $data)) {
            $options->setMileage($data['mileage'] !== null ? (int) $data['mileage'] : null);
        }

        return $options;
    }
}
