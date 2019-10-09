<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use \Exception;
use \ZipArchive;
use Carbon\Carbon;
use App\Models\MensagemCb;
use App\Models\Veiculo;

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            // $this->tempFile = dirname(__DIR__) . '/../../storage/datafiles/RequestVeiculo-20191008203926.zip';
            print("Baixando os dados de Veiculos ..." . PHP_EOL);
            $this->requestVeiculo();

            print("Extraindo os dados ..." . PHP_EOL);
            $data = $this->getContentFromZipFile($this->tempFile, 'Veiculo');

            print("Importando para o banco ..." . PHP_EOL);
            $this->importVeiculosToDataBase($data);

            print("Done." . PHP_EOL);
        } catch (Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }

    private function importVeiculosToDataBase($data)
    {
        foreach ($data as $key => $value) {
            $row = [];
            foreach ($value as $keyField => $valueField) {
                // coloca a chave em minúsculo
                $keyField = strtolower($keyField);
                // Verifica se o campo tem um array como valor
                if (is_array($valueField)) {
                    $row[$keyField] = null;
                    continue;
                }

                // Coloca a data no formato do banco
                if (in_array($keyField, ['valespelhamento'])) {
                    $row[$keyField] = Carbon::createFromFormat('d/m/Y', $valueField)->toDateString();
                    continue;
                }

                // trata o booleano que vem como texto
                if (in_array($valueField, ['true', 'false'])) {
                    $row[$keyField] = ($valueField == 'true');
                    continue;
                }

                $row[$keyField] = $valueField;
            }

            // Atualiza ou cria um novo
            $veiculo = Veiculo::firstOrNew(['veiid' => $row['veiid']]);
            $veiculo->fill($row);
            $veiculo->save();
        }
        return true;
    }

    private function requestVeiculo()
    {
        $onixsat = new OnixsatClient();
        $response = $onixsat->request('RequestVeiculo');

        $dir = dirname(__DIR__) . '/../../storage/datafiles';

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $this->tempFile = $dir . "/RequestVeiculo-" . date('YmdHis') . ".zip";

        file_put_contents($this->tempFile, $response);

        return true;
    }

    private function getContentFromZipFile($fileName, $tag = "MensagemCB")
    {
        $zip = new ZipArchive();
        $zip->open($this->tempFile);

        $result = [];
        for ($i=0; $i < $zip->numFiles; $i++) {
            $xml = $zip->getFromIndex($i);
            $json = json_encode(simplexml_load_string($xml));
            $array = json_decode($json, true);

            $result = array_merge($result, $array[$tag]);
        }
        return $result;
    }
}
