# Sistema de Indicação - CODE8734

## O que é?
O sistema de indicação da CODE8734 é um subsistema interno usado para validar o processo de indicação entre clientes para as unidades da rede. Para validar, documentar e bonificar as partes de uma indicação, é necessário buscar dados de diferentes fontes (via APIs) e registrar o estado dessa indicação (e de suas partes) em um banco de dados.

## Como funciona o processo de indicação
O processo de indicação de clientes possui três partes principais:
1. Captação;
2. Confirmação;
3. Bonificação.

Na Captação, são feitas a validação e o registro dos dados das partes. Na confirmação, a indicação sofre uma atualização e consolidação. Por fim, na bonificação, as partes são recompensadas uma única vez pela tarefa. No contexto, é importante definir:
1. Indicador: atual cliente da CODE8734, que deve constar na base de dados de clientes (SharpSpring);
2. Indicado: possível novo cliente da CODE8734, que, ao contrário do anterior, não deve constar na base de dados de clientes;
3. Indicação: relação entre indicado e indicado, que deve conter um *status* e *data de registro*.

Um indicador pode manter quantos indicados forem necessários. Entretanto, um indicado somente poderá ser atribuído a um único indicador. Com relação aos processos:

### Captação
Quando um atual cliente da CODE8734 realiza, durante o período de campanha promovido por sua unidade, a inserção de dados de um possível cliente. Caso o cliente atual faça a entrada de dados válida de um indicado, o sistema registra o ato de indicação. Um indicador é dito válido quando consta na base de dados de clientes da unidade (SharpSpring), enquanto um indicado é válido quando seu e-mail não está registrado na mesma base do indicador.

### Confirmação
Depois do ato de indicação por parte do indicador, é considerado que o indicado realize a matrícula. Nesse caso, o sistema de indicação consulta a base de dados responsável pelo cadastro de clientes das unidades e verifica se o novo cliente é oriundo de uma indicação. Em caso positivo, atualiza a base de dados de indicações para que a bonificação possa ser realizada (em adição: uma única vez).

### Bonificação
Com os dados de atualização, a aplicação responsável por recompensar os clientes deve rodar um script programado para avisar às bases de dados sobre as recompensas, sejam elas físicas ou virtuais. 


## Sobre o sistema
Conhecendo sobre a bonificação, podemos dividir o subsistema em diferentes módulos. A saber:

### Módulo de Captação
O Módulo de Captação (MC) consiste em uma aplicação para receber registros de indicação de páginas externas, desde que identificadas pelo MC. O MC deve consultar um registro de páginas permitidas no banco de dados do MC pela chave `page_key` advinda do JSON na requisição HTTP. Exemplo de entrada do MC:
```javascript
{
    page_key: "12345",
    data: {
        indicator: "cliente@code8734.com.br",
        indicated: "novapessoa@code8734.com.br",
        timestamp: "2021-08-12 16:57:30"
    }
}
```
O Módulo de captação deve então acionar o Módulo de Servicos Externos (MSE) e o Módulos de Dados Internos (MDI) para validar as informações da indicação nas diferentes bases. Exemplo de saída do MC:
```javascript
{
    status: "OK",
    log: "Indicação cadastrada com sucesso!";
}
```
São métodos do MC:
- `validatePageInfo(array $request) => throws 'ValidationException'` recebe a requisição HTTP da aplicação principal (main) e armazena as informações da request em uma variável interna.

### Módulo de Dados Internos
O Módulo de Dados Internos (MDI) tem a responsabilidade de registrar novas páginas de captação e consultar a existência delas, gerando a elas um atribuito `page_key` em uma tabela no banco de dados no primeiro caso. Também é responsável por responder ao MC se o indicado já foi registrado nas tabelas de indicação. Por fim, também é responsável por atualizar os registros no processo de Confirmação. Exemplo de chamada do MDI, avisando o Módulo de Bonificação (MB) de uma nova Confirmação em uma tabela:
```php
    $mdi = new InternalDataModule();

    $pageExists = $mdi->checkPageKey("12345");

    if ($pageExists) {
         ...
    }
```

São métodos do MDI:
- `checkPageKey(string $page_key) => boolean`: verifica se a string informada consta na tabela de páginas registradas.
- `registerPageKey(string $new_page_key) => boolean`: registra uma nova `page_key` na tabela de páginas.
- `checkIndication(string $indicated) => boolean`: verifica se o e-mail do indicado consta nas tabelas de indicação.
- `registerIndication(string $indicated, string $timestamp) => boolean`: registra uma nova indicação na tabela de indicações.

### Módulo de Serviços Externos
Módulo que realiza consultas em diferentes APIs, filtrando apenas as informações relevantes para as outras camadas da aplicação. Como, futuramente, a bonificação pode ser realizada também em serviços externos, deve estar aberta para extensões. Exemplo de chamada do MSE:
```php
    $mse = new ExternalServiceModule("SharpSpring");
    $mseResponse = $mse->callFunction("checkIndicator", $indicator->email);

    if ($mseResponse->status = "OK"){
        $indicatorExists = $mseResponse->returnValue;

        if ($indicatorExists) {
            ...
        }
    }
```
Ainda sobre o MSE, note que pode ser necessário instanciar objetos de subclasses, como uma classe que realiza métodos restritos à base de dados da SharpSpring, como mostra o exemplo acima. Por isso, é importante que a classe faça as validações antes de retornar valores. A saída do MSE é dada como abaixo:
```php
    [
        'status' => 'OK' | 'ERR_VAL' | 'ERR_API',
        'returnValue' => mixed
    ]
```

### Módulo de Bonificação
O Módulo de Bonificação (MB) entra em ação quando os registros são atualizados pelo MDI. Ao realizar