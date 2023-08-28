<?php

namespace Config;
use MongoDB\Exception\RuntimeException as Exception;

class MongoDB
{
    public function __construct($username, $password) {
        try {
            return "mongodb+srv://".$username.":".$password."@cluster0.iwnx7xq.mongodb.net/?retryWrites=true&w=majority";
        }catch (Exception $e) {
            echo $e->getMessage();
        }
    }
//    public $database = 'your_database_name';
//    public $db_debug = false;
//    public $options  = [];
}