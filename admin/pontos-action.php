<?php
// pontos-action.php
session_start();
require_once 'includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso_id'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'approve_point':
            $pointId = $_POST['point_id'] ?? 0;
            
            if ($pointId) {
                $stmt = $pdo->prepare("UPDATE pontos_ajuda SET ativo = 1 WHERE ponto_id = ?");
                $stmt->execute([$pointId]);
                
                echo json_encode(['success' => true, 'message' => 'Ponto aprovado com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID do ponto não informado']);
            }
            break;
            
        case 'reject_point':
            $pointId = $_POST['point_id'] ?? 0;
            $reason = $_POST['reason'] ?? '';
            
            if ($pointId) {
                $stmt = $pdo->prepare("DELETE FROM pontos_ajuda WHERE ponto_id = ?");
                $stmt->execute([$pointId]);
                
                echo json_encode(['success' => true, 'message' => 'Ponto reprovado com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID do ponto não informado']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
    }
} catch (PDOException $e) {
    error_log("Erro ao processar ação: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>