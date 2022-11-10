<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use \Exception;
use App\Services\OmnilinkService;

/**
 * Class SyncOnixsatCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class OmnilinkVeiculosImportCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "omnilink:import-veiculos";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Omnilink Webservice - Import Veiculos";

    public function __construct(OmnilinkService $omnilinkService)
    {
        parent::__construct();
        $this->omnilinkService = $omnilinkService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            print("Importando Veiculos ..." . PHP_EOL);
            $this->omnilinkService->importVehicles();

            print("Done." . PHP_EOL);
        } catch (Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }
}
