# Telegram Bot для управления заявками - Рефакторинг

## Основные изменения

### Исправленные проблемы:
1. **Ошибка 400 с reply markup** - исправлена сериализация клавиатур в `TelegramService`
2. **Ошибка "message is not modified"** - добавлен `MessageCacheService` для отслеживания изменений
3. **Неработающие кнопки инвентаризации** - исправлена маршрутизация в `CallbackHandler`
4. **Плохая структура кода** - разбито на логичные классы

### Новая архитектура:

```
App/
├── Http/Controllers/Api/
│   └── TelegramBotController.php          # Основной контроллер (минимальный)
├── Services/Telegram/
│   ├── TelegramService.php                # API для работы с Telegram
│   ├── MessageCacheService.php            # Кеширование сообщений (избегает дублирования)
│   ├── StateManager.php                   # Управление состояниями пользователей
│   ├── KeyboardService.php                # Генерация клавиатур
│   ├── CallbackHandler.php                # Обработка callback запросов
│   ├── MessageHandler.php                 # Обработка текстовых сообщений
│   └── Handlers/
│       ├── RepairHandler.php              # Обработка заявок на ремонт
│       ├── CartridgeHandler.php           # Обработка замены картриджей
│       ├── InventoryHandler.php           # Обработка инвентаризации
│       └── AdminHandler.php               # Админ-панель
└── Providers/
    └── TelegramServiceProvider.php        # Регистрация зависимостей
```

## Установка

### 1. Замените старый контроллер
Скопируйте новые файлы в соответствующие директории Laravel проекта.

### 2. Зарегистрируйте Service Provider
Добавьте в `config/app.php`:

```php
'providers' => [
    // ...
    App\Providers\TelegramServiceProvider::class,
],
```

### 3. Создайте директории
```bash
mkdir -p app/Services/Telegram/Handlers
```

### 4. Очистите кеш
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

## Основные улучшения

### 🔧 Исправления
- **Сериализация клавиатур**: Убрано двойное `json_encode`, теперь используется `asJson()` в HTTP клиенте
- **Предотвращение дублирования**: `MessageCacheService` отслеживает изменения и предотвращает ошибку "message is not modified"
- **Маршрутизация callback'ов**: Правильная обработка всех инвентарных действий
- **Обработка состояний**: Улучшена логика переходов между состояниями

### 🏗️ Архитектура
- **Разделение ответственности**: Каждый класс имеет четкую роль
- **Dependency Injection**: Все зависимости инжектируются через Service Provider
- **Модульность**: Легко добавлять новые типы обработчиков
- **Кеширование**: Умное кеширование предотвращает повторные запросы

### 📋 Функциональность
- **Инвентаризация**: Быстрое и ручное добавление оборудования
- **Админ-панель**: Управление заявками и статистика
- **Уведомления**: Автоматические уведомления администраторов
- **Защита от спама**: Предотвращение дублированных сообщений

## Использование

### Добавление нового обработчика
1. Создайте класс в `App\Services\Telegram\Handlers\`
2. Зарегистрируйте в `TelegramServiceProvider`
3. Добавьте маршрутизацию в `CallbackHandler` или `MessageHandler`

### Добавление новой клавиатуры
Добавьте метод в `KeyboardService`:

```php
public function getMyCustomKeyboard(): array
{
    return [
        'inline_keyboard' => [
            [
                ['text' => 'Кнопка 1', 'callback_data' => 'action_1'],
                ['text' => 'Кнопка 2', 'callback_data' => 'action_2']
            ]
        ]
    ];
}
```

### Добавление нового состояния
1. Добавьте состояние в `StateManager`
2. Обработайте в соответствующем Handler'е
3. Добавьте маршрутизацию в `MessageHandler`

## Тестирование

### Проверка основного функционала:
1. `/start` - должно показать главное меню
2. Кнопка "🔧 Вызов IT мастера" - должна открыть выбор филиала
3. Кнопка "📋 Управление инвентарем" (для админов) - должна работать без ошибок
4. Все callback кнопки должны отвечать без ошибок 400

### Проверка логов:
```bash
tail -f storage/logs/laravel.log | grep Telegram
```

Должны исчезнуть ошибки:
- `Bad Request: object expected as reply markup`
- `Bad Request: message is not modified`
- `Unknown callback action`

## Дополнительные возможности

### MessageCacheService
Новый сервис предотвращает ошибки дублирования сообщений:

```php
// Автоматически проверяет изменения перед редактированием
$telegram->editMessage($chatId, $messageId, $text, $keyboard);

// Принудительная очистка кеша (если нужно)
$messageCache->forget($chatId, $messageId);
```

### Безопасное редактирование
```php
// Если редактирование не удается, отправляет новое сообщение
$telegram->editMessageSafe($chatId, $messageId, $text, $keyboard);
```

## Отладка

### Если кнопки не работают:
1. Проверьте регистрацию Service Provider
2. Убедитесь, что все Handler'ы правильно инж