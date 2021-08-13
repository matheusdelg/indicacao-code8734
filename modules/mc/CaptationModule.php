<?

use mdi\InternalDataModule;
use mse\ExtenalServiceModule;

require_once( __DIR__ . "/../../Exceptions.php");
require_once( __DIR__ . "/../mdi/InternalDataModule.php");
require_once( __DIR__ . "/../mse/ExternalServiceModule.php");

class CaptationModule {

    protected $request;

    /**
     * Valida as informações recebidas da requisição HTTP. Não retorna dados
     *
     * @param array $request     Requsição HTTP (array de dados)
     * @throws ValidationException 
     */
    public function validatePageInfo($request) {

        if (!isset($request))
            throw new ValidationException("Requisição HTTP está vazia.");

        // TODO: trocar por for
        if(!isset($request['page_key']))
            throw new ValidationException("Requisição sem atributo 'page_key'.");

        if(!isset($request['indicator'])) 
            throw new ValidationException("Atributo 'indicator' faltando.");

        if(!isset($request['indicated'])) 
            throw new ValidationException("Atributo 'indicated' faltando.");
    
        if(!isset($request['timestamp'])) 
            throw new ValidationException("Atributo 'timestamp' faltando.");

        $mdi = new mdi\InternalDataModule();
        $pageExists = $mdi->checkPageKey($request['page_key']);

        if (!$pageExists) 
            throw new ValidationException("Atribuito 'page_key' inválido.");

        $this->request = $request;
    }

    /**
     * Verifica, usando o MDI, se o indicado é válido.
     *
     * @return boolean $indicatedIsValid
     */
    public function validateIndicated () {
        
        $mdi = new mdi\InternalDataModule();
        $indicatedIsValid = !$mdi->checkIndicated($this->request['indicated']);

        return $indicatedIsValid;
    }

    /**
     * Verifica, usando o MSE, se o indicador é válido.
     *
     * @return boolean $indicatorIsValid
     */
    public function validateIndicator () {

        $mse = new mse\ExternalServiceModule("SharpSpring");
        $indicatorIsValid = (boolean) $mse->call("checkIndicator", $this->request['indicator']);

        return $indicatorIsValid;
    }
}