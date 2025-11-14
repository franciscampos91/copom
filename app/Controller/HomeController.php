<?php

    class HomeController
    {
        public function index()
        {
            try {

                $loader = new \Twig\Loader\FilesystemLoader('app/View');
                $twig = new \Twig\Environment($loader);
                $template = $twig->load('home.html');

                $parametros = array();
                $parametros['aniversariantes'] = Efetivo::aniversarianteMes();
                $parametros['ferias']          = Efetivo::feriasMes();
                $parametros['efetivo']         = Efetivo::efetivoTotal();

                $conteudo = $template->render($parametros);

                echo $conteudo;


            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        }
    }