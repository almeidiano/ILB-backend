<?php

namespace App\Libraries;

use Cassandra\Collection;
use Config\MongoDB;
use Exception;
use MongoDB\Client;
use MongoDB\Database;
use MongoDB\Driver\Exception\ConnectionException;
use MongoDB\Driver\Exception\RuntimeException;

class DatabaseConnector {
    private $client;
    private $database;

    /**
     * @throws Exception
     */
    function __construct() {
        $uri = getenv('URL_ATLAS');
        $database = getenv('BANCO_DE_DADOS');


        if (empty($uri) || empty($database)) {
            throw new Exception('Você precisa declarar a URL_ATLAS e o BANCO_DE_DADOS no arquivo .env!', 500);
        }

        try {
            $this->client = new Client($uri);
        } catch(ConnectionException $ex) {
            throw new Exception('Não foi possível conectar-se ao banco de dados. Erro técnico: ' . $ex->getMessage(), 500);
        }

        try {
            $this->database = $this->client->selectDatabase($database);
        } catch(RuntimeException $ex) {
            throw new Exception("Não foi possível conectar-se ao banco de dados intitulado ".$database.". Erro técnico: " . $ex->getMessage(), 500);
        }
    }

    function getCollection($collection): \MongoDB\Collection
    {
        try {
            return $this->database->$collection;
        }Catch(ConnectionException $e) {
            throw new Exception("Não foi possível conectar-se à coleção ".$collection.". Erro técnico: " . $ex->getMessage(), 500);
        }
    }
}