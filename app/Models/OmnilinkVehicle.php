<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmnilinkVehicle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'omnilink_vehicles';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['idTerminal', 'terminal', 'placa'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
