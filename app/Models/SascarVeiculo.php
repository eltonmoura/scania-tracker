<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SascarVeiculo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sascar_veiculos';

    protected $primaryKey = 'idVeiculo';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idVeiculo',
        'idCliente',
        'placa',
        'portaBloqueio',
        'portaPanico',
        'satelital',
        'telemetria',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
