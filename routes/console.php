<?php
// routes/console.php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Кастомные консольные команды для IT Support Panel
Artisan::command('support:clear-old-states', function () {
    $this->info('Очистка старых состояний пользователей...');
    
    $deleted = \App\Models\UserState::where('updated_at', '<', now()->subDays(7))->delete();
    
    $this->info("Удалено $deleted старых состояний");
})->purpose('Очистить старые состояния пользователей');

Artisan::command('support:stats', function () {
    $this->info('=== Статистика IT Support Panel ===');
    
    $repairs = \App\Models\RepairRequest::count();
    $repairsNew = \App\Models\RepairRequest::where('status', 'нова')->count();
    $cartridges = \App\Models\CartridgeReplacement::count();
    $branches = \App\Models\Branch::where('is_active', true)->count();
    
    $this->table(['Метрика', 'Значение'], [
        ['Всего заявок на ремонт', $repairs],
        ['Новых заявок', $repairsNew],
        ['Замен картриджей', $cartridges],
        ['Активных филиалов', $branches],
    ]);
})->purpose('Показать статистику системы');

Artisan::command('support:create-admin {telegram_id} {name}', function ($telegram_id, $name) {
    $this->info("Создание администратора...");
    
    // Проверяем существование
    $existing = \App\Models\Admin::where('telegram_id', $telegram_id)->first();
    if ($existing) {
        $this->error("Администратор с Telegram ID $telegram_id уже существует!");
        return;
    }
    
    // Создаем администратора
    $admin = \App\Models\Admin::create([
        'telegram_id' => $telegram_id,
        'name' => $name,
        'is_active' => true
    ]);
    
    $this->info("Администратор создан: ID {$admin->id}, Telegram ID: $telegram_id, Имя: $name");
})->purpose('Создать администратора');

// Команды для работы с Telegram Bot
Artisan::command('telegram:set-webhook', function () {
    $this->info('Установка webhook для Telegram бота...');
    
    $botToken = config('services.telegram.bot_token');
    if (!$botToken) {
        $this->error('TELEGRAM_BOT_TOKEN не задан в .env файле!');
        return;
    }
    
    $webhookUrl = config('app.url') . '/api/telegram/webhook';
    $apiUrl = "https://api.telegram.org/bot{$botToken}/setWebhook";
    
    try {
        $response = \Illuminate\Support\Facades\Http::post($apiUrl, [
            'url' => $webhookUrl
        ]);
        
        $result = $response->json();
        
        if ($result['ok']) {
            $this->info("✅ Webhook успешно установлен: {$webhookUrl}");
        } else {
            $this->error("❌ Ошибка установки webhook: " . ($result['description'] ?? 'Unknown error'));
        }
    } catch (\Exception $e) {
        $this->error("❌ Ошибка запроса: " . $e->getMessage());
    }
})->purpose('Установить webhook для Telegram бота');

Artisan::command('telegram:webhook-info', function () {
    $this->info('Получение информации о webhook...');
    
    $botToken = config('services.telegram.bot_token');
    if (!$botToken) {
        $this->error('TELEGRAM_BOT_TOKEN не задан в .env файле!');
        return;
    }
    
    $apiUrl = "https://api.telegram.org/bot{$botToken}/getWebhookInfo";
    
    try {
        $response = \Illuminate\Support\Facades\Http::get($apiUrl);
        $result = $response->json();
        
        if ($result['ok']) {
            $info = $result['result'];
            
            $this->info('=== Информация о webhook ===');
            $this->table(['Параметр', 'Значение'], [
                ['URL', $info['url'] ?? 'Не установлен'],
                ['Статус', $info['has_custom_certificate'] ? 'С сертификатом' : 'Без сертификата'],
                ['Ожидающих обновлений', $info['pending_update_count'] ?? 0],
                ['Последняя ошибка', $info['last_error_message'] ?? 'Нет'],
                ['Дата последней ошибки', isset($info['last_error_date']) ? date('Y-m-d H:i:s', $info['last_error_date']) : 'Нет'],
                ['Максимальные соединения', $info['max_connections'] ?? 'По умолчанию'],
                ['Разрешенные обновления', isset($info['allowed_updates']) ? implode(', ', $info['allowed_updates']) : 'Все'],
            ]);
        } else {
            $this->error("❌ Ошибка получения информации: " . ($result['description'] ?? 'Unknown error'));
        }
    } catch (\Exception $e) {
        $this->error("❌ Ошибка запроса: " . $e->getMessage());
    }
})->purpose('Получить информацию о webhook');

Artisan::command('telegram:delete-webhook', function () {
    $this->info('Удаление webhook...');
    
    $botToken = config('services.telegram.bot_token');
    if (!$botToken) {
        $this->error('TELEGRAM_BOT_TOKEN не задан в .env файле!');
        return;
    }
    
    $apiUrl = "https://api.telegram.org/bot{$botToken}/deleteWebhook";
    
    try {
        $response = \Illuminate\Support\Facades\Http::post($apiUrl);
        $result = $response->json();
        
        if ($result['ok']) {
            $this->info("✅ Webhook успешно удален");
        } else {
            $this->error("❌ Ошибка удаления webhook: " . ($result['description'] ?? 'Unknown error'));
        }
    } catch (\Exception $e) {
        $this->error("❌ Ошибка запроса: " . $e->getMessage());
    }
})->purpose('Удалить webhook');

Artisan::command('telegram:test-bot', function () {
    $this->info('Тестирование бота...');
    
    $botToken = config('services.telegram.bot_token');
    if (!$botToken) {
        $this->error('TELEGRAM_BOT_TOKEN не задан в .env файле!');
        return;
    }
    
    $apiUrl = "https://api.telegram.org/bot{$botToken}/getMe";
    
    try {
        $response = \Illuminate\Support\Facades\Http::get($apiUrl);
        $result = $response->json();
        
        if ($result['ok']) {
            $bot = $result['result'];
            
            $this->info('=== Информация о боте ===');
            $this->table(['Параметр', 'Значение'], [
                ['ID', $bot['id']],
                ['Имя', $bot['first_name']],
                ['Username', '@' . $bot['username']],
                ['Тип', $bot['is_bot'] ? 'Бот' : 'Пользователь'],
                ['Может присоединяться к группам', $bot['can_join_groups'] ? 'Да' : 'Нет'],
                ['Может читать все сообщения', $bot['can_read_all_group_messages'] ? 'Да' : 'Нет'],
                ['Поддерживает inline запросы', $bot['supports_inline_queries'] ? 'Да' : 'Нет'],
            ]);
            
            $this->info("✅ Бот работает корректно!");
        } else {
            $this->error("❌ Ошибка получения информации о боте: " . ($result['description'] ?? 'Unknown error'));
        }
    } catch (\Exception $e) {
        $this->error("❌ Ошибка запроса: " . $e->getMessage());
    }
})->purpose('Протестировать бота');

Artisan::command('support:backup', function () {
    $this->info('Создание резервной копии...');
    
    $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    
    // Команда mysqldump (настройте под свои данные)
    $command = sprintf(
        'mysqldump -u %s -p%s %s > %s',
        env('DB_USERNAME'),
        env('DB_PASSWORD'),
        env('DB_DATABASE'),
        storage_path('app/backups/' . $filename)
    );
    
    // Создаем директорию для бэкапов
    if (!is_dir(storage_path('app/backups'))) {
        mkdir(storage_path('app/backups'), 0755, true);
    }
    
    $this->info("Резервная копия создана: $filename");
})->purpose('Создать резервную копию базы данных');

Artisan::command('telegram:diagnose', function () {
    $this->info('🔍 Диагностика Telegram Bot...');
    $this->newLine();
    
    // Проверка токена
    $botToken = config('services.telegram.bot_token');
    if (!$botToken) {
        $this->error('❌ TELEGRAM_BOT_TOKEN не установлен в .env');
        return;
    }
    $this->info('✅ Токен бота найден');
    
    // Проверка URL приложения
    $appUrl = config('app.url');
    if (!$appUrl || $appUrl === 'http://localhost') {
        $this->warn('⚠️ APP_URL установлен как localhost - webhook может не работать');
    } else {
        $this->info("✅ APP_URL: {$appUrl}");
    }
    
    // Проверка подключения к боту
    $this->info('🤖 Проверка подключения к боту...');
    try {
        $apiUrl = "https://api.telegram.org/bot{$botToken}/getMe";
        $response = \Illuminate\Support\Facades\Http::timeout(10)->get($apiUrl);
        $result = $response->json();
        
        if ($result['ok']) {
            $bot = $result['result'];
            $this->info("✅ Бот найден: @{$bot['username']} ({$bot['first_name']})");
        } else {
            $this->error("❌ Ошибка бота: " . ($result['description'] ?? 'Unknown'));
            return;
        }
    } catch (\Exception $e) {
        $this->error("❌ Не удается подключиться к Telegram API: " . $e->getMessage());
        return;
    }
    
    // Проверка webhook
    $this->info('🌐 Проверка webhook...');
    try {
        $apiUrl = "https://api.telegram.org/bot{$botToken}/getWebhookInfo";
        $response = \Illuminate\Support\Facades\Http::timeout(10)->get($apiUrl);
        $result = $response->json();
        
        if ($result['ok']) {
            $info = $result['result'];
            $webhookUrl = config('app.url') . '/api/telegram/webhook';
            
            if (empty($info['url'])) {
                $this->warn('⚠️ Webhook не установлен');
                $this->info("Рекомендация: php artisan telegram:set-webhook");
            } elseif ($info['url'] !== $webhookUrl) {
                $this->warn("⚠️ Webhook URL не совпадает:");
                $this->line("   Установлен: {$info['url']}");
                $this->line("   Ожидается: {$webhookUrl}");
                $this->info("Рекомендация: php artisan telegram:set-webhook");
            } else {
                $this->info("✅ Webhook установлен корректно");
            }
            
            if ($info['pending_update_count'] > 0) {
                $this->warn("⚠️ Ожидающих обновлений: {$info['pending_update_count']}");
            }
            
            if (!empty($info['last_error_message'])) {
                $this->error("❌ Последняя ошибка webhook: {$info['last_error_message']}");
                if (isset($info['last_error_date'])) {
                    $errorDate = date('Y-m-d H:i:s', $info['last_error_date']);
                    $this->line("   Время ошибки: {$errorDate}");
                }
            }
        }
    } catch (\Exception $e) {
        $this->error("❌ Ошибка проверки webhook: " . $e->getMessage());
    }
    
    // Проверка базы данных
    $this->info('💾 Проверка базы данных...');
    try {
        $adminsCount = \App\Models\Admin::where('is_active', true)->count();
        $branchesCount = \App\Models\Branch::where('is_active', true)->count();
        
        if ($adminsCount === 0) {
            $this->warn('⚠️ Нет активных администраторов');
            $this->info('Рекомендация: php artisan support:create-admin YOUR_TELEGRAM_ID "Ваше Имя"');
        } else {
            $this->info("✅ Активных администраторов: {$adminsCount}");
        }
        
        if ($branchesCount === 0) {
            $this->warn('⚠️ Нет активных филиалов');
        } else {
            $this->info("✅ Активных филиалов: {$branchesCount}");
        }
        
    } catch (\Exception $e) {
        $this->error("❌ Ошибка подключения к БД: " . $e->getMessage());
    }
    
    // Проверка маршрутов
    $this->info('🛣️ Проверка маршрутов...');
    try {
        $routes = collect(\Illuminate\Support\Facades\Route::getRoutes())->filter(function ($route) {
            return str_contains($route->uri(), 'telegram');
        });
        
        if ($routes->count() > 0) {
            $this->info("✅ Найдено маршрутов Telegram: {$routes->count()}");
        } else {
            $this->error("❌ Маршруты Telegram не найдены");
        }
    } catch (\Exception $e) {
        $this->error("❌ Ошибка проверки маршрутов: " . $e->getMessage());
    }
    
    // Проверка логов
    $this->info('📝 Проверка последних логов...');
    try {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            $telegramLogs = collect(explode("\n", $logs))
                ->filter(fn($line) => str_contains($line, 'Telegram'))
                ->take(-5);
                
            if ($telegramLogs->count() > 0) {
                $this->info("✅ Найдено записей в логах: {$telegramLogs->count()}");
                $this->line("Последние записи:");
                foreach ($telegramLogs as $log) {
                    $this->line("   " . substr($log, 0, 100) . "...");
                }
            } else {
                $this->warn('⚠️ Записей Telegram в логах не найдено');
            }
        } else {
            $this->warn('⚠️ Лог файл не найден');
        }
    } catch (\Exception $e) {
        $this->warn("⚠️ Не удается прочитать логи: " . $e->getMessage());
    }
    
    $this->newLine();
    $this->info('🎯 Диагностика завершена!');
    
    // Рекомендации
    $this->newLine();
    $this->info('📋 Полезные команды:');
    $this->line('   php artisan telegram:set-webhook     - Установить webhook');
    $this->line('   php artisan telegram:test-bot        - Протестировать бота');
    $this->line('   php artisan support:create-admin     - Создать администратора');
    $this->line('   php artisan support:stats            - Статистика системы');
    
})->purpose('Диагностика проблем с Telegram ботом');

// Добавьте эту команду в routes/console.php

Artisan::command('telegram:test-api', function () {
    $this->info('🔍 Тестирование Telegram API запросов...');
    $this->newLine();
    
    $botToken = config('services.telegram.bot_token') ?? env('TELEGRAM_BOT_TOKEN');
    
    if (!$botToken) {
        $this->error('❌ Токен не найден');
        return;
    }
    
    // Тестируем разные методы API
    $methods = [
        'getMe' => [],
        'sendMessage' => [
            'chat_id' => '123456789', // Фиктивный ID для теста
            'text' => 'Test message'
        ],
        'getUpdates' => ['limit' => 1]
    ];
    
    foreach ($methods as $method => $params) {
        $this->info("📡 Тестируем метод: {$method}");
        
        $apiUrl = "https://api.telegram.org/bot{$botToken}/{$method}";
        $this->line("   URL: {$apiUrl}");
        
        try {
            if (empty($params)) {
                $response = \Illuminate\Support\Facades\Http::timeout(10)->get($apiUrl);
            } else {
                $response = \Illuminate\Support\Facades\Http::timeout(10)->post($apiUrl, $params);
            }
            
            $this->line("   HTTP Status: {$response->status()}");
            
            $result = $response->json();
            if ($result) {
                if ($result['ok']) {
                    $this->info("   ✅ Успешно");
                } else {
                    $this->warn("   ⚠️ Ошибка API: {$result['description']} (код: {$result['error_code']})");
                }
            } else {
                $this->error("   ❌ Не удалось декодировать JSON ответ");
                $this->line("   Сырой ответ: " . $response->body());
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Исключение: " . $e->getMessage());
        }
        
        $this->newLine();
    }
    
    // Проверяем последние обновления
    $this->info('📨 Получение последних обновлений...');
    try {
        $apiUrl = "https://api.telegram.org/bot{$botToken}/getUpdates";
        $response = \Illuminate\Support\Facades\Http::timeout(10)->get($apiUrl, ['limit' => 5]);
        $result = $response->json();
        
        if ($result && $result['ok']) {
            $updates = $result['result'];
            $this->info("   Найдено обновлений: " . count($updates));
            
            foreach ($updates as $update) {
                if (isset($update['message'])) {
                    $message = $update['message'];
                    $chatId = $message['chat']['id'];
                    $text = $message['text'] ?? '[Нет текста]';
                    $date = date('Y-m-d H:i:s', $message['date']);
                    
                    $this->line("   - Update ID: {$update['update_id']}");
                    $this->line("     Chat ID: {$chatId}");
                    $this->line("     Текст: {$text}");
                    $this->line("     Дата: {$date}");
                    $this->newLine();
                }
            }
        } else {
            $this->error("   ❌ Не удалось получить обновления");
        }
        
    } catch (\Exception $e) {
        $this->error("   ❌ Ошибка получения обновлений: " . $e->getMessage());
    }
    
})->purpose('Тестирование Telegram API запросов');

Artisan::command('telegram:test-webhook', function () {
    $this->info('🔍 Тестирование webhook напрямую...');
    $this->newLine();
    
    $webhookUrl = config('app.url') . '/api/telegram/webhook';
    
    // Создаем тестовое сообщение (как от Telegram)
    $testUpdate = [
        'update_id' => 999999999,
        'message' => [
            'message_id' => 1,
            'from' => [
                'id' => 123456789,
                'is_bot' => false,
                'first_name' => 'Test',
                'username' => 'testuser'
            ],
            'chat' => [
                'id' => 123456789,
                'first_name' => 'Test',
                'username' => 'testuser',
                'type' => 'private'
            ],
            'date' => time(),
            'text' => '/start'
        ]
    ];
    
    $this->info("📤 Отправляем тестовый webhook на: {$webhookUrl}");
    
    try {
        $response = \Illuminate\Support\Facades\Http::timeout(30)
            ->post($webhookUrl, $testUpdate);
        
        $this->info("📨 HTTP Status: {$response->status()}");
        $this->info("📨 Response: " . $response->body());
        
        if ($response->successful()) {
            $this->info("✅ Webhook отвечает корректно");
        } else {
            $this->error("❌ Webhook вернул ошибку");
        }
        
    } catch (\Exception $e) {
        $this->error("❌ Ошибка при обращении к webhook: " . $e->getMessage());
    }
    
    $this->newLine();
    $this->info("🔍 Проверьте логи Laravel для получения подробностей:");
    $this->line("   tail -f storage/logs/laravel.log");
    
})->purpose('Тестирование webhook напрямую');