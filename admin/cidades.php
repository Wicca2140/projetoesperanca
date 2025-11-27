<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Cidades</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos base do sistema de usuários (mantidos) */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0;  }
        .admin-section { max-width: 1200px; margin: 0 auto; background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .section-header h2 {
            color: #2c3e50;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .search-box {
            position: relative;
            min-width: 300px;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #4361ee;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .filters-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-group label {
            font-weight: 500;
            color: #495057;
            margin-right: 5px;
        }

        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            background: white;
            color: #495057;
            font-size: 0.9rem;
        }

        .results-info {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 500;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            background: #f8f9fa;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #e9ecef;
            user-select: none;
        }

        .admin-table th i {
            margin-left: 5px;
            font-size: 0.8rem;
            opacity: 0.6;
        }

        .admin-table td {
            padding: 12px;
            border-bottom: 1px solid #f8f9fa;
        }

        .admin-table tr:hover {
            background-color: #f8f9fa;
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding: 15px 0;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-btn {
            width: 36px;
            height: 36px;
            border: 1px solid #e9ecef;
            background: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .page-btn:hover:not(:disabled) {
            background: #4361ee;
            color: white;
            border-color: #4361ee;
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-info {
            font-size: 0.9rem;
            color: #6c757d;
        }

        /* Estilos específicos para cidades */
        .city-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .city-info i {
            color: #4361ee;
        }

        .state-badge {
            padding: 4px 8px;
            border-radius: 12px;
            background: #e3f2fd;
            color: #1976d2;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .state-badge.large {
            padding: 6px 12px;
            font-size: 0.9rem;
        }

        .points-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .points-count {
            font-weight: 600;
            color: #2c3e50;
            min-width: 20px;
        }

        .points-bar {
            flex: 1;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }

        .points-fill {
            height: 100%;
            background: linear-gradient(90deg, #4cc9f0, #4361ee);
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        /* Estilos de Botão */
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #4361ee;
            color: white;
        }

        .btn-primary:hover {
            background-color: #3a56d4;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 0.8rem;
        }

        .btn-info {
            background-color: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background-color: #138496;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            animation: fadeIn 0.3s;
        }

        .modal.large .modal-content {
            max-width: 800px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .modal-header h3 {
            margin: 0;
            color: #2c3e50;
        }

        .modal-header button {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6c757d;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #495057;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        /* Preview Styles */
        .preview-card {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .preview-city {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .preview-city i {
            color: #4361ee;
        }

        .preview-city span {
            font-weight: 600;
            color: #2c3e50;
        }

        /* City Details Styles */
        .city-details .city-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .city-icon-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }

        .city-main-info h3 {
            margin: 0 0 8px 0;
            color: #2c3e50;
        }

        .city-stats-detailed {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stat-card i {
            font-size: 1.5rem;
            color: #4361ee;
        }

        .stat-number {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .city-actions-detailed {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* Points Modal */
        .points-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .point-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .point-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .point-type {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 10px;
            display: inline-block;
        }

        .point-type.alimento { background: #ffeaa7; color: #e17055; }
        .point-type.roupa { background: #a29bfe; color: white; }
        .point-type.higiene { background: #74b9ff; color: white; }

        .point-card h5 {
            margin: 0 0 8px 0;
            color: #2c3e50;
        }

        .point-card p {
            margin: 0 0 10px 0;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .point-status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
        }

        .point-status.active { background: #d4edda; color: #155724; }
        .point-status.inactive { background: #f8d7da; color: #721c24; }

        /* Toast Message */
        #toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 10000;
        }

        .toast {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 10px;
            opacity: 0;
            transition: opacity 0.5s, transform 0.5s;
            transform: translateY(20px);
            min-width: 250px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.16);
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast.success { background-color: #28a745; }
        .toast.error { background-color: #dc3545; }
        .toast.info { background-color: #17a2b8; }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header-actions {
                width: 100%;
                justify-content: space-between;
            }
            
            .search-box {
                min-width: auto;
                flex: 1;
            }
            
            .filters-bar {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .city-stats-detailed {
                grid-template-columns: 1fr;
            }
            
            .admin-table {
                font-size: 0.85rem;
            }
            
            .actions {
                flex-direction: column;
            }

            .table-container {
                overflow-x: auto;
            }

            .modal-content {
                width: 95%;
                margin: 20px;
            }
            
            .points-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="admin-section">
    <div class="section-header">
        <h2><i class="fas fa-city"></i> Gerenciar Cidades</h2>
        <div class="header-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="city-search" placeholder="Buscar cidades..." onkeyup="filterCities()">
            </div>
            <button class="btn btn-primary" onclick="openAddCityModal()">
                <i class="fas fa-plus"></i> Nova Cidade
            </button>
        </div>
    </div>

    <div class="filters-bar">
        <div class="filter-group">
            <label>Filtrar por estado:</label>
            <select id="state-filter" onchange="filterCities()">
                <option value="">Todos os estados</option>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM estados ORDER BY uf_sigla");
                    while ($estado = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . $estado['uf_id'] . '">' . htmlspecialchars($estado['uf_sigla']) . '</option>';
                    }
                } catch (PDOException $e) {
                    echo '<option value="">Erro ao carregar estados</option>';
                }
                ?>
            </select>
        </div>
        <div class="results-info">
            <span id="cities-count">0</span> cidades encontradas
        </div>
    </div>
    
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th data-sort="id">ID <i class="fas fa-sort"></i></th>
                    <th data-sort="name">Nome da Cidade <i class="fas fa-sort"></i></th>
                    <th data-sort="state">Estado <i class="fas fa-sort"></i></th>
                    <th data-sort="points">Pontos <i class="fas fa-sort"></i></th>
                    <th data-sort="date">Data Cadastro <i class="fas fa-sort"></i></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="cities-table">
                <?php
                try {
                    $stmt = $pdo->query("
                        SELECT c.*, e.uf_sigla, 
                               (SELECT COUNT(*) FROM pontos_ajuda p WHERE p.cidade_id = c.cidade_id) as total_pontos
                        FROM cidades c 
                        JOIN estados e ON c.uf_id = e.uf_id 
                        ORDER BY c.cidade_nome
                    ");
                    $cidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($cidades as $cidade) {
                        echo '<tr data-city-id="' . $cidade['cidade_id'] . '" data-state="' . $cidade['uf_id'] . '">';
                        echo '<td>' . $cidade['cidade_id'] . '</td>';
                        echo '<td>';
                        echo '<div class="city-info">';
                        echo '<i class="fas fa-city"></i>';
                        echo '<span>' . htmlspecialchars($cidade['cidade_nome']) . '</span>';
                        echo '</div>';
                        echo '</td>';
                        echo '<td><span class="state-badge">' . htmlspecialchars($cidade['uf_sigla']) . '</span></td>';
                        echo '<td>';
                        echo '<div class="points-indicator">';
                        echo '<span class="points-count">' . $cidade['total_pontos'] . '</span>';
                        echo '<div class="points-bar">';
                        echo '<div class="points-fill" style="width: ' . min($cidade['total_pontos'] * 10, 100) . '%"></div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</td>';
                        echo '<td>' . date('d/m/Y', strtotime($cidade['dt_criacao'])) . '</td>';
                        echo '<td class="actions">';
                        echo '<button type="button" class="btn btn-sm btn-secondary" onclick="openEditCityModal(' . $cidade['cidade_id'] . ', \'' . htmlspecialchars($cidade['cidade_nome']) . '\', ' . $cidade['uf_id'] . ')">';
                        echo '<i class="fas fa-edit"></i> Editar';
                        echo '</button>';
                        echo '<button type="button" class="btn btn-sm btn-info" onclick="viewCityDetails(' . $cidade['cidade_id'] . ')">';
                        echo '<i class="fas fa-eye"></i>';
                        echo '</button>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } catch (PDOException $e) {
                    echo '<tr><td colspan="6" class="text-center error">Erro ao carregar cidades: ' . $e->getMessage() . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="table-footer">
        <div class="pagination">
            <button class="page-btn" onclick="changePage(-1)"><i class="fas fa-chevron-left"></i></button>
            <span class="page-info">Página <span id="current-page">1</span> de <span id="total-pages">1</span></span>
            <button class="page-btn" onclick="changePage(1)"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</div>

<!-- Modal para adicionar/editar cidade -->
<div id="cityModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="cityModalTitle"><i class="fas fa-city"></i> Adicionar Cidade</h3>
            <button type="button" onclick="closeModal('cityModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="post" action="admin/cidades-action.php" id="cityForm">
                <input type="hidden" name="action" id="cityAction" value="add_city">
                <input type="hidden" name="cidade_id" id="cityId">
                
                <div class="form-group">
                    <label for="cidade_nome">Nome da Cidade:</label>
                    <input type="text" name="cidade_nome" id="cityName" required 
                           placeholder="Digite o nome da cidade">
                </div>
                
                <div class="form-group">
                    <label for="uf_id">Estado:</label>
                    <select name="uf_id" id="cityState" required>
                        <option value="">Selecione um estado</option>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT * FROM estados ORDER BY uf_sigla");
                            while ($estado = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $estado['uf_id'] . '">' . htmlspecialchars($estado['uf_sigla']) . ' - ' . htmlspecialchars($estado['uf_nome']) . '</option>';
                            }
                        } catch (PDOException $e) {
                            echo '<option value="">Erro ao carregar estados</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group" id="cityPreview" style="display: none;">
                    <label>Pré-visualização:</label>
                    <div class="preview-card">
                        <div class="preview-city">
                            <i class="fas fa-city"></i>
                            <span id="previewCityName"></span>
                        </div>
                        <div class="preview-state">
                            Estado: <span id="previewState" class="state-badge"></span>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" onclick="closeModal('cityModal')" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="citySubmitBtn">
                        <i class="fas fa-plus"></i> Adicionar Cidade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de detalhes da cidade -->
<div id="cityDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-info-circle"></i> Detalhes da Cidade</h3>
            <button type="button" onclick="closeModal('cityDetailsModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="cityDetailsContent">
                <!-- Conteúdo será preenchido via JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Modal de pontos da cidade -->
<div id="cityPointsModal" class="modal large">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-map-marker-alt"></i> Pontos de Ajuda</h3>
            <button type="button" onclick="closeModal('cityPointsModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="cityPointsContent">
                <!-- Conteúdo será preenchido via JavaScript -->
            </div>
        </div>
    </div>
</div>

<div id="toast-container"></div>

<script>
let currentCityPage = 1;
const citiesPerPage = 10;
let currentCitySort = { column: 'name', direction: 'asc' };

// Função genérica para fechar modais
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Função para mostrar notificações
function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    setTimeout(() => {
        toast.classList.remove('show');
        toast.addEventListener('transitionend', () => toast.remove());
    }, 4000);
}

// Filtrar cidades
function filterCities() {
    const searchTerm = document.getElementById('city-search').value.toLowerCase();
    const stateFilter = document.getElementById('state-filter').value;
    
    const tableRows = document.querySelectorAll('#cities-table tr[data-city-id]');
    let visibleCount = 0;
    
    tableRows.forEach(row => {
        const cityName = row.cells[1].textContent.toLowerCase();
        const state = row.dataset.state;
        
        const matchesSearch = cityName.includes(searchTerm);
        const matchesState = !stateFilter || state === stateFilter;
        
        if (matchesSearch && matchesState) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('cities-count').textContent = visibleCount;
    updateCityPagination(visibleCount);
}

// Ordenar cidades
function sortCities(column) {
    const rows = Array.from(document.querySelectorAll('#cities-table tr[data-city-id]'));
    const tbody = document.getElementById('cities-table');
    
    if (currentCitySort.column === column) {
        currentCitySort.direction = currentCitySort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentCitySort.column = column;
        currentCitySort.direction = 'asc';
    }
    
    rows.sort((a, b) => {
        let aValue, bValue;
        
        switch(column) {
            case 'id':
                aValue = parseInt(a.cells[0].textContent);
                bValue = parseInt(b.cells[0].textContent);
                break;
            case 'points':
                aValue = parseInt(a.cells[3].querySelector('.points-count').textContent);
                bValue = parseInt(b.cells[3].querySelector('.points-count').textContent);
                break;
            case 'date':
                aValue = new Date(a.cells[4].textContent.split('/').reverse().join('-'));
                bValue = new Date(b.cells[4].textContent.split('/').reverse().join('-'));
                break;
            default:
                aValue = a.cells[getCityColumnIndex(column)].textContent.toLowerCase();
                bValue = b.cells[getCityColumnIndex(column)].textContent.toLowerCase();
        }
        
        if (currentCitySort.direction === 'asc') {
            return aValue > bValue ? 1 : -1;
        } else {
            return aValue < bValue ? 1 : -1;
        }
    });
    
    // Remove todas as linhas
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }
    
    // Adiciona as linhas ordenadas
    rows.forEach(row => tbody.appendChild(row));
    
    // Atualiza ícones de ordenação
    updateCitySortIcons(column);
}

function getCityColumnIndex(column) {
    const columns = ['id', 'name', 'state', 'points', 'date'];
    return columns.indexOf(column);
}

function updateCitySortIcons(activeColumn) {
    document.querySelectorAll('#cities-table th[data-sort]').forEach(th => {
        const icon = th.querySelector('i');
        if (th.dataset.sort === activeColumn) {
            icon.className = currentCitySort.direction === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
        } else {
            icon.className = 'fas fa-sort';
        }
    });
}

// Modal para adicionar cidade
function openAddCityModal() {
    document.getElementById('cityModalTitle').innerHTML = '<i class="fas fa-plus"></i> Adicionar Cidade';
    document.getElementById('cityAction').value = 'add_city';
    document.getElementById('cityId').value = '';
    document.getElementById('cityName').value = '';
    document.getElementById('cityState').selectedIndex = 0;
    document.getElementById('cityPreview').style.display = 'none';
    document.getElementById('citySubmitBtn').innerHTML = '<i class="fas fa-plus"></i> Adicionar Cidade';
    
    document.getElementById('cityModal').style.display = 'flex';
}

// Modal para editar cidade
function openEditCityModal(cidadeId, cidadeNome, ufId) {
    document.getElementById('cityModalTitle').innerHTML = '<i class="fas fa-edit"></i> Editar Cidade';
    document.getElementById('cityAction').value = 'edit_city';
    document.getElementById('cityId').value = cidadeId;
    document.getElementById('cityName').value = cidadeNome;
    document.getElementById('cityState').value = ufId;
    document.getElementById('cityPreview').style.display = 'block';
    document.getElementById('citySubmitBtn').innerHTML = '<i class="fas fa-save"></i> Salvar Alterações';
    
    updateCityPreview();
    
    document.getElementById('cityModal').style.display = 'flex';
}

// Atualizar preview da cidade
function updateCityPreview() {
    const cityName = document.getElementById('cityName').value;
    const stateSelect = document.getElementById('cityState');
    const stateText = stateSelect.options[stateSelect.selectedIndex]?.text.split(' - ')[0] || '';
    
    if (cityName && stateText) {
        document.getElementById('previewCityName').textContent = cityName;
        document.getElementById('previewState').textContent = stateText;
        document.getElementById('cityPreview').style.display = 'block';
    } else {
        document.getElementById('cityPreview').style.display = 'none';
    }
}

// Visualizar detalhes da cidade
function viewCityDetails(cityId) {
    // Simulação de carregamento de dados da cidade
    const row = document.querySelector(`tr[data-city-id="${cityId}"]`);
    
    document.getElementById('cityDetailsContent').innerHTML = `
        <div class="city-details">
            <div class="city-header">
                <div class="city-icon-large">
                    <i class="fas fa-city"></i>
                </div>
                <div class="city-main-info">
                    <h3>${row.cells[1].textContent.trim()}</h3>
                    <span class="state-badge large">${row.cells[2].textContent.trim()}</span>
                </div>
            </div>
            
            <div class="city-stats-detailed">
                <div class="stat-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="stat-info">
                        <span class="stat-number">${row.cells[3].querySelector('.points-count').textContent}</span>
                        <span class="stat-label">Pontos de Ajuda</span>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar"></i>
                    <div class="stat-info">
                        <span class="stat-number">${row.cells[4].textContent}</span>
                        <span class="stat-label">Data de Cadastro</span>
                    </div>
                </div>
            </div>
            
            <div class="city-actions-detailed">
                <button class="btn btn-primary" onclick="viewCityPoints(${cityId})">
                    <i class="fas fa-map-marker-alt"></i> Ver Pontos
                </button>
                <button class="btn btn-secondary" onclick="openEditCityModal(${cityId}, '${row.cells[1].textContent.trim()}', ${row.dataset.state})">
                    <i class="fas fa-edit"></i> Editar Cidade
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('cityDetailsModal').style.display = 'flex';
}

// Visualizar pontos da cidade
function viewCityPoints(cityId) {
    // Simulação de carregamento de pontos da cidade
    document.getElementById('cityPointsContent').innerHTML = `
        <div class="points-list">
            <h4>Pontos de Ajuda nesta cidade</h4>
            <div class="points-grid">
                <div class="point-card">
                    <div class="point-type alimento">Alimento</div>
                    <h5>Doação de Cestas Básicas</h5>
                    <p><i class="fas fa-map-marker"></i> Rua Principal, 123</p>
                    <div class="point-status active">Ativo</div>
                </div>
                <div class="point-card">
                    <div class="point-type roupa">Roupa</div>
                    <h5>Campanha do Agasalho</h5>
                    <p><i class="fas fa-map-marker"></i> Av. Central, 456</p>
                    <div class="point-status active">Ativo</div>
                </div>
                <div class="point-card">
                    <div class="point-type higiene">Higiene</div>
                    <h5>Doação de Produtos</h5>
                    <p><i class="fas fa-map-marker"></i> Praça da Matriz, 789</p>
                    <div class="point-status inactive">Inativo</div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('cityPointsModal').style.display = 'flex';
}

// Atualizar paginação
function updateCityPagination(totalCities) {
    const totalPages = Math.ceil(totalCities / citiesPerPage);
    document.getElementById('current-page').textContent = currentCityPage;
    document.getElementById('total-pages').textContent = totalPages || 1;
}

// Mudar página
function changePage(direction) {
    currentCityPage += direction;
    if (currentCityPage < 1) currentCityPage = 1;
    
    const totalCities = document.querySelectorAll('#cities-table tr[data-city-id]').length;
    const totalPages = Math.ceil(totalCities / citiesPerPage);
    
    if (currentCityPage > totalPages) currentCityPage = totalPages;
    
    document.getElementById('current-page').textContent = currentCityPage;
    document.getElementById('total-pages').textContent = totalPages;
    
    // Implementar lógica de paginação aqui
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Ordenação da tabela
    document.querySelectorAll('#cities-table th[data-sort]').forEach(th => {
        th.style.cursor = 'pointer';
        th.addEventListener('click', () => sortCities(th.dataset.sort));
    });
    
    // Preview em tempo real no modal
    document.getElementById('cityName').addEventListener('input', updateCityPreview);
    document.getElementById('cityState').addEventListener('change', updateCityPreview);
    
    // Contar cidades inicialmente
    const totalCities = document.querySelectorAll('#cities-table tr[data-city-id]').length;
    document.getElementById('cities-count').textContent = totalCities;
    updateCityPagination(totalCities);
    
    // Fechar modal ao clicar fora dele
    window.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
});
</script>

</body>
</html>