<?php
require_once '../includes/config.php';

// Verificar se é admin
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $cidade_id = $_POST['cidade_id'] ?? null;
    $cidade_nome = trim($_POST['cidade_nome']);
    $uf_id = (int)$_POST['uf_id'];
    
    // Validações adicionais
    if (empty($cidade_nome) || empty($uf_id)) {
        $_SESSION['erro'] = "Todos os campos são obrigatórios.";
        header("Location: ../admin.php?pagina=cidades");
        exit();
    }
    
    try {
        if ($action == 'add_city') {
            // Verificar se cidade já existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM cidades WHERE cidade_nome = ? AND uf_id = ?");
            $stmt->execute([$cidade_nome, $uf_id]);
            
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['erro'] = "Esta cidade já está cadastrada neste estado.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO cidades (cidade_nome, uf_id) VALUES (?, ?)");
                $stmt->execute([$cidade_nome, $uf_id]);
                $_SESSION['sucesso'] = "Cidade cadastrada com sucesso!";
                
                // Log da ação
                logAction("Cidade adicionada: {$cidade_nome}");
            }
        } elseif ($action == 'edit_city' && $cidade_id) {
            // Verificar se cidade já existe (excluindo a atual)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM cidades WHERE cidade_nome = ? AND uf_id = ? AND cidade_id != ?");
            $stmt->execute([$cidade_nome, $uf_id, $cidade_id]);
            
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['erro'] = "Já existe outra cidade com este nome neste estado.";
            } else {
                $stmt = $pdo->prepare("UPDATE cidades SET cidade_nome = ?, uf_id = ?, dt_atualizacao = NOW() WHERE cidade_id = ?");
                $stmt->execute([$cidade_nome, $uf_id, $cidade_id]);
                $_SESSION['sucesso'] = "Cidade atualizada com sucesso!";
                
                // Log da ação
                logAction("Cidade atualizada: {$cidade_nome} (ID: {$cidade_id})");
            }
        }
    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao processar solicitação: " . $e->getMessage();
        
        // Log do erro
        error_log("Erro em cidades-action.php: " . $e->getMessage());
    }
}

header("Location: ../admin.php?pagina=cidades");
exit();

// Função para registrar ações no log
function logAction($action) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO logs (usuario_id, acao, dt_criacao) VALUES (?, ?, NOW())");
        $stmt->execute([$_SESSION['usuario_id'], $action]);
    } catch (PDOException $e) {
        error_log("Erro ao registrar log: " . $e->getMessage());
    }
}
?>