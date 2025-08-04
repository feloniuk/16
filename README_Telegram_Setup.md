# Настройка Telegram Bot для IT Support Panel

## 1. Получение токена бота

1. Откройте Telegram и найдите бота @BotFather
2. Отправьте команду `/newbot`
3. Следуйте инструкциям для создания бота
4. Скопируйте полученный токен

## 2. Настройка переменных окружения

Добавьте в ваш `.env` файл:

```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_WEBHOOK_TOKEN=your_webhook_secret_token
```

**Важно:** `TELEGRAM_WEBHOOK_TOKEN` - это любая случайная строка для дополнительной безопасности (необязательно).

## 3. Установка webhook

Выполните команду для установки webhook:

```bash
php artisan telegram:set-webhook
```

## 4. Проверка работы бота

Проверьте статус бота:

```bash
php artisan telegram:test-bot
```

Проверьте информацию о webhook:

```bash
php artisan telegram:webhook-info
```

## 5. Создание администратора

Создайте первого администратора бота:

```bash
php artisan support:create-admin YOUR_TELEGRAM_USER_ID "Ваше Имя"
```

**Как узнать свой Telegram ID:**
1. Отправьте сообщение боту @userinfobot
2. Или используйте бота @RawDataBot
3. Скопируйте ваш ID (число)

## 6. Проверка работы

1. Найдите вашего бота в Telegram
2. Отправьте команду `/start`
3. Бот должен ответить приветственным сообщением с кнопками

## 7. Отладка проблем

### Проблема: "Not Found" в логах

**Причина:** Неправильно настроен webhook или токен бота.

**Решение:**
1. Проверьте правильность токена в `.env`
2. Убедитесь, что APP_URL в `.env` корректный
3. Переустановите webhook:
   ```bash
   php artisan telegram:delete-webhook
   php artisan telegram:set-webhook
   ```

### Проблема: Бот не отвечает

**Решение:**
1. Проверьте логи Laravel: `tail -f storage/logs/laravel.log`
2. Убедитесь, что webhook установлен: `php artisan telegram:webhook-info`
3. Проверьте доступность вашего сервера из интернета

### Проблема: "Unauthorized" ошибка

**Причина:** Неправильный токен бота.

**Решение:**
1. Проверьте токен в `.env`
2. Убедитесь, что токен скопирован полностью
3. Протестируйте бота: `php artisan telegram:test-bot`

## 8. Команды для управления

```bash
# Статистика системы
php artisan support:stats

# Очистка старых состояний пользователей
php artisan support:clear-old-states

# Создание администратора
php artisan support:create-admin TELEGRAM_ID "Имя"

# Информация о webhook
php artisan telegram:webhook-info

# Удаление webhook
php artisan telegram:delete-webhook

# Тестирование бота
php artisan telegram:test-bot
```

## 9. Структура проекта

- `app/Http/Controllers/Api/TelegramBotController.php` - основной контроллер бота
- `routes/api.php` - API маршруты для webhook
- `app/Models/Admin.php` - модель администраторов
- `app/Models/UserState.php` - состояния пользователей бота

## 10. Безопасность

1. Никогда не публикуйте токен бота
2. Используйте HTTPS для webhook
3. Регулярно очищайте старые состояния пользователей
4. Проверяйте права доступа администраторов

## 11. Логирование

Все действия бота логируются в `storage/logs/laravel.log`. Для мониторинга используйте:

```bash
tail -f storage/logs/laravel.log | grep Telegram
```