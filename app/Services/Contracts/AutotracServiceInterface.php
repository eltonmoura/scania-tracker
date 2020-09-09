<?php

namespace App\Services\Contracts;

interface AutotracServiceInterface
{
    /**
     * Busca todas as contas ativas da companhia
     */
    public function getAccounts();

    /**
     * Busca dados expandidos de OBC por veículo
     */
    public function getExpandedAlerts($vehicleCode);

    /**
     * Busca mensagens de retorno
     */
    public function getReturnMessages($vehicleCode);

    /**
     * Busca mensagens de envio por veículo
     */
    public function getForwardMessages($vehicleCode);
}
