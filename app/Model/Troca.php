<?php

class Troca
{


    public static function gravarTroca($dados)
    {
        $con = Connection::getConn();

        $criado = date('Y-m-d H:i:s');
        $atualizado = date('Y-m-d H:i:s');

        $sql = "INSERT INTO copom_troca_servico 
                   (re_troca, re_compensa, data_servico, data_compensacao, tipo_troca, observacao, turno_compensa, turno_troca, criado_em, atualizado_em) 
                VALUES 
                   (:re_troca, :re_compensa, :data_servico, :data_compensacao, :tipo_troca, :observacao, :turno_compensa, :turno_troca, :criado_em, :atualizado_em)";

        $stmt = $con->prepare($sql);

        $stmt->bindValue(':re_troca', $dados['interessado']);
        $stmt->bindValue(':re_compensa', $dados['compensacao'] ? $dados['compensacao'] : '0');
        $stmt->bindValue(':data_servico', $dados['data-interessado']);
        $stmt->bindValue(':data_compensacao', $dados['data-compensacao']);
        $stmt->bindValue(':turno_troca', $dados['turno-folga']);
        $stmt->bindValue(':turno_compensa', $dados['turno-compensacao']);
        $stmt->bindValue(':tipo_troca', isset($dados['tipo_troca']) ? $dados['tipo_troca'] : 'SERVICO');
        $stmt->bindValue(':observacao', isset($dados['observacao']) ? $dados['observacao'] : null);
        $stmt->bindValue(':criado_em', $criado);
        $stmt->bindValue(':atualizado_em', $atualizado);


        return $stmt->execute();
    }


    public static function listarTrocas()
    {
        $con = Connection::getConn();

        $sql = "SELECT t.*, 
                       i.nome AS interessado_nome, 
                       c.nome AS compensacao_nome,
                       c.guerra AS compensacao_guerra,
                       i.guerra AS interessado_guerra,
                       c.pt_gr AS compensacao_pt_gr,
                       i.pt_gr AS interessado_pt_gr
                FROM copom_troca_servico t
                LEFT JOIN copom_efetivo i ON t.re_troca = i.re
                LEFT JOIN copom_efetivo c ON t.re_compensa = c.re
                ORDER BY t.criado_em DESC";

        $stmt = $con->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}