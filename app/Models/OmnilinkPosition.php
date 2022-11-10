<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmnilinkPosition extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'omnilink_positions';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'numeroSequencia',
        'idTerminal',
        'dataHoraEvento',
        'latitude',
        'longitude',
        'localizacao',
        'idSeqVeiculo',
        'cidade',
        'uf',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
