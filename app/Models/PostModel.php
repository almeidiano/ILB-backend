<?php
namespace App\Models;

use App\Controllers\ResponseController;
use App\Libraries\DatabaseConnector;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use Exception;

class PostModel {
    private Collection $collection;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->collection = $database->posts;
    }

    /**
     * @throws \Exception
     */

    /**
     * Retorna um post especifico via ID
     *
     * @param $id
     * @return string
     *
     */

    function getPost($id): Object|string
    {
        if($id) try {
            return $this->collection->findOne(['_id' => new ObjectId($id)]);
        }catch(Exception $e) {
            exit('Não foi possível obter post especifico com id: '.$id);
        }
    }

    /**
     * Retorna todos os posts
     *
     * @return array
     * @return object
     */

    function getAllPosts(): array
    {
        try {
            $data = [];
            $cursor = $this->collection->find();

            foreach ($cursor as $document) {
                $data[] = $document;
            }

            return $data;
        } catch(Exception $ex) {
            exit('Erro ao obter todos os posts');
        }
    }

    function createPost($json) {
        // Obrigatório
        $title = $json['title'];
        $content = $json['content'];

        // Opcional
        $districtName = $json['districtName'];
        $restricted = $json['restricted'];

        if($title && $content) {
            try {
                $this->collection->insertOne([
                    'title' => $title,
                    'content' => $content,
                    'districtName' => $districtName,
                    'restricted' => $restricted
                ]);

                return ResponseController::index(200, 'Post adicionado com sucesso', false);
            }catch (Exception $e) {
                return ResponseController::index(500, 'Ocorreu um erro ao adicionar post, erro técnico: '.$e->getMessage(), true);
            }
        }else {
            return ResponseController::index(401, 'Titulo e conteúdo não especificados', true);
        }
    }

    function getPostsByUserId($userId): array {
        if($userId) try {
            $data = [];
            $postsByUserId = $this->collection->find(['user_id' => $userId]);

            foreach ($postsByUserId as $document) {
                $data[] = $document;
            }

            return $data;
        }catch(Exception $e) {
            exit('Não foi possível obter posts do usuário com id: '.$userId);
        }
    }

    function deletePost($postId) {
        if($postId) try {
            $this->collection->deleteOne(['_id' => new ObjectId($postId)]);
            return ResponseController::index(200, 'Post deletado com sucesso', false);
        }catch(Exception $e) {
            return ResponseController::index(500, 'Ocorreu um erro ao deletar post, erro técnico: '.$e->getMessage(), true);
        }
    }

    function updatePost($postId, $json) {
        if($postId) {
            try {
                $postFound = $this->collection->findOne(['_id' => new ObjectId($postId)]);
            }catch(Exception $e) {
                return ResponseController::index(500, 'Não foi possível obter post para atualização com o id '.$postId.' Erro técnico: '.$e->getMessage(), true);
            }

            if($postFound) {
                // Obrigatório
                $title = $json['title'];
                $content = $json['content'];

                if($title && $content) {
                    try {
                        $this->collection->updateOne([
                            '_id' => $postFound['_id'],
                            'title' => $postFound['title'],
                            'content' => $postFound['content']
                        ], ['$set' => ['title' => $title, 'content' => $content]]
                        );

                        return ResponseController::index(200, 'Post atualizado com sucesso', false);
                    }catch(Exception $e) {
                        return ResponseController::index(500, 'Não foi possível atualizar post com o id '.$postId.' Erro técnico: '.$e->getMessage(), true);
                    }
                }else {
                    return ResponseController::index(401, 'Titulo ou conteúdo não especificados', true);
                }
            }
        }
    }

}