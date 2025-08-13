<?php

    require_once 'app/Core/Core.php';
    require_once 'lib/Database/Connection.php';

    require_once 'app/Controller/HomeController.php';
    require_once 'app/Controller/ErrorController.php';
    require_once 'app/Controller/PolicialController.php';
    require_once 'app/Controller/IndexController.php';
    require_once 'app/Controller/LogoutController.php';
    require_once 'app/Controller/EfetivoController.php';
    require_once 'app/Controller/EscalaController.php';
    require_once 'app/Controller/AfastamentosController.php';


    require_once 'app/Model/Efetivo.php';
    require_once 'app/Model/Afastamentos.php';
    require_once 'app/Model/Dejem.php';


    require_once 'vendor/autoload.php';

    date_default_timezone_set('America/Sao_Paulo');

    session_start();

    //O método file_get_contents() é usado para ler o conteúdo de um arquivo em uma string
    $template = file_get_contents('app/Template/template.phtml');

    // Verifica se o nome do usuário está na sessão e define a área do usuário
    if (isset($_SESSION['nome'])) {
        $nome = $_SESSION['nome'];
        $area_usuario = "<span>$nome</span>";
    } else {
        $area_usuario = "<a href='signin'>Logar</a>"; // Corrigido: Fechamento da tag href
    }

    if (!isset($_SESSION['nome'])){
        header('Location: ./signin/');
        exit;
    }

    // Inicia o buffer de saída
    ob_start();
        $core = new Core;
        $core->start($_GET);
        // Obtém o conteúdo do buffer de saída
        $saida = ob_get_contents();
    ob_end_clean();

    // Atribui valores das variáveis de sessão a variáveis locais
    $usuario = $_SESSION['usuario'] ?? null;
    $cpf = $_SESSION['cpf'] ?? null;
    $re = $_SESSION['re'] ?? null;
    $digre = $_SESSION['digre'] ?? null;
    $ptgr = $_SESSION['ptgr'] ?? null;
    $codptgr = $_SESSION['codptgr'] ?? null;
    $nome = $_SESSION['nome'] ?? null;
    $guerra = $_SESSION['guerra'] ?? null;
    $sexo = $_SESSION['sexo'] ?? null;
    $batalhao = $_SESSION['batalhao'] ?? null;
    $unidade = $_SESSION['unidade'] ?? null;
    $cmdo = $_SESSION['cmdo'] ?? null;
    $gcmdo = $_SESSION['gcmdo'] ?? null;
    $codopm = $_SESSION['codopm'] ?? null;
    $situacaoLegal = $_SESSION['situacaoLegal'] ?? null;
    $foto = $_SESSION['foto'] ?? null;
    $fotoBase = $_SESSION['fotoBase'] ?? null;
    $email = $_SESSION['email'] ?? null;
    $telefone = $_SESSION['telefone'] ?? null;
    $funcao = $_SESSION['funcao'] ?? null;
    $funcoes = $_SESSION['funcoes'] ?? null;
    $dataAdmissao = $_SESSION['dataAdmissao'] ?? null;
    $dataNascimento = $_SESSION['dataNascimento'] ?? null;
    $estadoCivil = $_SESSION['estadoCivil'] ?? null;

    // Define as variáveis e valores para substituição
    $variaveis = array(
        "{{usuario}}",
        "{{cpf}}",
        "{{re}}",
        "{{digre}}",
        "{{ptgr}}",
        "{{codptgr}}",
        "{{nome}}",
        "{{guerra}}",
        "{{sexo}}",
        "{{batalhao}}",
        "{{unidade}}",
        "{{cmdo}}",
        "{{gcmdo}}",
        "{{codopm}}",
        "{{situacaoLegal}}",
        "{{foto}}",
        "{{fotoBase}}",
        "{{email}}",
        "{{telefone}}",
        "{{funcao}}",
        "{{dataAdmissao}}",
        "{{dataNascimento}}",
        "{{estadoCivil}}",
        "{{area_dinamica}}",
    );

    $saidas = array(
        $usuario,
        $cpf,
        $re,
        $digre,
        $ptgr,
        $codptgr,
        $nome,
        $guerra,
        $sexo,
        $batalhao,
        $unidade,
        $cmdo,
        $gcmdo,
        $codopm,
        $situacaoLegal,
        $foto,
        $fotoBase,
        $email,
        $telefone,
        $funcao,
        $dataAdmissao,
        $dataNascimento,
        $estadoCivil,
        $saida // Adiciona a variável 'saida' ao final
    );

    // Substitui os placeholders no template com os valores correspondentes
    $tplpronto = str_replace($variaveis, $saidas, $template);

    // Exibe o template pronto
    echo $tplpronto;



    /**
     * Explicação detalhada:
     * 
     * 1. require_once: Inclui os arquivos necessários para o funcionamento do sistema, como classes Core, Model e Controllers.
     * 
     * 2. file_get_contents('app/Template/template.html'): Este comando lê o conteúdo do arquivo template.html que está localizado na pasta app/Template e guarda todo o conteúdo na variável $template.
     * 
     * 3. ob_start(): Inicia o buffer de saída. Isso significa que qualquer saída gerada a partir deste ponto não será enviada diretamente para o navegador ou cliente que fez a requisição HTTP, mas sim será armazenada internamente em um buffer.
     * 
     * 4. $core = new Core;: Cria uma nova instância da classe Core.
     * 
     * 5. $core->start($_GET): Chama o método start da instância $core, passando o array $_GET como argumento. O $_GET é uma superglobal do PHP que contém variáveis passadas para o script via parâmetros de URL (query string).
     * 
     * 6. ob_get_contents(): Obtém o conteúdo do buffer de saída atual, ou seja, tudo o que foi gerado desde o início do buffer até o momento antes desta chamada.
     * 
     * 7. ob_end_clean(): Limpa o buffer de saída e desativa o buffering de saída. Isso significa que o conteúdo que foi armazenado no buffer (com ob_start()) não será enviado para o navegador, mas sim armazenado na variável $saida.
     * 
     * 8. str_replace('{{area_dinamica}}', $saida, $template): Substitui a marcação {{area_dinamica}} no template pelo conteúdo dinâmico gerado e armazenado em $saida, resultando em $str.
     * 
     * 9. echo $str;: Exibe o resultado final no navegador, que é o conteúdo HTML completo com a área dinâmica preenchida.
     * 
     * Resumo:
     * 
     * O código apresentado carrega um template HTML, executa a lógica do sistema (representada pela classe Core e seus métodos) que gera conteúdo dinâmico baseado nos parâmetros recebidos via $_GET, e insere esse conteúdo dinâmico no template. Finalmente, o código exibe o HTML resultante para o usuário final.
     */