<?php
require_once 'includes/config.php';

// Redirecionar se não estiver logado
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$erro = "";
$sucesso = "";

// Obter informações do usuário
$user = getUserInfo($pdo, $usuario_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_completo = trim($_POST['nome_completo']);
    $razao_social = trim($_POST['razao_social']);
    $nome_fantasia = trim($_POST['nome_fantasia']);
    $endereco = trim($_POST['endereco']);
    $cidade_id = $_POST['cidade_id'];
    $email = trim($_POST['email']); // Novo campo email
    
    // Upload de foto de perfil
    $foto_perfil = $user['foto_perfil'];
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($extensao, $extensoes_permitidas)) {
            $nome_arquivo = uniqid() . '_' . time() . '.' . $extensao;
            $caminho_destino = 'uploads/' . $nome_arquivo;
            
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $caminho_destino)) {
                // Remover foto antiga se existir
                if ($foto_perfil && file_exists($foto_perfil)) {
                    unlink($foto_perfil);
                }
                $foto_perfil = $caminho_destino;
            } else {
                $erro = "Erro ao fazer upload da imagem.";
            }
        } else {
            $erro = "Formato de arquivo não permitido. Use JPG, PNG ou GIF.";
        }
    }
    
    // Validação do email
    if (!$erro && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Por favor, informe um email válido.";
    }
    
    if (!$erro) {
        try {
            // Verificar se o email já existe (excluindo o usuário atual)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ? AND usuario_id != ?");
            $stmt->execute([$email, $usuario_id]);
            
            if ($stmt->fetchColumn() > 0) {
                $erro = "Este email já está em uso por outro usuário.";
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios SET nome_completo = ?, razao_social = ?, nome_fantasia = ?, endereco = ?, cidade_id = ?, email = ?, foto_perfil = ?, dt_atualizacao = NOW() WHERE usuario_id = ?");
                $stmt->execute([$nome_completo, $razao_social, $nome_fantasia, $endereco, $cidade_id, $email, $foto_perfil, $usuario_id]);
                
                $sucesso = "Perfil atualizado com sucesso!";
                $user = getUserInfo($pdo, $usuario_id); // Atualizar dados do usuário
            }
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar perfil: " . $e->getMessage();
        }
    }
}

$pagina_titulo = "Meu Perfil";
require_once 'includes/header.php';
?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <?php if ($user['foto_perfil']): ?>
                <img src="<?php echo htmlspecialchars($user['foto_perfil']); ?>" alt="Foto de perfil">
            <?php else: ?>
                <div class="avatar-placeholder">
                    <i class="fas fa-user"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($user['nome_completo']); ?></h1>
            <p class="profile-username">@<?php echo htmlspecialchars($user['usuario']); ?></p>
            <p class="profile-email">
                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email'] ?? 'Não informado'); ?>
            </p>
            <p class="profile-role">
                <?php 
                $stmt = $pdo->prepare("SELECT cargo FROM permissoes WHERE permissao_id = ?");
                $stmt->execute([$user['nivel_acesso_id']]);
                $cargo = $stmt->fetchColumn();
                echo htmlspecialchars($cargo);
                ?>
            </p>
        </div>
    </div>
    
    <div class="card">
        <h2><i class="fas fa-user-edit"></i> Editar Perfil</h2>
        
        <?php if ($erro): ?>
            <div class="alert alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?php echo $sucesso; ?></div>
        <?php endif; ?>
        
        <form method="post" action="perfil.php" enctype="multipart/form-data" class="profile-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="nome_completo">Nome Completo:</label>
                    <input type="text" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($user['nome_completo']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required placeholder="seu@email.com">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="razao_social">Razão Social:</label>
                    <input type="text" id="razao_social" name="razao_social" value="<?php echo htmlspecialchars($user['razao_social']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="nome_fantasia">Nome Fantasia:</label>
                    <input type="text" id="nome_fantasia" name="nome_fantasia" value="<?php echo htmlspecialchars($user['nome_fantasia'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="endereco">Endereço:</label>
                    <input type="text" id="endereco" name="endereco" value="<?php echo htmlspecialchars($user['endereco'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="cidade_id">Cidade:</label>
                    <select id="cidade_id" name="cidade_id" required>
                        <option value="">Selecione uma cidade</option>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT cidade_id, cidade_nome FROM cidades ORDER BY cidade_nome");
                            while ($cidade = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $selected = $cidade['cidade_id'] == $user['cidade_id'] ? 'selected' : '';
                                echo '<option value="' . $cidade['cidade_id'] . '" ' . $selected . '>' . htmlspecialchars($cidade['cidade_nome']) . '</option>';
                            }
                        } catch (PDOException $e) {
                            echo '<option value="">Erro ao carregar cidades</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="foto_perfil">Foto de Perfil:</label>
                <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
                <small>Formatos permitidos: JPG, PNG, GIF. Tamanho máximo: 2MB</small>
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Alterações</button>
        </form>
    </div>
    
    <div class="card">
        <h2><i class="fas fa-map-marker-alt"></i> Meus Pontos de Ajuda</h2>
        <?php
        try {
            $stmt = $pdo->prepare("
                SELECT p.*, c.cidade_nome 
                FROM pontos_ajuda p 
                JOIN cidades c ON p.cidade_id = c.cidade_id 
                WHERE p.usuario_id = ? 
                ORDER BY p.dt_criacao DESC
            ");
            $stmt->execute([$usuario_id]);
            $pontos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($pontos) {
                echo '<div class="my-points-grid">';
                foreach ($pontos as $ponto) {
                    echo '<div class="my-point-card">';
                    echo '<span class="point-status ' . ($ponto['ativo'] ? 'active' : 'inactive') . '">';
                    echo $ponto['ativo'] ? 'Ativo' : 'Inativo';
                    echo '</span>';
                    echo '<h3>' . htmlspecialchars($ponto['titulo']) . '</h3>';
                    echo '<p class="point-type point-type-' . $ponto['tipo_ajuda'] . '">' . ucfirst($ponto['tipo_ajuda']) . '</p>';
                    echo '<p><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($ponto['endereco']) . ', ' . htmlspecialchars($ponto['cidade_nome']) . '</p>';
                    echo '<div class="point-actions">';
                    echo '<a href="editar-ponto.php?id=' . $ponto['ponto_id'] . '" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i> Editar</a>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>Você ainda não cadastrou nenhum ponto de ajuda.</p>';
                echo '<a href="adicionar-ponto.php" class="btn btn-primary"><i class="fas fa-plus"></i> Adicionar Primeiro Ponto</a>';
            }
        } catch (PDOException $e) {
            echo '<div class="alert alert-error">Erro ao carregar pontos: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>