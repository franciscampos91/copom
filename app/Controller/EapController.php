<?php


class EapController
{

    public function index()
    {
        try {

            $loader = new \Twig\Loader\FilesystemLoader('app/View/eap');
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('home.html');


            $parametros = array();

         
            //consulta pm sirh


            //consulta medalhas

            $conteudo = $template->render($parametros);

            echo $conteudo;


        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }


    public function turmas()
    {
        try {

            $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('eap/turmas.html');


            $parametros = [
                'msg' => $_SESSION['msg'] ?? null,
                'msgText' => $_SESSION['msgText'] ?? null
            ];
            unset($_SESSION['msg'], $_SESSION['msgText']);
    
            $conteudo = $template->render($parametros);

            echo $conteudo;


        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }


    public function removerTurma()
    {
        $id = $_POST['id'];

        //função para remover a turma



        //retornar para página turmas


        $_SESSION['msg'] = 'success';
        $_SESSION['msgText'] = 'Turma excluída com sucesso.';

        header('location: ?pagina=eap&metodo=turmas');

    }


    public function editarTurma()
    {
        $id = $_POST['id'];

        //função para remover a turma



        //retornar para página turmas


        $_SESSION['msg'] = 'attention';
        $_SESSION['msgText'] = 'Turma editada com sucesso.';

        header('location: ?pagina=eap&metodo=turmas');

    }

    public function incluirTurma()
    {
   
        //função para incluir a turma



        //retornar para página turmas


        $_SESSION['msg'] = 'error';
        $_SESSION['msgText'] = 'Turma incluída com sucesso.';

        header('location: ?pagina=eap&metodo=turmas');



    }

    public function turma()
    {  
        $id = $_GET['id'];

        
        $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
        $twig = new \Twig\Environment($loader);
        $template = $twig->load('eap/visualizar-turma.html');


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

            unset($_SESSION['parametros']);

         }

        $parametros['turma'] = 'EAP Cb/Sd PM I/25';
        $parametros['tipo'] = 'Cb/Sd PM';
        $parametros['periodo'] = '15-03-2025 a 18-03-2025';
        $parametros['discentes'] = '21';


        $conteudo = $template->render($parametros);

        echo $conteudo;
        
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
            ];

             $_SESSION['parametros'] = $parametros;

        } else {
            
            $_SESSION['msg'] = "error";
            $_SESSION['msgText'] = "Policial não localizado.";
          
            
        }


        header('location: ?pagina=eap&metodo=turma&id='.$turma);

 

    }

    public function incluirPMTurma()
    {
       // $id_turma = $_POST['id_turma'];
        $id_turma = 1;
        //função para incluir o pm na turma



        //retornar para página turma + id_turma


        $_SESSION['msg'] = 'success';
        $_SESSION['msgText'] = 'Policial incluído na turma.';

        header('location: ?pagina=eap&metodo=turma&id='. $id_turma);
    }


    public function removerPMTurma()
    {
        $id= $_POST['id_turma'];
        $id = 1;
        



        //retornar para página turma

        $_SESSION['msg'] = 'success';
        $_SESSION['msgText'] = 'Policial excluído da turma.';

        header('location: ?pagina=eap&metodo=turma&id='. $id);
    }

    public function alterarIAS()
    {
        $id= $_POST['id_turma'];

        $id_discente = $_POST['id_discente'];
        



        //retornar para página turma

        $_SESSION['msg'] = 'success';
        $_SESSION['msgText'] = 'Data da IAS alterada.';

        header('location: ?pagina=eap&metodo=turma&id='. $id);
    }
    
}