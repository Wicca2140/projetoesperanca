<?php
require_once 'includes/config.php';

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

// Verificar se os parâmetros foram enviados
if (!isset($_POST['ponto_id']) || !isset($_POST['status'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit();
}

$ponto_id = intval($_POST['ponto_id']);
$status = intval($_POST['status']);

try {
    // Verificar se o ponto pertence ao usuário
    $stmt = $pdo->prepare("SELECT usuario_id FROM pontos_ajuda WHERE ponto_id = ?");
    $stmt->execute([$ponto_id]);
    $ponto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ponto) {
        echo json_encode(['success' => false, 'message' => 'Ponto não encontrado']);
        exit();
    }
    
    if ($ponto['usuario_id'] != $_SESSION['usuario_id']) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['success' => false, 'message' => 'Você não tem permissão para alterar este ponto']);
        exit();
    }
    
    // Atualizar o status do ponto
    $stmt = $pdo->prepare("UPDATE pontos_ajuda SET ativo = ? WHERE ponto_id = ?");
    $stmt->execute([$status, $ponto_id]);
    
    echo json_encode(['success' => true, 'message' => 'Status alterado com sucesso']);
    
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Erro no servidor: ' . $e->getMessage()]);
}
?>