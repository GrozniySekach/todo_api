# Todo API (Laravel)

API для управления задачами с авторизацией, проектами и тегами.

## 🚀 Установка

1. Клонировать репозиторий:
```bash
git clone https://github.com/ваш-репозиторий.git
cd todo_api
```

2. Установить зависимости:
```bash
composer install
```

3. Настройка БД (PostgreSQL):
```bash
cp .env.example .env
# Обновите настройки DB_* в .env
```

4. Запустить миграции:
```bash
php artisan migrate --seed 
```

Для повторного использования:
```bash
php artisan migrate:fresh --seed 
```

5. Запустить сервер:
```bash
php artisan serve
```

## 📚 Документация API

### Импорт Postman
1. Скачайте [TodoAPI.postman_collection.json](ссылка_на_файл)
2. Импортируйте в Postman
3. Используйте Environment:
   - `baseUrl`: http://localhost:8000

### Основные эндпоинты

#### Аутентификация
```http
POST {{baseUrl}}/api/register
POST {{baseUrl}}/api/login
```

#### Профиль
```http
POST {{baseUrl}}/api/profiles
PUT {{baseUrl}}/api/profiles/{{profile_id}}
```

#### Проекты
```http
POST {{baseUrl}}/api/projects
```

#### Задачи
```http
POST {{baseUrl}}/api/tasks
GET {{baseUrl}}/api/tasks?search={{task_title}}&tags=urgent&project_id={{project_id}} # Поиск по названию и другая фильтрация
POST {{baseUrl}}/api/tasks/{{task_id}}/share
DELETE {{baseUrl}}/api/tasks/{{task_id}} # Мягкое удаление
POST {{baseUrl}}/api/tasks/{{task_id}}/restore # Восстановление
```

#### Пример запроса:
```json
POST /api/tasks
{
    "title": "My Task",
    "project_id": 1,
    "tags": ["urgent"]
}
```

## 🛠 Технологии
- Laravel 12+
- PostgreSQL
- Sanctum для аутентификации
- Eloquent ORM

## 📦 Postman Коллекция
Все запросы доступны в [Postman Collection](https://github.com/GrozniySekach/todo_api/blob/main/TodoAPI.postman_collection.json):
- Авторизация
- Управление задачами с фильтрами
- Примеры тестовых данных
