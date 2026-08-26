# Выкат обновления Telegram-бота на прод (cpmsd16.online)

**Уточните точный актуальный домен перед стартом** — в файлах проекта встречаются три разных варианта (`cpms16.online` — старый, `cpmsd16.online`, `cpmsd16.website`). `.env.example` сейчас приведён к `cpmsd16.online`, но подтвердите это с реальным DNS/хостингом прежде чем выставлять `APP_URL` в проде.

## ⚠️ Секреты, обнаруженные в репозитории — считать скомпрометированными

При подготовке этой инструкции обнаружено, что `.env.production2` был закоммичен в git (не был в `.gitignore`) и содержал реальный `APP_KEY`, пароль БД, `TELEGRAM_BOT_TOKEN` и `TELEGRAM_WEBHOOK_SECRET` в открытом виде. Также `.env`, `.env.prod`, `storage/.env` на диске содержат тот же `TELEGRAM_WEBHOOK_SECRET=ZyQf8AL0kKyby4Kwry`.

Сделано в рамках этой сессии:
- `.env.production2` снят с трекинга git (`git rm --cached`) — сам файл на диске остался, но не будет закоммичен снова.
- `.gitignore` ужесточён: теперь `.env.*` игнорируется целиком (с явным исключением `.env.example`), плюс добавлен `storage/.env` — раньше паттерн `.env.prod`/`.env.production` не покрывал `.env.production2` и `storage/.env`.
- `.env.example` очищен от реальных секретов, домен приведён к `cpmsd16.online`.

**Осталось сделать вам (не могу выполнить за вас):**
1. Перевыпустить bot token через `@BotFather` → `/revoke` для затронутого бота — токен `7663510884:AAH...` считать скомпрометированным независимо от дальнейших действий (он навсегда остался в истории git, если этот коммит уже был запушен на удалённый репозиторий).
2. Сгенерировать новый `TELEGRAM_WEBHOOK_SECRET` (не переиспользовать `ZyQf8AL0kKyby4Kwry`).
3. Сменить пароль БД, если репозиторий на GitHub/GitLab не приватный или мог быть замечен кем-то посторонним.
4. Если коммит с `.env.production2` уже был запушен в удалённый репозиторий — одного `git rm --cached` недостаточно, секреты остаются в истории. Потребуется `git filter-repo`/BFG Repo-Cleaner + force-push, либо (проще и надёжнее) просто ротация всех секретов, что и так необходимо сделать.

## 0. Что деплоим

Незакоммиченные изменения этой сессии (5 фаз доработки бота): проверьте `git status` перед стартом — сейчас это рабочая копия, ещё не в git. Сначала закоммитьте и запушьте (или подготовьте архив файлов для FTP), если ещё не сделали.

```bash
git add -A
git status   # проверьте, что не попадает лишнее (.codex/, .serena/, AGENTS.md — решите, нужны ли эти файлы в репо)
git commit -m "Telegram bot overhaul: profiles, onboarding, workplace memory, admin panel"
git push origin main
```

Новые файлы, которые обязательно должны попасть на прод:
- `app/Models/TelegramProfile.php`
- `app/Services/Telegram/TelegramProfileService.php`
- `app/Services/Telegram/Handlers/OnboardingHandler.php`
- `app/Services/Telegram/Handlers/AdminPanelHandler.php`
- `database/migrations/2026_08_26_105354_create_telegram_profiles_table.php`
- `database/migrations/2026_08_26_135812_add_phone_to_telegram_profiles_table.php`
- изменённые: `TelegramBotController.php`, `TelegramServiceProvider.php`, `CallbackHandler.php`, `MessageHandler.php`, `KeyboardService.php`, `ReplyKeyboardService.php`, `RepairHandler.php`, `CartridgeHandler.php`, `routes/api.php`, `config/services.php`
- удалённые: `app/Services/Telegram/RepairHandler.php`, `app/Services/Telegram/InventoryHandler.php` (мёртвые дубли — на проде эти файлы тоже нужно удалить, не оставлять)

## 1. Загрузка файлов (FTP / файловый менеджер хостинга)

1. Заранее сделайте бэкап текущей продовой директории проекта и текущей БД (экспорт через phpMyAdmin/консоль MySQL в hPanel) — на случай отката.
2. Загрузите изменённые/новые файлы поверх текущих на сервере (FTP или File Manager в hPanel).
3. **Убедитесь, что старые файлы `app/Services/Telegram/RepairHandler.php` и `app/Services/Telegram/InventoryHandler.php` (НЕ в подпапке `Handlers/`) удалены на сервере вручную** — простая загрузка новых файлов их не удалит, FTP-синхронизация не убирает лишнее автоматически.
4. Если на хостинге нет `vendor/` в git (обычно так и есть) — либо загрузите актуальный `vendor/` целиком (если `composer install` недоступен на сервере), либо выполните `composer install --no-dev --optimize-autoloader` в терминале хостинга (см. шаг 2).

## 2. Выполнение команд через терминал хостинга (hPanel → Advanced → SSH Access, или Terminal)

Зайдите в браузерный терминал хостинга, перейдите в директорию проекта и выполните по порядку:

```bash
cd /domains/cpmsd16.online/public_html   # путь может отличаться — уточните реальный путь в hPanel

# Если vendor/ не залит вручную:
composer install --no-dev --optimize-autoloader

# Применить новые миграции (создаёт telegram_profiles, добавляет phone)
php artisan migrate --force

# Сбросить закэшированный конфиг/роуты (обязательно после правок config/services.php и routes/api.php)
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache

# Прогнать тесты бота на проде опционально, если PHPUnit доступен там же — иначе доверяем прогону, сделанному локально
```

## 3. Проверка `.env` на проде перед перезапуском webhook

Убедитесь, что в **боевом** `.env` (не в `.env.example`) выставлено:

```env
APP_URL=https://cpmsd16.online
TELEGRAM_BOT_TOKEN=<реальный боевой токен бота>
TELEGRAM_WEBHOOK_SECRET=<новый, сгенерированный секрет — не тот, что в .env.example>
```

`TELEGRAM_WEBHOOK_SECRET` обязателен — без него в production вебхук теперь отвечает 503 (это защита, добавленная в этой доработке).

Сгенерировать секрет можно, например:
```bash
php -r "echo bin2hex(random_bytes(16)), PHP_EOL;"
```

После правки `.env` обязательно повторите `php artisan config:cache`.

## 4. Переустановка webhook на новый домен/секрет

```bash
php artisan telegram:set-webhook
php artisan telegram:webhook-info   # проверить, что url = https://cpmsd16.online/api/telegram/webhook и статус ok
```

Если `set-webhook` покажет ошибку — проверьте `telegram:test-bot` и `telegram:diagnose` (обе команды уже есть в проекте, см. `routes/console.php`).

## 5. Проверка работы бота

```bash
php artisan telegram:test-bot
```

Затем вручную в Telegram:
1. Откройте бота, отправьте `/start` — должно прийти приветствие и (для новых пользователей) предложение поделиться контактом/настроить рабочее место.
2. Проверьте, что видна **только** обновлённая клавиатура: «🔧 Виклик IT майстра» / «🖨️ Заміна картриджа» (без старых пунктов инвентаря/админки в reply-клавиатуре).
3. Оформите тестовую заявку на ремонт до конца — убедитесь, что она создаётся и приходит уведомление админам.
4. Если вы (или другой пользователь) — admin/director в `users.role` и у вас заполнен `users.telegram_id` — на главном меню должна появиться кнопка «👑 Адмін-панель». Если её нет: зайдите в веб-профиль (`/profile`) и привяжите Telegram, либо проверьте, что `users.telegram_id` реально совпадает с вашим Telegram user id.

## 6. Регистрация меню в Telegram (важный отдельный пункт)

Тут два независимых понятия — не путайте их:

**a) Клавиатуры бота (reply/inline) — обновляются автоматически.** Ничего регистрировать отдельно не нужно: как только задеплоен новый код и вебхук работает, бот на любое следующее сообщение/`/start` сам пришлёт новую клавиатуру. Никакого шага в BotFather для этого не требуется.

**b) Список слэш-команд бота в интерфейсе Telegram (тот, что выпадает по кнопке "Меню"/"/" в чате) — регистрируется отдельно через @BotFather, если вы хотите обновить список.** Сейчас это не менялось программно (проект не вызывает `setMyCommands` через API). Если хотите обновить список видимых команд:

1. Откройте чат с [@BotFather](https://t.me/BotFather) в Telegram.
2. Отправьте `/setcommands`.
3. Выберите вашего бота из списка.
4. Пришлите новый список в формате `команда - описание`, например:
   ```
   start - Головне меню
   help - Довідка
   cancel - Скасувати поточну дію
   ```
   (Не включайте `/admin` и `/status` в публичный список, если не хотите светить их обычным пользователям — команда всё равно будет работать по прямому вводу, просто не будет в подсказке меню.)
5. BotFather подтвердит: "Success! Command list updated."

Это чисто косметический пункт (список подсказок), функционально бот работает и без него.

## 7. Откат в случае проблем

Если что-то пошло не так после `php artisan migrate --force`:

```bash
php artisan migrate:rollback --step=2   # откатывает 2 последние миграции (telegram_profiles + phone-колонка)
```

Затем верните предыдущую версию файлов из бэкапа (шаг 1) и восстановите `TELEGRAM_WEBHOOK_SECRET`/webhook на прежние значения, если меняли.
