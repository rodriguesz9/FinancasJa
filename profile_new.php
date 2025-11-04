<?php
/**
 * ========================================
 * PÁGINA DE PERFIL DO USUÁRIO
 * ========================================
 *
 * Página completa de perfil com:
 * - Informações pessoais
 * - Resumo financeiro
 * - Estatísticas de investimentos
 * - Histórico de transações
 * - Metas financeiras ativas
 * - Gráfico de evolução patrimonial
 *
 * @package FinançasJá
 * @version 2.0
 */

session_start();
require_once 'config/database.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Buscar informações do usuário do banco de dados
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Usar o email real do banco de dados
$user_email = $user['email'] ?? 'usuario@email.com';

// Calcular resumo financeiro
$stmt = $pdo->prepare("SELECT
    COALESCE(SUM(CASE WHEN tipo = 'receita' THEN valor END), 0) as total_receitas,
    COALESCE(SUM(CASE WHEN tipo = 'despesa' THEN valor END), 0) as total_despesas,
    COUNT(DISTINCT DATE_FORMAT(data_transacao, '%Y-%m')) as meses_ativos
    FROM transactions WHERE user_id = ?");
$stmt->execute([$user_id]);
$resumo = $stmt->fetch(PDO::FETCH_ASSOC);

$saldo_total = $resumo['total_receitas'] - $resumo['total_despesas'];

// Buscar investimentos
$stmt = $pdo->prepare("SELECT COUNT(*) as total_investimentos,
    COALESCE(SUM(quantidade * preco_compra), 0) as total_investido
    FROM investments WHERE user_id = ?");
$stmt->execute([$user_id]);
$investimentos = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar metas ativas
$stmt = $pdo->prepare("SELECT COUNT(*) as total_metas,
    COALESCE(SUM(valor_objetivo), 0) as soma_objetivos,
    COALESCE(SUM(valor_atual), 0) as soma_atual
    FROM financial_goals WHERE user_id = ? AND status = 'ativa'");
$stmt->execute([$user_id]);
$metas = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar últimas transações
$stmt = $pdo->prepare("SELECT * FROM transactions
    WHERE user_id = ?
    ORDER BY data_transacao DESC, created_at DESC
    LIMIT 5");
$stmt->execute([$user_id]);
$ultimas_transacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar evolução patrimonial (últimos 6 meses)
$stmt = $pdo->prepare("SELECT
    DATE_FORMAT(data_transacao, '%Y-%m') as mes,
    SUM(CASE WHEN tipo = 'receita' THEN valor ELSE -valor END) as saldo_mes
    FROM transactions
    WHERE user_id = ? AND data_transacao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(data_transacao, '%Y-%m')
    ORDER BY mes ASC");
$stmt->execute([$user_id]);
$evolucao = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular saldo acumulado
$saldo_acumulado = 0;
$dados_grafico = [];
foreach ($evolucao as $mes_data) {
    $saldo_acumulado += $mes_data['saldo_mes'];
    $dados_grafico[] = [
        'mes' => date('M/y', strtotime($mes_data['mes'] . '-01')),
        'saldo' => $saldo_acumulado
    ];
}

// Membro desde
$membro_desde = isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - FinançasJá</title>
    <meta name="description" content="Visualize suas informações pessoais, resumo financeiro e estatísticas de investimentos no FinançasJá.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 3rem 0 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 1rem;
            border: 4px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .profile-avatar:hover {
            transform: scale(1.05);
            border-color: rgba(255, 255, 255, 0.6);
        }

        .stat-card-profile {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            backdrop-filter: blur(10px);
            border: 2px solid rgba(130, 10, 209, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card-profile:hover {
            transform: translateY(-5px);
            border-color: var(--primary-purple);
            box-shadow: 0 10px 30px rgba(138, 43, 226, 0.3);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 2rem;
        }

        .info-item {
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .badge-premium {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: #000;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            display: inline-block;
        }

        .transaction-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
        }

        .transaction-item:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(130, 10, 209, 0.3);
        }

        .chart-container-profile {
            position: relative;
            height: 300px;
            margin-top: 1rem;
        }

        .quick-action-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(130, 10, 209, 0.3);
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .quick-action-btn:hover {
            background: rgba(130, 10, 209, 0.2);
            border-color: var(--primary-purple);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-modern sticky-top">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <i class="bi bi-gem me-2"></i>FinançasJá
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">
                            <i class="bi bi-house-fill me-1"></i>Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="investments.php">
                            <i class="bi bi-graph-up me-1"></i>Investimentos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="conversabot.php">
                            <i class="bi bi-robot me-1"></i>Assistente IA
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="education.php">
                            <i class="bi bi-mortarboard me-1"></i>Academia
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($user_name) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item active" href="profile.php"><i class="bi bi-person me-2"></i>Perfil</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Configurações</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <!-- Cabeçalho do Perfil -->
        <div class="profile-header text-white text-center">
            <div class="profile-avatar" title="Clique para alterar foto">
                <i class="bi bi-person-fill"></i>
            </div>
            <h2 class="fw-bold mb-2" style="position: relative; z-index: 2;"><?= htmlspecialchars($user_name) ?></h2>
            <p class="mb-2" style="position: relative; z-index: 2; opacity: 0.9;"><?= htmlspecialchars($user_email) ?></p>
            <span class="badge-premium">
                <i class="bi bi-star-fill me-1"></i>Membro desde <?= $membro_desde ?>
            </span>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card-profile">
                    <div class="stat-icon bg-success bg-opacity-25 text-success">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <h6 class="text-muted mb-2">Saldo Total</h6>
                    <h3 class="fw-bold <?= $saldo_total >= 0 ? 'text-success' : 'text-danger' ?>">
                        R$ <?= number_format(abs($saldo_total), 2, ',', '.') ?>
                    </h3>
                    <small class="text-muted">
                        <i class="bi bi-graph-<?= $saldo_total >= 0 ? 'up' : 'down' ?> me-1"></i>
                        <?= $resumo['meses_ativos'] ?> meses ativos
                    </small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card-profile">
                    <div class="stat-icon bg-primary bg-opacity-25 text-primary">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h6 class="text-muted mb-2">Investimentos</h6>
                    <h3 class="fw-bold text-white">
                        R$ <?= number_format($investimentos['total_investido'], 2, ',', '.') ?>
                    </h3>
                    <small class="text-muted">
                        <i class="bi bi-briefcase me-1"></i>
                        <?= $investimentos['total_investimentos'] ?> ativos
                    </small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card-profile">
                    <div class="stat-icon bg-info bg-opacity-25 text-info">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <h6 class="text-muted mb-2">Metas Ativas</h6>
                    <h3 class="fw-bold text-white">
                        <?= $metas['total_metas'] ?? 0 ?>
                    </h3>
                    <small class="text-muted">
                        <?php if ($metas['soma_objetivos'] > 0): ?>
                            <?php $progresso_metas = ($metas['soma_atual'] / $metas['soma_objetivos']) * 100; ?>
                            <i class="bi bi-check-circle me-1"></i>
                            <?= number_format($progresso_metas, 1) ?>% completado
                        <?php else: ?>
                            <i class="bi bi-plus-circle me-1"></i>
                            Criar primeira meta
                        <?php endif; ?>
                    </small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card-profile">
                    <div class="stat-icon bg-warning bg-opacity-25 text-warning">
                        <i class="bi bi-trophy"></i>
                    </div>
                    <h6 class="text-muted mb-2">Transações</h6>
                    <h3 class="fw-bold text-white">
                        <?= array_sum([$resumo['total_receitas'], $resumo['total_despesas']]) > 0 ?
                            count($ultimas_transacoes) : 0 ?>
                    </h3>
                    <small class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i>
                        Este mês
                    </small>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Coluna Esquerda -->
            <div class="col-lg-8">
                <!-- Gráfico de Evolução Patrimonial -->
                <div class="card-modern mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-graph-up-arrow text-purple me-2"></i>
                            Evolução Patrimonial
                        </h5>
                        <p class="text-muted mb-4">Acompanhe o crescimento do seu patrimônio nos últimos 6 meses</p>
                        <div class="chart-container-profile">
                            <canvas id="evolutionChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Últimas Transações -->
                <div class="card-modern">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-clock-history text-purple me-2"></i>
                                Transações Recentes
                            </h5>
                            <a href="dashboard.php" class="btn btn-sm btn-outline-purple">
                                Ver Todas
                                <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>

                        <?php if (empty($ultimas_transacoes)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-muted mt-3">Nenhuma transação registrada ainda</p>
                                <a href="dashboard.php" class="btn btn-purple btn-modern mt-2">
                                    <i class="bi bi-plus-circle me-2"></i>Adicionar Transação
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($ultimas_transacoes as $trans): ?>
                                <div class="transaction-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="stat-icon <?= $trans['tipo'] == 'receita' ? 'bg-success' : 'bg-danger' ?> bg-opacity-25 <?= $trans['tipo'] == 'receita' ? 'text-success' : 'text-danger' ?>" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                                    <i class="bi bi-<?= $trans['tipo'] == 'receita' ? 'arrow-down-circle' : 'arrow-up-circle' ?>"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($trans['descricao']) ?></h6>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($trans['categoria']) ?> •
                                                    <?= date('d/m/Y', strtotime($trans['data_transacao'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <h5 class="mb-0 <?= $trans['tipo'] == 'receita' ? 'text-success' : 'text-danger' ?>">
                                                <?= $trans['tipo'] == 'receita' ? '+' : '-' ?>R$ <?= number_format($trans['valor'], 2, ',', '.') ?>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Coluna Direita -->
            <div class="col-lg-4">
                <!-- Informações do Perfil -->
                <div class="info-card mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-person-badge text-purple me-2"></i>
                        Informações do Perfil
                    </h5>
                    <div class="info-item">
                        <span class="text-muted">Nome Completo</span>
                        <strong><?= htmlspecialchars($user_name) ?></strong>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">E-mail</span>
                        <strong><?= htmlspecialchars($user_email) ?></strong>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Membro desde</span>
                        <strong><?= $membro_desde ?></strong>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Status da Conta</span>
                        <span class="badge bg-success">Ativa</span>
                    </div>
                </div>

                <!-- Ações Rápidas -->
                <div class="info-card">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-lightning text-purple me-2"></i>
                        Ações Rápidas
                    </h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="dashboard.php" class="quick-action-btn d-block text-decoration-none text-white">
                                <i class="bi bi-plus-circle d-block mb-2" style="font-size: 2rem;"></i>
                                <small>Nova<br>Transação</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="investments.php" class="quick-action-btn d-block text-decoration-none text-white">
                                <i class="bi bi-graph-up d-block mb-2" style="font-size: 2rem;"></i>
                                <small>Adicionar<br>Investimento</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="dashboard.php#metas" class="quick-action-btn d-block text-decoration-none text-white">
                                <i class="bi bi-bullseye d-block mb-2" style="font-size: 2rem;"></i>
                                <small>Criar<br>Meta</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="settings.php" class="quick-action-btn d-block text-decoration-none text-white">
                                <i class="bi bi-gear d-block mb-2" style="font-size: 2rem;"></i>
                                <small>Configurações</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-modern">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand mb-3">FinançasJá</div>
                    <p>Sua plataforma completa para gestão financeira pessoal.</p>
                </div>
                <div class="col-lg-4">
                    <h6 class="fw-bold mb-3">Links Rápidos</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="dashboard.php" class="footer-link">Dashboard</a></li>
                        <li class="mb-2"><a href="investments.php" class="footer-link">Investimentos</a></li>
                        <li class="mb-2"><a href="education.php" class="footer-link">Academia</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="fw-bold mb-3">Suporte</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="support1.php" class="footer-link">Central de Ajuda</a></li>
                        <li class="mb-2"><a href="about.php" class="footer-link">Sobre Nós</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(138, 43, 226, 0.2);">
            <div class="text-center">
                <p class="mb-0">&copy; 2025 FinançasJá. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dados para o gráfico de evolução
        const dadosEvolucao = <?= json_encode($dados_grafico) ?>;

        // Criar gráfico de evolução patrimonial
        const ctx = document.getElementById('evolutionChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dadosEvolucao.map(d => d.mes),
                datasets: [{
                    label: 'Saldo Acumulado',
                    data: dadosEvolucao.map(d => d.saldo),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: 'white', font: { size: 14, weight: 'bold' } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Saldo: R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            color: 'white',
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    },
                    x: {
                        ticks: { color: 'white' },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    }
                }
            }
        });

        console.log('✅ Profile page inicializada com sucesso!');
        console.log(`👤 Usuário: <?= htmlspecialchars($user_name) ?>`);
        console.log(`💰 Saldo: R$ <?= number_format($saldo_total, 2, ',', '.') ?>`);
    </script>
</body>
</html>
