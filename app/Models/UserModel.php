<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;
use CodeIgniter\Model;
use Config\Services;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use Exception;
use App\Controllers\Home;

class UserModel
{
    private Collection $collection;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->collection = $database->users;
    }

    /**
     * @throws Exception
     */

    function login($email, $password): array
    {
        if($email && $password) {
            $jwtKey = bin2hex(random_bytes(32));
            $jwtPayload = $this->collection->findOne(['email' => $email, 'password' => $password]);
            $jwt = Services::jwt()->encode([$jwtPayload], $jwtKey, 'HS256');

            if($jwtPayload) {
                return [
                    'user' => $jwtPayload,
                    'accessToken' => $jwt
                ];
            }else {
                return [
                    'error' => true
                ];
            }
        }
    }
//    function getUserById($userId) {
//        if($userId) try {
//            return $this->collection->findOne(['_id' => new ObjectId($userId)]);
//        }catch(Exception $e) {
//            exit('Não foi possível obter user com id: '.$userId);
//        }
//    }

    function getUserById($userId) {
        if($userId) try {
            return $this->collection->findOne(['_id' => new ObjectId($userId)]);
        }catch(Exception $e) {
            exit('Erro ao obter todos os posts do usuário. Erro: '.$e->getMessage());
        }
    }
}
