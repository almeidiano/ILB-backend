<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ResponseController extends BaseController
{

    public static function index(int $http_code, string $message, bool $error): array
    {
        http_response_code($http_code);

        return [
            'http_code' => $http_code,
            'message' => $message,
            'error' => $error
        ];
    }
}
