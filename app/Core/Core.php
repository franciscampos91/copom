<?php

    // Declaração da classe Core
    class Core
    {
        // Método start que recebe um array $urlGet como parâmetro
        public function start($urlGet)
        {
            // Define a ação padrão como 'index'
            $acao = 'index';
            
            // Verifica se a chave 'pagina' existe no array $_GET
            if (isset($_GET['pagina'])) {
                // Se existir, cria o nome do controlador concatenando 'Controller' com o valor de 'pagina' e colocando a primeira letra em maiúsculo
                $controller = ucfirst($urlGet['pagina'] . 'Controller');
            } else {
                // Se não existir, define o controlador padrão como 'HomeController'
                $controller = 'HomeController';
            }

            if (isset($_GET['metodo'])){
                $acao = $_GET['metodo'];
            }


            // Verifica se a classe do controlador existe
            if (!class_exists($controller)) {
                $controller = 'ErrorController';
            }

            // Chama o método $acao (por padrão 'index') do controlador criado
            // O método call_user_func_array permite chamar um método de uma classe de forma dinâmica
            call_user_func_array(array(new $controller, $acao), array());
        }
    }


    /** 
     * Aqui está uma explicação detalhada de cada parte:
     * 
     * Declaração da Classe e Método:
     * 
     * class Core declara uma classe chamada Core.
     * public function start($urlGet) define um método público start que aceita um parâmetro $urlGet.
     * 
     * Definição da Ação Padrão:
     * 
     * $acao = 'index'; define a variável $acao com o valor padrão 'index', presumivelmente o método padrão que será chamado no controlador.
     * 
     * Determinação do Controlador:
     * 
     * if (isset($_GET['pagina'])) { verifica se o parâmetro 'pagina' existe na URL.
     * Se existir, ucfirst($urlGet['pagina'] . 'Controller') cria o nome do controlador, colocando a primeira letra da página em maiúsculo e concatenando 'Controller'.
     * Se não existir, $controller = 'HomeController'; define o controlador padrão como 'HomeController'.
     * 
     * Verificação da Existência da Classe:
     * 
     * if (!class_exists($controller)) { verifica se a classe definida em $controller existe.
     * Se a classe não existir, echo 'ErrorController'; imprime 'ErrorController' e adicionamos return; para evitar a tentativa de instanciar uma classe inexistente.
     * 
     * Chamada Dinâmica do Método:
     * 
     * call_user_func_array(array(new $controller, $acao), array()); chama dinamicamente o método $acao do controlador $controller criado.
     * array(new $controller, $acao) cria uma nova instância do controlador e chama o método $acao (por padrão, index).
     * O segundo parâmetro array() é um array de argumentos passados para o método, que, neste caso, está vazio.
     * 
     * Este código é uma abordagem comum em frameworks MVC para rotear dinamicamente as requisições para diferentes controladores com base nos parâmetros da URL.
     */
