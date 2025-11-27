<?php
require_once 'vendor/autoload.php';

function enviarEmailRecuperacao($email, $nome, $token) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Configurações SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'seuemail@gmail.com';
        $mail->Password = 'suasenhaapp';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Destinatário
        $mail->setFrom('noreply@projetoesperanca.com', 'Projeto Esperança');
        $mail->addAddress($email, $nome);
        
        // Conteúdo
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/redefinir-senha.php?token=$token";
        
        $mail->isHTML(true);
        $mail->Subject = 'Recuperação de Senha - Projeto Esperança';
        $mail->Body = criarTemplateEmail($nome, $reset_link);
        $mail->AltBody = criarTextoEmail($nome, $reset_link);
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("Erro ao enviar email: " . $mail->ErrorInfo);
        return false;
    }
}

function criarTemplateEmail($nome, $link) {
    return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #667eea;'>Recuperação de Senha</h2>
            <p>Olá <strong>$nome</strong>,</p>
            <p>Recebemos uma solicitação para redefinir sua senha.</p>
            <p>Clique no botão abaixo para redefinir sua senha:</p>
            <p style='text-align: center; margin: 30px 0;'>
                <a href='$link' style='background-color: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                    Redefinir Senha
                </a>
            </p>
            <p>Se você não solicitou esta redefinição, ignore este email.</p>
            <p><strong>Este link expira em 1 hora.</strong></p>
            <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
            <p style='color: #666; font-size: 12px;'>
                Se o botão não funcionar, copie e cole este link no seu navegador:<br>
                $link
            </p>
        </div>
    ";
}

function criarTextoEmail($nome, $link) {
    return "Recuperação de Senha - Projeto Esperança\n\n" .
           "Olá $nome,\n\n" .
           "Recebemos uma solicitação para redefinir sua senha.\n\n" .
           "Clique no link abaixo para redefinir sua senha:\n" .
           "$link\n\n" .
           "Se você não solicitou esta redefinição, ignore este email.\n" .
           "Este link expira em 1 hora.\n\n" .
           "Atenciosamente,\nEquipe Projeto Esperança";
}