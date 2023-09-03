<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;
use App\Models\PostModel;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use Exception;

class LikeModel
{
    private Collection $collection;
    private Collection $usersCollection;
    private Collection $postsCollection;

    function __construct()
    {
        $database = new DatabaseConnector();
        $this->collection = $database->getCollection("likes");
        $this->usersCollection = $database->getCollection("users");
        $this->postsCollection = $database->getCollection("posts");
    }

    function getPostsLikedFromUser($userId) {
        try {
            // Checa se o id do usuário corresponde ao resultado encontrado
            $objectIdOfPostsIdFromLikesData = [];

            $likesWithUserId = $this->collection->find(['user_id' => new ObjectId($userId)]);

            foreach ($likesWithUserId as $document) {
                if($userId == (string) $document['user_id']) {
                    $objectIdOfPostsIdFromLikesData = [
                        new ObjectId($document['post_id'])
                    ];
                }else exit('mai!');
            }

            // Pega o id id do post gostado para validação
            $posts = $this->postsCollection->find(['_id' => ['$in' => $objectIdOfPostsIdFromLikesData]]);

            foreach ($posts as $data) {
                return $data;
            }
        }Catch(\Exception $e) {
            throw new \Exception("Não foi possível obter os posts gostados pelo usuário com id: ".$userId.". Erro técnico: ".$e->getMessage(), 500);
        }
    }

    private function getPostIdAsString($postId) {
        $postmodel = new PostModel();
        $postFound = $postmodel->getPost($postId);
        return (string) $postFound['_id'];
    }

    function likePost($postId, $user_id) {
        $postIdAsString = $this->getPostIdAsString($postId);

        $usermodel = new UserModel();
        $userFound = $usermodel->getUserById($user_id);

        $likedPosts = $this->collection->find(['user_id' => $userFound['_id']]);

        foreach($likedPosts as $LP) {
            if($LP['post_id'] == $postIdAsString) {
                throw new Exception("Você já gostou deste post", 409);
            }
        }

        try {
            $this->collection->insertOne([
                'user_id' => $userFound['_id'],
                'post_id' => $postIdAsString,
                'action_field' => 'post'
            ]);

            $this->incrementPostLikeNumber($postId);
            return 'Post Gostado';
        }Catch(Exception $e) {
            throw new Exception("Falha ao gostar do post", 500);
        }
    }

    function likeComment($commentId, $user_id) {
        try {
            $commentFound = $this->postsCollection->findOne(['comments._id' => new ObjectId($commentId)], ['comments.$' => 1]);
            $commentFound = $commentFound->comments[0];

            $usermodel = new UserModel();
            $userFound = $usermodel->getUserById($user_id);

            // Aqui terá validação em loop...
            $likedComments = $this->collection->find(['user_id' => new ObjectId($userFound['_id'])]);

            foreach($likedComments as $LC) {
                if($LC['comment_id'] == $commentFound['_id']) {
                    throw new Exception("Você já gostou deste comentário", 409);
                }
            }

            try {
                $this->collection->insertOne([
                    'user_id' => $userFound['_id'],
                    'comment_id' => $commentFound['_id'],
                    'action_field' => 'comment'
                ]);

//                $this->incrementPostLikeNumber($postId);
                return 'Comentário gostado.';
            }Catch(Exception $e) {
                throw new Exception("Falha ao gostar do comentário.", 500);
            }
        }Catch(\ErrorException $e) {
            throw new \ErrorException("Falha ao gostar do comentário com id: ".$commentId.". Erro técnico: ".$e->getMessage(), 500);
        }
    }

    function deleteLikedPost($postId, $user_id) {
        $usermodel = new UserModel();
        $userFound = $usermodel->getUserById($user_id);

        try {
            $this->collection->deleteOne(['user_id' => $userFound['_id']]);
            $this->decrementPostLikeNumber($postId);
            return 'Gosto retirado';
        }Catch(Exception $e) {
            throw new Exception("Falha ao gostar do post", 500);
        }
    }

    private function decrementPostLikeNumber($postId) {
        try {
            return $this->postsCollection->updateOne(
                ['_id' => new ObjectId($postId)],
                ['$inc' => ['nrLikes' => -1]],
                ['upsert' => true]
            );
        }catch(Exception $e) {
            throw new Exception("Falha ao decrementar like do post", 500);
        }
    }

    private function incrementPostLikeNumber($postId) {
        try {
            return $this->postsCollection->updateOne(
                ['_id' => new ObjectId($postId)],
                ['$inc' => ['nrLikes' => 1]],
                ['upsert' => true]
            );
        }catch(Exception $e) {
            throw new Exception("Falha ao incrementar like do post", 500);
        }
    }
}
