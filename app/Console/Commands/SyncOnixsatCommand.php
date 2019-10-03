<?php
namespace App\Console\Commands;

use \Exception;
use Illuminate\Console\Command;
use GuzzleHttp\Client as HttpClient;

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
            $rep = $this->doRequest();
            print("ok\n");
        } catch (Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }

    private function doRequest()
    {
        $url = "http://webservice.onixsat.com.br";

        $xml = sprintf(
            "<RequestMensagemCB><login>%s</login><senha>%s</senha><mId>1</mId></RequestMensagemCB>",
            $this->login,
            $this->password
        );

        $options = [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ],
            'body' => $xml,
        ];

        $this->tempFile = sprintf(
            '%s/../../storage/datafiles/RequestMensagemCB-%s.%s',
            dirname(__DIR__),
            date('YmdHis'),
            'zip'
        );

        $client = new HttpClient();
        $res = $client->request('POST', $url, $options);

        file_put_contents($this->tempFile, $res->getBody());

        return true;
    }

    private function processZipFile()
    {
        //
    }
}
