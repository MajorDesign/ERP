<?php
require_once 'conexao.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, nome FROM categorias WHERE status = 1 ORDER BY nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categorias);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}