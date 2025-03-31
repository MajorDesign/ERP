<?php
require_once '../conexao.php';

header('Content-Type: application/json');

$response = [];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome'] ?? '');

        if (empty($nome)) {
            throw new Exception('O nome da categoria é obrigatório.');
        }

        $stmt = $conn->prepare("INSERT INTO categorias (nome) VALUES (?)");
        $stmt->bind_param("s", $nome);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Categoria adicionada com sucesso!';
        } else {
            throw new Exception('Erro ao adicionar a categoria.');
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