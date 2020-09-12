<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use \Exception;
use App\Services\SascarService;

/**
 * Class SyncOnixsatCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class SascarVeiculosImportCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "sascar:import-veiculos";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "sascar Webservice - Import Veiculos (limite 5 min)";

    public function __construct(SascarService $sascarService)
    {
        parent::__construct();
        $this->sascarService = $sascarService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            print("Consultando os dados de Veiculos ..." . PHP_EOL);
            $data = $this->sascarService->getVeiculosFromWS();

            print("Importando para o banco ..." . PHP_EOL);
            $this->sascarService->saveVeiculosToDB($data);

            print("Done." . PHP_EOL);
        } catch (Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }
}
