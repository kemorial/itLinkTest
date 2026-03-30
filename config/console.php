<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
        '@entities' => '@app/infrastructure/doctrine/entities'
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'container' => [
        'singletons' => [
            EntityManagerInterface::class => function () {
                $paths = [Yii::getAlias('@entities')];

                $config = ORMSetup::createAttributeMetadataConfiguration(
                    $paths,
                    YII_DEBUG,
                    Yii::getAlias('@runtime/doctrine/proxies')
                );

                $connection = [
                    'driver' => 'pdo_pgsql',
                    'user' => 'postgres',
                    'password' => 'superPassword',
                    'dbname' => 'car_service',
                    'host' => 'database',
                ];

                return EntityManager::create($connection, $config);
            },
            \app\domain\repositories\CarRepositoryInterface::class => \app\infrastructure\doctrine\repositories\DoctrineCarRepository::class,
            \app\application\services\CarService::class => function (\yii\di\Container $container) {
                return new \app\application\services\CarService(
                    $container->get(\app\domain\repositories\CarRepositoryInterface::class)
                );
            },
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
}

return $config;
