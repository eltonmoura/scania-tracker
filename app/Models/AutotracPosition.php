<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutotracPosition extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'autotrac_positions';
    
    protected $primaryKey = 'id';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'AccountNumber',
        'VehicleName',
        'VehicleAddress',
        'VehicleIgnition',
        'Velocity',
        'Odometer',
        'Hourmeter',
        'Latitude',
        'Longitude',
        'Landmark',
        'UF',
        'CountryDescription',
        'PositionTime',
        'Direction',
        'DirectionGPS',
        'Distance',
        'ReceivedTime',
        'TransmissionChannel',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
