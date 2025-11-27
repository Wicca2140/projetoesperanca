<?php
require_once 'includes/config.php';

// Redirecionar se não estiver logado
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Configurações de paginação - 6 PONTOS POR PÁGINA
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 6;
$offset = ($pagina - 1) * $por_pagina;

// Filtros
$filtro_tipo = $_GET['tipo'] ?? '';
$filtro_cidade = $_GET['cidade'] ?? '';
$filtro_busca = $_GET['busca'] ?? '';

// Construir query base
$query = "
    SELECT p.*, u.nome_completo, u.foto_perfil, c.cidade_nome 
    FROM pontos_ajuda p 
    JOIN usuarios u ON p.usuario_id = u.usuario_id 
    JOIN cidades c ON p.cidade_id = c.cidade_id 
    WHERE p.ativo = 1
";

$query_count = "SELECT COUNT(*) FROM pontos_ajuda p WHERE p.ativo = 1";
$params = [];
$params_count = [];

// Aplicar filtros
if ($filtro_tipo) {
    $query .= " AND p.tipo_ajuda = ?";
    $query_count .= " AND p.tipo_ajuda = ?";
    $params[] = $filtro_tipo;
    $params_count[] = $filtro_tipo;
}

if ($filtro_cidade) {
    $query .= " AND p.cidade_id = ?";
    $query_count .= " AND p.cidade_id = ?";
    $params[] = $filtro_cidade;
    $params_count[] = $filtro_cidade;
}

if ($filtro_busca) {
    $query .= " AND (p.titulo LIKE ? OR p.descricao LIKE ? OR p.endereco LIKE ?)";
    $query_count .= " AND (p.titulo LIKE ? OR p.descricao LIKE ? OR p.endereco LIKE ?)";
    $busca_param = "%$filtro_busca%";
    $params = array_merge($params, [$busca_param, $busca_param, $busca_param]);
    $params_count = array_merge($params_count, [$busca_param, $busca_param, $busca_param]);
}

// Ordenação e paginação
$query .= " ORDER BY p.dt_criacao DESC LIMIT $por_pagina OFFSET $offset";

// Executar queries
try {
    // Total de registros
    $stmt_count = $pdo->prepare($query_count);
    $stmt_count->execute($params_count);
    $total_registros = $stmt_count->fetchColumn();
    
    // Dados da página atual
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $pontos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_paginas = ceil($total_registros / $por_pagina);
    
    // Obter cidades para filtro
    $cidades = $pdo->query("SELECT cidade_id, cidade_nome FROM cidades ORDER BY cidade_nome")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $erro = "Erro ao carregar pontos: " . $e->getMessage();
    $pontos = [];
    $total_paginas = 1;
    $cidades = [];
}

$pagina_titulo = "Pontos de Ajuda";
require_once 'includes/header.php';
?>

<style>
.points-container {
    padding: 20px 0;
}

.filters-header {
    background: #2c3e50; /* VOLTEI COM A COR ORIGINAL */
    color: white;
    padding: 20px;
    border-radius: 10px 10px 0 0;
    margin-bottom: 0;
}

.filters-header h2 {
    margin: 0 0 15px 0;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-form {
    background: rgba(255, 255, 255, 0.1);
    padding: 15px;
    border-radius: 8px;
    backdrop-filter: blur(10px);
}

.filter-row {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: end;
}

.filter-group {
    flex: 1;
    min-width: 180px;
}

.filter-group input,
.filter-group select {
    width: 100%;
    padding: 10px 12px;
    border: none;
    border-radius: 6px;
    background: white;
    font-size: 14px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.filter-group button {
    width: 100%;
    margin-bottom: 5px;
}

.points-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* CARDS MENORES */
    gap: 20px;
    padding: 25px;
    background: #f8f9fa;
}

.point-card {
    background: white;
    border-radius: 12px;
    padding: 0;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    overflow: hidden;
    position: relative;
    height: fit-content;
}

.point-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}

.point-type {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: white;
    z-index: 2;
}

.point-type-alimento { background: linear-gradient(135deg, #4CAF50, #45a049); }
.point-type-roupa { background: linear-gradient(135deg, #2196F3, #1976D2); }
.point-type-medicamento { background: linear-gradient(135deg, #FF9800, #F57C00); }
.point-type-abrigo { background: linear-gradient(135deg, #9C27B0, #7B1FA2); }
.point-type-outros { background: linear-gradient(135deg, #607D8B, #455A64); }

.card-header {
    padding: 20px 20px 0 20px;
    position: relative;
}

.card-header h3 {
    margin: 0 0 12px 0;
    font-size: 1.1rem;
    color: #2c3e50;
    line-height: 1.4;
    padding-right: 70px;
}

.card-body {
    padding: 0 20px 15px 20px;
}

.point-description {
    color: #6c757d;
    line-height: 1.5;
    margin-bottom: 15px;
    font-size: 0.9rem;
}

.point-details {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.point-details p {
    margin: 6px 0;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #495057;
    font-size: 0.85rem;
}

.point-details i {
    width: 14px;
    color: #6c757d;
}

.point-actions {
    display: flex;
    gap: 8px;
    justify-content: space-between;
}

.btn {
    padding: 8px 15px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.8rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    justify-content: center;
    flex: 1;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.btn-secondary {
    background: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background: #7f8c8d;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(149, 165, 166, 0.3);
}

.empty-state {
    text-align: center;
    padding: 50px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    color: #dee2e6;
    margin-bottom: 15px;
}

.empty-state h3 {
    color: #495057;
    margin-bottom: 10px;
    font-size: 1.3rem;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    padding: 25px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.pagination-link {
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    text-decoration: none;
    color: #495057;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.9rem;
}

.pagination-link:hover {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.pagination-link.active {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    padding: 12px 15px;
    border-radius: 6px;
    margin: 15px;
    border: 1px solid #f5c6cb;
    font-size: 0.9rem;
}

/* Responsividade */
@media (max-width: 768px) {
    .filter-row {
        flex-direction: column;
    }
    
    .filter-group {
        min-width: 100%;
    }
    
    .points-grid {
        grid-template-columns: 1fr;
        padding: 20px 15px;
        gap: 15px;
    }
    
    .point-actions {
        flex-direction: column;
    }
    
    .pagination {
        flex-wrap: wrap;
        padding: 20px 15px;
    }
    
    .filters-header {
        padding: 15px;
    }
    
    .filters-header h2 {
        font-size: 1.3rem;
    }
}
</style>

<div class="points-container">
    <div class="card" style="border-radius: 10px; overflow: hidden; border: 1px solid #dee2e6;">
        <div class="filters-header">
            <h2><i class="fas fa-map-marker-alt"></i> Pontos de Ajuda</h2>
            <div class="filters">
                <form method="get" action="pontos.php" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <input type="text" name="busca" placeholder="Buscar pontos..." value="<?php echo htmlspecialchars($filtro_busca); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <select name="tipo">
                                <option value="">Todos os tipos</option>
                                <option value="alimento" <?php echo $filtro_tipo == 'alimento' ? 'selected' : ''; ?>>Alimento</option>
                                <option value="roupa" <?php echo $filtro_tipo == 'roupa' ? 'selected' : ''; ?>>Roupa</option>
                                <option value="medicamento" <?php echo $filtro_tipo == 'medicamento' ? 'selected' : ''; ?>>Medicamento</option>
                                <option value="abrigo" <?php echo $filtro_tipo == 'abrigo' ? 'selected' : ''; ?>>Abrigo</option>
                                <option value="outros" <?php echo $filtro_tipo == 'outros' ? 'selected' : ''; ?>>Outros</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <select name="cidade">
                                <option value="">Todas as cidades</option>
                                <?php foreach ($cidades as $cidade): ?>
                                    <option value="<?php echo $cidade['cidade_id']; ?>" <?php echo $filtro_cidade == $cidade['cidade_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cidade['cidade_nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
                            <a href="pontos.php" class="btn btn-secondary"><i class="fas fa-times"></i> Limpar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if (isset($erro)): ?>
            <div class="alert alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <?php if ($pontos): ?>
            <div class="points-grid">
                <?php foreach ($pontos as $ponto): ?>
                    <div class="point-card">
                        <div class="card-header">
                            <span class="point-type point-type-<?php echo $ponto['tipo_ajuda']; ?>">
                                <?php echo ucfirst($ponto['tipo_ajuda']); ?>
                            </span>
                            <h3><?php echo htmlspecialchars($ponto['titulo']); ?></h3>
                        </div>
                        
                        <div class="card-body">
                            <p class="point-description">
                                <?php echo htmlspecialchars(substr($ponto['descricao'], 0, 120)); ?>
                                <?php if (strlen($ponto['descricao']) > 120): ?>...<?php endif; ?>
                            </p>
                            
                            <div class="point-details">
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($ponto['endereco']); ?>, <?php echo htmlspecialchars($ponto['cidade_nome']); ?></p>
                                <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($ponto['nome_completo']); ?></p>
                                <p><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($ponto['dt_criacao'])); ?></p>
                            </div>
                            
                            <div class="point-actions">
                                <a href="mapa.php?ponto=<?php echo $ponto['ponto_id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-map"></i> Ver Mapa
                                </a>
                                <?php if ($ponto['usuario_id'] == $_SESSION['usuario_id'] || isAdmin() || isGerente()): ?>
                                    <a href="editar-ponto.php?id=<?php echo $ponto['ponto_id']; ?>" class="btn btn-secondary">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Paginação -->
            <?php if ($total_paginas > 1): ?>
                <div class="pagination">
                    <?php if ($pagina > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $pagina - 1])); ?>" class="pagination-link">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $i])); ?>" 
                           class="pagination-link <?php echo $i == $pagina ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($pagina < $total_paginas): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $pagina + 1])); ?>" class="pagination-link">
                            Próxima <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Nenhum ponto de ajuda encontrado</h3>
                <p>Tente ajustar os filtros ou cadastre um novo ponto de ajuda.</p>
                <a href="adicionar-ponto.php" class="btn btn-primary"><i class="fas fa-plus"></i> Adicionar Ponto</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>