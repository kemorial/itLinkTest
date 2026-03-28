<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use app\domain\entities\Car;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class GoController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex()
    {
        $entityManager = Yii::$app->doctrine->getEntityManager();

        // Example: Find a user by ID
        $car = $entityManager->find(Car::class, 1);
        dd($car);

        // Example: Create and persist a new entity
        // $newCar = new Car();
        // $newCar->set('John Doe');
        // $entityManager->persist($newCar);
        // $entityManager->flush();;

        return ExitCode::OK;
    }
}
