<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use \Exception;
use App\Services\AutotracService;

/**
 * Class SyncOnixsatCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class AutotracImportCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "autotrac:import";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Autotrac Webservice - Import positions";

    public function __construct(AutotracService $autotracService)
    {
        parent::__construct();
        $this->autotracService = $autotracService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->autotracService->importPositions();
        } catch (Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }
}
