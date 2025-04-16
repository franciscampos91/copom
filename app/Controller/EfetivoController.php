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


                unset($_SESSION['parametros']);

            }
            

            

            $parametros['efetivo'] = Efetivo::listarEfetivo();



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
                'sexo' => $policial->sexo
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



}