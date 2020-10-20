<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OnixsatService;

/**
 * Class SyncOnixsatCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class OnixsatImportMensagensCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "onixsat:import-mensagens";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Onixsat Webservice - Import MensagemCB (limite 30 seg)";

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
            $this->onixsatService->importMensagemCb();
        } catch (Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }
}
