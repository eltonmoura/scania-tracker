<?php

namespace App\Services;

use App\Services\Contracts\TracServiceInterface;
use App\Models\MensagemCb;
use App\Models\Veiculo;
use Carbon\Carbon;
use \ZipArchive;
use \Exception;

class OnixsatService implements TracServiceInterface
{
    public function getLastPosition($numberPlate)
    {
        $veiculo = Veiculo::where('placa', $numberPlate)->first();
        if (empty($veiculo)) {
            return [];
        }

        $mensagens = MensagemCb::where('veiid', $veiculo->veiid)
            ->orderBy('dt', 'desc')
            ->first();

        if (empty($mensagens)) {
            return [];
        }

        return [
            'placa' => $numberPlate,
            'modelo' => $veiculo->ident,
            'latitude' => floatval($mensagens->lat),
            'longitude' => floatval($mensagens->lon),
            'data_hora' => $mensagens->dt,
        ];
    }

    public function importVeiculos()
    {
        $this->setTempFile('RequestVeiculo');
        $this->requestOnixsat('RequestVeiculo');
        $data = $this->getContentFromZipFile('Veiculo');
        $this->importVeiculosToDataBase($data);
        print("Done." . PHP_EOL);
    }

    public function importMensagemCb()
    {
        $this->setTempFile('RequestMensagemCB');
        $maxId = $this->getMaxId();
        $this->requestOnixsat('RequestMensagemCB', ['mId' =>  $maxId]);
        $data = $this->getContentFromZipFile('MensagemCB');
        $this->importMensagemCbToDataBase($data);
        print("Done." . PHP_EOL);
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
        print("max(mId): $maxId \n");

        return $maxId;
    }

    private function importVeiculosToDataBase($data)
    {
        print("Importando para o banco ..." . PHP_EOL);

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

    private function importMensagemCbToDataBase($data)
    {
        print("Importando para o banco ..." . PHP_EOL);

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

    private function requestOnixsat($endpoint, $params=[])
    {
        print("Baixando os dados de $endpoint ..." . PHP_EOL);

        $onixsat = new OnixsatClient();
        $response = $onixsat->request($endpoint, $params);
        file_put_contents($this->tempFile, $response);
        return true;
    }

    private function getContentFromZipFile($tag)
    {
        print("Extraindo os dados ..." . PHP_EOL);

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
        return $result;
    }
}
