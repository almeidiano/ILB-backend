<?php

namespace App\Models\CMS;

use App\Controllers\ResponseController;
use App\Libraries\DatabaseConnector;
use CodeIgniter\Model;
use Config\Services;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use Exception;

class NavigationModel extends Model
{
    private Collection $collection;

    function __construct() {
        $connection = new DatabaseConnector('ILB_CMS');
        $this->collection = $connection->getCollection("NavigationInfo");
    }

    /**
     * addNavItem
     * CREATE
     * @return void
     */
    public function addNavItem($json) {
        try {
            if($json['text'] != null) {
                $this->collection->updateOne(
                    ['_id' => new ObjectId("65b6f4613f521046d162bfab")],
                    ['$push' => ['items' => $json]]
                );
            }else return exit('Texto do item não especificado.');

            return 'Item adicionado';
        } catch (Exception $ex) {
            throw new Exception("Erro ao obter todos os dados. Erro técnico: ".$ex->getMessage(), 500);
        }
    }
        
    /**
     * getNavInfo
     * READ
     * @return void
     */
    public function getNavInfo() {
        try {
            $cursor = $this->collection->find();
            return $cursor->toArray()[0];
        } catch (Exception $ex) {
            throw new Exception("Erro ao obter todos os dados. Erro técnico: ".$ex->getMessage(), 500);
        }
    }
        
    /**
     * getNavInfo
     * READ
     * @return void
     */
    private function getNavItem($itemText) {
        try {
            // Procurar o documento
            $document = $this->collection->findOne([
                '_id' => new ObjectId("65b6f4613f521046d162bfab"),
                'items.text' => $itemText,
            ]);

            // Verificar se o documento foi encontrado
            if ($document) {
                // Encontrar o item específico dentro do array "items"
                $item = null;

                foreach ($document['items'] as $item) {
                    if ($item['text'] == $itemText) {
                        break;
                    }
                }

                if ($item) {
                    // O item foi encontrado
                    return $item;
                } else {
                    // O item não foi encontrado
                    echo "Item não encontrado.";
                }
            } else {
                // O documento não foi encontrado
                exit("Documento não encontrado.");
            }
        } catch (Exception $ex) {
            throw new Exception("Erro ao obter todos os dados. Erro técnico: ".$ex->getMessage(), 500);
        }
    }

    /**
     * updateNavItem
     * UPDATE
     * @return void
     */
    public function updateNavItem($json) {
        try {
            if($json['oldText'] != null && $json['oldUrl'] != null) {
                $itemFound = $this->getNavItem($json['oldText']);

                $this->collection->updateOne(
                    [
                        'items.text' => $itemFound['text']
                    ],
                    [
                        '$set' => [
                            'items.$.text' => $json['newText'],
                            'items.$.url' => $json['newUrl'],
                        ],
                    ]
                );
            }else return exit('Dados não especificados.');

            return 'Item atualizado';
        } catch (Exception $ex) {
            throw new Exception("Erro ao obter todos os dados. Erro técnico: ".$ex->getMessage(), 500);
        }
    }

    /**
     * deleteNavItem
     * DELETE
     * @return void
     */
    public function deleteNavItem($json) {
        try {
            $this->collection->updateOne(
                [
                    'items.text' => $json['text'],
                    'items.url' => $json['url']
                ],
                ['$pull' => ['items' => ['text' => $json['text'], 'url' => $json['url']]]]
            );

            return 'Item deletado';
        }catch(Exception $e) {
            throw new Exception("Ocorreu um erro ao deletar. Erro técnico: ".$e->getMessage(), 500);
        }
    }
}
