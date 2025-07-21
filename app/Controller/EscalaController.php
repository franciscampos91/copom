<?php

class EscalaController
{
    public function index()
    {
        $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
        $twig = new \Twig\Environment($loader);
        $template = $twig->load('escala/home.html');



        // Pega a data da query ou usa a data atual
        $dataConsulta = isset($_GET['data']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['data'])
            ? $_GET['data']
            : date('Y-m-d');

        // Carrega config
        $configPath = __DIR__ . '/../config/escala.json';
        if (!file_exists($configPath)) {
            die('Arquivo de configuração não encontrado.');
        }

        $config = json_decode(file_get_contents($configPath), true);
        if (!$config || !isset($config['data_referencia'])) {
            die('Erro na configuração da escala.');
        }

        $dataRef = new DateTime($config['data_referencia']);
        $dataAtual = new DateTime($dataConsulta);
        $diasPassados = $dataRef->diff($dataAtual)->days;

        $escalaDia = $config['dia'];
        $escalaNoite = $config['noite'];

        $equipeDia = $escalaDia[$diasPassados % count($escalaDia)];
        $equipeNoite = $escalaNoite[$diasPassados % count($escalaNoite)];

        // Folga = todas as equipes menos as que estão em serviço
        $todasEquipes = array_unique(array_merge($escalaDia, $escalaNoite));
        $emFolga = array_diff($todasEquipes, [$equipeDia, $equipeNoite]);

        // Exibição
        /*echo "<h2>Escala do dia {$dataConsulta}</h2>";

        echo "<h3>✅ Serviço de Dia:</h3>";
        echo $equipeDia;

        echo "<h3>🌙 Serviço de Noite:</h3>";
        echo $equipeNoite;

        echo "<h3>🛌 Em Folga:</h3>";
        echo implode(', ', $emFolga);*/

        $parametros = array();

        $parametros['dataConsulta'] = $dataConsulta;
        $parametros['equipeDia']    = $equipeDia;
        $parametros['equipeNoite']  = $equipeNoite;
        $parametros['emFolga']      = $emFolga;


        $parametros['efetivoDia']   = Efetivo::consultaPorEquipe($equipeDia);
        $parametros['efetivoNoite'] = Efetivo::consultaPorEquipe($equipeNoite);


        $efetivoPorEquipe = Efetivo::listarEfetivoPorEquipe($dataConsulta); // ou data dinâmica
        $parametros['efetivoPorEquipe'] = $efetivoPorEquipe;

     //   var_dump($parametros['efetivoPorEquipe']);

        
        $conteudo = $template->render($parametros);

        echo $conteudo;
    }
}
