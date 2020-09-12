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
class SascarPacotePosicaoImportCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "sascar:import-pacote-posicao";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "sascar Webservice - Import PacotePosicao (limite 1 min)";

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
            print("Consultando os dados de PacotePosicao ..." . PHP_EOL);
            $data = $this->sascarService->getPacotePosicaoFromWS();

            print("Importando para o banco ..." . PHP_EOL);
            $this->sascarService->savePacotePosicaoToDB($data);

            print("Done." . PHP_EOL);
        } catch (Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }
}
