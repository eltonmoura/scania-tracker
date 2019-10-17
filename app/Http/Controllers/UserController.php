<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

    public function create(Request $request)
    {
        return parent::create($this->encryptPassord($request));
    }

    public function update(Request $request, $id)
    {
        return parent::update($this->encryptPassord($request), $id);
    }

    private function encryptPassord(Request $request)
    {
        $password = $request->input('password');
        if (!empty($password)) {
            $request->merge(['password' => Hash::make($password)]);
        }
        return $request;
    }
}
