<?php

namespace App\Services\Contracts;

class TracResponse
{
    private $plate;
    private $model;
    private $latitude;
    private $longitude;
    private $time;

    public function __construct($plate, $model, $latitude, $longitude, $time) {
        $this->plate = $plate;
        $this->model = $model;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->time = $time;
    }

    public function toArray() {
        return [
            'placa' => $this->plate,
            'modelo' => $this->model,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'data_hora' => $this->time,
        ];
    }
}
