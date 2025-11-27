<?php
require_once 'includes/config.php';

// Configuração da paginação - 6 PONTOS POR PÁGINA
$pontos_por_pagina = 6;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $pontos_por_pagina;

// Obter o total de pontos para calcular o número de páginas
try {
    $stmt_total = $pdo->query("SELECT COUNT(*) as total FROM pontos_ajuda WHERE ativo = 1");
    $total_pontos = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
    $total_paginas = ceil($total_pontos / $pontos_por_pagina);
    
    // Garantir que a página atual está dentro dos limites
    if ($pagina_atual < 1) $pagina_atual = 1;
    if ($pagina_atual > $total_paginas) $pagina_atual = $total_paginas;
    
    // Obter os pontos para a página atual
    $stmt = $pdo->prepare("
        SELECT p.*, c.cidade_nome, u.nome_completo 
        FROM pontos_ajuda p 
        JOIN cidades c ON p.cidade_id = c.cidade_id 
        JOIN usuarios u ON p.usuario_id = u.usuario_id 
        WHERE p.ativo = 1 
        ORDER BY p.dt_criacao DESC 
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $pontos_por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $pontos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $pontos = [];
    $total_pontos = 0;
    $total_paginas = 1;
}

$pagina_titulo = "Mapa de Pontos de Ajuda";
require_once 'includes/header.php';
?>

<style>
.map-container {
    padding: 20px 0;
}

.card {
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #dee2e6;
    margin-bottom: 20px;
}

.card:first-child {
    background: #2c3e50;
    color: white;
    padding: 20px;
    border-radius: 10px 10px 0 0;
    margin-bottom: 0;
}

.card:first-child h2 {
    margin: 0 0 10px 0;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card:first-child p {
    margin: 0;
    opacity: 0.9;
}

#map {
    height: 600px;
    width: 100%;
}

.points-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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

/* Estilos específicos para o mapa */
.map-popup {
    max-width: 300px;
}

.map-popup h3 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 1.1rem;
}

.map-popup p {
    margin: 5px 0;
    font-size: 0.85rem;
    color: #495057;
}

.popup-actions {
    margin-top: 10px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.75rem;
}

/* Responsividade */
@media (max-width: 768px) {
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
    
    #map {
        height: 400px;
    }
    
    .card:first-child {
        padding: 15px;
    }
    
    .card:first-child h2 {
        font-size: 1.3rem;
    }
}
</style>

<div class="map-container">
    <div class="card">
        <h2><i class="fas fa-map"></i> Mapa de Pontos de Ajuda</h2>
        <p>Encontre pontos de ajuda próximos a você no mapa interativo abaixo.</p>
    </div>
    
    <div class="card">
        <div id="map"></div>
    </div>
    
    <?php if ($pontos): ?>
        <div class="card" style="border-radius: 10px; overflow: hidden; border: 1px solid #dee2e6;">
            <div class="filters-header" style="background: #2c3e50; color: white; padding: 20px; border-radius: 10px 10px 0 0; margin-bottom: 0;">
                <h2><i class="fas fa-list"></i> Lista de Pontos</h2>
            </div>
            
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
                                <button class="btn btn-primary" onclick="focusOnPoint(<?php echo $ponto['latitude']; ?>, <?php echo $ponto['longitude']; ?>)">
                                    <i class="fas fa-map-marker-alt"></i> Localizar
                                </button>
                                <a href="mapa.php?ponto=<?php echo $ponto['ponto_id']; ?>" class="btn btn-secondary">
                                    <i class="fas fa-info-circle"></i> Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Paginação igual ao código 1 -->
            <?php if ($total_paginas > 1): ?>
                <div class="pagination">
                    <?php if ($pagina_atual > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $pagina_atual - 1])); ?>" class="pagination-link">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $i])); ?>" 
                           class="pagination-link <?php echo $i == $pagina_atual ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($pagina_atual < $total_paginas): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $pagina_atual + 1])); ?>" class="pagination-link">
                            Próxima <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="empty-state">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Nenhum ponto de ajuda disponível</h3>
                <p>Não há pontos de ajuda cadastrados no momento.</p>
                <?php if (isLoggedIn()): ?>
                    <a href="adicionar-ponto.php" class="btn btn-primary"><i class="fas fa-plus"></i> Adicionar Primeiro Ponto</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Fazer Login para Adicionar</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
let map;
let markers = [];

function initMap() {
    // Coordenadas padrão (centro de São José dos Campos)
    const defaultCoords = [-23.1791, -45.8872];
    
    // Inicializar o mapa
    map = L.map('map').setView(defaultCoords, 13);
    
    // Adicionar camada do OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Adicionar marcadores dos pontos
    <?php foreach ($pontos as $ponto): ?>
        const marker<?php echo $ponto['ponto_id']; ?> = L.marker([<?php echo $ponto['latitude']; ?>, <?php echo $ponto['longitude']; ?>]).addTo(map);
        
        marker<?php echo $ponto['ponto_id']; ?>.bindPopup(`
            <div class="map-popup">
                <h3><?php echo addslashes($ponto['titulo']); ?></h3>
                <p><strong>Tipo:</strong> <?php echo ucfirst($ponto['tipo_ajuda']); ?></p>
                <p><strong>Endereço:</strong> <?php echo addslashes($ponto['endereco']); ?>, <?php echo addslashes($ponto['cidade_nome']); ?></p>
                <p><strong>Descrição:</strong> <?php echo addslashes(substr($ponto['descricao'], 0, 200)); ?>...</p>
                <p><strong>Responsável:</strong> <?php echo addslashes($ponto['nome_completo']); ?></p>
                <div class="popup-actions">
                    <button class="btn btn-sm btn-primary" onclick="focusOnPoint(<?php echo $ponto['latitude']; ?>, <?php echo $ponto['longitude']; ?>)">
                        Localizar no Mapa
                    </button>
                </div>
            </div>
        `);
        
        markers.push(marker<?php echo $ponto['ponto_id']; ?>);
    <?php endforeach; ?>
    
    // Tentar obter localização do usuário
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const userCoords = [position.coords.latitude, position.coords.longitude];
                
                // Adicionar marcador da localização do usuário
                const userMarker = L.marker(userCoords, {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map);
                
                userMarker.bindPopup('<strong>Sua localização atual</strong>').openPopup();
                
                // Centralizar mapa na localização do usuário
                map.setView(userCoords, 15);
            },
            function(error) {
                console.log('Não foi possível obter a localização do usuário:', error);
            }
        );
    }
}

function focusOnPoint(lat, lng) {
    map.setView([lat, lng], 15);
    
    // Abrir popup do marcador
    markers.forEach(marker => {
        const markerLatLng = marker.getLatLng();
        if (markerLatLng.lat === lat && markerLatLng.lng === lng) {
            marker.openPopup();
        }
    });
}

// Inicializar o mapa quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    
    // Verificar se há um ponto específico para focar (vindo da URL)
    const urlParams = new URLSearchParams(window.location.search);
    const pontoId = urlParams.get('ponto');
    
    if (pontoId) {
        <?php foreach ($pontos as $ponto): ?>
            if (<?php echo $ponto['ponto_id']; ?> == pontoId) {
                focusOnPoint(<?php echo $ponto['latitude']; ?>, <?php echo $ponto['longitude']; ?>);
            }
        <?php endforeach; ?>
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>