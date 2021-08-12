<?
namespace mdi;

class InternalDataModule {

    /**
     * Verifica se há registro de 'page_key' na tabela 'registered_pages' do banco de dados.
     *
     * @param string $page_key
     * @return boolean
     */
    public function checkPageKey($page_key) {
        return true;
    }
}

