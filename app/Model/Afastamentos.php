<?php

class Afastamento
{

    public static function listar()
    {
        $con = Connection::getConn();

        $sql = "SELECT * 
                FROM copom_afastamentos as af
                JOIN copom_tipo_afastamento as ta
                ON af.cod_afastamento = ta.cod_afastamento
                JOIN copom_efetivo as co
                ON af.re = co.re
                ORDER BY inicio ASC";

        $stmt = $con->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function gravarAfastamento($dados)
    {
        $con = Connection::getConn();

        $sql = "INSERT INTO copom_afastamentos (re, cod_afastamento, dias, inicio, termino)
                VALUES (:re, :afastamento, :dias, :inicio, :termino)";
        
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':re', $dados['re']);
        $stmt->bindValue(':afastamento', $dados['afastamento']);
        $stmt->bindValue(':dias', $dados['dias']);
        $stmt->bindValue(':inicio', $dados['inicio'] . ' 00:00:00');
        $stmt->bindValue(':termino', $dados['termino'] . ' 00:00:00');

        $stmt->execute();

        return $con->lastInsertId(); // Opcional
    }


    public static function tiposAfastamentos()
    {
        $con = Connection::getConn();

        $sql = "SELECT * FROM copom_tipo_afastamento;";
        $stmt = $con->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
