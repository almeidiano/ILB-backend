<?php

namespace App\Models;

use App\Controllers\ResponseController;
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
    private Collection $postCollection;

    function __construct() {
        $connection = new DatabaseConnector();
        $this->collection = $connection->getCollection("users");
    }

    /**
     * @throws Exception
     */

    private function getPostIdAsString($postId) {
        $postmodel = new PostModel();
        $postFound = $postmodel->getPost($postId);
        return (string) $postFound['_id'];
    }

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
    function getUserById($userId) {
        if($userId) try {
            return $this->collection->findOne(['_id' => new ObjectId($userId)]);
        }catch(Exception $e) {
            throw new Exception("Erro ao obter usuário. Erro técnico: ".$e->getMessage(), 500);
        }
    }

    function savePost($postId, $user_id) {
        $postIdAsString = $this->getPostIdAsString($postId);

        $usermodel = new UserModel();
        $userFound = $usermodel->getUserById($user_id);

        foreach($userFound['savedPosts'] as $savedPosts) {
            if($savedPosts['post_id'] == $postIdAsString) {
                throw new Exception("Você já salvou este post", 409);
            }
        }

        try {
            $this->collection->updateOne(
                ['_id' => $userFound['_id']],
                ['$push' => ['savedPosts' => ['post_id' => $postIdAsString]]]
            );

            return 'Post salvo com sucesso';
        }Catch(Exception $e) {
            throw new Exception("Falha ao salvar post", 500);
        }
    }

    /**
     * @throws Exception
     */
    function deleteSavedPost($postId, $user_id) {
        $postIdAsString = $this->getPostIdAsString($postId);
        $userFound = $this->getUserById($user_id);

        try {
            foreach($userFound['savedPosts'] as $savedPosts) {
                if($savedPosts['post_id'] == $postIdAsString) {
                    $this->collection->updateOne(
                        ['_id' => $userFound['_id']],
                        ['$pull' => ['savedPosts' => ['post_id' => $postIdAsString]]]
                    );

                    return 'Post guardado eliminado';
                }
            }
        }Catch(Exception $e) {
            throw new Exception("O post salvo já foi excluido", 409);
        }
    }
}
