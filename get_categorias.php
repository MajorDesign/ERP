<?php
require_once 'conexao.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT id, nome FROM categorias WHERE status = 1";
    $result = $conn->query($sql);
    
    $categorias = [];
    while($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    }
    
    echo json_encode($categorias);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>