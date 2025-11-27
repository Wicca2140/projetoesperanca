<?php
require_once 'includes/config.php';

// Redirecionar se já estiver logado
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $email = trim($_POST['email']);
    $nome_completo = trim($_POST['nome_completo']);
    $razao_social = trim($_POST['razao_social']);
    $nome_fantasia = trim($_POST['nome_fantasia']);
    $endereco = trim($_POST['endereco']);
    $cidade_id = $_POST['cidade_id'];
    $nivel_acesso_id = 3; // Padrão: Voluntário

    // Validações
    if (strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres.";
    } elseif ($senha !== $confirmar_senha) {
        $erro = "As senhas não coincidem.";
    } elseif (strlen($usuario) < 3) {
        $erro = "O usuário deve ter pelo menos 3 caracteres.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Por favor, informe um email válido.";
    } else {
        try {
            // Verificar se o usuário já existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = ?");
            $stmt->execute([$usuario]);
            if ($stmt->fetchColumn() > 0) {
                $erro = "Este nome de usuário já está em uso.";
            } else {
                // Verificar se o email já existe
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetchColumn() > 0) {
                    $erro = "Este email já está em uso.";
                } else {
                    // Verificar se a razão social já existe
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE razao_social = ?");
                    $stmt->execute([$razao_social]);
                    if ($stmt->fetchColumn() > 0) {
                        $erro = "Esta razão social já está em uso.";
                    } else {
                        // Hash da senha
                        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                        
                        // Inserir novo usuário
                        $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, email, senha_hash, nome_completo, razao_social, nome_fantasia, endereco, cidade_id, nivel_acesso_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                        $stmt->execute([$usuario, $email, $senha_hash, $nome_completo, $razao_social, $nome_fantasia, $endereco, $cidade_id, $nivel_acesso_id]);
                        
                        $sucesso = "Conta criada com sucesso! Você já pode fazer login.";
                    }
                }
            }
        } catch (PDOException $e) {
            $erro = "Erro ao criar conta: " . $e->getMessage();
        }
    }
}

$pagina_titulo = "Registro";
require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2><i class="fas fa-user-plus"></i> Criar uma Conta</h2>
        
        <?php if ($erro): ?>
            <div class="alert alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?php echo $sucesso; ?></div>
        <?php endif; ?>
        
        <form method="post" action="registro.php" class="auth-form">
            <div class="form-group">
                <label for="usuario">Usuário *:</label>
                <input type="text" id="usuario" name="usuario" required minlength="3" placeholder="Escolha um nome de usuário">
            </div>
            
            <div class="form-group">
                <label for="email">Email *:</label>
                <input type="email" id="email" name="email" required placeholder="seu@email.com">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="senha">Senha *:</label>
                    <input type="password" id="senha" name="senha" required minlength="6" placeholder="Mínimo 6 caracteres">
                </div>
                
                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Senha *:</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required placeholder="Digite a senha novamente">
                </div>
            </div>
            
            <div class="form-group">
                <label for="nome_completo">Nome Completo *:</label>
                <input type="text" id="nome_completo" name="nome_completo" required placeholder="Seu nome completo">
            </div>
            
            <div class="form-group">
                <label for="razao_social">Razão Social *:</label>
                <input type="text" id="razao_social" name="razao_social" required placeholder="Nome da organização/empresa">
            </div>
            
            <div class="form-group">
                <label for="nome_fantasia">Nome Fantasia:</label>
                <input type="text" id="nome_fantasia" name="nome_fantasia" placeholder="Nome fantasia (opcional)">
            </div>
            
            <div class="form-group">
                <label for="endereco">Endereço:</label>
                <input type="text" id="endereco" name="endereco" placeholder="Endereço completo">
            </div>
            
            <div class="form-group">
                <label for="cidade_id">Cidade:</label>
                <select id="cidade_id" name="cidade_id" required>
                    <option value="">Selecione uma cidade</option>
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT cidade_id, cidade_nome FROM cidades ORDER BY cidade_nome");
                        while ($cidade = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . $cidade['cidade_id'] . '">' . htmlspecialchars($cidade['cidade_nome']) . '</option>';
                        }
                    } catch (PDOException $e) {
                        echo '<option value="">Erro ao carregar cidades</option>';
                    }
                    ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full"><i class="fas fa-user-plus"></i> Criar Conta</button>
        </form>
        
        <div class="auth-links">
            <p>Já tem uma conta? <a href="login.php">Faça login aqui</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>