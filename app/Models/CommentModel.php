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
        $database = new DatabaseConnector();
        $this->collection = $database->getCollection("posts");
        $this->userCollection = $database->getCollection("users");
//        $connection = new DatabaseConnector();
//        $database = $connection->getDatabase();
//        $this->collection = $database->posts;
//        $this->userCollection = $database->users;
    }

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

                            $now = new \MongoDB\BSON\UTCDateTime();

                            $newComment = [
                                '_id' => new \MongoDB\BSON\ObjectId(),
                                'date' => $now,
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

                                return 'Comentário adicionado com sucesso';
                            }Catch(Exception $e) {
                                throw new Exception("Não foi possivel adicionar comentário ao post com id '.$postId.' Erro técnico: ".$e->getMessage(), 500);                            }
                        }
                    }catch(Exception $e) {
                        throw new Exception("Não foi possível obter post para criar comentário com o id '.$postId.' Erro técnico: ".$e->getMessage(), 500);
                    }
                }else {
                    throw new Exception('Comentário não especificado', 401);
                }

            }else {
                throw new Exception('Corpo do comentário não especificado', 401);
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

            return 'Comentário editado com sucesso';
        }Catch(Exception $e) {
            throw new Exception("Ocorreu um erro ao editar comentário. Erro técnico: ".$e->getMessage(), 500);
        }
    }

    public function deleteComment($commentId) {
        $commentId = new ObjectId($commentId);

        if($commentId) try {
            $this->collection->updateOne(
                ['comments._id' => $commentId],
                ['$pull' => ['comments' => ['_id' => $commentId]]]
            );

            return 'Comentário deletado com sucesso';
        }catch(Exception $e) {
            throw new Exception("Ocorreu um erro ao deletar comentário. Erro técnico: ".$e->getMessage(), 500);
        }
    }
}
