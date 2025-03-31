<?php
require_once '../conexao.php';

header('Content-Type: application/json');

try {
    $queryCategorias = "SELECT id, nome FROM categorias ORDER BY nome ASC";
    $resultCategorias = $conn->query($queryCategorias);

    $categorias = [];
    if ($resultCategorias && $resultCategorias->num_rows > 0) {
        while ($row = $resultCategorias->fetch_assoc()) {
            $categorias[] = $row;
        }
    }

    echo json_encode($categorias);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar categorias']);
}
?>