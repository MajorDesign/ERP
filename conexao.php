<?php
// Configurações do banco de dados
$host = "localhost";  // Servidor do banco (normalmente "localhost")
$dbname = "banco_projeto";  // Nome do banco de dados (substitua pelo nome correto)
$usuario = "root";  // Usuário do banco de dados
$senha = "";  // Senha do banco de dados

try {
    // Criando a conexão com PDO e definindo o banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $senha);

    // Configurando o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>