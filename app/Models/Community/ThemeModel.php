<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;
use Exception;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;


class ThemeModel
{
    private Collection $collection;

    function __construct() {
        $database = new DatabaseConnector('ILB_comunidade');
        $this->collection = $database->getCollection('Themes');
    }

    function getAllThemes() {
        try {
            $data = [];
            $cursor = $this->collection->find();

            foreach ($cursor as $document) {
                $data[] = $document;
            }

            return $data;
        } catch (Exception $ex) {
            throw new Exception("Erro ao obter todos os temas. Erro técnico: ".$ex->getMessage(), 500);
        }
    }

    function getAllPendingUsersFromThemes() {
        try {
            $data = [];
            $cursor = $this->collection->find();

            foreach ($cursor as $document) {
                if($document['isPublic'] === false) {
                    $data[] = $document;
                }
            }

            return $data;
        } catch (Exception $ex) {
            throw new Exception("Erro ao obter todos os temas", 500);
        }
    }

    function checkIfUserBelongsToPrivateTheme($themeId, $userId) {
        $themeFound = $this->getTheme($themeId);

        forEach($themeFound->allowedUsers as $allowedUser) {
            if($allowedUser === $userId) {
                return true;
            }else return false;
        }
    }

    function getTheme($id) {
        try {
            return $this->collection->findOne(['_id' => new ObjectId($id)]);
        } catch (Exception $e) {
            throw new Exception("Não foi possível obter o tema com id: ".$id."", 500);
        }
    }

    function createTheme($json) {
        if($json) {
            try {
                $this->collection->insertOne([
                    'name' => $json['name'],
                    'allowedUsers' => [],
                    'pendindUsers' => [],
                    'isPublic' => $json['isPublic'],
                    'posts' => []
                ]);

                return 'Tema adicionado';
            } catch (Exception $e) {
                throw new Exception("Ocorreu um erro ao adicionar post. Erro técnico: " . $e->getMessage(), 500);
            }
        }else {
            throw new Exception("Corpo do tema não especificado", 401);
        }
    }

    function enterTheme($themeId, $userId) {
        try {
            $usermodel = new UserModel();
            $userFound = $usermodel->getUserById($userId); 

            $this->collection->updateOne(
                ['_id' => new ObjectId($themeId)],
                ['$addToSet' => ['pendingUsers' => [
                    'id' => new ObjectId(),
                    'userId' => $userFound['_id'],
                    'email' => $userFound['email'],
                    'name' => $userFound['name']
                ]]]
            );

            return 'Invite solicitado!';
        } catch (Exception $e) {
            throw new Exception("Ocorreu um erro ao enviar solicitação ao tema. Erro técnico: " . $e->getMessage(), 500);
        }
    }

    function updateTheme($json, $themeID) {
        if($json) {
            try {
                $this->collection->updateOne(
                    ['_id' => new ObjectId($themeID)],
                ['$set' => ['name' => $json['name'], 'isPublic' => $json['isPublic']]]
                );

                return 'Tema atualizado';
            } catch (Exception $e) {
                throw new Exception("Ocorreu um erro ao atualizar tema. Erro técnico: " . $e->getMessage(), 500);
            }
        }else {
            throw new Exception("Corpo do tema não especificado", 401);
        }
    }
    function acceptUserToTheme($userId, $themeId) {
        try {
            $usermodel = new UserModel();
            $userFound = $usermodel->getUserById($userId); 

            $this->collection->updateOne(
                ['_id' => new ObjectId($themeId)],
                ['$pull' => ['pendingUsers' => 
                    [
                        'email' => $userFound['email'],
                        'name' => $userFound['name']
                    ]
                ]]
            );

            $this->moveUserToAllowedList($themeId, $userFound);

            return 'Usuário aceito!';
        } catch (Exception $e) {
            throw new Exception("Ocorreu um erro ao enviar solicitação ao tema. Erro técnico: " . $e->getMessage(), 500);
        }
    }

    function refuseUserFromTheme($userId, $themeId) {
        try {
            $usermodel = new UserModel();
            $userFound = $usermodel->getUserById($userId); 
            

            $this->collection->updateOne(
                ['_id' => new ObjectId($themeId)],
                ['$pull' => ['pendingUsers' => 
                    [
                        'email' => $userFound['email'],
                        'name' => $userFound['name']
                    ]
                ]]
            );

            $this->removeUserFromPendingList($themeId, $userFound);

            return 'Usuário recusado!';
        } catch (Exception $e) {
            throw new Exception("Ocorreu um erro ao enviar solicitação ao tema. Erro técnico: " . $e->getMessage(), 500);
        }
    }

    private function moveUserToAllowedList($themeId, $userFound) {
        return $this->collection->updateOne(
            ['_id' => new ObjectId($themeId)],
            ['$addToSet' => ['allowedUsers' => 
                $userFound['_id']
            ]]
        );
    }

    private function removeUserFromPendingList($themeId, $userFound) {
        return $this->collection->updateOne(
            ['_id' => new ObjectId($themeId)],
            ['$pull' => ['pendingUsers' => 
                $userFound['_id']
            ]]
        );
    }
    function deleteTheme($themeID) {
        try {
            $this->collection->deleteOne(['_id' => new ObjectId($themeID)]);
            return 'Tema apagado.';
        } catch (Exception $e) {
            throw new Exception("Ocorreu um erro ao apagar tema, erro técnico: " . $e->getMessage(), 500);
        }
    }
}
