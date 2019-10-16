<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * The Model class associated with this Controller.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Get relationships when return objects
     *
     * @var array
     */
    protected $withRelationships = [
    ];

    /**
     * Fields where the search will be made
     *
     * @var array
     */
    protected $searchFields = [
        'email',
    ];
}
