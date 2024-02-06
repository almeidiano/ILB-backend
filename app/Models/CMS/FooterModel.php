<?php

namespace App\Models\CMS;

use App\Controllers\ResponseController;
use App\Libraries\DatabaseConnector;
use CodeIgniter\Model;
use Config\Services;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use Exception;

class FooterModel extends Model
{
    private Collection $collection;

    function __construct() {
        $connection = new DatabaseConnector('ILB_CMS');
        $this->collection = $connection->getCollection("FooterInfo");
    }

    public function getFooterInfo() {
        try {
            $cursor = $this->collection->find();
            return $cursor->toArray()[0];
        } catch (Exception $ex) {
            throw new Exception("Erro ao obter todos os dados. Erro técnico: ".$ex->getMessage(), 500);
        }
    }

    public function updateFooterAnchor($content, $target) {
        try {
            $footer = $this->getFooterInfo();

            $this->collection->updateOne(
                ['_id' => new ObjectId($footer['_id'])],
                ['$set' => [$target => $content]]
            );

            return 'Item atualizado';
        } catch (Exception $ex) {
            throw new Exception("Erro ao atualizar. Erro técnico: ".$ex->getMessage(), 500);
        }
    }

    public function updateFooterMainNavigation($json) {
        if($json) {
            try {
                $this->collection->updateOne(
                    ['_id' => new ObjectId("65b6ed773f521046d162bf94")],
                    ['$set' => ['mainNavigation' => $json]]
                );

                return 'Atualizado';
            } catch (Exception $e) {
                throw new Exception("Ocorreu um erro ao atualizar. Erro técnico: " . $e->getMessage(), 500);
            }
        }else {
            throw new Exception("Corpo não especificado", 401);
        }
    }
}
