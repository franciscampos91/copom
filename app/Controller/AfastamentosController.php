<?php

class AfastamentosController
{

    public function index()
    {
        $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
        $twig = new \Twig\Environment($loader);
        $template = $twig->load('afastamentos/home.html');
        
        $parametros = array();

        $parametros['afastamentos'] = Afastamento::listar();

        $conteudo = $template->render($parametros);

        echo $conteudo;
    }


    public function gravarAfastamento()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            try {
                Afastamento::gravarAfastamento($_POST);
                echo "<div class='alert alert-success'>Afastamento salvo com sucesso.</div>";
                // Redirecionar ou carregar view
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>Erro ao salvar afastamento: " . $e->getMessage() . "</div>";
            }
        }
    }
}