<?

require_once( __DIR__ . "/../exceptions/ExternalException.php");
require_once( __DIR__ . "/../mdi/InternalDataModule.php");

class CaptationModule {

    protected $request;

    /**
     * Valida as informações recebidas da requisição HTTP. Não retorna dados
     *
     * @param array $request     Requsição HTTP (array de dados)
     * @throws ExternalException 
     */
    public function validatePageInfo($request) {

        if (!isset($request)) {
            throw new ExternalException("Requisição HTTP está vazia.");
        }

        if(!isset($request['page_key'])) {
            throw new ExternalException("Requisição sem atributo 'page_key'.");
        }

        if(!isset($request['indicator'])) {
            throw new ExternalException("Atributo 'indicator' faltando.");
        }

        if(!isset($request['indicated'])) {
            throw new ExternalException("Atributo 'indicated' faltando.");
        }
        
        if(!isset($request['timestamp'])) {
            throw new ExternalException("Atributo 'timestamp' faltando.");
        }

        $mdi = new mdi\InternalDataModule();
        $pageExists = $mdi->checkPageKey($request['page_key']);

        if (!$pageExists) {
            throw new ExternalException("Atribuito 'page_key' inválido.");
        }

        $this->request = $request;
    }
}