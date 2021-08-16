<?php

require_once("DatabaseManipulator.php");

class InternalDataModule extends DatabaseManipulator {

    /**
     * Verifica se há registro de 'page_key' na tabela 'registered_pages' do banco de dados.
     *
     * @param string $page_key
     * @return boolean
     */
    public function checkPageKey(string $page_key) {
        $pages = $this->getRecords('registered_pages', ['page_key' => $page_key]);

        return count($pages) > 0;
    }

    /**
     * Verifica se há registro de 'indicated_email' na tabela 'indications' no banco de dados.
     *
     * @param string $indicated_email
     * @return boolean
     */
    public function checkIndicated(string $indicated_email) {
        $indications = $this->getRecords('indications', ['indicated' => $indicated_email]);
        
        return count($indications) == 0;
    }

    /**
     * Registra uma nova 'page_key' na tabela 'registered_pages' do banco de dados.
     *
     * @param string $new_page_key
     */
    public function registerPageKey(string $new_page_key) {

        $page_exists = $this->checkPageKey($new_page_key);

        if (!$page_exists) {
            $this->insertRecord('registered_pages', ['page_key' => $new_page_key]);
        }
    }
}

