<?php

namespace app\presentation\controller;

use app\application\services\CarService;
use app\presentation\request\CarDTO as CarRequestDTO;
use app\presentation\response\CarDTO as CarResponseDTO;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\BadRequestHttpException;
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
        $requestDTO = new CarRequestDTO(Yii::$app->request->bodyParams);
        $validationErrors = $requestDTO->validate();
        if ($validationErrors !== []) {
            Yii::$app->response->statusCode = 422;
            return $validationErrors;
        }

        $service = Yii::$container->get(CarService::class);
        $createdCar = $service->createFromDTO($requestDTO->toApplicationDTO());

        Yii::$app->response->statusCode = 201;
        return (new CarResponseDTO($createdCar))->toArray();
    }

    public function actionView(int $id)
    {
        $service = Yii::$container->get(CarService::class);
        $car = $service->getDTOById($id);
        if ($car === null) {
            throw new NotFoundHttpException('Car not found.');
        }

        return (new CarResponseDTO($car))->toArray();
    }

    public function actionList()
    {
        $page = Yii::$app->request->get('page', 1);
        $perPage = Yii::$app->request->get('per_page', 10);

        if (!$this->isPositiveInteger($page)) {
            throw new BadRequestHttpException('Invalid "page" query param.');
        }

        if (!$this->isPositiveInteger($perPage)) {
            throw new BadRequestHttpException('Invalid "per_page" query param.');
        }

        $page = (int) $page;
        $perPage = min((int) $perPage, 100);

        $service = Yii::$container->get(CarService::class);
        $result = $service->getPaginatedDTO($page, $perPage);
        $cars = array_map(
            static fn ($car) => (new CarResponseDTO($car))->toArray(),
            $result['items']
        );
        $total = $result['total'];
        $totalPages = $total === 0 ? 0 : (int) ceil($total / $perPage);

        return [
            'items' => $cars,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ];
    }

    private function isPositiveInteger(mixed $value): bool
    {
        return filter_var(
            $value,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        ) !== false;
    }
}
