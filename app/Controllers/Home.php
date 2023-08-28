<?php

namespace App\Controllers;

//helper('inflector');

use App\Models\PostModel;
use CodeIgniter\HTTP\RequestInterface;

class Home extends BaseController
{

//    public static function result(): \CodeIgniter\HTTP\ResponseInterface
//    {
//        $status = [
//            'result' => 'ok',
//            'error' => false
//        ];
//
//        return $this->response->setJSON($status);
//    }
    static array $result = [
        'result' => 'ok',
        'error' => false
    ];
}
