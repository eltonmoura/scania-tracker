<?php

namespace App\Services\Contracts;

interface TracServiceInterface
{
    /**
     * Busca mensagens de envio por veículo
     */
    public function getLastPosition($numberPlat);
}
