<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Telegram\TelegramService;
use App\Services\Telegram\StateManager;
use App\Services\Telegram\KeyboardService;
use App\Services\Telegram\CallbackHandler;
use App\Services\Telegram\MessageHandler;
use App\Services\Telegram\MessageCacheService;
use App\Services\Telegram\Handlers\RepairHandler;
use App\Services\Telegram\Handlers\CartridgeHandler;
use App\Services\Telegram\Handlers\InventoryHandler;
use App\Services\Telegram\Handlers\AdminHandler;

class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Основные сервисы
        $this->app->singleton(MessageCacheService::class);
        
        $this->app->singleton(TelegramService::class, function ($app) {
            return new TelegramService($app->make(MessageCacheService::class));
        });
        
        $this->app->singleton(StateManager::class);
        
        // Сервис клавиатур зависит от TelegramService
        $this->app->singleton(KeyboardService::class, function ($app) {
            return new KeyboardService($app->make(TelegramService::class));
        });

        // Обработчики
        $this->app->singleton(RepairHandler::class, function ($app) {
            return new RepairHandler(
                $app->make(TelegramService::class),
                $app->make(StateManager::class),
                $app->make(KeyboardService::class)
            );
        });

        $this->app->singleton(CartridgeHandler::class, function ($app) {
            return new CartridgeHandler(
                $app->make(TelegramService::class),
                $app->make(StateManager::class),
                $app->make(KeyboardService::class)
            );
        });

        $this->app->singleton(InventoryHandler::class, function ($app) {
            return new InventoryHandler(
                $app->make(TelegramService::class),
                $app->make(StateManager::class),
                $app->make(KeyboardService::class)
            );
        });

        $this->app->singleton(AdminHandler::class, function ($app) {
            return new AdminHandler(
                $app->make(TelegramService::class),
                $app->make(StateManager::class),
                $app->make(KeyboardService::class)
            );
        });

        // Главные обработчики
        $this->app->singleton(CallbackHandler::class, function ($app) {
            return new CallbackHandler(
                $app->make(TelegramService::class),
                $app->make(StateManager::class),
                $app->make(KeyboardService::class),
                $app->make(RepairHandler::class),
                $app->make(CartridgeHandler::class),
                $app->make(InventoryHandler::class),
                $app->make(AdminHandler::class)
            );
        });

        $this->app->singleton(MessageHandler::class, function ($app) {
            return new MessageHandler(
                $app->make(TelegramService::class),
                $app->make(StateManager::class),
                $app->make(KeyboardService::class),
                $app->make(RepairHandler::class),
                $app->make(CartridgeHandler::class),
                $app->make(InventoryHandler::class),
                $app->make(AdminHandler::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}