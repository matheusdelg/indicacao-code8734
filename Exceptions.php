<?php


require_once(__DIR__ . "/config.php");

class ValidationException extends Exception {

    public function __construct(string $description) {

        $obj = [
            "status" => ERR_VAL,
            "description" => $description,
        ];

        parent::__construct(json_encode($obj, JSON_UNESCAPED_UNICODE));
    }
}

class ServiceException extends Exception {

    public function __construct($description) {
        
        $obj = [
            "status" => ERR_API,
            "description" => $description,
        ];

        parent::__construct(json_encode($obj, JSON_UNESCAPED_UNICODE));
    }

}

class InternalException extends Exception {
    public function __construct($description) {
        
        $obj = [
            "status" => ERR_INT,
            "description" => $description,
        ];

        parent::__construct(json_encode($obj, JSON_UNESCAPED_UNICODE));
    }
}