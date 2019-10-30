<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Exception;
use Carbon\Carbon;
use App\Models\MensagemCb;

/**
 * Class SyncOnixsatCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class PurgeDataBaseCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "purge:database";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Apaga reegistros antigos do Banco de Dados";

    private $limitDays;

    public function __construct()
    {
        $this->limitDays = env('PURGE_DATABASE_DAYS');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $date = Carbon::now()->subDays($this->limitDays)->toDateTimeString();
            print("Apagando osregistros de MensagemCB anteriores a '$date'" . PHP_EOL);

            $mensagens = MensagemCb::where('dt', '<', $date);
            $count = $mensagens->count();
            $mensagens->delete();

            print("Registros apagados: $count" . PHP_EOL);
        } catch (Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }
}
