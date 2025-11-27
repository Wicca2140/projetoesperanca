<?php
require_once 'includes/config.php';

// Redirecionar se já estiver logado
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);
    $senha = $_POST['senha'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($senha, $user['senha_hash'])) {
            if ($user['status'] == 1) {
                $_SESSION['usuario_id'] = $user['usuario_id'];
                $_SESSION['usuario'] = $user['usuario'];
                $_SESSION['nivel_acesso'] = $user['nivel_acesso_id'];
                
                // Atualizar último login
                $stmt = $pdo->prepare("UPDATE usuarios SET dt_atualizacao = NOW() WHERE usuario_id = ?");
                $stmt->execute([$user['usuario_id']]);
                
                header("Location: index.php");
                exit();
            } else {
                $erro = "Sua conta está inativa. Entre em contato com o administrador.";
            }
        } else {
            $erro = "Usuário ou senha incorretos.";
        }
    } catch (PDOException $e) {
        $erro = "Erro ao processar login: " . $e->getMessage();
    }
}

$pagina_titulo = "Login";
require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
        
        <?php if ($erro): ?>
            <div class="alert alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <form method="post" action="login.php" class="auth-form">
            <div class="form-group">
                <label for="usuario">Usuário:</label>
                <div class="input-with-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="usuario" name="usuario" required placeholder="Digite seu usuário">
                </div>
            </div>
            
            <div class="form-group">
                <label for="senha">Senha:</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="senha" name="senha" required placeholder="Digite sua senha">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full"><i class="fas fa-sign-in-alt"></i> Entrar</button>
        </form>
        
        <div class="auth-links">
            <p>Não tem uma conta? <a href="registro.php">Registre-se aqui</a></p>
            <p><a href="recuperar-senha.php">Esqueceu sua senha?</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>