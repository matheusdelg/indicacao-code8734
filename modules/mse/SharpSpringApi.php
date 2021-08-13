<?php
class SharpSpringAPI {

    private $accountID, $secretKey;

    public function __construct () {
        $this->accountID = SHARPSPRING_CONFIG['ACCOUNT_ID'];
        $this->secretKey = SHARPSPRING_CONFIG['SECRET_KEY'];
    }

    /**
     * Valida os parâmetros da chamada à API antes de executá-la.
     *
     * @param  string $fctName
     * @param  array  $params
     * @param  string $expectedResult
     */
    private function _validateApiCall($fctName, $params, $expectedResult) {
        
        if (!in_array($fctName, SHARPSPRING_CONFIG['ALLOWED_METHODS']))
            throw new ValidationException("Método '".$fctName."' Não encontrado/permitido.");
        
        if (!isset($params['where']))
            throw new ValidationException("O campo 'where', mesmo que vazio, é obrigatório.");

        if(SHARPSPRING_CONFIG['ALLOWED_RESPONSES'][$fctName] != $expectedResult)
            throw new ValidationException("Não é possível determinar resposta '".
                                          $expectedResult."' do método '".$fctName."'.");
    }

    /**
     * Faz chamada à API da SharpSpring. Mais informações sobre o schema e métodos em:
     * https://help.sharpspring.com/hc/en-us/articles/115001069228-Understanding-SharpSpring-Open-API-Overview
     *
     * @param  string $fctName           Nome do método da API
     * @param  array  $params            Parâmetros da chamada
     * @param  string $expectedResult    Resultado esperado da API
     * @return array  $response          Resposta da chamada à API (filtrada por $expectedResult)
     */
    public function _makeApiCall($fctName, $params, $expectedResult) {

        $postFields = json_encode([
            'method' => $fctName,
            'params' => $params,
            'id'     => uniqid(rand()), 
        ], JSON_UNESCAPED_UNICODE);

        $curlHeaders = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Content-Length: '. strlen($postFields),
        ];

        $ch = curl_init();
        $url = SHARPSPRING_CONFIG['ACTION_URL']. 
               "?accountID=" . SHARPSPRING_CONFIG["ACCOUNT_ID"].
               "&secretKey=" . SHARPSPRING_CONFIG["SECRET_KEY"];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);

        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);

        if ($response->result == [])
            throw new ServiceException("A API não retornou resultados. Resposta: "
                                        . $response->error->message); 
        
        return $response->result->{$expectedResult};
    }

    /**
     * A API da SharpSpring possui uma cota máxima de resultados entregues. Para realizar chamadas
     * que podem exceder o valor máximo com segurança, este método "quebra" a requisição em tamanhos
     * menores, aceitáveis pela API daSharpSpring.
     * 
     * @param [type] $fctName
     * @param [type] $params
     * @param [type] $expectedResult
     * @return void
     */
    private function _makeApiLargeCall($fctName, $params, $expectedResult) {

        $retrivied = []; $buffered = [];

        $offset = isset($params['offset']) ? $params['offset'] : 0;
        $subParams = $params;

        do {
            $subParams['offset'] = $offset;
            
            if (isset($params['limit']))
              if(count($buffered) >= $subParams['limit'])
                break;  

            $retrivied = $this->_makeApiCall($fctName, $subParams, $expectedResult);
            $buffered = array_merge($retrivied, $buffered);

            $offset += count($retrivied);

        } while (count($retrivied) >= SHARPSPRING_CONFIG['MAX_QUOTA']);

        return $buffered;
    }

    /**
     * Encapsula as funções de validação e chamada da API:
     *
     * @param [type] $fctName
     * @param [type] $params
     * @param [type] $expectedResult
     * @return void
     */
    public function makeApiCall($fctName, $params, $expectedResult) {

        $this->_validateApiCall($fctName, $params, $expectedResult);
        return $this->_makeApiLargeCall($fctName, $params, $expectedResult);
    }
}