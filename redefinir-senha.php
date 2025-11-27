<?php
require_once 'includes/config.php';

// Redirecionar se já estiver logado
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$erro = "";
$sucesso = "";

// Verificar se token é válido
$token = $_GET['token'] ?? '';
if (!$token) {
    header("Location: login.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE reset_token = ? AND reset_expira > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $erro = "Token inválido ou expirado. Solicite um novo link de recuperação.";
    }
} catch (PDOException $e) {
    $erro = "Erro ao verificar token: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$erro) {
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    if (strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres.";
    } elseif ($senha !== $confirmar_senha) {
        $erro = "As senhas não coincidem.";
    } else {
        try {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE usuarios SET senha_hash = ?, reset_token = NULL, reset_expira = NULL, dt_atualizacao = NOW() WHERE usuario_id = ?");
            $stmt->execute([$senha_hash, $user['usuario_id']]);
            
            $sucesso = "Senha redefinida com sucesso! Você já pode fazer login.";
        } catch (PDOException $e) {
            $erro = "Erro ao redefinir senha: " . $e->getMessage();
        }
    }
}

$pagina_titulo = "Redefinir Senha";
require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2><i class="fas fa-key"></i> Redefinir Senha</h2>
        
        <?php if ($erro): ?>
            <div class="alert alert-error"><?php echo $erro; ?></div>
            <?php if (strpos($erro, 'Token inválido') !== false): ?>
                <div class="text-center">
                    <a href="recuperar-senha.php" class="btn btn-primary">Solicitar Novo Link</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?php echo $sucesso; ?></div>
            <div class="text-center">
                <a href="login.php" class="btn btn-primary">Fazer Login</a>
            </div>
        <?php else: ?>
            <?php if (!$erro): ?>
                <div class="alert alert-info">
                    <p>Redefinindo senha para: <strong><?php echo htmlspecialchars($user['email']); ?></strong></p>
                </div>
                
                <form method="post" action="redefinir-senha.php?token=<?php echo htmlspecialchars($token); ?>" class="auth-form">
                    <div class="form-group">
                        <label for="senha">Nova Senha:</label>
                        <input type="password" id="senha" name="senha" required minlength="6" placeholder="Mínimo 6 caracteres">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Nova Senha:</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required placeholder="Digite a senha novamente">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full"><i class="fas fa-save"></i> Redefinir Senha</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="auth-links">
            <p><a href="login.php">Voltar para o login</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>