<?php

namespace App\Libraries;

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
    function showError(string $errorMessage, int $httpErrorCode) {
        http_response_code($httpErrorCode);
        throw new Exception($errorMessage);
    }

    /**
     * @throws Exception
     */
    function __construct() {
//        $uri = getenv('ATLAS_URI');
//        $database = getenv('DATABASE');

//        if (empty($uri) || empty($database)) {
//            throw new Exception('You need to declare ATLAS_URI and DATABASE in your .env file!');
//        }

        try {
            $this->client = new Client("mongodb+srv://almeidiano:BTYCyUpOEwRtOb30@cluster0.iwnx7xq.mongodb.net/?retryWrites=true");
        } catch(ConnectionException $ex) {
            throw new Exception('Couldn\'t connect to database: ' . $ex->getMessage(), 500);
        }

        try {
            $this->database = $this->client->selectDatabase("ILB_comunidade");
        } catch(RuntimeException $ex) {
            throw new Exception('Error while fetching database with name: ');
        }
    }

    function getDatabase(): Database
    {
        return $this->database;
    }
}