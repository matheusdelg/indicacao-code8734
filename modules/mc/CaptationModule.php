<?php

require_once("Exceptions.php");
require_once("modules/mdi/InternalDataModule.php");
require_once("modules/mse/ExternalServiceModule.php");

class CaptationModule {

    protected $request;

    /**
     * Valida as informações recebidas da requisição HTTP. Não retorna dados
     *
     * @param array $request     Requsição HTTP (array de dados)
     * @throws ValidationException 
     */
    public function validateRequest($request) {

        if (!isset($request))
            throw new ValidationException("Requisição HTTP está vazia.");

        // TODO: trocar por for
        if(!isset($request['page_key']))
            throw new ValidationException("Requisição sem atributo 'page_key'.");

        if(!isset($request['data']))
            throw new ValidationException("Atributo 'data' faltando.");

        if(!isset($request['data']['indicator'])) 
            throw new ValidationException("Atributo 'indicator' faltando.");

        if(!isset($request['data']['indicated'])) 
            throw new ValidationException("Atributo 'indicated' faltando.");
    
        if(!isset($request['data']['create_timestamp'])) 
            throw new ValidationException("Atributo 'create_timestamp' faltando.");

        $mdi = new InternalDataModule();
        $pageExists = $mdi->checkPageKey($request['page_key']);

        if (!$pageExists) 
            throw new ValidationException("Atribuito 'page_key' inválido.");

        $this->request = $request['data'];
    }

    /**
     * Verifica, usando o MDI, se o indicado é válido.
     *
     * @throws ServiceException 
     */
    public function validateIndicated () {
        
        $indicated = $this->request['indicated'];

        $mdi = new InternalDataModule();
        $indicatedIsValid = !$mdi->checkIndicated($indicated);

        if (!$indicatedIsValid)
            throw new InternalException('Já existem indicações com o e-mail '.$indicated);
    }

    /**
     * Verifica, usando o MSE, se o indicador é válido.
     *
     * @throws ServiceException 
     */
    public function validateIndicator () {

        $mse = new ExternalServiceModule("SharpSpring");
        
        $params = ['where' => [
            'emailAddress' => $this->request['indicator']
        ]];

        $indicatorIsValid = $mse->call("getLeads", $params);
        $indicatorIsValid = count($indicatorIsValid) > 0;

        if (!$indicatorIsValid) 
            throw new ServiceException('Não exisem registros do indicador na base de dados externa.');
    }
}

$test_request = [
    'page_key'  => '12345',
    'data' => [
        'indicated'        => 'outrocliente@gmail.com',
        'indicator'        => 'matheus.delgado@code8734.com.br',
        'create_timestamp' => '2021-08-16 13:55:22',
    ],
];