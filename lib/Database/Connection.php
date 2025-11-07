<?php

// Definindo uma classe abstrata chamada Connection
abstract class Connection
{
    // Propriedade estática privada para armazenar a conexão com o banco de dados
    private static $conn;

    // Método estático público para obter a conexão com o banco de dados
    public static function getConn()
    {
        // Verifica se a conexão ainda não foi estabelecida
        if (self::$conn == null) {
            try {
                // Cria uma nova conexão PDO com o banco de dados MySQL
                self::$conn = new PDO(
                    'mysql:host=10.36.177.136;dbname=copom;charset=utf8mb4', 
                    'policial', 
                    'cpi7@cpi7'
                );

                // Configurar PDO para lançar exceções em caso de erro
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die('Erro de conexão: ' . $e->getMessage());
            }
        }

        // Retorna a conexão com o banco de dados
        return self::$conn;
    }
}
?>
