<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MensagemCb extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mensagem_cb';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'mid', 'veiid', 'dt', 'lat', 'lon', 'mun', 'uf', 'rod',
        'rua', 'vel', 'evt4', 'evt13', 'ori', 'tpmsg', 'dtinc', 'evtg',
        'odm', 'bat',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
