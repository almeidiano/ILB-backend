<?php

namespace App\Models;

use App\Controllers\ResponseController;
use App\Libraries\DatabaseConnector;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use Exception;

class CommentModel
{
    private Collection $collection;
    private Collection $userCollection;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->collection = $database->posts;
        $this->userCollection = $database->users;
    }

//    function getUserCollection() {
//        $connection = new DatabaseConnector();
//        $database = $connection->getDatabase();
//        $this->userCollection = $database->users;
//    }

    public function createComment($postId, $json, $allImages) {
        if($postId) {
            if($json) {
                // Conteúdo do post obrigatório
                $content = $json['content'];

                if($content) {
                    try {
                        $postFound = $this->collection->findOne(['_id' => new ObjectId($postId)]);

                        // Recebendo usuário que comentou para preencher
                        // os campos do comentário que pertence ao usuário recebido.

                        $userFound = $this->userCollection->findOne(['_id' => new ObjectId($json['user_id'])]);

                        if($postFound) {

                            $newComment = [
                                '_id' => new \MongoDB\BSON\ObjectId(),
                                'content' => $content,
                                'post_id' => $postFound['_id'],
                                'user_id' => $userFound['_id'],
                                'userLiked' => false,
                                'userName' => $userFound['name'],
                                'userPhoto' => 'fotodousuario.png',
                                'images' => $allImages
                            ];

                            try{
                                $this->collection->updateOne(
                                    ['_id' => $postFound['_id']],
                                    ['$push' => ['comments' => $newComment]]
                                );

                                return ResponseController::index(200, 'Comentário adicionado com sucesso', false);
                            }Catch(Exception $e) {
                                return ResponseController::index(500, 'Não foi possivel adicionar comentário ao post com id '.$postId.' Erro técnico: '.$e->getMessage(), true);
                            }
                        }
                    }catch(Exception $e) {
                        return ResponseController::index(500, 'Não foi possível obter post para criar comentário com o id '.$postId.' Erro técnico: '.$e->getMessage(), true);
                    }
                }else {
                    return ResponseController::index(401, 'Comentário não especificado', true);
                }

            }else {
                return ResponseController::index(401, 'Corpo do comentário não especificado', true);
            }
        }
    }

    public function updateComment($commentId, $json) {
        $commentFound = $this->collection->findOne(['comments._id' => new ObjectId($commentId)], ['comments.$' => 1]);
        $commentFound = $commentFound->comments[0];

        if($commentFound) try{
            // Conteúdo Obrigatório
            $content = $json['content'];

            $this->collection->updateOne(
                ['comments._id' => $commentFound['_id']],
                ['$set' => ['comments.$.content' => $content]]
            );

            return ResponseController::index(200, 'Comentário editado com sucesso', false);
        }Catch(Exception $e) {
            return ResponseController::index(500, 'Ocorreu um erro ao editar comentário, erro técnico: '.$e->getMessage(), true);
        }
    }

    public function deleteComment($commentId) {
        $commentId = new ObjectId($commentId);

        if($commentId) try {
            $this->collection->updateOne(
                ['comments._id' => $commentId],
                ['$pull' => ['comments' => ['_id' => $commentId]]]
            );

            return ResponseController::index(200, 'Comentário deletado com sucesso', false);
        }catch(Exception $e) {
            return ResponseController::index(500, 'Ocorreu um erro ao deletar comentário, erro técnico: '.$e->getMessage(), true);
        }
    }
}
