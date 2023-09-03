<?php
namespace App\Models;

use App\Controllers\ResponseController;
use App\Libraries\DatabaseConnector;
use InvalidArgumentException;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;
use Exception;

class PostModel
{
    private Collection $collection;
    private Collection $usersCollection;

    /**
     * @throws Exception
     */
    function __construct()
    {
        $database = new DatabaseConnector();
        $this->collection = $database->getCollection("posts");
        $this->usersCollection = $database->getCollection("users");
    }

    private function getPostIdAsString($postId) {
        $postmodel = new PostModel();
        $postFound = $postmodel->getPost($postId);
        return (string) $postFound['_id'];
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

    function getPost($id)
    {
        if ($id) try {
            return $this->collection->findOne(['_id' => new ObjectId($id)]);
        } catch (Exception $e) {
            throw new Exception("O post especificado com id: ".$id." não existe", 500);
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
        } catch (Exception $ex) {
            throw new Exception("Erro ao obter todos os posts", 500);
        }
    }

    function createPost($json)
    {
        // Obrigatório
        $title = $json['title'];
        $content = $json['content'];

        // Opcional
        $districtName = $json['districtName'];
        $restricted = $json['restricted'];

        if ($title && $content) {
            try {
                $now = new UTCDateTime();

                $this->collection->insertOne([
                    'date' => $now,
                    'title' => $title,
                    'content' => $content,
                    'districtName' => $districtName,
                    'restricted' => $restricted
                ]);

                return 'Post adicionado com sucesso';
            } catch (Exception $e) {
                throw new Exception("Ocorreu um erro ao adicionar post. Erro técnico: " . $e->getMessage(), 500);
            }
        } else {
            throw new Exception("Titulo e conteúdo não especificados", 401);
        }
    }

    function getPostsByUserId($userId): array
    {
        if ($userId) try {
            $data = [];
            $postsByUserId = $this->collection->find(['user_id' => $userId]);

            foreach ($postsByUserId as $document) {
                $data[] = $document;
            }

            return $data;
        } catch (Exception $e) {
            throw new Exception('Não foi possível obter posts do usuário com id: '.$userId, 500);
        }
    }

    function deletePost($postId)
    {
        if ($postId) try {
            $this->collection->deleteOne(['_id' => new ObjectId($postId)]);
            return 'Post deletado com sucesso';
        } catch (Exception $e) {
            throw new Exception("Ocorreu um erro ao deletar post, erro técnico: " . $e->getMessage(), 500);
        }
    }

    function updatePost($postId, $json)
    {
        if ($postId) {
            try {
                $postFound = $this->collection->findOne(['_id' => new ObjectId($postId)]);
            } catch (Exception $e) {
                throw new Exception("Não foi possível obter post para atualização com o id ' .$postId. ' Erro técnico: " . $e->getMessage(), 500);
            }

            if ($postFound) {
                // Obrigatório
                $title = $json['title'];
                $content = $json['content'];

                if ($title && $content) {
                    try {
                        $this->collection->updateOne([
                            '_id' => $postFound['_id'],
                            'title' => $postFound['title'],
                            'content' => $postFound['content']
                        ], ['$set' => ['title' => $title, 'content' => $content]]
                        );

                        return 'Post atualizado com sucesso';
                    } catch (Exception $e) {
                        throw new Exception("Não foi possível atualizar post com o id ' . $postId . ' Erro técnico: " . $e->getMessage(), 500);
                    }
                } else {
                    throw new Exception("Titulo ou conteúdo não especificados", 401);
                }
            }
        }
    }

    function getPostsCommentedByUser($userId)
    {
        if ($userId) try {

            $pipeline = [
                [
                    '$match' => [
                        'comments.user_id' => new ObjectId($userId)
                    ]
                ]
            ];

            return $this->collection->aggregate($pipeline)->toArray();
        } catch (Exception $e) {
            throw new Exception("Não foi possível retornar os posts via id do comentário selecionado. Erro técnico: " . $e->getMessage(), 500);
        }
    }

    function getPostsSavedByUser($userId) {
        try {
            try {
                $userFound = $this->usersCollection->findOne(['_id' => new ObjectId($userId)]);
                $savedPostsByUserFound = $userFound['savedPosts'];

                $savedPostsIds = [];

                forEach($savedPostsByUserFound as $savedPosts) {
                    $savedPostsIds = [
                        new ObjectId($savedPosts['post_id'])
                    ];
                }
            }Catch(\ErrorException $e) {
                throw new \ErrorException('O usuário não possui posts salvos. Erro técnico: '.$e->getMessage(), 500);
            }

            // Procurando por ids dos posts para verificação

            try {
                // Uso do operador $in para corresponder documentos com _id no array
                $matchedPosts = $this->collection->find([
                    '_id' => ['$in' => $savedPostsIds]
                ]);

                foreach ($matchedPosts as $document) {
                    return $document;
                }
            } catch (Exception $ex) {
                throw new Exception("Erro ao obter todos os posts que são correspondentes com os posts salvados do user", 500);
            }
        }Catch(\InvalidArgumentException $e) {
            throw new InvalidArgumentException('Parametro especificado do tipo não esperado. Erro técnico: '.$e->getMessage(), 500);
        }
    }

    function getPostsLikedByUser($userId) {
        exit('ok');
//        $userFound = $this->usersCollection->findOne(['_id' => new ObjectId($userId)]);
//        $likedPostsByUserFound = $userFound['likedPosts'];
//
//        $likedPostsIds = [];
//
//        forEach($likedPostsByUserFound as $likedPosts) {
//            $likedPostsIds = [
//                new ObjectId($likedPosts['post_id'])
//            ];
//        }
//
//        // Procurando por ids dos posts para verificação
//
//        try {
//            // Uso do operador $in para corresponder documentos com _id no array
//            $matchedPosts = $this->collection->find([
//                '_id' => ['$in' => $likedPostsIds]
//            ]);
//
//            foreach ($matchedPosts as $document) {
//                return $document;
//            }
//        } catch (Exception $ex) {
//            throw new Exception("Erro ao obter todos os posts que são correspondentes com os posts salvados do user", 500);
//        }
    }
}