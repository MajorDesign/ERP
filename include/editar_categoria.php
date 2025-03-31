<?php
require_once '../conexao.php';

header('Content-Type: application/json');

$response = [];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');

        if ($id <= 0 || empty($nome)) {
            throw new Exception('Dados inválidos.');
        }

        $stmt = $conn->prepare("UPDATE categorias SET nome = ? WHERE id = ?");
        $stmt->bind_param("si", $nome, $id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Categoria editada com sucesso!';
        } else {
            throw new Exception('Erro ao atualizar a categoria.');
        }
    } else {
        throw new Exception('Método de requisição inválido.');
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>