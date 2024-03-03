<?php

namespace App\Services;

use App\Services\Contracts\TracServiceInterface;
use App\Services\Contracts\TracResponse;
use App\Models\MensagemCb;
use App\Models\Veiculo;
use Carbon\Carbon;
use \ZipArchive;
use \Exception;
use Illuminate\Support\Facades\Log;

class OnixsatService implements TracServiceInterface
{
    public function getLastPosition($numberPlate): ?TracResponse
    {
        $veiculo = Veiculo::where('placa', $numberPlate)->first();
        if (empty($veiculo)) {
            return null;
        }

        $mensagens = MensagemCb::where('veiid', $veiculo->veiid)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($mensagens)) {
            return null;
        }

        return new TracResponse(
            $numberPlate,
            $veiculo->ident,
            floatval($mensagens->lat),
            floatval($mensagens->lon),
            Carbon::createFromFormat('Y-m-d H:i:s', $mensagens->dt)->format('d/m/Y H:i:s')
        );
    }

    public function importVeiculos()
    {
        try {
            Log::info("OnixsatService:importVeiculos");
            $this->setTempFile('RequestVeiculo');
            $this->requestOnixsat('RequestVeiculo');
            $data = $this->getContentFromZipFile('Veiculo');
            $this->importVeiculosToDataBase($data);
        } catch (Exception $e) {
            Log::error('OnixsatService => ' . $e->getMessage());
        }
    }

    public function importMensagemCb()
    {
        try {
            Log::info("OnixsatService:importMensagemCb");
            $this->setTempFile('RequestMensagemCB');
            $maxId = $this->getMaxId();
            $this->requestOnixsat('RequestMensagemCB', ['mId' =>  $maxId]);
            $data = $this->getContentFromZipFile('MensagemCB');
            $this->importMensagemCbToDataBase($data);
        } catch (Exception $e) {
            Log::error('OnixsatService => ' . $e->getMessage());
        }
    }

    private function setTempFile($name)
    {
        $dir = dirname(__FILE__) . '/../../storage/datafiles';
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $this->tempFile = "$dir/$name-" . date('YmdHis') . ".zip";
    }

    private function getMaxId()
    {
        $maxId = \DB::table('mensagem_cb')->max('mid');
        $maxId = (is_int($maxId)) ? $maxId : 1;
        Log::info("OnixsatService:getMaxId $maxId");
        return $maxId;
    }

    private function importVeiculosToDataBase($data)
    {
        $placas = [];
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

            $placas[] = $row['placa'];

            // Atualiza ou cria um novo
            $veiculo = Veiculo::firstOrNew(['veiid' => $row['veiid']]);
            $veiculo->fill($row);
            $veiculo->save();
        }

        Log::info("OnixsatService:importVeiculosToDataBase atualizando " . implode(', ', $placas));
        return true;
    }

    private function importMensagemCbToDataBase($data)
    {
        $lastDate = null;
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

            $lastDate = $row['dt'];
            MensagemCb::create($row);
        }

        Log::info("OnixsatService:importMensagemCbToDataBase count: " . count($data) . " lastDate: " . $lastDate);
        return true;
    }

    private function requestOnixsat($endpoint, $params=[])
    {
        $onixsat = new OnixsatClient();
        $response = $onixsat->request($endpoint, $params);
        file_put_contents($this->tempFile, $response);
        return true;
    }

    private function getContentFromZipFile($tag)
    {
        $zip = new ZipArchive();
        $zip->open($this->tempFile);

        $result = [];
        for ($i=0; $i < $zip->numFiles; $i++) {
            $xml = $zip->getFromIndex($i);
            $json = json_encode(simplexml_load_string($xml));
            $array = json_decode($json, true);

            if (!isset($array[$tag])) {
               throw new Exception(json_encode($array));
            }
            $result = array_merge($result, $array[$tag]);
        }

        $zip->close();
        // remove file
        unlink($this->tempFile);

        if (isset($result['erro'])) {
            throw new Exception($result['erro'], $result['codigo']);
        }

        return $result;
    }
}
