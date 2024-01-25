<?php

namespace App\Models\CMS;

use App\Controllers\ResponseController;
use App\Libraries\DatabaseConnector;
use CodeIgniter\Model;
use Config\Services;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use Exception;

class UserModel
{
    private Collection $collection;

    function __construct() {
        $connection = new DatabaseConnector('ILB_CMS');
        $this->collection = $connection->getCollection("users");
    }
    
    /**
     * createUser
     * CREATE
     * @param  mixed $json
     * @return void
     */
    function createUser($json) {
        try {
            $this->collection->insertOne([
                'name' => $json['name'],
                'username' => $json['username'],
                'password' => password_hash($json['password'], PASSWORD_DEFAULT),
                'email' => $json['email'],
                'role' => $json['role']
            ]);

            exit('Usuário adicionado');
        } catch (Exception $e) {
            exit("Ocorreu um erro ao adicionar. Erro técnico: ".$e->getMessage());
        }
    }   

    /**
     * getTheme
     * READ
     * @param  mixed $id
     * @return void
     */
    function getUser($id) {
        try {
            return $this->collection->findOne(['_id' => new ObjectId($id)]);
        } catch (Exception $e) {
            throw new Exception("Não foi possível obter com o id: ".$id."", 500);
        }
    }

    /**
     * getAllThemes
     * READ
     * @param  mixed $id
     * @return void
     */
    function getAllUsers() {
        try {
            $data = [];
            $cursor = $this->collection->find();

            foreach ($cursor as $document) {
                $data[] = $document;
            }

            return $data;
        } catch (Exception $ex) {
            throw new Exception("Erro ao obter todos os dados. Erro técnico: ".$ex->getMessage(), 500);
        }
    }

    /**
     * updateUser
     * UPDATE
     * @param  mixed $json
     * @return void
     */
    function updateUser($json, $id) {
        try {
            $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => ['name' => $json['name'], 'username' => $json['username'], 'password' => $json['password'], 'email' => $json['email'], 'role' => $json['role']]]
            );

            exit('Usuário atualizado');
        } catch (Exception $e) {
            exit("Ocorreu um erro ao adicionar. Erro técnico: ".$e->getMessage());
        }
    }   

    /**
     * deleteUser
     * DELETE
     * @param  mixed $json
     * @return void
     */
    function deleteUser($id) {
        try {
            $this->collection->deleteOne(['_id' => new ObjectId($id)]);
            
            return 'Usuário deletado.';
        } catch (Exception $e) {
            throw new Exception("Ocorreu um erro ao deletar, erro técnico: " . $e->getMessage(), 500);
        }
    }
}
