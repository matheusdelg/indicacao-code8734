<?php

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

    /**
     * Verifica se há registro de 'indicated_email' na tabela 'indications' no banco de dados.
     *
     * @param string $indicated_email
     * @return boolean
     */
    public function checkIndicated($indicated_email) {
        return false;
    }
}

