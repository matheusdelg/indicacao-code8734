<?php
namespace mse;

use JetBrains\PhpStorm\ExpectedValues;
use ServiceException;
use ValidationException;


require_once ('Exceptions.php');
require_once ('SharpSpringApi.php');

class ExternalServiceModule {

    protected $apiInstance, $serviceName;

    public function __construct($serviceName) {
        
        if(!in_array($serviceName, AVAILABLE_APIS)) 
            throw new ServiceException("Nome de serviço inválido: '". $serviceName. "'.");

        $this->serviceName = $serviceName;
        $serviceName = $serviceName."API";
        $this->apiInstance = new $serviceName();
    }

    public function call($fctName, $params) {
        return call_user_func_array(
                [$this->apiInstance, "makeApiCall"],
                [$fctName, $params, EXPECTED_OUTPUT_DICT[$this->serviceName][$fctName]]);
    }
}

$mse = new ExternalServiceModule("SharpSpring");

echo count($mse->call("getLeads", ['where' => ['emailAddress' => 'matheus.delgado@code8734.com.br']]));