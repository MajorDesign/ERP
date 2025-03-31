<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$dbname = "banco_projeto";

try {
    // Conexão com MySQLi
    $conn = new mysqli($servidor, $usuario, $senha, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Falha na conexão: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");
} catch (Exception $e) {
    error_log("Erro de conexão: " . $e->getMessage());
    die("Erro ao conectar com o banco de dados");
}

try {
    // Criando a conexão com PDO e definindo o banco de dados
    $pdo = new PDO("mysql:host=$servidor;dbname=$dbname;charset=utf8", $usuario, $senha);

    // Configurando o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>