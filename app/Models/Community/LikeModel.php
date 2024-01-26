<?php

namespace App\Models\Community;

use App\Libraries\DatabaseConnector;
use App\Models\Community\PostModel;
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
        $database = new DatabaseConnector('ILB_comunidade');
        $this->collection = $database->getCollection("Likes");
        $this->usersCollection = $database->getCollection("Users");
        $this->postsCollection = $database->getCollection("Posts");
    }

    function getLikedComments($postId, $userId) {
        $postmodel = new PostModel();

        $pipeline = [
            [
                '$match' => [
                    'user_id' => new ObjectId($userId),
                    'action_field' => 'comment'
                ]
            ]
        ];

        $allLikedCommentsWithUserId = $this->collection->aggregate($pipeline)->toArray();

        if($postId) {
            $postFound = $postmodel->getPost($postId);
    
            $allLikedComments = [];
            $allLikedCommentsFromAllPosts = [];
            $result = [];

            foreach ($allLikedCommentsWithUserId as $LC) {
                forEach($postFound->comments as $postComment) {
                    if($postComment['_id'] == $LC['comment_id']) {
                        $allLikedComments[] = $postComment;
                        break;
                    }
                }
            }

            return $allLikedComments;
        }else {
            $posts = $postmodel->getAllPosts();

            forEach($posts as $post) {
                foreach ($allLikedCommentsWithUserId as $LC) {
                    forEach($post->comments as $commentsFromPosts) {
                        if($LC['comment_id'] == $commentsFromPosts['_id']) {
                            $allLikedCommentsFromAllPosts[] = $commentsFromPosts;
                        }
                    }
                }
            }

            return $allLikedCommentsFromAllPosts;
        }
    }

    function getPostsLikedFromUser($userId, $postId, $method) {
        try {
            // Checa se o id do usuário corresponde ao resultado encontrado
            $objectIdOfPostsIdFromLikesData = [];

        //    $likesWithUserId = $this->collection->find(['user_id' => new ObjectId($userId)]);

            $pipeline = [
                [
                    '$match' => [
                        'user_id' => new ObjectId($userId),
                        'action_field' => 'post'
                    ]
                ]
            ];

            $likesWithUserId = $this->collection->aggregate($pipeline)->toArray();

            if($likesWithUserId) {
                foreach ($likesWithUserId as $document) {
                    $objectIdOfPostsIdFromLikesData[] = new ObjectId($document['post_id']);
                }

                // Pega o id do post gostado para validação
                try {
                    $posts = $this->postsCollection->find(['_id' => ['$in' => $objectIdOfPostsIdFromLikesData]]);

                    if($method == 'all') {
                        foreach ($posts as $data) {
                            $data['user']['liked'] = true;
                            $result[] = $data;
                        }

                        return $result;
                    }else if($method == 'one') {
                        if(!empty($objectIdOfPostsIdFromLikesData)) {
                            forEach($posts as $post) {
                                if($post['_id'] == $postId) {
                                    return $post;
                                }
                            }
                        }else return [];
                    }else return [];
                } catch (\Throwable $th) {
                    return [];
                }

            }else return [];
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

        $pipeline = [
            [
                '$match' => [
                    'user_id' => new ObjectId($userFound['_id']),
                    'post_id' => $postId,
                    'action_field' => 'post'
                ]
            ]
        ];

        $likedPost = $this->collection->aggregate($pipeline)->toArray();

        if($likedPost) {
            exit("Você já gostou deste post");
        }else {
            $this->collection->insertOne([
                'user_id' => $userFound['_id'],
                'post_id' => $postIdAsString,
                'action_field' => 'post'
            ]);

            $this->changePostLikeNumber($postId, 'increment');
            return 'Post Gostado';
        }
    }

    private function getComment($id) {
        $postFound = $this->postsCollection->findOne(['comments._id' => new ObjectId($id)], ['comments.$' => 1]);

        forEach($postFound->comments as $comment) {
            if ($comment['_id'] == $id) {
                return $comment;
            }
        }
    }

    function likeComment($commentId, $user_id) {
        try {
            $commentFound = $this->getComment($commentId);

            $usermodel = new UserModel();
            $userFound = $usermodel->getUserById($user_id);

            $pipeline = [
                [
                    '$match' => [
                        'user_id' => new ObjectId($userFound['_id']),
                        'comment_id' => new ObjectId($commentId),
                        'action_field' => 'comment'
                    ]
                ]
            ];
    
            $likedComment = $this->collection->aggregate($pipeline)->toArray();
        
            if($likedComment) {
                exit("Você já gostou deste comentário");
            }else {
                try {
                    $this->collection->insertOne([
                        'user_id' => $userFound['_id'],
                        'comment_id' => $commentFound['_id'],
                        'action_field' => 'comment'
                    ]);

                    $this->changeCommentLikeNumber($commentFound['post_id'], $commentId, 'increment');
                    return 'Comentário gostado.';
                }Catch(Exception $e) {
                    throw new Exception("Falha ao gostar do comentário.", 500);
                }
            }
        }Catch(\ErrorException $e) {
            throw new \ErrorException("Falha ao gostar do comentário com id: ".$commentId.". Erro técnico: ".$e->getMessage(), 500);
        }
    }

    function deleteLikedPost($postId, $user_id) {
        $usermodel = new UserModel();
        $userFound = $usermodel->getUserById($user_id);

        try {
            // $this->collection->deleteOne(['user_id' => $userFound['_id']]);
            $pipeline = [
                [
                    '$match' => [
                        'user_id' => new ObjectId($userFound['_id']),
                        'post_id' => $postId,
                        'action_field' => 'post'
                    ]
                ]
            ];
    
            $likedPost = $this->collection->aggregate($pipeline)->toArray();

            if($likedPost) {
                $this->collection->deleteOne(['user_id' => $userFound['_id'], 'post_id' => $postId]);
                $this->changePostLikeNumber($postId, 'decrement');
                return 'Gosto retirado';
            }else return [];
        }Catch(Exception $e) {
            throw new Exception("Falha ao retirar like do post", 500);
        }
    }

    function deleteLikedComment($commentId, $user_id) {
        $usermodel = new UserModel();
        $postmodel = new PostModel();
        $userFound = $usermodel->getUserById($user_id);

        try {
            $this->collection->deleteOne(['comment_id' => new ObjectId($commentId)]);
            $commentFound = $this->getComment($commentId);
            $this->changeCommentLikeNumber($commentFound['post_id'], $commentId, 'decrement');
            return 'Gosto retirado';
        }Catch(Exception $e) {
            throw new Exception("Falha ao retirar like do comentário", 500);
        }
    }

    private function changeCommentLikeNumber($postId, $commentId, $method) {
        try {
            return $this->postsCollection->updateOne(
                ['_id' => new ObjectId($postId), 'comments._id' => new ObjectId($commentId)],
                ['$inc' => ['comments.$.likesCount' => ($method == 'increment') ? 1 : (($method == 'decrement') ? -1 : exit('Método de like no comentário não especificado')) ]]
            );
        }catch(Exception $e) {
            throw new Exception("Falha ao incrementar like do post", 500);
        }
    }

    private function changePostLikeNumber($postId, $method) {
        try {
            return $this->postsCollection->updateOne(
                ['_id' => new ObjectId($postId)],
                ['$inc' => ['likesCount' => ($method == 'increment') ? 1 : (($method == 'decrement') ? -1 : exit('Método de like no post não especificado')) ]]
            );
        }catch(Exception $e) {
            throw new Exception("Falha ao incrementar like do post", 500);
        }
    }

    // private function decrementPostLikeNumber($postId) {
    //     try {
    //         return $this->postsCollection->updateOne(
    //             ['_id' => new ObjectId($postId)],
    //             ['$inc' => ['likesCount' => -1]]
    //         );
    //     }catch(Exception $e) {
    //         throw new Exception("Falha ao decrementar like do post", 500);
    //     }
    // }

    // private function incrementPostLikeNumber($postId) {
    //     try {
    //         return $this->postsCollection->updateOne(
    //             ['_id' => new ObjectId($postId)],
    //             ['$inc' => ['likesCount' => 1]],
    //             ['upsert' => true]
    //         );
    //     }catch(Exception $e) {
    //         throw new Exception("Falha ao incrementar like do post", 500);
    //     }
    // }
}
