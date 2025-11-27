<?php
require_once 'includes/config.php';
require_once 'vendor/autoload.php';


if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    try {
        
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? OR usuario = ?");
        $stmt->execute([$email, $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Gerar token de recuperação
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Salvar token no banco
            $stmt = $pdo->prepare("UPDATE usuarios SET reset_token = ?, reset_expira = ? WHERE usuario_id = ?");
            $stmt->execute([$token, $expira, $user['usuario_id']]);
            
            // Configurar PHPMailer (USE VARIÁVEIS DE AMBIENTE!)
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            try {
                // Configurações do servidor SMTP (MELHOR: usar variáveis de ambiente)
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = getenv('SMTP_USER') ?: 'rodrigordg04299@gmail.com';
                $mail->Password = getenv('SMTP_PASS') ?: 'nklolsdaumsstgbm';
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                // Configurações do email
                $mail->setFrom('noreply@projetoesperanca.com', 'Projeto Esperança');
                $mail->addAddress($user['email'] ?: $user['usuario'], $user['nome_completo']);
                $mail->addReplyTo('suporte@projetoesperanca.com', 'Suporte');
                
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/nova_pasta/redefinir-senha.php?token=$token";
                
                $mail->isHTML(true);
                $mail->Subject = 'Recuperação de Senha - Projeto Esperança';
                $mail->Body = "
                    <h2>Recuperação de Senha</h2>
                    <p>Olá " . htmlspecialchars($user['nome_completo']) . ",</p>
                    <p>Recebemos uma solicitação para redefinir sua senha.</p>
                    <p>Clique no link abaixo para redefinir sua senha:</p>
                    <p><a href='$reset_link' style='background-color: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Redefinir Senha</a></p>
                    <p>Se você não solicitou esta redefinição, ignore este email.</p>
                    <p>Este link expira em 1 hora.</p>
                    <br>
                    <p><strong>Link alternativo:</strong> $reset_link</p>
                    <br>
                    <p>Atenciosamente,<br>Equipe Projeto Esperança</p>
                ";
                
                $mail->AltBody = "Recuperação de Senha - Projeto Esperança\n\n" .
                    "Olá " . $user['nome_completo'] . ",\n\n" .
                    "Recebemos uma solicitação para redefinir sua senha.\n\n" .
                    "Clique no link abaixo para redefinir sua senha:\n" .
                    "$reset_link\n\n" .
                    "Se você não solicitou esta redefinição, ignore este email.\n" .
                    "Este link expira em 1 hora.\n\n" .
                    "Atenciosamente,\nEquipe Projeto Esperança";
                
                $mail->send();
                $sucesso = "Instruções para redefinição de senha foram enviadas para seu email.";
                
            } catch (Exception $e) {
                $erro = "Erro ao enviar email. Entre em contato com o suporte.";
                // Log do erro (não mostrar detalhes para o usuário)
                error_log("Erro PHPMailer: " . $mail->ErrorInfo);
            }
        } else {
            $erro = "Email não encontrado em nosso sistema.";
        }
    } catch (PDOException $e) {
        $erro = "Erro ao processar solicitação. Tente novamente.";
        error_log("Erro PDO recuperar-senha: " . $e->getMessage());
    }
}

$pagina_titulo = "Recuperar Senha";
require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2><i class="fas fa-key"></i> Recuperar Senha</h2>
        
        <?php if ($erro): ?>
            <div class="alert alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?php echo $sucesso; ?></div>
            <div class="text-center">
                <a href="login.php" class="btn btn-primary">Voltar para Login</a>
            </div>
        <?php else: ?>
            <p>Digite seu email para receber instruções de recuperação de senha.</p>
            
            <form method="post" action="recuperar-senha.php" class="auth-form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" required placeholder="Digite seu email">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-paper-plane"></i> Enviar Instruções
                </button>
            </form>
        <?php endif; ?>
        
        <div class="auth-links">
            <p><a href="login.php">Voltar para o login</a></p>
            <p>Não tem uma conta? <a href="registro.php">Registre-se aqui</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>