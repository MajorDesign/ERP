<?php
require_once 'conexao.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];

    // Prepare a SQL statement to insert the new category
    $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (:nome)");
    $stmt->bindParam(':nome', $nome);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'nome' => $nome]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>