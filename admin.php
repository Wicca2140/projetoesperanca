<?php
require_once 'includes/config.php';

// Redirecionar se não estiver logado ou não for admin/gerente
if (!isLoggedIn() || (!isAdmin() && !isGerente())) {
    header("Location: index.php");
    exit();
}

$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'dashboard';
$usuario_id = $_SESSION['usuario_id'];

// Processar ações administrativas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'toggle_user_status':
            if (isset($_POST['user_id'])) {
                $target_user_id = (int)$_POST['user_id'];
                try {
                    $stmt = $pdo->prepare("UPDATE usuarios SET status = 1 - status, dt_atualizacao = NOW() WHERE usuario_id = ?");
                    $stmt->execute([$target_user_id]);
                    $sucesso = "Status do usuário atualizado com sucesso!";
                } catch (PDOException $e) {
                    $erro = "Erro ao atualizar status: " . $e->getMessage();
                }
            }
            break;
            
        case 'toggle_point_status':
            if (isset($_POST['point_id'])) {
                $point_id = (int)$_POST['point_id'];
                try {
                    $stmt = $pdo->prepare("UPDATE pontos_ajuda SET ativo = 1 - ativo, dt_atualizacao = NOW() WHERE ponto_id = ?");
                    $stmt->execute([$point_id]);
                    $sucesso = "Status do ponto atualizado com sucesso!";
                } catch (PDOException $e) {
                    $erro = "Erro ao atualizar status: " . $e->getMessage();
                }
            }
            break;
            
        case 'update_user_role':
            if (isset($_POST['user_id'], $_POST['nivel_acesso_id']) && isAdmin()) {
                $target_user_id = (int)$_POST['user_id'];
                $nivel_acesso_id = (int)$_POST['nivel_acesso_id'];
                try {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nivel_acesso_id = ?, dt_atualizacao = NOW() WHERE usuario_id = ?");
                    $stmt->execute([$nivel_acesso_id, $target_user_id]);
                    $sucesso = "Nível de acesso atualizado com sucesso!";
                } catch (PDOException $e) {
                    $erro = "Erro ao atualizar nível de acesso: " . $e->getMessage();
                }
            }
            break;
    }
}

// Obter estatísticas para o dashboard
try {
    $total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $usuarios_ativos = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE status = 1")->fetchColumn();
    $total_pontos = $pdo->query("SELECT COUNT(*) FROM pontos_ajuda")->fetchColumn();
    $pontos_ativos = $pdo->query("SELECT COUNT(*) FROM pontos_ajuda WHERE ativo = 1")->fetchColumn();
    $total_cidades = $pdo->query("SELECT COUNT(DISTINCT cidade_id) FROM pontos_ajuda")->fetchColumn();
} catch (PDOException $e) {
    $erro = "Erro ao carregar estatísticas: " . $e->getMessage();
}

$pagina_titulo = "Painel Administrativo";
require_once 'includes/header.php';
?>

<div class="admin-container">
    <div class="admin-sidebar">
        <h3>Painel Administrativo</h3>
        <ul>
            <li class="<?php echo $pagina == 'dashboard' ? 'active' : ''; ?>">
                <a href="?pagina=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="<?php echo $pagina == 'usuarios' ? 'active' : ''; ?>">
                <a href="?pagina=usuarios"><i class="fas fa-users"></i> Gerenciar Usuários</a>
            </li>
            <li class="<?php echo $pagina == 'pontos' ? 'active' : ''; ?>">
                <a href="?pagina=pontos"><i class="fas fa-map-marker-alt"></i> Gerenciar Pontos</a>
            </li>
            <?php if (isAdmin()): ?>
                <li class="<?php echo $pagina == 'cidades' ? 'active' : ''; ?>">
                    <a href="?pagina=cidades"><i class="fas fa-city"></i> Gerenciar Cidades</a>
                </li>
                <!-- NOVO ITEM PARA APROVAÇÃO DE PONTOS -->
                <li class="<?php echo $pagina == 'aprovacao-pontos' ? 'active' : ''; ?>">
                    <a href="?pagina=aprovacao-pontos"><i class="fas fa-clipboard-check"></i> Aprovar Pontos</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    
    <div class="admin-content">
        <?php if (isset($erro)): ?>
            <div class="alert alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <?php if (isset($sucesso)): ?>
            <div class="alert alert-success"><?php echo $sucesso; ?></div>
        <?php endif; ?>
        
        <?php
        // INCLUIR AS PÁGINAS CORRETAMENTE
        switch ($pagina) {
            case 'dashboard':
                include 'admin/dashboard.php';
                break;
                
            case 'usuarios':
                include 'admin/usuarios.php';
                break;
                
            case 'pontos':
                include 'admin/pontos.php';
                break;
                
            case 'cidades':
                if (isAdmin()) {
                    include 'admin/cidades.php';
                } else {
                    echo '<div class="alert alert-error">Acesso não autorizado.</div>';
                }
                break;
                
            case 'aprovacao-pontos':
                if (isAdmin()) {
                    include 'admin/aprovacao-pontos.php';
                } else {
                    echo '<div class="alert alert-error">Acesso não autorizado.</div>';
                }
                break;
                
            default:
                include 'admin/dashboard.php';
                break;
        }
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>