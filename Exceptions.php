<?php

use Exception;

require_once(__DIR__ . "/config.php");

class ValidationException extends Exception {

    public function __construct(string $description) {

        $obj = [
            "status" => ERR_VAL,
            "description" => $description,
        ];

        parent::__construct(json_encode($obj));
    }
}

class ServiceException extends Exception {

    public function __construct($description) {
        
        $obj = [
            "status" => ERR_API,
            "description" => $description,
        ];

        parent::__construct(json_encode($obj));
    }

}