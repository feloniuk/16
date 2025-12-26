<?php

namespace App\Console\Commands;

use App\Models\RoomInventory;
use Illuminate\Console\Command;

class DistributeWarehouseCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warehouse:distribute-categories {--force : Force update even if category already set}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically distribute warehouse items to predefined categories based on keywords';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categories = config('warehouse-categories');
        $force = $this->option('force');

        // Mapping keywords to categories
        $categoryKeywords = [
            'орг техніка' => [
                'ноутбук', 'комп\'ютер', 'монітор', 'принтер', 'телефон',
                'миша', 'клавіатура', 'дбж', 'озу', 'сервер', 'гарячий',
                'процесор', 'материнська', 'блок живлення', 'комутатор',
                'свіч', 'хаб', 'роутер', 'модем', 'сканер',
            ],
            'електрика' => [
                'кабель', 'провід', 'розетка', 'вимикач', 'лампа',
                'електрон', 'батарей', 'акумулятор', 'зарядка',
                'usb', 'hdmi', 'vga', 'sata', 'патч-корд', 'подовжувач',
            ],
            'господарчі товари' => [
                'мило', 'миючий', 'серветка', 'папір', 'туалетний',
                'шпак', 'гель', 'моючий', 'дезинфікуючий', 'спирт',
            ],
            'канцелярські товари' => [
                'ручка', 'олівець', 'папір', 'зошит', 'блокнот',
                'скоч', 'скріпка', 'кнопка', 'закладка', 'картонна',
                'конверт', 'папка', 'файл', 'кліпса',
            ],
            'сантехніка' => [
                'труба', 'вентиль', 'кран', 'фітинг', 'мусор',
                'раковина', 'умивальник', 'бак', 'зливний',
            ],
            'буд матеріали' => [
                'цегла', 'гіпс', 'штукатур', 'краска', 'фарба',
                'грунтовка', 'лак', 'клей', 'цемент', 'пісок',
                'цвях', 'шуруп', 'дюбель', 'дошка', 'брус',
            ],
        ];

        // Get all warehouse items
        $query = RoomInventory::where('branch_id', 6);

        if (! $force) {
            $query->whereNull('category');
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            $this->info('No items to distribute'.($force ? '.' : ' (use --force to update existing).'));

            return 0;
        }

        $distributed = 0;
        $undistributed = 0;

        $this->withProgressBar($items, function ($item) use ($categoryKeywords, &$distributed, &$undistributed) {
            $equipmentType = mb_strtolower($item->equipment_type);

            $foundCategory = null;

            // Search for matching keywords
            foreach ($categoryKeywords as $category => $keywords) {
                foreach ($keywords as $keyword) {
                    if (mb_strpos($equipmentType, mb_strtolower($keyword)) !== false) {
                        $foundCategory = $category;
                        break 2;
                    }
                }
            }

            if ($foundCategory) {
                $item->update(['category' => $foundCategory]);
                $distributed++;
            } else {
                // Default to 'різне' if no match found
                $item->update(['category' => 'різне']);
                $undistributed++;
            }
        });

        $this->newLine();
        $this->info('Distribution complete!');
        $this->line("- Distributed by keywords: <info>{$distributed}</info>");
        $this->line("- Assigned to 'різне': <info>{$undistributed}</info>");

        return 0;
    }
}
