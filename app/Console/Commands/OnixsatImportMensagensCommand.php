<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Exception;
use \ZipArchive;
use Carbon\Carbon;
use App\Models\MensagemCb;

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


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            print("Baixando os dados de MensagemCB ..." . PHP_EOL);
            $this->requestMensagemCB();

            // $this->tempFile = dirname(__DIR__) . '/../../storage/datafiles/RequestMensagemCB-20191003184444.zip';
            print("Extraindo os dados ..." . PHP_EOL);
            $data = $this->getContentFromZipFile($this->tempFile);

            print("Importando para o banco ..." . PHP_EOL);
            $this->importMensagemCbToDataBase($data);

            print("Novos registros: " . count($data) . PHP_EOL);
        } catch (Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }

    private function importMensagemCbToDataBase($data)
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
                if (in_array($keyField, ['dt','dtinc'])) {
                    $row[$keyField] = (new Carbon($valueField))->toDateTimeString();
                    continue;
                }

                // Coloca a lat e lon no formato decimal
                if (in_array($keyField, ['lat','lon'])) {
                    $row[$keyField] = str_replace(',', '.', $valueField);
                    continue;
                }

                // trata o booleano que vem como texto
                if (in_array($valueField, ['true', 'false'])) {
                    $row[$keyField] = ($valueField == 'true');
                    continue;
                }

                $row[$keyField] = $valueField;
            }

            MensagemCb::create($row);
        }
        return true;
    }

    private function requestMensagemCB()
    {
        $maxId = \DB::table('mensagem_cb')->max('mid');
        print("max(mId): $maxId \n");

        $onixsat = new OnixsatClient();
        $response = $onixsat->request('RequestMensagemCB', ['mId' =>  $maxId]);

        $dir = dirname(__DIR__) . '/../../storage/datafiles';

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $this->tempFile = $dir . "/RequestMensagemCB-" . date('YmdHis') . ".zip";

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
