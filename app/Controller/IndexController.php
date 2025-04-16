<?php

Class Indexcontroller {


    public function index()
    {
        try {

            $loader = new \Twig\Loader\FilesystemLoader('app/View');
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('index.html');

            $conteudo = $template->render();

            echo $conteudo;


        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }



}