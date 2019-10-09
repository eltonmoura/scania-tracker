<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'veiculos';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'veiid', 'placa', 'vs', 'st1', 'st2', 'st3', 'tcmd', 'tmac', 'ecmd',
        'tp', 'ta', 'eqp', 'mot', 'prop', 'die', 'ie', 'loc', 'ident',
        'vmanut', 'valespelhamento', 'propcancelamento',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
