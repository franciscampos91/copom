<?php

class Dejem
{


public static function DejemDiurna($data)
{
    $con = Connection::getConn();

    $inicio = $data . " 06:00:00";
    $fim    = $data . " 18:00:00";

    $sql = "SELECT *
            FROM copom_dejem
            WHERE (
                (dejem_data >= :inicio AND dejem_data < :fim)
                OR
                (dejem_termino > :inicio AND dejem_termino < :fim)
            )";

    $stmt = $con->prepare($sql);
    $stmt->bindValue(':inicio', $inicio);
    $stmt->bindValue(':fim', $fim);
    $stmt->execute();


    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function DejemNoturna($data)
{
    $con = Connection::getConn();

    $inicio = $data . " 18:00:00";

    // Calcula o dia seguinte
    $proximoDia = date('Y-m-d', strtotime($data . ' +1 day'));
    $fim = $proximoDia . " 06:00:00";

    $sql = "SELECT *
            FROM copom_dejem
            WHERE (
                (dejem_data >= :inicio AND dejem_data < :fim)
                OR
                (dejem_termino >= :inicio AND dejem_termino < :fim)
            )";

    $stmt = $con->prepare($sql);
    $stmt->bindValue(':inicio', $inicio);
    $stmt->bindValue(':fim', $fim);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



}