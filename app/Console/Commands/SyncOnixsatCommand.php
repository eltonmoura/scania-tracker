<?php
namespace App\Console\Commands;

use \Exception;
use Illuminate\Console\Command;
use GuzzleHttp\Client as HttpClient;
use \ZipArchive;

/**
 * Class SyncOnixsatCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class SyncOnixsatCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "onixsat:sync";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Sync Onixsat Webservice";

    private $uri = 'http://webservice.onixsat.com.br';
    private $login = '01161180000156';
    private $password = '594444';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->requestMensagemCB();
            // $this->tempFile = dirname(__DIR__) . '/../../storage/datafiles/RequestMensagemCB-20191003184444.zip';

            $content = $this->getContentFromZipFile($this->tempFile);

            print_r($content);

            print("ok\n");
        } catch (Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }

    private function requestMensagemCB()
    {
        $xml = sprintf(
            "<RequestMensagemCB>
                <login>%s</login>
                <senha>%s</senha>
                <mId>1</mId>
            </RequestMensagemCB>",
            $this->login,
            $this->password
        );

        $options = [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ],
            'body' => $xml,
        ];

        $dir = dirname(__DIR__) . '/../../storage/datafiles';

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $this->tempFile = $dir . "/RequestMensagemCB-" . date('YmdHis') . ".zip";

        $client = new HttpClient();
        $res = $client->request('POST', $this->uri, $options);

        file_put_contents($this->tempFile, $res->getBody());

        return true;
    }

    private function getContentFromZipFile($fileName)
    {
        $zip = new ZipArchive();
        $zip->open($this->tempFile);

        $result = [];
        for ($i=0; $i<$zip->numFiles; $i++) {
            $xml = $zip->getFromIndex($i);
            $json = json_encode(simplexml_load_string($xml));
            $array = json_decode($json, true);

            $result = array_merge($result, $array['MensagemCB']);
        }
        return $result;
    }
}
