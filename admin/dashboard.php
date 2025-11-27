<?php
// Consulta para contar pontos pendentes de aprovação
try {
    $stmt_pendentes = $pdo->query("SELECT COUNT(*) as total_pendentes FROM pontos_ajuda WHERE ativo = 0");
    $resultado_pendentes = $stmt_pendentes->fetch(PDO::FETCH_ASSOC);
    $solicitacoes_pendentes = $resultado_pendentes['total_pendentes'];
} catch (PDOException $e) {
    $solicitacoes_pendentes = 0;
    error_log("Erro ao contar pontos pendentes: " . $e->getMessage());
}
?>

<div class="admin-dashboard">
    <div class="dashboard-header">
        <h2><i class="fas fa-tachometer-alt"></i> Dashboard Administrativo</h2>
        <div class="dashboard-actions">
            <button class="btn btn-secondary" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt"></i> Atualizar
            </button>
            <button class="btn btn-primary" onclick="exportDashboardData()">
                <i class="fas fa-download"></i> Exportar Relatório
            </button>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon users">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3 id="total-users"><?php echo $total_usuarios; ?></h3>
                <p>Total de Usuários</p>
                <div class="stat-trend positive">
                    <i class="fas fa-arrow-up"></i> 12% este mês
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon active-users">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-info">
                <h3 id="active-users"><?php echo $usuarios_ativos; ?></h3>
                <p>Usuários Ativos</p>
                <div class="stat-trend positive">
                    <i class="fas fa-arrow-up"></i> 8% este mês
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon points">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="stat-info">
                <h3 id="total-points"><?php echo $total_pontos; ?></h3>
                <p>Total de Pontos</p>
                <div class="stat-trend positive">
                    <i class="fas fa-arrow-up"></i> 15% este mês
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon active-points">
                <i class="fas fa-map-marker"></i>
            </div>
            <div class="stat-info">
                <h3 id="active-points"><?php echo $pontos_ativos; ?></h3>
                <p>Pontos Ativos</p>
                <div class="stat-trend negative">
                    <i class="fas fa-arrow-down"></i> 3% este mês
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon cities">
                <i class="fas fa-city"></i>
            </div>
            <div class="stat-info">
                <h3 id="total-cities"><?php echo $total_cidades; ?></h3>
                <p>Cidades Atendidas</p>
                <div class="stat-trend positive">
                    <i class="fas fa-arrow-up"></i> 5% este mês
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon requests">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <div class="stat-info">
                <h3 id="pending-requests"><?php echo $solicitacoes_pendentes; ?></h3>
                <p>Solicitações Pendentes</p>
                <div class="stat-trend <?php echo $solicitacoes_pendentes > 0 ? 'warning' : 'positive'; ?>">
                    <i class="fas fa-<?php echo $solicitacoes_pendentes > 0 ? 'exclamation' : 'check'; ?>"></i>
                    <?php if ($solicitacoes_pendentes > 0): ?>
                        <a href="?pagina=aprovacao-pontos" style="color: inherit; text-decoration: none;">
                            Ver solicitações
                        </a>
                    <?php else: ?>
                        Em dia
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="content-grid">
                <div class="recent-activity">
                    <div class="activity-header">
                        <h3><i class="fas fa-history"></i> Atividade Recente</h3>
                        <div class="activity-filters">
                            <select id="activity-filter" onchange="filterActivity()">
                                <option value="all">Todas as atividades</option>
                                <option value="users">Apenas usuários</option>
                                <option value="points">Apenas pontos</option>
                            </select>
                        </div>
                    </div>

                    <div class="activity-timeline">
                        <?php
                        try {
                            // Últimas atividades (usuários e pontos combinados)
                            $stmt = $pdo->query("
                            (SELECT 
                                'user' as type,
                                u.nome_completo as title,
                                u.dt_criacao as date,
                                CONCAT('Novo usuário: ', u.nome_completo) as description,
                                u.usuario_id as id,
                                NULL as cidade_nome
                             FROM usuarios u 
                             ORDER BY u.dt_criacao DESC 
                             LIMIT 8)
                            
                            UNION ALL
                            
                            (SELECT 
                                'point' as type,
                                p.titulo as title,
                                p.dt_criacao as date,
                                CONCAT('Novo ponto: ', p.titulo) as description,
                                p.ponto_id as id,
                                c.cidade_nome
                             FROM pontos_ajuda p 
                             JOIN cidades c ON p.cidade_id = c.cidade_id 
                             ORDER BY p.dt_criacao DESC 
                             LIMIT 8)
                            
                            ORDER BY date DESC 
                            LIMIT 10
                        ");
                            $recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($recentActivities) {
                                foreach ($recentActivities as $activity) {
                                    $icon = $activity['type'] == 'user' ? 'fa-user' : 'fa-map-marker-alt';
                                    $color = $activity['type'] == 'user' ? 'user-activity' : 'point-activity';
                                    $location = $activity['type'] == 'point' ? ' - ' . htmlspecialchars($activity['cidade_nome']) : '';

                                    echo '<div class="activity-item ' . $color . '" data-type="' . $activity['type'] . '">';
                                    echo '<div class="activity-icon">';
                                    echo '<i class="fas ' . $icon . '"></i>';
                                    echo '</div>';
                                    echo '<div class="activity-content">';
                                    echo '<h4>' . htmlspecialchars($activity['title']) . '</h4>';
                                    echo '<p>' . htmlspecialchars($activity['description']) . $location . '</p>';
                                    echo '<span class="activity-time">' . date('d/m/Y H:i', strtotime($activity['date'])) . '</span>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="empty-state">';
                                echo '<i class="fas fa-inbox"></i>';
                                echo '<p>Nenhuma atividade recente</p>';
                                echo '</div>';
                            }
                        } catch (PDOException $e) {
                            echo '<div class="alert alert-error">Erro ao carregar atividade recente: ' . $e->getMessage() . '</div>';
                        }
                        ?>
                    </div>
                </div>

                <div class="quick-stats">
                    <div class="stats-card">
                        <h4><i class="fas fa-chart-bar"></i> Estatísticas Rápidas</h4>
                        <div class="stats-list">
                            <div class="stat-item">
                                <span class="stat-label">Taxa de Atividade</span>
                                <span class="stat-value">78%</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Novos Cadastros (7 dias)</span>
                                <span class="stat-value">24</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Pontos por Cidade (Média)</span>
                                <span class="stat-value">3.7</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Tempo de Resposta</span>
                                <span class="stat-value">2.4h</span>
                            </div>
                        </div>
                    </div>

                    <div class="actions-card">
                        <h4><i class="fas fa-bolt"></i> Ações Rápidas</h4>
                        <div class="action-buttons">
                            <button class="action-btn" onclick="openAddCityModal()">
                                <i class="fas fa-plus"></i>
                                <span>Nova Cidade</span>
                            </button>
                            <button class="action-btn" onclick="window.location.href='../admin.php?pagina=usuarios'">
                                <i class="fas fa-user-plus"></i>
                                <span>Ver Usuários</span>
                            </button>
                            <button class="action-btn" onclick="window.location.href='../admin.php?pagina=pontos'">
                                <i class="fas fa-map-marker-plus"></i>
                                <span>Ver Pontos</span>
                            </button>
                            <button class="action-btn" onclick="generateReport()">
                                <i class="fas fa-file-export"></i>
                                <span>Gerar Relatório</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function refreshDashboard() {
            showToast('Atualizando dados...', 'info');
            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        function filterActivity() {
            const filter = document.getElementById('activity-filter').value;
            const activities = document.querySelectorAll('.activity-item');

            activities.forEach(activity => {
                if (filter === 'all' || activity.dataset.type === filter) {
                    activity.style.display = 'flex';
                } else {
                    activity.style.display = 'none';
                }
            });
        }

        function exportDashboardData() {
            showToast('Preparando exportação...', 'info');
            // Simulação de exportação
            setTimeout(() => {
                showToast('Dados exportados com sucesso!', 'success');
            }, 2000);
        }

        function generateReport() {
            showToast('Gerando relatório...', 'info');
            // Simulação de geração de relatório
            setTimeout(() => {
                showToast('Relatório gerado com sucesso!', 'success');
            }, 1500);
        }

        function showToast(message, type = 'success') {
            // Implementação do toast notification
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : 'info'}"></i>
        <span>${message}</span>
    `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Atualizar dados em tempo real (simulação)
        setInterval(() => {
            // Em uma implementação real, isso faria uma requisição AJAX
            console.log('Atualizando dados do dashboard...');
        }, 30000);
    </script>

    <style>
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .dashboard-header h2 {
            color: #2c3e50;
            font-weight: 600;
        }

        .dashboard-header h2 i {
            margin-right: 10px;
            color: #4361ee;
        }

        .dashboard-actions {
            display: flex;
            gap: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-left: 4px solid;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            align-items: center;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.8rem;
            color: white;
        }

        .stat-icon.users {
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            border-color: #4361ee;
        }

        .stat-icon.active-users {
            background: linear-gradient(135deg, #4cc9f0, #3ab5d4);
            border-color: #4cc9f0;
        }

        .stat-icon.points {
            background: linear-gradient(135deg, #7209b7, #6108a0);
            border-color: #7209b7;
        }

        .stat-icon.active-points {
            background: linear-gradient(135deg, #f8961e, #e0861b);
            border-color: #f8961e;
        }

        .stat-icon.cities {
            background: linear-gradient(135deg, #4895ef, #3a85d4);
            border-color: #4895ef;
        }

        .stat-icon.requests {
            background: linear-gradient(135deg, #f72585, #e11573);
            border-color: #f72585;
        }

        .stat-info h3 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .stat-info p {
            color: #6c757d;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .stat-trend {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .stat-trend.positive {
            background: #d4edda;
            color: #155724;
        }

        .stat-trend.negative {
            background: #f8d7da;
            color: #721c24;
        }

        .stat-trend.warning {
            background: #fff3cd;
            color: #856404;
        }

        .dashboard-content {
            margin-top: 30px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }

        .recent-activity {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .activity-header h3 {
            color: #2c3e50;
            font-weight: 600;
        }

        .activity-header h3 i {
            margin-right: 10px;
            color: #4361ee;
        }

        .activity-filters select {
            padding: 8px 12px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            background: white;
            color: #495057;
        }

        .activity-timeline {
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #f8f9fa;
            transition: background-color 0.2s ease;
        }

        .activity-item:hover {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 0 -15px;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .user-activity .activity-icon {
            background: #e3f2fd;
            color: #1976d2;
        }

        .point-activity .activity-icon {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .activity-content h4 {
            margin: 0 0 5px 0;
            font-weight: 600;
            color: #2c3e50;
        }

        .activity-content p {
            margin: 0 0 8px 0;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .activity-time {
            font-size: 0.8rem;
            color: #adb5bd;
        }

        .quick-stats {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .stats-card,
        .actions-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .stats-card h4,
        .actions-card h4 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .stats-card h4 i,
        .actions-card h4 i {
            margin-right: 10px;
            color: #4361ee;
        }

        .stats-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .stat-value {
            font-weight: 600;
            color: #2c3e50;
            background: #f8f9fa;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px 10px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            color: #495057;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .action-btn:hover {
            background: #4361ee;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        .action-btn i {
            font-size: 1.2rem;
            margin-bottom: 8px;
        }

        .action-btn span {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
            animation: slideInRight 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .toast-success {
            background: #28a745;
        }

        .toast-info {
            background: #17a2b8;
        }

        .toast-error {
            background: #dc3545;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 1200px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .dashboard-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>