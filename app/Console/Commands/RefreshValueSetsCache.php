<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Exceptions\CoronaCheckServiceException;
use App\Services\CoronaCheck\ValueSetsService;
use Illuminate\Console\Command;

class RefreshValueSetsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'value-sets:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the value sets cache';

    /**
     * Execute the console command.
     *
     * @param ValueSetsService $service
     * @return int
     */
    public function handle(ValueSetsService $service): int
    {
        $service->clearCache();
        $this->info("Cache cleared");

        $this->info("Fetch remote value sets");
        try {
            $data = $service->fetch();
        } catch (CoronaCheckServiceException $e) {
            report($e);
            $this->error("Failed to fetch remote value sets: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info("Found value sets:");
        $this->table(
            ['Key', 'Item count'],
            collect($data)->map(function ($value, $key) {
                if (is_array($value)) {
                    return [$key, count($value)];
                }
                return [$key, $value];
            })->toArray()
        );

        return Command::SUCCESS;
    }
}
