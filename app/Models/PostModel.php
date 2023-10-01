<?php
namespace App\Models;

use App\Controllers\ResponseController;
use App\Libraries\DatabaseConnector;
use InvalidArgumentException;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;
use Exception;
use PhpParser\Node\Expr\Cast\Object_;

class PostModel
{
    private Collection $collection;
    private Collection $usersCollection;
    private Collection $themesCollection;

    /**
     * @throws Exception
     */
    function __construct()
    {
        $database = new DatabaseConnector();
        $this->collection = $database->getCollection("posts");
        $this->usersCollection = $database->getCollection("Users");
        $this->themesCollection = $database->getCollection("themes");
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
        try {
            return $this->collection->findOne(['_id' => new ObjectId($id)]);
        } catch (Exception $e) {
            throw new Exception("O post especificado com id: ".$id." não existe", 500);
        }
    }

    function getInteractedPostFromUserId($postId, $userId) {
        try {
            $likemodel = new LikeModel();
            $post = $this->getPost($postId);
            $postSavedByUser = $this->getPostsSavedByUser($userId, $postId, 'one');
            $postLikedByUser = $likemodel->getPostsLikedFromUser($userId, $postId, 'one');

            $postSavedByUser && $post['user']['saved'] = true;
            $postLikedByUser && $post['user']['liked'] = true;

            if(isset($post->comments)) {
                $commentsId = [];
                $comments = $post->comments;
                forEach($comments as $comment) $commentsId[] = $comment['_id'];
                $likedComments = $likemodel->getLikedComments($postId, $userId);

                if($likedComments) {
                    forEach($comments as $c) {
                        forEach($likedComments as $lc) {
                            $lc['_id'] == $c['_id'] && $c['userLiked'] = true;
                        }
                    }
                }
            }else return $post;

            return $post;
        } catch (Exception $e) {
            throw new Exception("Ocorreu um erro. Erro técnico: ".$e->getMessage(), 500);
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

    function getAllRecommendedPosts(): array
    {
        try {
            $posts = [];
            $cursor = $this->collection->aggregate([
                [
                    '$project' => [
                        '_id' => 1,
                        'createdAt' => 1,
                        'title' => 1,
                        'content' => 1,
                        'author' => 1,
                        'user' => 1,
                        'theme' => 1,
                        'isPublic' => 1,
                        'likesCount' => ['$sum' => '$likesCount'], // Calcula o tamanho do array de likes
                        'comments' => 1,
                        'image' => 1
                    ]
                ],
                [
                    '$sort' => [
                        'likes' => -1, // Ordem decrescente por likes
                        'comments' => -1 // Ordem decrescente por comentários
                    ]
                ],
                [
                    '$limit' => 5 // Obtém os top 10 posts
                ]
            ]);

            foreach ($cursor as $document) {
                $recommendedPosts[] = $document;
            }

            return $recommendedPosts;
        } catch (Exception $ex) {
            throw new Exception("Erro ao obter todos os posts recomendados. Erro técnico: ".$ex->getMessage(), 500);
        }
    }

    function createPost($json, $imagePath) {
        // Obrigatório
        $title = $json['title'];
        $content = $json['content'];

        if ($title && $content) {
            try {
                $now = new UTCDateTime();
                $userFound = $this->usersCollection->findOne(['_id' => new ObjectId($json['author_id'])]);
                $themeFound = $this->themesCollection->findOne(['_id' => new ObjectId($json['theme_id'])]);

                $this->collection->insertOne([
                    'createdAt' => $now,
                    'title' => $title,
                    'content' => $content,
                    'author' => [
                        'id' => $json['author_id'],
                        'name' => $userFound['name'],
                        'photo' => $userFound['photo']
                    ],
                    'user' => [
                        'liked' => false,
                        'saved' => false
                    ],
                    'theme' => [
                        'id' => $themeFound['_id'],
                        'name' => $themeFound['name']
                    ],
                    'isPublic' => $json['public'] ?? true,
                    'likesCount' => 0,
                    'comments' => [],
                    'image' => $imagePath
                ]);

                return 'Post adicionado com sucesso';
            } catch (Exception $e) {
                return "Ocorreu um erro ao adicionar post. Erro técnico: ".$e->getMessage();
            }
        } else {
            throw new Exception("Titulo e conteúdo não especificados", 401);
        }
    }

    function getPostsByUserId($userId): array
    {
        $pipeline = [
            [
                '$match' => [
                    'author.id' => new ObjectId($userId)
                ]
            ]
        ];

        $posts = $this->collection->aggregate($pipeline)->toArray();

        if($posts) {
            return $posts;
        }else return [];
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

    function updatePost($postId, $json, $imagePath) {
        try {
            $postFound = $this->collection->findOne(['_id' => new ObjectId($postId)]);
        } catch (Exception $e) {
            exit("Não foi possível obter post para atualização com o id ' .$postId. ' Erro técnico: " . $e->getMessage());
        }

        if ($postFound) {
            // Obrigatório
            $title = $json['title'];
            $content = $json['content'];

            if ($title && $content) {
                try {
                    $this->collection->updateOne(
                        ['_id' => $postFound['_id']],
                        ['$set' => [
                            'title' => $title,
                            'content' => $content,
                            'isPublic' => $json['public'],
                            'image' => $imagePath
                        ]]
                    );

                    return 'Post atualizado com sucesso';
                } catch (Exception $e) {
                    exit("Não foi possível atualizar post com o id '.$postId.' Erro técnico: " . $e->getMessage());
                }
            } else {
                exit("Titulo ou conteúdo não especificados");
            }
        }
    }

    function getPostsCommentedByUser($userId)
    {
        if ($userId) try {

            $pipeline = [
                [
                    '$match' => [
                        'comments.author.id' => new ObjectId($userId)
                    ]
                ]
            ];

            return $this->collection->aggregate($pipeline)->toArray();
        } catch (Exception $e) {
            throw new Exception("Não foi possível retornar os posts via id do comentário selecionado. Erro técnico: " . $e->getMessage(), 500);
        }
    }

    function getPostsSavedByUser($userId, $postId, $method) {
        try {
            $userFound = $this->usersCollection->findOne(['_id' => new ObjectId($userId)]);
            $savedPostsByUserFound = $userFound['savedPosts'];

            $savedPostsIds = [];

            if(count($savedPostsByUserFound) !== 0) {
                forEach($savedPostsByUserFound as $savedPosts) {
                    $savedPostsIds[] = new ObjectId($savedPosts['post_id']);
                }
            }else return [];

            if($method == 'all') {
                $posts = $this->collection->find(['_id' => ['$in' => $savedPostsIds]]);

                foreach ($posts as $data) {
                    $data['user']['saved'] = true;
                    $result[] = $data;
                }

                return $result;
            }else if($method == 'one') {
                $posts = $this->collection->find(['_id' => ['$in' => $savedPostsIds]]);

                foreach ($posts as $data) {
                    if($data['_id'] == $postId) {
                        $data['user']['saved'] = true;
                        return $data;
                    }
                }
            }
        }Catch(\InvalidArgumentException $e) {
            throw new InvalidArgumentException('Erro ao obter todos os posts que são correspondentes com os posts salvados do user. Erro técnico: '.$e->getMessage(), 500);
        }
    }

    function getPostsLikedByUser($userId) {
       $userFound = $this->usersCollection->findOne(['_id' => new ObjectId($userId)]);
       $likedPostsByUserFound = $userFound['likedPosts'];

       $likedPostsIds = [];

       forEach($likedPostsByUserFound as $likedPosts) {
           $likedPostsIds = [
               new ObjectId($likedPosts['post_id'])
           ];
       }

       // Procurando por ids dos posts para verificação

       try {
           // Uso do operador $in para corresponder documentos com _id no array
           $matchedPosts = $this->collection->find([
               '_id' => ['$in' => $likedPostsIds]
           ]);

           foreach ($matchedPosts as $document) {
               return $document;
           }
       } catch (Exception $ex) {
           throw new Exception("Erro ao obter todos os posts que são correspondentes com os posts salvados do user", 500);
       }
    }

    function getPostByThemeId($postId, $themeId) {
        try {
            return $this->collection->findOne(['_id' => new ObjectId($postId), 'theme.id' => new ObjectId($themeId)]);
        } catch (Exception $e) {
            throw new Exception("Erro ao obter post pelo id do tema ".$themeId."", 500);
        }
    }
    function getPostsByThemeId($themeId) {
        try {
            $posts = $this->collection->find(['theme.id' => new ObjectId($themeId)]);
            $postsByThemeId = [];

            foreach ($posts as $data) {
                $postsByThemeId[] = $data;
            }

            return $postsByThemeId;
        } catch (Exception $e) {
            throw new Exception("Erro ao obter todos os posts pelo id do tema ".$themeId."", 500);
        }
    }
}