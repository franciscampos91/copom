<?php


class EfetivoController
{


    public function index()
    {

        try {


            $parametros = array();

            $parametros = [
                'msg' => $_SESSION['msg'] ?? null,
                'msgText' => $_SESSION['msgText'] ?? null
            ];
            unset($_SESSION['msg'], $_SESSION['msgText']);

            //verificar se existem parametros de consulta pm
            if (isset($_SESSION['parametros'])) {

                $parametros['retornoConsulta'] = true;
                $parametros['foto'] = $_SESSION['parametros']['foto'];
                $parametros['ptgr'] = $_SESSION['parametros']['ptgr'];
                $parametros['re'] = $_SESSION['parametros']['re'];
                $parametros['dgre'] = $_SESSION['parametros']['dgre'];
                $parametros['nome'] = $_SESSION['parametros']['nome'];
                $parametros['guerra'] = $_SESSION['parametros']['guerra'];
                $parametros['email'] = $_SESSION['parametros']['email'];
                $parametros['opm'] = $_SESSION['parametros']['opm'];
                $parametros['funcao'] = $_SESSION['parametros']['funcao'];
                $parametros['codopm'] = $_SESSION['parametros']['codopm'];
                $parametros['cpf'] = $_SESSION['parametros']['cpf'];
                $parametros['rg'] = $_SESSION['parametros']['rg'];
                $parametros['sexo'] = $_SESSION['parametros']['sexo'];
                $parametros['data_nascimento'] = $_SESSION['parametros']['dataNascimento'];
                $parametros['data_admissao'] = $_SESSION['parametros']['dataAdmissao'];



                unset($_SESSION['parametros']);

            }
            

            

            $parametros['efetivo'] = Efetivo::listarEfetivo();


            $parametros['dashboard_efetivo'] = Efetivo::efetivoDashboard();

            // Inicializa o Twig
            $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('efetivo/efetivo.html');

      
            $conteudo = $template->render($parametros);

            echo $conteudo;
        } catch (Exception $ex) {
            echo "Erro: " . $ex->getMessage();
        }


    }

    public function buscaPMInsercao()
    {

        
        $re = $_POST['busca-re'];
        $turma = 2;
        
        $policial = PolicialController::buscaPM($re);
        
        if ($policial) {
            $parametros = [
                'foto' => $policial->fotoBase,
                're' => $policial->re,
                'nome' => $policial->nome,
                'guerra' => $policial->guerra,
                'email' => $policial->email,
                'opm' => $policial->opm,
                'funcao' => $policial->funcao,
                'ptgr' => $policial->ptgr,
                'dgre' => $policial->dgre,
                'codopm' => $policial->codopm,
                'cpf' => $policial->cpf,
                'rg' => $policial->rg,
                'sexo' => $policial->sexo,
                'dataNascimento' => $policial->dataNascimento,
                'dataAdmissao' => $policial->dataAdmissao
            ];

             $_SESSION['parametros'] = $parametros;

        } else {
            
            $_SESSION['msg'] = "error";
            $_SESSION['msgText'] = "Policial não localizado.";
          
            
        }


        header('location: ?pagina=efetivo');

 

    }


    public function incluirEfetivo()
    {
        //verificar se já cadastrado
        if (Efetivo::buscaEfetivo($_POST['re'])) {
            $_SESSION['msg'] = "attention";
            $_SESSION['msgText'] = "Policial já cadastrado.";
            header('location: ?pagina=efetivo');
            exit;
        }

        $resultado = Efetivo::incluirEfetivo($_POST);

        if ($resultado) {

            //salvar foto
            Efetivo::uploadFoto($_POST);

            $_SESSION['msg'] = "success";
            $_SESSION['msgText'] = "Cadastrado com sucesso.";  
        } else {            
            $_SESSION['msg'] = "error";
            $_SESSION['msgText'] = "Erro cadastrar.";                     
        }

        header('location: ?pagina=efetivo');

    }


    public function editarPM()
    {
        if (Efetivo::editarPM($_POST)) {
            $_SESSION['msg'] = "success";
            $_SESSION['msgText'] = "Alterado com sucesso.";  
        } else {
            // Falha - redirecionar ou retornar erro
            echo "Erro ao atualizar o efetivo.";
            $_SESSION['msg'] = "error";
            $_SESSION['msgText'] = "Erro ao atualizar.";
        }

        header('location: ?pagina=efetivo');
        exit;

    }



    public function quadro()
    {

        try {

            $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('efetivo/quadro.html');

            $parametros = array();

            
            $parametros['efetivo'] = Efetivo::listarEfetivo();
            $parametros['dashboard_efetivo'] = Efetivo::efetivoDashboard();

            $parametros['chefe'] = Efetivo::buscaChefeCopom();

            
            $parametros['adm']   = Efetivo::buscaAdm();
            
      
            $conteudo = $template->render($parametros);

            echo $conteudo;
        } catch (Exception $ex) {
            echo "Erro: " . $ex->getMessage();
        }

    }

    public function atualizaAniversario()
    {
       

        $efetivo = Efetivo::listarEfetivo();


        foreach($efetivo as $pm) {
            echo $pm['re'] . '<br>';


            $re = $pm['re'];
            $id = $pm['id_efetivo'];

            $policial = PolicialController::buscaPM($re);


            $datanascimento = $policial->dataNascimento;
            $dataAdmissao = $policial->dataAdmissao;
            $rg = $policial->rg;


            $pm['re'] = $re;
            $pm['foto'] = $policial->fotoBase;


           /* Efetivo::atualizaDataFoto($id, $datanascimento, $dataAdmissao, $rg);*/


           Efetivo::uploadFoto($pm);



        }

    }


    public function quadroEfetivo()
    {
        $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
        $twig = new \Twig\Environment($loader);
        $template = $twig->load('efetivo/quadro-efetivo.html');



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

        $parametros = array();

        $parametros['dataConsulta'] = $dataConsulta;
        $parametros['equipeDia']    = $equipeDia;
        $parametros['equipeNoite']  = $equipeNoite;
        $parametros['emFolga']      = $emFolga;


        $parametros['efetivoDia']   = Efetivo::consultaPorEquipe($equipeDia);
        $parametros['efetivoNoite'] = Efetivo::consultaPorEquipe($equipeNoite);


        $efetivoPorEquipe = Efetivo::listarEfetivoPorEquipe($dataConsulta); // ou data dinâmica
        $parametros['efetivoPorEquipe'] = $efetivoPorEquipe;

        $parametros['chefe'] = Efetivo::buscaChefeCopom();            
        $parametros['adm']   = Efetivo::buscaAdm();
        $parametros['efetivo'] = Efetivo::listarEfetivo();


        $conteudo = $template->render($parametros);

        echo $conteudo;
    }



}