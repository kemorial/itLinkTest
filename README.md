# Car Service

REST API для публикации и просмотра объявлений об автомобилях на `Yii2 + Doctrine + PostgreSQL`.

## Стек

- PHP 8.4
- Yii2
- Doctrine ORM
- PostgreSQL
- Docker / Docker Compose
- Codeception

## Архитектура

Проект разделён на слои:

- `domain` - доменные модели и интерфейсы репозиториев/сервисов
- `application` - DTO и application service
- `infrastructure` - Doctrine entities и реализации репозиториев
- `presentation` - контроллеры и request/response DTO

## Структура объявления

### Car

- `id` - integer
- `title` - string
- `description` - string
- `price` - decimal(10,2), в API передаётся строкой
- `photo_url` - string|null
- `contacts` - string
- `created_at` - datetime, ISO 8601
- `options` - object|null

Поле `options` можно либо не передавать вообще, либо передавать полностью заполненным объектом.

### Car options

- `brand` - string
- `model` - string
- `year` - integer
- `body` - string
- `mileage` - integer

## API

Базовый префикс всех ручек: `/api`

### 1. Создать объявление

`POST /api/car/create`

`photo_url` в запросе необязателен.

#### Request body

```json
{
  "title": "BMW X5",
  "description": "Diesel SUV",
  "price": "35000.50",
  "photo_url": "https://example.com/car.jpg",
  "contacts": "+1-202-555-0123",
  "options": {
    "brand": "BMW",
    "model": "X5",
    "year": 2020,
    "body": "SUV",
    "mileage": 80000
  }
}
```

#### Response

- `201 Created`

```json
{
  "id": 1,
  "title": "BMW X5",
  "description": "Diesel SUV",
  "price": "35000.50",
  "photo_url": "https://example.com/car.jpg",
  "contacts": "+1-202-555-0123",
  "created_at": "2026-03-30T11:20:00+00:00",
  "options": {
    "brand": "BMW",
    "model": "X5",
    "year": 2020,
    "body": "SUV",
    "mileage": 80000
  }
}
```

#### Validation error

- `422 Unprocessable Entity`

Пример:

```json
{
  "error": "Validation failed",
  "missing": ["title", "price"]
}
```

или

```json
{
  "error": "Validation failed",
  "field": "price"
}
```

### 2. Получить объявление по id

`GET /api/car/{id}`

#### Response

- `200 OK`

```json
{
  "id": 1,
  "title": "BMW X5",
  "description": "Diesel SUV",
  "price": "35000.50",
  "photo_url": "https://example.com/car.jpg",
  "contacts": "+1-202-555-0123",
  "created_at": "2026-03-30T11:20:00+00:00",
  "options": {
    "brand": "BMW",
    "model": "X5",
    "year": 2020,
    "body": "SUV",
    "mileage": 80000
  }
}
```

#### Not found

- `404 Not Found`

### 3. Получить список объявлений

`GET /api/car/list?page=1&per_page=10`

#### Response

- `200 OK`

```json
{
  "items": [
    {
      "id": 1,
      "title": "BMW X5",
      "description": "Diesel SUV",
      "price": "35000.50",
      "photo_url": "https://example.com/car.jpg",
      "contacts": "+1-202-555-0123",
      "created_at": "2026-03-30T11:20:00+00:00",
      "options": {
        "brand": "BMW",
        "model": "X5",
        "year": 2020,
        "body": "SUV",
        "mileage": 80000
      }
    }
  ],
  "pagination": {
    "page": 1,
    "per_page": 10,
    "total": 1,
    "total_pages": 1
  }
}
```

## Запуск проекта

### Вариант 1. Локально

#### 1. Установить зависимости

```bash
composer install
```

#### 2. Поднять PostgreSQL

Нужна база:

- `host`: `localhost`
- `port`: `5432`
- `dbname`: `car_service`
- `user`: `postgres`
- `password`: `superPassword`

Если параметры отличаются, поправь подключение в [config/db.php](/Users/kemorial/projects/testTasks/itLink/config/db.php), [config/web.php](/Users/kemorial/projects/testTasks/itLink/config/web.php) и [config/console.php](/Users/kemorial/projects/testTasks/itLink/config/console.php).

#### 3. Применить миграции

```bash
php yii migrate --interactive=0
```

#### 4. Запустить приложение

```bash
php yii serve 0.0.0.0:8080
```

Приложение будет доступно по адресу:

```text
http://localhost:8080
```

### Вариант 2. Через Docker Compose

#### 1. Собрать и запустить контейнеры

```bash
docker compose up --build
```

или в фоне:

```bash
docker compose up --build -d
```

Миграции запускаются сами по необходимости при помощи скрипта entrypoint.sh

#### 2. Приложение будет доступно по адресу

```text
http://localhost
```

Порт `80` на хосте проброшен в `8080` контейнера.

#### 3. Остановить контейнеры

```bash
docker compose down
```

#### 4. Удалить контейнеры и volume базы

```bash
docker compose down -v
```

## Полезные команды

### Применить миграции вручную

```bash
docker compose exec php php yii migrate --interactive=0
```

### Посмотреть логи php-контейнера

```bash
docker compose logs -f php
```

### Зайти внутрь php-контейнера

```bash
docker compose exec php sh
```

### Запустить unit-тесты

```bash
vendor/bin/codecept run unit
```

### Запустить конкретный unit-тест сервиса

```bash
vendor/bin/codecept run unit tests/unit/application/services/CarServiceTest.php
```

## Примеры запросов

### Создание объявления

```bash
curl -X POST http://localhost/api/car/create \
  -H "Content-Type: application/json" \
  -d '{
    "title": "BMW X5",
    "description": "Diesel SUV",
    "price": "35000.50",
    "photo_url": "https://example.com/car.jpg",
    "contacts": "+1-202-555-0123",
    "options": {
      "brand": "BMW",
      "model": "X5",
      "year": 2020,
      "body": "SUV",
      "mileage": 80000
    }
  }'
```

### Получение одного объявления

```bash
curl http://localhost/api/car/1
```

### Получение списка объявлений

```bash
curl "http://localhost/api/car/list?page=1&per_page=10"
```

## Тестирование

Запуск всех unit и functional тестов:

```bash
vendor/bin/codecept run
```

Только unit-тесты:

```bash
vendor/bin/codecept run unit
```
