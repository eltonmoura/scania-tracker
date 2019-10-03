<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    // HTTP Status
    const CODE_INTERNAL_ERROR = 500;
    const CODE_BAD_REQUEST = 400;
    const CODE_UNAUTHORIZED = 401;
    const CODE_NOT_FOUND = 404;
    const CODE_SUCCESS = 200;
    const CODE_CREATED = 201;
}
