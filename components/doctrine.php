<?php

namespace app\components;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class Doctrine extends Component
{
    public $isDevMode = true;
    public $paths = [];
    public $dbParams = [];
    public $cache = null;
    public $proxyDir = null;

    private $_entityManager;

    public function init()
    {
        parent::init();
        $this->initEntityManager();
    }

    private function resolveValue($value)
    {
        if (is_callable($value)) {
            return call_user_func($value);
        }
        return $value;
    }

    private function initEntityManager()
    {
        // Проверяем наличие путей
        if (empty($this->paths)) {
            throw new InvalidConfigException('Paths for entities must be specified');
        }

        // Преобразуем paths в массив и разрешаем алиасы
        $paths = (array)$this->paths;
        $resolvedPaths = [];

        foreach ($paths as $path) {
            // Разрешаем Yii алиас
            $realPath = Yii::getAlias($path);

            if (!is_string($realPath)) {
                throw new InvalidConfigException("Invalid path: {$path}");
            }

            // Проверяем существование директории
            if (!is_dir($realPath)) {
                Yii::warning("Entity directory does not exist: {$realPath}", __METHOD__);
                // Не выбрасываем исключение, просто логируем
            }

            $resolvedPaths[] = $realPath;
            Yii::info("Entity path resolved: {$realPath}", __METHOD__);
        }

        // Настройка прокси директории
        $proxyDir = $this->proxyDir ? Yii::getAlias($this->proxyDir) : Yii::getAlias('@runtime/doctrine/proxies');

        // Создаем директорию для прокси, если её нет
        if (!is_dir($proxyDir)) {
            mkdir($proxyDir, 0755, true);
        }

        // Создаем конфигурацию с использованием атрибутов (PHP 8+)
        $config = Setup::createAttributeMetadataConfiguration(
            $resolvedPaths,
            $this->isDevMode,
            $proxyDir,
            $this->resolveValue($this->cache)
        );

        // Разрешаем dbParams
        $dbParams = $this->resolveValue($this->dbParams);

        // Создаем EntityManager
        $this->_entityManager = EntityManager::create($dbParams, $config);
    }

    public function getEntityManager()
    {
        return $this->_entityManager;
    }
}
