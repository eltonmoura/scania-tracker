<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OnixsatService;
use \Exception;

/**
 * Class SyncOnixsatCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class OnixsatImportVeiculosCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "onixsat:import-veiculos";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Onixsat Webservice - Import Veiculos (limite 5 min)";

    public function __construct(OnixsatService $onixsatService)
    {
        parent::__construct();
        $this->onixsatService = $onixsatService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->onixsatService->importVeiculos();
        } catch (Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }
}
