<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Usuários</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* -------------------------------------- */
        /* CSS INCLUÍDO AQUI (SEM ALTERAÇÕES)     */
        /* -------------------------------------- */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9;  }
        .admin-section { max-width: 1200px; margin: 0 auto; }
        
        /* Estilos fornecidos pelo usuário */
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

        .user-avatar-small {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 8px;
        }

        .role-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .role-1 { background: #ffeaa7; color: #e17055; }
        .role-2 { background: #a29bfe; color: white; }
        .role-3 { background: #74b9ff; color: white; }

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

        /* Modal Styles */
        .modal {
            display: none; /* Alterado para none por padrão */
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

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
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

        /* User Preview & Details */
        .user-info-preview {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .user-avatar-large {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .user-avatar-xlarge {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 600;
        }

        .user-details-full .user-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .user-main-info h3 {
            margin: 0 0 5px 0;
            color: #2c3e50;
        }

        .user-username {
            color: #6c757d;
            margin: 0 0 10px 0;
        }

        .user-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-item label {
            font-weight: 500;
            color: #495057;
            font-size: 0.9rem;
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

        .user-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        /* Toast Message (Simulação de notificação) */
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
            
            .user-info-grid {
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
        }
    </style>
</head>
<body>

<?php
// --------------------------------------------------------------------------------------------------
// BLOCO PHP: CONEXÃO (ASSUME QUE ESTÁ DEFINIDA) E CARREGAMENTO DE DADOS INICIAIS
// --------------------------------------------------------------------------------------------------

// ***** IMPORTANTE *****
// Você deve definir a variável $pdo com sua conexão de banco de dados PDO ANTES deste bloco
// Exemplo (Remova os comentários e preencha com seus dados):
/*
$host = 'localhost';
$db   = 'seu_banco';
$user = 'seu_usuario';
$pass = 'sua_senha';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
*/
// Se $pdo não estiver definida, o código abaixo falhará.
// ----------------------

$cargos = [];
$usuarios = [];
$db_error = null;

if (isset($pdo)) {
    try {
        // Carrega as permissões (cargos)
        $stmt_cargos = $pdo->query("SELECT * FROM permissoes ORDER BY permissao_id");
        $cargos = $stmt_cargos->fetchAll(PDO::FETCH_ASSOC);

        // Carrega a lista completa de usuários
        $stmt_usuarios = $pdo->query("
            SELECT u.*, p.cargo 
            FROM usuarios u 
            JOIN permissoes p ON u.nivel_acesso_id = p.permissao_id 
            ORDER BY u.dt_criacao DESC
        ");
        $usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $db_error = 'Erro ao carregar dados do banco: ' . $e->getMessage();
    }
} else {
    $db_error = "Erro: Variável \$pdo não está definida. Verifique sua conexão com o banco de dados.";
}
?>

<div class="admin-section">
    <div class="section-header">
        <h2><i class="fas fa-users"></i> Gerenciar Usuários</h2>
        <div class="header-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="user-search" placeholder="Buscar usuários..." onkeyup="filterUsers()">
            </div>
            <button class="btn btn-primary" onclick="exportUsers()">
                <i class="fas fa-download"></i> Exportar
            </button>
        </div>
    </div>

    <div class="filters-bar">
        <div class="filter-group">
            <label>Filtrar por:</label>
            <select id="status-filter" onchange="filterUsers()">
                <option value="">Todos os status</option>
                <option value="active">Ativos</option>
                <option value="inactive">Inativos</option>
            </select>
            <select id="role-filter" onchange="filterUsers()">
                <option value="">Todos os cargos</option>
                <?php
                if ($db_error) {
                    echo '<option value="">' . htmlspecialchars($db_error) . '</option>';
                } else {
                    foreach ($cargos as $cargo) {
                        echo '<option value="' . $cargo['permissao_id'] . '">' . htmlspecialchars($cargo['cargo']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="results-info">
            <span id="users-count">0</span> usuários encontrados
        </div>
    </div>
    
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th data-sort="id">ID <i class="fas fa-sort"></i></th>
                    <th data-sort="username">Usuário <i class="fas fa-sort"></i></th>
                    <th data-sort="name">Nome Completo <i class="fas fa-sort"></i></th>
                    <th data-sort="company">Razão Social <i class="fas fa-sort"></i></th>
                    <th data-sort="role">Cargo <i class="fas fa-sort"></i></th>
                    <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                    <th data-sort="date">Data Cadastro <i class="fas fa-sort"></i></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="users-table">
                <?php
                if ($db_error) {
                    echo '<tr><td colspan="8" class="text-center error" style="text-align:center; color: red;">' . htmlspecialchars($db_error) . '</td></tr>';
                } else {
                    foreach ($usuarios as $usuario) {
                        $is_active = $usuario['status'] ? 'active' : 'inactive';
                        $status_text = $usuario['status'] ? 'Ativo' : 'Inativo';
                        $data_cadastro = isset($usuario['dt_criacao']) ? date('d/m/Y', strtotime($usuario['dt_criacao'])) : 'N/A';
                        
                        // Note que todas as linhas são renderizadas no HTML, mas o JS cuidará de ocultar/mostrar
                        echo '<tr data-user-id="' . $usuario['usuario_id'] . '" data-status="' . $is_active . '" data-role="' . $usuario['nivel_acesso_id'] . '">';
                        echo '<td>' . htmlspecialchars($usuario['usuario_id']) . '</td>';
                        echo '<td>';
                        echo '<div class="user-avatar-small">';
                        echo strtoupper(substr($usuario['usuario'], 0, 1));
                        echo '</div>';
                        echo htmlspecialchars($usuario['usuario']);
                        echo '</td>';
                        echo '<td>' . htmlspecialchars($usuario['nome_completo']) . '</td>';
                        echo '<td>' . htmlspecialchars($usuario['razao_social']) . '</td>';
                        echo '<td><span class="role-badge role-' . $usuario['nivel_acesso_id'] . '">' . htmlspecialchars($usuario['cargo']) . '</span></td>';
                        
                        // Coluna do Status (contém o toggle e o texto)
                        echo '<td>'; 
                        echo '<label class="toggle-switch">';
                        echo '<input type="checkbox" ' . ($usuario['status'] ? 'checked' : '') . ' onchange="toggleUserStatus(' . $usuario['usuario_id'] . ', this.checked)">';
                        echo '<span class="toggle-slider"></span>';
                        echo '</label>';
                        echo '<span class="status-text">' . $status_text . '</span>';
                        echo '</td>';
                        
                        // Coluna da Data (Índice 7)
                        echo '<td>' . htmlspecialchars($data_cadastro) . '</td>';

                        // Coluna de Ações
                        echo '<td class="actions">';
                        echo '<button type="button" class="btn btn-sm btn-secondary" onclick="openEditModal(' . $usuario['usuario_id'] . ', ' . $usuario['nivel_acesso_id'] . ')">';
                        echo '<i class="fas fa-edit"></i> Editar';
                        echo '</button>';
                        echo '<button type="button" class="btn btn-sm btn-info" onclick="viewUserDetails(' . $usuario['usuario_id'] . ')">';
                        echo '<i class="fas fa-eye"></i>';
                        echo '</button>';
                        echo '</td>';
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="table-footer">
        <div></div> <div class="pagination">
            <button class="page-btn" onclick="changePage(-1)"><i class="fas fa-chevron-left"></i></button>
            <span class="page-info">Página <span id="current-page">1</span> de <span id="total-pages">1</span></span>
            <button class="page-btn" onclick="changePage(1)"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-edit"></i> Editar Cargo do Usuário</h3>
            <button type="button" onclick="closeModal('editModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="post" id="editForm" action="usuarios-action.php">
                <input type="hidden" name="action" value="update_user_role">
                <input type="hidden" name="user_id" id="editUserId">
                
                <div class="user-preview" id="userPreview">
                    </div>

                <div class="form-group">
                    <label for="nivel_acesso_id">Novo Cargo:</label>
                    <select name="nivel_acesso_id" id="editNivelAcesso" required>
                        <?php
                        // Repete o carregamento de cargos para o modal
                        if ($db_error) {
                            echo '<option value="">' . htmlspecialchars($db_error) . '</option>';
                        } else {
                            foreach ($cargos as $cargo) {
                                echo '<option value="' . $cargo['permissao_id'] . '">' . htmlspecialchars($cargo['cargo']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" onclick="closeModal('editModal')" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="userDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user"></i> Detalhes do Usuário</h3>
            <button type="button" onclick="closeModal('userDetailsModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="userDetailsContent">
                </div>
            <div class="user-actions">
                <button class="btn btn-secondary" id="editRoleFromDetails">
                    <i class="fas fa-edit"></i> Editar Cargo
                </button>
                <button class="btn btn-info">
                    <i class="fas fa-envelope"></i> Enviar Mensagem
                </button>
            </div>
        </div>
    </div>
</div>

<div id="toast-container"></div>


<script>
let currentPage = 1;
const usersPerPage = 10;
let currentSort = { column: 'id', direction: 'asc' };

// Função genérica para fechar modais
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// ----------------------------------------------------
// Toast e AJAX Simulado
// ----------------------------------------------------

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

function toggleUserStatus(userId, isActive) {
    if (confirm(`Tem certeza que deseja ${isActive ? 'ativar' : 'desativar'} este usuário?`)) {
        showToast(`Alterando status do Usuário ${userId}...`, 'info');
        // Simulação de requisição AJAX
        /* fetch('usuarios-action.php', {
            // ... (Lógica de requisição real)
        })
        */
        
        setTimeout(() => {
            // Atualiza o estado visual após a "simulação de sucesso"
            const row = document.querySelector(`tr[data-user-id="${userId}"]`);
            if (row) {
                row.querySelector('.status-text').textContent = isActive ? 'Ativo' : 'Inativo';
                row.dataset.status = isActive ? 'active' : 'inactive';
                showToast(`Usuário ${isActive ? 'ativado' : 'desativado'} com sucesso!`, 'success');
                // Re-renderiza para garantir a paginação e filtros
                renderUsers(); 
            }
        }, 800);
        
    } else {
        // Reverter o toggle se o usuário cancelar
        const checkbox = document.querySelector(`tr[data-user-id="${userId}"] input[type="checkbox"]`);
        checkbox.checked = !isActive;
    }
}

function exportUsers() {
    showToast('Preparando exportação para Excel...', 'info');
    // Implementação real requer comunicação com o backend (PHP)
    setTimeout(() => {
        showToast('Usuários exportados com sucesso! (Simulação concluída)', 'success');
    }, 2000);
}


// ----------------------------------------------------
// Modais
// ----------------------------------------------------

function getRowData(userId) {
    const row = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (!row) return null;
    return {
        id: row.cells[0].textContent,
        username: row.cells[1].textContent.trim().replace(row.cells[1].querySelector('.user-avatar-small').textContent, ''), // Remove avatar initial
        name: row.cells[2].textContent,
        company: row.cells[3].textContent,
        roleText: row.cells[4].textContent,
        roleId: row.dataset.role,
        status: row.dataset.status,
        statusText: row.cells[6].textContent,
        date: row.cells[7].textContent
    };
}

function openEditModal(userId, nivelAcessoId) {
    const userInfo = getRowData(userId);
    if (!userInfo) return;
    
    // 1. Preenche os campos do formulário
    document.getElementById('editUserId').value = userId;
    document.getElementById('editNivelAcesso').value = nivelAcessoId;
    
    // 2. Preenche o preview do usuário
    document.getElementById('userPreview').innerHTML = `
        <div class="user-info-preview">
            <div class="user-avatar-large">${userInfo.name.charAt(0)}</div>
            <div class="user-details">
                <h4>${userInfo.name}</h4>
                <p>Usuário: ${userInfo.username}</p>
                <p>Empresa: ${userInfo.company}</p>
                <p>Cargo atual: <strong>${userInfo.roleText}</strong></p>
            </div>
        </div>
    `;
    
    // 3. Exibe o modal
    document.getElementById('editModal').style.display = 'flex';
    // Garante que o modal de detalhes está fechado (se aberto)
    closeModal('userDetailsModal'); 
}

function viewUserDetails(userId) {
    const userInfo = getRowData(userId);
    if (!userInfo) return;

    const statusClass = userInfo.status === 'active' ? 'active' : 'inactive';

    document.getElementById('userDetailsContent').innerHTML = `
        <div class="user-details-full">
            <div class="user-header">
                <div class="user-avatar-xlarge">${userInfo.name.charAt(0)}</div>
                <div class="user-main-info">
                    <h3>${userInfo.name}</h3>
                    <p class="user-username">@${userInfo.username}</p>
                    <span class="role-badge role-${userInfo.roleId}">${userInfo.roleText}</span>
                </div>
            </div>
            
            <div class="user-info-grid">
                <div class="info-item">
                    <label>Razão Social:</label>
                    <span>${userInfo.company}</span>
                </div>
                <div class="info-item">
                    <label>Status:</label>
                    <span class="status-badge ${statusClass}">${userInfo.statusText}</span>
                </div>
                <div class="info-item">
                    <label>Data de Cadastro:</label>
                    <span>${userInfo.date}</span>
                </div>
                <div class="info-item">
                    <label>Último Acesso:</label>
                    <span>15/06/2023 14:30</span>
                </div>
            </div>
        </div>
    `;
    
    // Adiciona o listener para o botão de "Editar Cargo" no modal de detalhes
    const editButton = document.getElementById('editRoleFromDetails');
    // Remove qualquer listener anterior
    editButton.onclick = null; 
    // Adiciona o novo listener para abrir o modal de edição
    editButton.onclick = () => openEditModal(userId, userInfo.roleId);

    document.getElementById('userDetailsModal').style.display = 'flex';
}


// ----------------------------------------------------
// Paginação, Filtro e Ordenação (Lógica Unificada)
// ----------------------------------------------------

function getColumnIndex(column) {
    // Índices de coluna no HTML (baseado na estrutura da <tbody>)
    switch(column) {
        case 'id': return 0;
        case 'username': return 1;
        case 'name': return 2;
        case 'company': return 3;
        case 'role': return 4;
        case 'status': return 6; // Coluna 6 contém o texto de status
        case 'date': return 7; // Coluna 7 contém a data de cadastro
        default: return -1;
    }
}

function getVisibleUsers() {
    const searchTerm = document.getElementById('user-search').value.toLowerCase();
    const statusFilter = document.getElementById('status-filter').value;
    const roleFilter = document.getElementById('role-filter').value;
    const allRows = document.querySelectorAll('#users-table tr[data-user-id]');
    
    return Array.from(allRows).filter(row => {
        // Dados para busca textual:
        const username = row.cells[1].textContent.toLowerCase();
        const name = row.cells[2].textContent.toLowerCase();
        const company = row.cells[3].textContent.toLowerCase();
        // Dados para filtros de select:
        const status = row.dataset.status;
        const role = row.dataset.role;
        
        const matchesSearch = username.includes(searchTerm) || name.includes(searchTerm) || company.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        const matchesRole = !roleFilter || role === roleFilter;
        
        return matchesSearch && matchesStatus && matchesRole;
    });
}

function renderUsers() {
    const rows = getVisibleUsers();
    
    // 1. Aplica a ordenação
    rows.sort((a, b) => {
        let aValue, bValue;
        const column = currentSort.column;

        switch(column) {
            case 'id':
                aValue = parseInt(a.cells[0].textContent);
                bValue = parseInt(b.cells[0].textContent);
                break;
            case 'date':
                // Converte d/m/Y para Date para comparação
                aValue = new Date(a.cells[getColumnIndex('date')].textContent.split('/').reverse().join('-')); 
                bValue = new Date(b.cells[getColumnIndex('date')].textContent.split('/').reverse().join('-'));
                break;
            case 'status':
                aValue = a.dataset.status;
                bValue = b.dataset.status;
                break;
            default: // username, name, company, role
                const index = getColumnIndex(column);
                aValue = a.cells[index].textContent.toLowerCase();
                bValue = b.cells[index].textContent.toLowerCase();
        }
        
        let comparison = 0;
        if (aValue > bValue) {
            comparison = 1;
        } else if (aValue < bValue) {
            comparison = -1;
        }
        
        return currentSort.direction === 'asc' ? comparison : comparison * -1;
    });

    // 2. Paginação
    const startIndex = (currentPage - 1) * usersPerPage;
    const endIndex = startIndex + usersPerPage;
    const paginatedRows = rows.slice(startIndex, endIndex);

    // 3. Exibe/Esconde as linhas
    const allRows = document.querySelectorAll('#users-table tr[data-user-id]');
    allRows.forEach(row => row.style.display = 'none'); // Esconde tudo primeiro

    paginatedRows.forEach(row => row.style.display = ''); // Mostra só as da página atual

    // 4. Atualiza a contagem e a paginação
    document.getElementById('users-count').textContent = rows.length;
    updatePagination(rows.length);
}

function filterUsers() {
    currentPage = 1; // Volta para a primeira página ao filtrar
    renderUsers();
}

function sortUsers(column) {
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        // Remove ícone da coluna anterior
        if(currentSort.column) updateSortIcons(currentSort.column); 

        currentSort.column = column;
        currentSort.direction = 'asc';
    }
    currentPage = 1; // Volta para a primeira página ao ordenar
    updateSortIcons(column);
    renderUsers(); // Renderiza com a nova ordem
}

function updateSortIcons(activeColumn) {
    document.querySelectorAll('th[data-sort]').forEach(th => {
        const icon = th.querySelector('i');
        if (th.dataset.sort === activeColumn) {
            icon.className = currentSort.direction === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
        } else {
            // Se for outra coluna, reseta para o ícone padrão
            icon.className = 'fas fa-sort'; 
        }
    });
}

function changePage(direction) {
    const totalUsers = getVisibleUsers().length;
    const totalPages = Math.ceil(totalUsers / usersPerPage);
    
    let newPage = currentPage + direction;
    
    if (newPage < 1) {
        newPage = 1;
    } else if (newPage > totalPages) {
        newPage = totalPages > 0 ? totalPages : 1;
    }
    
    if (newPage !== currentPage) {
        currentPage = newPage;
        renderUsers();
    }
}

function updatePagination(totalUsers) {
    const totalPages = Math.ceil(totalUsers / usersPerPage);
    document.getElementById('current-page').textContent = totalUsers > 0 ? currentPage : 0;
    document.getElementById('total-pages').textContent = totalPages || 1;
    
    // Desabilita botões de navegação
    document.querySelector('.pagination .page-btn:first-child').disabled = currentPage === 1;
    document.querySelector('.pagination .page-btn:last-child').disabled = (currentPage === totalPages) || (totalPages === 0);
}


// ----------------------------------------------------
// Inicialização
// ----------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar event listeners para ordenação
    document.querySelectorAll('th[data-sort]').forEach(th => {
        th.style.cursor = 'pointer';
        th.addEventListener('click', () => sortUsers(th.dataset.sort));
    });

    // Inicializa a ordenação (ex: por ID, descendente, para mostrar os mais recentes primeiro)
    currentSort = { column: 'date', direction: 'desc' };
    updateSortIcons(currentSort.column);

    // Inicializa os modais como escondidos (o CSS já faz isso, mas garante)
    closeModal('editModal');
    closeModal('userDetailsModal');

    // Dispara a renderização inicial, que aplica ordenação e paginação (10 primeiros)
    renderUsers();
});
</script>

</body>
</html>