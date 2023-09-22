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
        $database = new DatabaseConnector();
        $this->collection = $database->getCollection('themes');
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
            throw new Exception("Erro ao obter todos os temas", 500);
        }
    }

    function createTheme($json) {
        if($json) {
            try {
                $this->collection->insertOne([
                    'name' => $json['name'],
                    'private' => $json['private']
                ]);

                return 'Tema adicionado';
            } catch (Exception $e) {
                throw new Exception("Ocorreu um erro ao adicionar post. Erro técnico: " . $e->getMessage(), 500);
            }
        }else {
            throw new Exception("Corpo do tema não especificado", 401);
        }
    }

    function updateTheme($json, $themeID) {
        if($json) {
            try {
                $this->collection->updateOne(
                    ['_id' => new ObjectId($themeID)],
                ['$set' => ['name' => $json['name'], 'private' => $json['private']]]
                );

                return 'Tema atualizado';
            } catch (Exception $e) {
                throw new Exception("Ocorreu um erro ao atualizar tema. Erro técnico: " . $e->getMessage(), 500);
            }
        }else {
            throw new Exception("Corpo do tema não especificado", 401);
        }
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
