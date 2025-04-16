<?php

class Efetivo
{


    public static function listarEfetivo()
    {
        $con = Connection::getConn();

        $sql = "SELECT * FROM copom.copom_efetivo";
        $stmt = $con->prepare($sql);
        $stmt->execute();

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;

    }


    public static function incluirEfetivo($dados)
    {
        $con = Connection::getConn();

        // Removendo máscaras
        $dados['cpf'] = preg_replace('/\D/', '', $dados['cpf']);
        $dados['telefone'] = preg_replace('/\D/', '', $dados['telefone']);


        // Tratando campo de data vazia
        $dados['saida_copom'] = empty($dados['saida_copom']) ? null : $dados['saida_copom'];
        $dados['chegada_copom'] = empty($dados['chegada_copom']) ? null : $dados['chegada_copom'];

        
        $sql = "INSERT INTO copom_efetivo (
                    pt_gr, re, dg_re, nome, guerra, opm, codopm, email, telefone,
                    cpf, rg, endereco, municipio, funcao_copom, equipe,
                    chegada_copom, saida_copom, situacao
                ) VALUES (
                    :pt_gr, :re, :dg_re, :nome, :guerra, :opm, :codopm, :email, :telefone,
                    :cpf, :rg, :endereco, :municipio, :funcao_copom, :equipe,
                    :chegada_copom, :saida_copom, :situacao
                )";
    
        $stmt = $con->prepare($sql);
    
        $stmt->bindValue(':pt_gr',         $dados['ptgr']);
        $stmt->bindValue(':re',            $dados['re']);
        $stmt->bindValue(':dg_re',         $dados['dgre']);
        $stmt->bindValue(':nome',          $dados['nome']);
        $stmt->bindValue(':guerra',        $dados['guerra']);
        $stmt->bindValue(':opm',           $dados['opm']);
        $stmt->bindValue(':codopm',        $dados['codopm']);
        $stmt->bindValue(':email',         $dados['email']);
        $stmt->bindValue(':telefone',      $dados['telefone']);
        $stmt->bindValue(':cpf',           $dados['cpf']);
        $stmt->bindValue(':rg',            $dados['rg']);
        $stmt->bindValue(':endereco',      $dados['endereco']);
        $stmt->bindValue(':municipio',     $dados['municipio']);
        $stmt->bindValue(':funcao_copom',  $dados['funcao']);
        $stmt->bindValue(':equipe',        $dados['equipe']);
        $stmt->bindValue(':chegada_copom', $dados['chegada_copom']);
        $stmt->bindValue(':saida_copom',   $dados['saida_copom']);
        $stmt->bindValue(':situacao',      $dados['situacao']);
    
        return $stmt->execute();
    }


    public static function uploadFoto($dados)
    {
        // Verifica e salva a imagem se vier no campo 'foto'
        if (!empty($dados['foto'])) {
            // Extrai apenas o conteúdo base64 (caso venha com prefixo data:image/png;base64,...)
            if (strpos($dados['foto'], 'base64') !== false) {
                $base64 = explode(',', $dados['foto'])[1]; // Pega só a parte base64 após a vírgula
            } else {
                $base64 = $dados['foto'];
            }

            $imgData = base64_decode($base64);
            $re = preg_replace('/\D/', '', $dados['re']); // garante que o nome do arquivo seja só números
            $caminho = 'public/assets/foto-efetivo/' . $re . '.png';

            // Salva o arquivo
            file_put_contents($caminho, $imgData);
        }
    }
    


    public static function buscaEfetivo($re)
    {
        $con = Connection::getConn();
    
        $sql = "SELECT * FROM copom_efetivo WHERE re = :re LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':re', $re);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}