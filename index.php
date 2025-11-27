<?php
require_once 'includes/config.php';
$pagina_titulo = "Página Inicial";
require_once 'includes/header.php';

// Obter estatísticas
try {
    $total_pontos = $pdo->query("SELECT COUNT(*) FROM pontos_ajuda WHERE ativo = 1")->fetchColumn();
    $total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE status = 1")->fetchColumn();
    $total_cidades = $pdo->query("SELECT COUNT(DISTINCT cidade_id) FROM pontos_ajuda WHERE ativo = 1")->fetchColumn();
} catch (PDOException $e) {
    $total_pontos = 0;
    $total_usuarios = 0;
    $total_cidades = 0;
}
?>

<div class="hero-section">
    <div class="hero-content">
        <h2>Bem-vindo ao Projeto Esperança</h2>
        <p>Conectando pessoas em situação de vulnerabilidade com pontos de ajuda espalhados pela cidade.</p>
        
        <?php if (!isLoggedIn()): ?>
            <div class="hero-buttons">
                <a href="registro.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Cadastre-se</a>
                <a href="login.php" class="btn btn-secondary"><i class="fas fa-sign-in-alt"></i> Entrar</a>
            </div>
        <?php else: 
            $user = getUserInfo($pdo, $_SESSION['usuario_id']);
        ?>
            <div class="hero-buttons">
                <p>Olá, <strong><?php echo htmlspecialchars($user['nome_completo']); ?></strong>!</p>
                <a href="mapa.php" class="btn btn-primary"><i class="fas fa-map"></i> Ver Mapa</a>
                <a href="adicionar-ponto.php" class="btn btn-secondary"><i class="fas fa-plus"></i> Adicionar Ponto</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="stats-section">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-map-marker-alt"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_pontos; ?></h3>
            <p>Pontos de Ajuda</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_usuarios; ?></h3>
            <p>Voluntários</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-city"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_cidades; ?></h3>
            <p>Cidades Atendidas</p>
        </div>
    </div>
</div>

<div class="card">
    <h2>Como Funciona</h2>
    <div class="features-grid">
        <div class="feature">
            <div class="feature-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Encontre Ajuda</h3>
            <p>Busque pontos de ajuda próximos a você no nosso mapa interativo.</p>
        </div>
        
        <div class="feature">
            <div class="feature-icon">
                <i class="fas fa-hands-helping"></i>
            </div>
            <h3>Ofereça Ajuda</h3>
            <p>Cadastre pontos de ajuda para conectar com pessoas necessitadas.</p>
        </div>
        
        <div class="feature">
            <div class="feature-icon">
                <i class="fas fa-share-alt"></i>
            </div>
            <h3>Compartilhe</h3>
            <p>Divulgue pontos de ajuda para ampliar o alcance das doações.</p>
        </div>
    </div>
</div>

<?php
// Listar pontos de ajuda recentes
try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.nome_completo, u.foto_perfil, c.cidade_nome 
        FROM pontos_ajuda p 
        JOIN usuarios u ON p.usuario_id = u.usuario_id 
        JOIN cidades c ON p.cidade_id = c.cidade_id 
        WHERE p.ativo = 1 
        ORDER BY p.dt_criacao DESC 
        LIMIT 6
    ");
    $stmt->execute();
    $pontos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($pontos) {
        echo '<div class="card">';
        echo '<h2>Pontos de Ajuda Recentes</h2>';
        echo '<div class="points-grid">';
        
        foreach ($pontos as $ponto) {
            echo '<div class="point-card">';
            echo '<span class="point-type point-type-' . $ponto['tipo_ajuda'] . '">' . ucfirst($ponto['tipo_ajuda']) . '</span>';
            echo '<h3>' . htmlspecialchars($ponto['titulo']) . '</h3>';
            echo '<p class="point-description">' . htmlspecialchars(substr($ponto['descricao'], 0, 100)) . '...</p>';
            echo '<div class="point-details">';
            echo '<p><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($ponto['endereco']) . ', ' . htmlspecialchars($ponto['cidade_nome']) . '</p>';
            echo '<p><i class="fas fa-user"></i> ' . htmlspecialchars($ponto['nome_completo']) . '</p>';
            echo '</div>';
            echo '</div>';
        }
        
        echo '</div>';
        echo '<div class="text-center" style="margin-top: 2rem;">';
        echo '<a href="pontos.php" class="btn btn-primary"><i class="fas fa-list"></i> Ver Todos os Pontos</a>';
        echo '</div>';
        echo '</div>';
    }
} catch (PDOException $e) {
    echo '<div class="alert alert-error">Erro ao carregar pontos de ajuda: ' . $e->getMessage() . '</div>';
}
?>

<?php require_once 'includes/footer.php'; ?>