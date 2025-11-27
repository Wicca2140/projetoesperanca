<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Pontos de Ajuda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos base do sistema (mantidos) */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0;}
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

        /* Toggle Switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            margin-right: 8px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #28a745;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(20px);
        }

        .status-text {
            font-size: 0.85rem;
            font-weight: 500;
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

        /* Estilos específicos para pontos de ajuda */
        .point-title {
            max-width: 200px;
        }

        .point-description {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 4px;
        }

        .address-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .address-info i {
            color: #4361ee;
            font-size: 0.9rem;
        }

        .user-info-small {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Responsável sem avatar circular */
        .responsible-name {
            font-weight: 500;
            color: #495057;
        }

        .point-type {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
        }

        .point-type-alimento { background: #ffeaa7; color: #e17055; }
        .point-type-roupa { background: #a29bfe; color: white; }
        .point-type-higiene { background: #74b9ff; color: white; }
        .point-type-outros { background: #dfe6e9; color: #636e72; }

        /* Point Details Styles */
        .point-details .point-header {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .point-type-large {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }

        .point-type-large.point-type-alimento { background: #ffeaa7; color: #e17055; }
        .point-type-large.point-type-roupa { background: #a29bfe; color: white; }
        .point-type-large.point-type-higiene { background: #74b9ff; color: white; }
        .point-type-large.point-type-outros { background: #dfe6e9; color: #636e72; }

        .point-main-info h3 {
            margin: 0 0 8px 0;
            color: #2c3e50;
        }

        .point-description-full {
            color: #6c757d;
            margin: 0;
            line-height: 1.5;
        }

        .point-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .info-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

        .info-section h4 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item label {
            font-weight: 500;
            color: #495057;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-contact {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .point-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-badge.active {
            background-color: #28a74520;
            color: #28a745;
        }

        .status-badge.inactive {
            background-color: #dc354520;
            color: #dc3545;
        }

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
        .toast.warning { background-color: #ffc107; color: #212529; }

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
            
            .point-info-grid {
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
            
            .point-actions {
                flex-direction: column;
            }
            
            .table-footer {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

<div class="admin-section">
    <div class="section-header">
        <h2><i class="fas fa-map-marker-alt"></i> Gerenciar Pontos de Ajuda</h2>
        <div class="header-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="point-search" placeholder="Buscar pontos..." onkeyup="filterPoints()">
            </div>
            <button class="btn btn-primary" onclick="exportPoints()">
                <i class="fas fa-download"></i> Exportar
            </button>
        </div>
    </div>

    <div class="filters-bar">
        <div class="filter-group">
            <label>Filtrar por:</label>
            <select id="status-filter" onchange="filterPoints()">
                <option value="">Todos os status</option>
                <option value="active">Ativos</option>
                <option value="inactive">Inativos</option>
            </select>
            <select id="type-filter" onchange="filterPoints()">
                <option value="">Todos os tipos</option>
                <option value="alimento">Alimento</option>
                <option value="roupa">Roupa</option>
                <option value="higiene">Higiene</option>
                <option value="outros">Outros</option>
            </select>
            <select id="city-filter" onchange="filterPoints()">
                <option value="">Todas as cidades</option>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM cidades ORDER BY cidade_nome");
                    while ($cidade = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . $cidade['cidade_id'] . '">' . htmlspecialchars($cidade['cidade_nome']) . '</option>';
                    }
                } catch (PDOException $e) {
                    echo '<option value="">Erro ao carregar cidades</option>';
                }
                ?>
            </select>
        </div>
        <div class="results-info">
            <span id="points-count">0</span> pontos encontrados
        </div>
    </div>
    
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th data-sort="id">ID <i class="fas fa-sort"></i></th>
                    <th data-sort="title">Título <i class="fas fa-sort"></i></th>
                    <th data-sort="type">Tipo <i class="fas fa-sort"></i></th>
                    <th data-sort="address">Endereço <i class="fas fa-sort"></i></th>
                    <th data-sort="city">Cidade <i class="fas fa-sort"></i></th>
                    <th data-sort="responsible">Responsável <i class="fas fa-sort"></i></th>
                    <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                    <th data-sort="date">Data Cadastro <i class="fas fa-sort"></i></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="points-table">
                <?php
                try {
                    $stmt = $pdo->query("
                        SELECT p.*, u.nome_completo, c.cidade_nome 
                        FROM pontos_ajuda p 
                        JOIN usuarios u ON p.usuario_id = u.usuario_id 
                        JOIN cidades c ON p.cidade_id = c.cidade_id 
                        ORDER BY p.dt_criacao DESC
                    ");
                    $pontos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($pontos as $ponto) {
                        echo '<tr data-point-id="' . $ponto['ponto_id'] . '" 
                              data-status="' . ($ponto['ativo'] ? 'active' : 'inactive') . '" 
                              data-type="' . $ponto['tipo_ajuda'] . '" 
                              data-city="' . $ponto['cidade_id'] . '">';
                        echo '<td>' . $ponto['ponto_id'] . '</td>';
                        echo '<td>';
                        echo '<div class="point-title">';
                        echo '<strong>' . htmlspecialchars($ponto['titulo']) . '</strong>';
                        if ($ponto['descricao']) {
                            echo '<div class="point-description">' . htmlspecialchars(substr($ponto['descricao'], 0, 50)) . '...</div>';
                        }
                        echo '</div>';
                        echo '</td>';
                        echo '<td><span class="point-type point-type-' . $ponto['tipo_ajuda'] . '">' . ucfirst($ponto['tipo_ajuda']) . '</span></td>';
                        echo '<td>';
                        echo '<div class="address-info">';
                        echo '<i class="fas fa-map-marker-alt"></i>';
                        echo '<span>' . htmlspecialchars($ponto['endereco']) . '</span>';
                        echo '</div>';
                        echo '</td>';
                        echo '<td>' . htmlspecialchars($ponto['cidade_nome']) . '</td>';
                        echo '<td>';
                        echo '<span class="responsible-name">' . htmlspecialchars($ponto['nome_completo']) . '</span>';
                        echo '</td>';
                        echo '<td>';
                        echo '<label class="toggle-switch">';
                        echo '<input type="checkbox" ' . ($ponto['ativo'] ? 'checked' : '') . ' onchange="togglePointStatus(' . $ponto['ponto_id'] . ', this.checked)">';
                        echo '<span class="toggle-slider"></span>';
                        echo '</label>';
                        echo '<span class="status-text">' . ($ponto['ativo'] ? 'Ativo' : 'Inativo') . '</span>';
                        echo '</td>';
                        echo '<td>' . date('d/m/Y H:i', strtotime($ponto['dt_criacao'])) . '</td>';
                        echo '<td class="actions">';
                        
                        echo '<button type="button" class="btn btn-sm btn-secondary" onclick="editPoint(' . $ponto['ponto_id'] . ')">';
                        echo '<i class="fas fa-edit"></i>';
                        echo '</button>';
                        
                        echo '<button type="button" class="btn btn-sm btn-info" onclick="viewPointDetails(' . $ponto['ponto_id'] . ')">';
                        echo '<i class="fas fa-eye"></i>';
                        echo '</button>';
                        
                        echo '</td>';
                        echo '</tr>';
                    }
                } catch (PDOException $e) {
                    echo '<tr><td colspan="9" class="text-center error">Erro ao carregar pontos: ' . $e->getMessage() . '</td></tr>';
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

<!-- Modal de detalhes do ponto -->
<div id="pointDetailsModal" class="modal large">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-info-circle"></i> Detalhes do Ponto de Ajuda</h3>
            <button type="button" onclick="closeModal('pointDetailsModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="pointDetailsContent">
                <!-- Conteúdo será preenchido via JavaScript -->
            </div>
        </div>
    </div>
</div>

<div id="toast-container"></div>

<script>
let currentPointPage = 1;
const pointsPerPage = 10;
let currentPointSort = { column: 'date', direction: 'desc' };

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

// Filtrar pontos
function filterPoints() {
    const searchTerm = document.getElementById('point-search').value.toLowerCase();
    const statusFilter = document.getElementById('status-filter').value;
    const typeFilter = document.getElementById('type-filter').value;
    const cityFilter = document.getElementById('city-filter').value;
    
    const rows = document.querySelectorAll('#points-table tr[data-point-id]');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const title = row.cells[1].textContent.toLowerCase();
        const type = row.dataset.type;
        const status = row.dataset.status;
        const city = row.dataset.city;
        
        const matchesSearch = title.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        const matchesType = !typeFilter || type === typeFilter;
        const matchesCity = !cityFilter || city === cityFilter;
        
        if (matchesSearch && matchesStatus && matchesType && matchesCity) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('points-count').textContent = visibleCount;
    updatePointPagination(visibleCount);
}

// Ordenar pontos
function sortPoints(column) {
    const rows = Array.from(document.querySelectorAll('#points-table tr[data-point-id]'));
    const tbody = document.getElementById('points-table');
    
    if (currentPointSort.column === column) {
        currentPointSort.direction = currentPointSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentPointSort.column = column;
        currentPointSort.direction = 'desc';
    }
    
    rows.sort((a, b) => {
        let aValue, bValue;
        
        switch(column) {
            case 'id':
                aValue = parseInt(a.cells[0].textContent);
                bValue = parseInt(b.cells[0].textContent);
                break;
            case 'date':
                aValue = new Date(a.cells[7].textContent.split(' ')[0].split('/').reverse().join('-') + ' ' + a.cells[7].textContent.split(' ')[1]);
                bValue = new Date(b.cells[7].textContent.split(' ')[0].split('/').reverse().join('-') + ' ' + b.cells[7].textContent.split(' ')[1]);
                break;
            default:
                aValue = a.cells[getPointColumnIndex(column)].textContent.toLowerCase();
                bValue = b.cells[getPointColumnIndex(column)].textContent.toLowerCase();
        }
        
        if (currentPointSort.direction === 'asc') {
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
    updatePointSortIcons(column);
}

function getPointColumnIndex(column) {
    const columns = ['id', 'title', 'type', 'address', 'city', 'responsible', 'status', 'date'];
    return columns.indexOf(column);
}

function updatePointSortIcons(activeColumn) {
    document.querySelectorAll('#points-table th[data-sort]').forEach(th => {
        const icon = th.querySelector('i');
        if (th.dataset.sort === activeColumn) {
            icon.className = currentPointSort.direction === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
        } else {
            icon.className = 'fas fa-sort';
        }
    });
}

function togglePointStatus(pointId, isActive) {
    if (confirm(`Tem certeza que deseja ${isActive ? 'ativar' : 'desativar'} este ponto?`)) {
        // Simulação de requisição AJAX
        showToast(`Alterando status do ponto ${pointId}...`, 'info');
        
        setTimeout(() => {
            // Atualizar texto de status
            const row = document.querySelector(`tr[data-point-id="${pointId}"]`);
            row.querySelector('.status-text').textContent = isActive ? 'Ativo' : 'Inativo';
            row.dataset.status = isActive ? 'active' : 'inactive';
            showToast(`Ponto ${isActive ? 'ativado' : 'desativado'} com sucesso!`, 'success');
        }, 800);
    } else {
        // Reverter o toggle se o usuário cancelar
        const checkbox = document.querySelector(`tr[data-point-id="${pointId}"] input[type="checkbox"]`);
        checkbox.checked = !isActive;
    }
}

function viewPointDetails(pointId) {
    // Simulação de carregamento de dados do ponto
    const row = document.querySelector(`tr[data-point-id="${pointId}"]`);
    
    document.getElementById('pointDetailsContent').innerHTML = `
        <div class="point-details">
            <div class="point-header">
                <div class="point-type-large point-type-${row.dataset.type}">
                    <i class="fas fa-${getPointTypeIcon(row.dataset.type)}"></i>
                </div>
                <div class="point-main-info">
                    <h3>${row.cells[1].querySelector('strong').textContent}</h3>
                    <p class="point-description-full">${row.cells[1].querySelector('.point-description')?.textContent || 'Sem descrição adicional'}</p>
                </div>
            </div>
            
            <div class="point-info-grid">
                <div class="info-section">
                    <h4><i class="fas fa-map-marker-alt"></i> Localização</h4>
                    <div class="info-item">
                        <label>Endereço:</label>
                        <span>${row.cells[3].textContent}</span>
                    </div>
                    <div class="info-item">
                        <label>Cidade:</label>
                        <span>${row.cells[4].textContent}</span>
                    </div>
                </div>
                
                <div class="info-section">
                    <h4><i class="fas fa-user"></i> Responsável</h4>
                    <div class="user-info">
                        <div class="user-details">
                            <strong>${row.cells[5].textContent}</strong>
                            <span class="user-contact">contato@exemplo.com</span>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <h4><i class="fas fa-info-circle"></i> Informações</h4>
                    <div class="info-item">
                        <label>Status:</label>
                        <span class="status-badge ${row.dataset.status === 'active' ? 'active' : 'inactive'}">${row.dataset.status === 'active' ? 'Ativo' : 'Inativo'}</span>
                    </div>
                    <div class="info-item">
                        <label>Data de Cadastro:</label>
                        <span>${row.cells[7].textContent}</span>
                    </div>
                    <div class="info-item">
                        <label>Horário de Funcionamento:</label>
                        <span>08:00 - 18:00</span>
                    </div>
                </div>
            </div>
            
            <div class="point-actions">
                <button class="btn btn-secondary" onclick="editPoint(${pointId})">
                    <i class="fas fa-edit"></i> Editar Ponto
                </button>
                <button class="btn btn-info">
                    <i class="fas fa-phone"></i> Contatar
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('pointDetailsModal').style.display = 'flex';
}

function getPointTypeIcon(type) {
    const icons = {
        'alimento': 'utensils',
        'roupa': 'tshirt',
        'higiene': 'soap',
        'outros': 'hands-helping'
    };
    return icons[type] || 'map-marker-alt';
}

function editPoint(pointId) {
    // Redirecionar para página de edição
    showToast(`Redirecionando para edição do ponto ${pointId}...`, 'info');
    // window.location.href = `editar-ponto.php?id=${pointId}`;
}

function exportPoints() {
    showToast('Preparando exportação de pontos...', 'info');
    // Simulação de exportação
    setTimeout(() => {
        showToast('Pontos exportados com sucesso!', 'success');
    }, 2000);
}

function updatePointPagination(totalPoints) {
    const totalPages = Math.ceil(totalPoints / pointsPerPage);
    document.getElementById('current-page').textContent = currentPointPage;
    document.getElementById('total-pages').textContent = totalPages || 1;
}

function changePage(direction) {
    currentPointPage += direction;
    if (currentPointPage < 1) currentPointPage = 1;
    
    const totalPoints = document.querySelectorAll('#points-table tr[data-point-id]').length;
    const totalPages = Math.ceil(totalPoints / pointsPerPage);
    
    if (currentPointPage > totalPages) currentPointPage = totalPages;
    
    document.getElementById('current-page').textContent = currentPointPage;
    document.getElementById('total-pages').textContent = totalPages;
    
    // Implementar lógica de paginação aqui
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Ordenação da tabela
    document.querySelectorAll('#points-table th[data-sort]').forEach(th => {
        th.style.cursor = 'pointer';
        th.addEventListener('click', () => sortPoints(th.dataset.sort));
    });
    
    // Contar pontos inicialmente
    const totalPoints = document.querySelectorAll('#points-table tr[data-point-id]').length;
    document.getElementById('points-count').textContent = totalPoints;
    updatePointPagination(totalPoints);
    
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