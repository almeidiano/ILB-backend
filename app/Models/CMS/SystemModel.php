<?php

namespace App\Models\CMS;

use App\Controllers\ResponseController;
use App\Libraries\DatabaseConnector;
use CodeIgniter\Model;
use Config\Services;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use Exception;

class SystemModel extends Model
{
    private Collection $collection;

    function __construct() {
        $connection = new DatabaseConnector('ILB_CMS');
        $this->collection = $connection->getCollection("SysInfo");
    }

    public function getSystemInfo() {
        try {
            $cursor = $this->collection->find();
            return $cursor->toArray()[0];
        } catch (Exception $ex) {
            throw new Exception("Erro ao obter todos os dados. Erro técnico: ".$ex->getMessage(), 500);
        }
    }

    public function updateSystemInfo($json) {
        try {
            $sysInfo = $this->getSystemInfo();

            $this->collection->updateOne(
                ['_id' => new ObjectId($sysInfo['_id'])],
                ['$set' => [
                    'websiteTitle' => $json['websiteTitle'],
                    'contactNumber' => $json['contactNumber'],
                    'websiteEmail' => $json['websiteEmail'],
                    'websiteDescription' => $json['websiteDescription'],
                    'websiteKeywords' => $json['websiteKeywords'],
                ]]
            );

            return 'Item atualizado';
        } catch (Exception $ex) {
            throw new Exception("Erro ao atualizar. Erro técnico: ".$ex->getMessage(), 500);
        }
    }
}
