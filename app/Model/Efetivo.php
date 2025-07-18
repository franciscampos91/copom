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


    public static function buscaChefeCopom()
    {
        $con = Connection::getConn();
    
        $sql = "SELECT * FROM copom_efetivo WHERE funcao_copom = 'Chefe' LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function efetivoDashboard()
    {
        $con = Connection::getConn();

        $sql = "SELECT
                    COUNT(*) AS efetivo_total,
                    SUM(CASE WHEN situacao = 'Agregado' THEN 1 ELSE 0 END) AS efetivo_agregado,
                    SUM(CASE WHEN funcao_copom = 'Supervisor' AND situacao IN ('Ativo', 'Agregado') THEN 1 ELSE 0 END) AS supervisores,
                    SUM(CASE WHEN funcao_copom = 'Atendente 190' AND situacao IN ('Ativo', 'Agregado') THEN 1 ELSE 0 END) AS atendentes,
                    SUM(CASE WHEN funcao_copom = 'Adm' AND situacao IN ('Ativo', 'Agregado') THEN 1 ELSE 0 END) AS adm,
                    SUM(CASE WHEN funcao_copom = 'Chefe' AND situacao IN ('Ativo', 'Agregado') THEN 1 ELSE 0 END) AS chefe,
                    SUM(CASE WHEN funcao_copom = '' AND situacao IN ('Ativo', 'Agregado') THEN 1 ELSE 0 END) AS sem_funcao,
                    SUM(CASE WHEN funcao_copom = 'Despachador' AND situacao IN ('Ativo', 'Agregado') THEN 1 ELSE 0 END) AS despachadores
                FROM copom_efetivo
                WHERE situacao IN ('Ativo', 'Agregado');";

        $stmt = $con->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }


    public static function editarPM($dados)
    {
        $pdo = Connection::getConn();

        $data_nascimento = !empty($dados['data_nascimento']) ? $dados['data_nascimento'] : null;
        $data_admissao = !empty($dados['data_admissao']) ? $dados['data_admissao'] : null;
        $chegada_copom = !empty($dados['chegada_copom']) ? $dados['chegada_copom'] : null;
        $saida_copom = !empty($dados['saida_copom']) ? $dados['saida_copom'] : null;

        $sql = "UPDATE copom_efetivo SET 
                    pt_gr = :pt_gr,
                    dg_re = :dg_re,
                    nome = :nome,
                    guerra = :guerra,
                    opm = :opm,
                    codopm = :codopm,
                    email = :email,
                    telefone = :telefone,
                    cpf = :cpf,
                    rg = :rg,
                    data_nascimento = :data_nascimento,
                    data_admissao = :data_admissao,
                    endereco = :endereco,
                    municipio = :municipio,
                    funcao_copom = :funcao_copom,
                    equipe = :equipe,
                    chegada_copom = :chegada_copom,
                    saida_copom = :saida_copom,
                    situacao = :situacao
                WHERE id_efetivo = :id_efetivo";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
                                ':pt_gr' => $dados['ptgr'],
                                ':dg_re' => $dados['dgre'],
                                ':nome' => $dados['nome'],
                                ':guerra' => $dados['guerra'],
                                ':opm' => $dados['opm'],
                                ':codopm' => $dados['codopm'],
                                ':email' => $dados['email'],
                                ':telefone' => $dados['telefone'],
                                ':cpf' => $dados['cpf'],
                                ':rg' => $dados['rg'],
                                ':data_nascimento' => $data_nascimento,
                                ':data_admissao' => $data_admissao,
                                ':endereco' => $dados['endereco'],
                                ':municipio' => $dados['municipio'],
                                ':funcao_copom' => $dados['funcao'],
                                ':equipe' => $dados['equipe'],
                                ':chegada_copom' => $chegada_copom,
                                ':saida_copom' => $saida_copom,
                                ':situacao' => $dados['situacao'],
                                ':id_efetivo' => $dados['id_efetivo'],
                            ]);
    }


    public static function buscaAdm()
    {
        $con = Connection::getConn();
    
        $sql = "SELECT * FROM copom_efetivo WHERE funcao_copom = 'adm'";
        $stmt = $con->prepare($sql);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function consultaPorEquipe($equipe)
    {
        $con = Connection::getConn();
    
        $sql = "SELECT * FROM copom_efetivo WHERE equipe = :equipe";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':equipe', $equipe);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}