<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SascarPacotePosicao extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sascar_pacote_posicao';
    
    protected $primaryKey = 'idPacote';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idPacote',
        'idVeiculo',
        'integradoraId',
        'ignicao',
        'horimetro',
        'odometro',
        'rpm',
        'velocidade',
        'latitude',
        'longitude',
        'cidade',
        'rua',
        'uf',
        'dataPacote',
        'dataPosicao',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
