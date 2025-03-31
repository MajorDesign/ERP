<?php
require_once '../conexao.php';

header('Content-Type: application/json');

$response = [];

try {
    // Verifica se o ID foi enviado via POST
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        throw new Exception('ID inválido.');
    }

    $id = intval($_POST['id']);

    // Prepara a consulta para excluir a categoria
    $stmt = $conn->prepare("DELETE FROM categorias WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Categoria excluída com sucesso!';
    } else {
        throw new Exception('Erro ao excluir a categoria.');
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Retorna a resposta como JSON
echo json_encode($response);
?>