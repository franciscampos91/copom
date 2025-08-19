<?php

class TrocaController
{

    public function index()
    {
        $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
        $twig = new \Twig\Environment($loader);
        $template = $twig->load('troca/home.html');

        $parametros = array();

        $parametros['efetivo'] = Efetivo::listarEfetivo();
        $parametros['trocas']  = Troca::listarTrocas();

        $conteudo = $template->render($parametros);

        echo $conteudo;
    }


     public function gravarTroca()
    {
        if (Troca::gravarTroca($_POST)) {
            $_SESSION['msg'] = "success";
            $_SESSION['msgText'] = "Troca registrada com sucesso.";
        } else {
            $_SESSION['msg'] = "error";
            $_SESSION['msgText'] = "Erro ao registrar a troca.";
        }

        header('location: ?pagina=troca');
        exit;
    }



}