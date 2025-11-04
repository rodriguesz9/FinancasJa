<?php
/**
 * ========================================
 * PÁGINA DE GESTÃO DE INVESTIMENTOS
 * ========================================
 *
 * Sistema completo para gerenciar e acompanhar investimentos pessoais
 * com integração de APIs financeiras para dados em tempo real.
 *
 * Funcionalidades:
 * - Cadastro, edição e exclusão de investimentos
 * - Suporte a múltiplos tipos: Ações, FIIs, Criptomoedas, Renda Fixa
 * - Cotações em tempo real via APIs externas
 * - Cálculo automático de rentabilidade e lucro/prejuízo
 * - Gráficos interativos de alocação e performance
 * - Histórico de preços dos últimos 7 dias
 * - Interface totalmente em português brasileiro
 *
 * APIs Utilizadas:
 * - BRAPI: Cotações da B3 (Bolsa de Valores Brasileira)
 * - CoinGecko: Preços de criptomoedas
 * - ExchangeRate: Taxas de câmbio
 * - Yahoo Finance: Dados históricos
 *
 * @package FinançasJá
 * @author Equipe FinançasJá
 * @version 2.0
 * @since 2025
 * ========================================
 */

session_start();
require_once 'config/database.php';

requireLogin();

// Token da API BRAPI para acesso às cotações da B3
define('BRAPI_TOKEN', 'm17pMcSDMTBk7FqjrGvyAW');

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Deletar investimento
if (isset($_GET['delete_investment'])) {
    $investmentId = (int)$_GET['delete_investment'];
    $stmt = $pdo->prepare("SELECT * FROM investments WHERE id = ? AND user_id = ?");
    $stmt->execute([$investmentId, $user_id]);
    $investment = $stmt->fetch();

    if ($investment) {
        $stmt = $pdo->prepare("DELETE FROM investments WHERE id = ?");
        $stmt->execute([$investmentId]);
    }
    header('Location: investments.php');
    exit();
}

// Editar investimento
if ($_POST && isset($_POST['edit_investment'])) {
    $investmentId = (int)$_POST['investment_id'];
    $tipo = $_POST['tipo_investimento'];
    $nome = strtoupper(trim($_POST['nome_ativo']));
    $quantidade = (float)$_POST['quantidade'];
    $preco = (float)$_POST['preco_compra'];
    $data = $_POST['data_compra'];

    $stmt = $pdo->prepare("UPDATE investments SET tipo_investimento = ?, nome_ativo = ?, quantidade = ?, preco_compra = ?, data_compra = ? WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$tipo, $nome, $quantidade, $preco, $data, $investmentId, $user_id])) {
        header('Location: investments.php');
        exit();
    }
}

// Adicionar investimento
if ($_POST && isset($_POST['add_investment'])) {
    $tipo = $_POST['tipo_investimento'];
    $nome = strtoupper(trim($_POST['nome_ativo']));
    $quantidade = (float)$_POST['quantidade'];
    $preco = (float)$_POST['preco_compra'];
    $data = $_POST['data_compra'];

    $stmt = $pdo->prepare("INSERT INTO investments (user_id, tipo_investimento, nome_ativo, quantidade, preco_compra, data_compra) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $tipo, $nome, $quantidade, $preco, $data])) {
        header('Location: investments.php');
        exit();
    }
}

// Buscar investimentos
$stmt = $pdo->prepare("SELECT * FROM investments WHERE user_id = ? ORDER BY data_compra DESC");
$stmt->execute([$user_id]);
$investments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_invested = 0;
foreach ($investments as $investment) {
    $total_invested += $investment['quantidade'] * $investment['preco_compra'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investimentos - FinançasJá | Acompanhe sua Carteira em Tempo Real</title>
    <meta name="description" content="Gerencie sua carteira de investimentos com dados em tempo real. Acompanhe ações, FIIs, criptomoedas e renda fixa com análises profissionais.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .currency-card {
            border-radius: 15px;
            padding: 20px;
            color: white;
            position: relative;
            overflow: hidden;
            background-size: cover;
            background-position: center;
            min-height: 180px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .currency-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .currency-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
        }

        .currency-card-content {
            position: relative;
            z-index: 2;
        }

        #dollar-card {
            background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Flag_of_the_United_States.svg/1200px-Flag_of_the_United_States.svg.png');
        }

        #euro-card {
            background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/b/b7/Flag_of_Europe.svg/1280px-Flag_of_Europe.svg.png');
        }

        #bitcoin-card {
            background: linear-gradient(135deg, #f7931a 0%, #ff6b00 100%);
        }

        .currency-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .currency-value {
            font-size: 1.8rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .currency-label {
            font-size: 0.9rem;
            opacity: 0.9;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .update-time {
            font-size: 0.75rem;
            opacity: 0.8;
            margin-top: 10px;
        }

        .spinner-modern {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .chart-container {
            position: relative;
            height: 200px;
            margin-top: 15px;
        }

        .expandable-content {
            display: none;
            background: #f8f9fa;
            padding: 20px;
        }

        .expandable-content.show {
            display: table-row;
        }

        .expand-btn {
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .expand-btn.active {
            transform: rotate(180deg);
        }

        .allocation-chart-container {
            position: relative;
            height: 350px;
        }

        .market-insight-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border: 2px solid var(--primary-purple);
            border-radius: 15px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .market-insight-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(130, 10, 209, 0.2);
        }

        .trend-indicator {
            display: inline-flex;
            align-items: center;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .trend-up {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .trend-down {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .performance-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .quick-action-card {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(130, 10, 209, 0.3);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .quick-action-card:hover {
            border-color: var(--primary-purple);
            background: rgba(130, 10, 209, 0.1);
            transform: translateY(-3px);
        }

        .quick-action-card i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--primary-purple);
        }

        @media (max-width: 768px) {
            .currency-card {
                min-height: 160px;
            }

            .currency-value {
                font-size: 1.5rem;
            }
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
                        <a class="nav-link active" href="investments.php">
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Mais
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="about.php"><i class="bi bi-info-circle me-2"></i>Sobre</a></li>
                            <li><a class="dropdown-item" href="plans.php"><i class="bi bi-star me-2"></i>Planos</a></li>
                            <li><a class="dropdown-item" href="support1.php"><i class="bi bi-headset me-2"></i>Suporte</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($user_name) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Perfil</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Configurações</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-modern">
        <div class="container">
            <div class="hero-content">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-5 fw-bold mb-3 fade-in-up">Carteira de Investimentos</h1>
                        <p class="lead mb-4 fade-in-up" style="animation-delay: 0.2s;">Acompanhe seus ativos e cotações em tempo real com análises profissionais</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <button class="btn btn-purple btn-modern mb-2" data-bs-toggle="modal" data-bs-target="#addInvestmentModal">
                            <i class="bi bi-plus-lg me-2"></i>Novo Investimento
                        </button>
                        <a href="ativos.php" class="btn btn-outline-purple btn-modern mb-2">
                            <i class="bi bi-list-ul me-2"></i>Lista B3
                        </a>
                        <a href="calculators.php" class="btn btn-outline-purple btn-modern mb-2">
                            <i class="bi bi-calculator me-2"></i>Calculadoras
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container mt-4 mb-5">
        <!-- Cotações de Moedas -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Cotações em Tempo Real</h3>
                <button class="btn btn-outline-purple btn-modern" onclick="atualizarCotacoesMoedas()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Atualizar
                </button>
            </div>

            <div class="row g-4">
                <!-- DÓLAR -->
                <div class="col-md-4">
                    <div class="currency-card" id="dollar-card">
                        <div class="currency-card-content">
                            <div class="currency-icon">💵</div>
                            <h5 class="fw-bold mb-3">Dólar (USD)</h5>
                            <div class="currency-value-wrapper">
                                <div class="currency-label">Compra</div>
                                <div class="currency-value">R$ <span id="dollar_compra"><span class="spinner-modern"></span></span></div>
                            </div>
                            <div class="currency-value-wrapper">
                                <div class="currency-label">Venda</div>
                                <div class="currency-value">R$ <span id="dollar_venda"><span class="spinner-modern"></span></span></div>
                            </div>
                            <div class="update-time">Atualizado: <span id="dollar_update">-</span></div>
                        </div>
                    </div>
                </div>

                <!-- EURO -->
                <div class="col-md-4">
                    <div class="currency-card" id="euro-card">
                        <div class="currency-card-content">
                            <div class="currency-icon">💶</div>
                            <h5 class="fw-bold mb-3">Euro (EUR)</h5>
                            <div class="currency-value-wrapper">
                                <div class="currency-label">Compra</div>
                                <div class="currency-value">R$ <span id="euro_compra"><span class="spinner-modern"></span></span></div>
                            </div>
                            <div class="currency-value-wrapper">
                                <div class="currency-label">Venda</div>
                                <div class="currency-value">R$ <span id="euro_venda"><span class="spinner-modern"></span></span></div>
                            </div>
                            <div class="update-time">Atualizado: <span id="euro_update">-</span></div>
                        </div>
                    </div>
                </div>

                <!-- BITCOIN -->
                <div class="col-md-4">
                    <div class="currency-card" id="bitcoin-card">
                        <div class="currency-card-content">
                            <div class="currency-icon">₿</div>
                            <h5 class="fw-bold mb-3">Bitcoin (BTC)</h5>
                            <div class="currency-value-wrapper">
                                <div class="currency-label">Preço Atual</div>
                                <div class="currency-value">R$ <span id="bitcoin_preco"><span class="spinner-modern"></span></span></div>
                            </div>
                            <div class="currency-value-wrapper">
                                <div class="currency-label">&nbsp;</div>
                                <div class="currency-value">&nbsp;</div>
                            </div>
                            <div class="update-time">Atualizado: <span id="bitcoin_update">-</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card-modern success">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1" style="color: #6c757d;">Total Investido</h6>
                            <h3 class="text-success mb-0">R$ <?= number_format($total_invested, 2, ',', '.') ?></h3>
                        </div>
                        <div class="text-success" style="font-size: 2.5rem;">
                            <i class="bi bi-piggy-bank-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card-modern info">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1" style="color: #6c757d;">Valor Atual</h6>
                            <h3 class="text-info mb-0" id="currentValue"><span class="spinner-modern"></span></h3>
                        </div>
                        <div class="text-info" style="font-size: 2.5rem;">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card-modern">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1" style="color: #6c757d;">Rentabilidade</h6>
                            <h3 class="text-purple mb-0" id="profitability"><span class="spinner-modern"></span></h3>
                        </div>
                        <div class="text-purple" style="font-size: 2.5rem;">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card-modern warning">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1" style="color: #6c757d;">Ativos</h6>
                            <h3 class="text-warning mb-0"><?= count($investments) ?></h3>
                        </div>
                        <div class="text-warning" style="font-size: 2.5rem;">
                            <i class="bi bi-collection-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alocação de Portfólio -->
        <?php if (!empty($investments)): ?>
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card-modern">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">
                            <i class="bi bi-pie-chart text-purple me-2"></i>Alocação de Portfólio
                        </h4>
                        <div class="allocation-chart-container">
                            <canvas id="allocationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-modern">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">
                            <i class="bi bi-graph-up text-info me-2"></i>Performance por Tipo
                        </h4>
                        <div id="performanceByType">
                            <div class="text-center py-5">
                                <div class="spinner-border text-purple" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Insights de Mercado -->
        <div class="mb-5">
            <h3 class="fw-bold mb-4">
                <i class="bi bi-lightbulb text-warning me-2"></i>Insights de Mercado
            </h3>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="market-insight-card">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <i class="bi bi-graph-up-arrow text-success" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="fw-bold">Ibovespa Hoje</h5>
                                <h3 class="text-success mb-2" id="ibovespa_value">--</h3>
                                <span class="trend-indicator trend-up" id="ibovespa_change">
                                    <i class="bi bi-arrow-up me-1"></i>--
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="market-insight-card">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <i class="bi bi-currency-dollar text-info" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="fw-bold">Dólar Comercial</h5>
                                <h3 class="text-info mb-2" id="market_dollar">--</h3>
                                <span class="trend-indicator" id="market_dollar_change">
                                    <i class="bi bi-dash me-1"></i>--
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="market-insight-card">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <i class="bi bi-percent text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="fw-bold">Taxa Selic</h5>
                                <h3 class="text-warning mb-2">13,75% a.a.</h3>
                                <small class="text-muted">Última reunião COPOM</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carteira -->
        <div class="card-modern mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0"><i class="bi bi-wallet2 text-purple me-2"></i>Minha Carteira</h4>
                    <button class="btn btn-outline-purple btn-modern" onclick="atualizarPrecos()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Atualizar Cotações
                    </button>
                </div>

                <?php if (empty($investments)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-graph-up text-purple" style="font-size: 5rem; opacity: 0.3;"></i>
                        <h4 style="color: #6c757d;" class="mt-4">Nenhum investimento encontrado</h4>
                        <p style="color: #6c757d;" class="mb-4">Comece adicionando seu primeiro investimento e acompanhe sua evolução</p>
                        <button class="btn btn-purple btn-modern" data-bs-toggle="modal" data-bs-target="#addInvestmentModal">
                            <i class="bi bi-plus-lg me-2"></i>Adicionar Primeiro Investimento
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ativo</th>
                                    <th>Tipo</th>
                                    <th>Quantidade</th>
                                    <th>Preço Médio</th>
                                    <th>Investido</th>
                                    <th>Cotação Atual</th>
                                    <th>Valor Atual</th>
                                    <th>Resultado</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($investments as $inv): ?>
                                    <tr data-investment-id="<?= $inv['id'] ?>" data-symbol="<?= htmlspecialchars($inv['nome_ativo']) ?>" data-type="<?= htmlspecialchars($inv['tipo_investimento']) ?>" data-quantity="<?= $inv['quantidade'] ?>" data-buy-price="<?= $inv['preco_compra'] ?>">
                                        <td>
                                            <strong><?= htmlspecialchars($inv['nome_ativo']) ?></strong>
                                            <br><small style="color: #6c757d;"><?= date('d/m/Y', strtotime($inv['data_compra'])) ?></small>
                                        </td>
                                        <td><span class="badge badge-purple"><?= htmlspecialchars($inv['tipo_investimento']) ?></span></td>
                                        <td><?= number_format($inv['quantidade'], 2, ',', '.') ?></td>
                                        <td>R$ <?= number_format($inv['preco_compra'], 2, ',', '.') ?></td>
                                        <td>R$ <?= number_format($inv['quantidade'] * $inv['preco_compra'], 2, ',', '.') ?></td>
                                        <td class="current-price"><span class="spinner-modern" style="border-color: #6c757d; border-top-color: #667eea;"></span></td>
                                        <td class="current-value">-</td>
                                        <td class="investment-result">-</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal(<?= htmlspecialchars(json_encode($inv)) ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="?delete_investment=<?= $inv['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir este investimento?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="mb-5">
            <h3 class="fw-bold mb-4">
                <i class="bi bi-lightning text-warning me-2"></i>Ações Rápidas
            </h3>
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="quick-action-card" data-bs-toggle="modal" data-bs-target="#addInvestmentModal">
                        <i class="bi bi-plus-circle-fill"></i>
                        <h6 class="fw-bold mb-1">Adicionar Investimento</h6>
                        <small class="text-muted">Registre novo ativo</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="quick-action-card" onclick="window.location.href='calculators.php'">
                        <i class="bi bi-calculator-fill"></i>
                        <h6 class="fw-bold mb-1">Calculadoras</h6>
                        <small class="text-muted">Simule investimentos</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="quick-action-card" onclick="window.location.href='ativos.php'">
                        <i class="bi bi-list-ul"></i>
                        <h6 class="fw-bold mb-1">Explorar Ativos</h6>
                        <small class="text-muted">Ver lista completa B3</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="quick-action-card" onclick="window.location.href='conversabot.php'">
                        <i class="bi bi-robot"></i>
                        <h6 class="fw-bold mb-1">Consultoria IA</h6>
                        <small class="text-muted">Tirar dúvidas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aviso -->
        <div class="row g-4">
            <div class="col-12">
                <div class="card-modern" style="background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%); color: white;">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-exclamation-triangle-fill fs-1 mb-3"></i>
                        <h5 class="fw-bold mb-3">AVISO IMPORTANTE</h5>
                        <p class="mb-0">
                            As informações fornecidas são aproximadas e para fins informativos. 
                            Use fontes oficiais (B3, corretoras, CVM) para operações reais. 
                            Investimentos envolvem riscos e rentabilidade passada não garante resultados futuros.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Adicionar -->
    <div class="modal fade" id="addInvestmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle text-purple me-2"></i>Novo Investimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de Investimento</label>
                            <select class="form-select" name="tipo_investimento" required>
                                <option value="">Selecione o tipo</option>
                                <option value="Ação">Ação</option>
                                <option value="FII">Fundo Imobiliário (FII)</option>
                                <option value="Cripto">Criptomoeda</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome do Ativo</label>
                            <input type="text" class="form-control" name="nome_ativo" placeholder="Ex: PETR4, MXRF11, BTC" required>
                            <small style="color: #6c757d;">Digite o código da ação, FII ou criptomoeda</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Quantidade</label>
                            <input type="number" step="0.01" class="form-control" name="quantidade" placeholder="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Preço de Compra (R$)</label>
                            <input type="text" class="form-control" id="preco_compra" placeholder="25,50" required>
                            <small style="color: #6c757d;">Digite o valor (exemplo: 25,50)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Data da Compra</label>
                            <input type="date" class="form-control" name="data_compra" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="add_investment" class="btn btn-purple btn-modern">
                            <i class="bi bi-check-lg me-2"></i>Adicionar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="editInvestmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil text-purple me-2"></i>Editar Investimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="investment_id" id="edit_investment_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de Investimento</label>
                            <select class="form-select" name="tipo_investimento" id="edit_tipo_investimento" required>
                                <option value="">Selecione o tipo</option>
                                <option value="Ação">Ação</option>
                                <option value="FII">Fundo Imobiliário (FII)</option>
                                <option value="Cripto">Criptomoeda</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome do Ativo</label>
                            <input type="text" class="form-control" name="nome_ativo" id="edit_nome_ativo" placeholder="Ex: PETR4, MXRF11, BTC" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Quantidade</label>
                            <input type="number" step="0.01" class="form-control" name="quantidade" id="edit_quantidade" placeholder="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Preço de Compra (R$)</label>
                            <input type="text" class="form-control" id="edit_preco_compra" placeholder="25,50" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Data da Compra</label>
                            <input type="date" class="form-control" name="data_compra" id="edit_data_compra" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="edit_investment" class="btn btn-purple btn-modern">
                            <i class="bi bi-check-lg me-2"></i>Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /**
         * ========================================
         * SISTEMA DE GESTÃO DE INVESTIMENTOS
         * ========================================
         *
         * Sistema completo para acompanhamento de carteira de investimentos
         * com dados reais de mercado em tempo real.
         *
         * APIs Integradas:
         * - BRAPI: Cotações da B3 (ações e FIIs brasileiros)
         * - CoinGecko: Preços de criptomoedas em BRL
         * - ExchangeRate: Taxas de câmbio (USD, EUR)
         * - Yahoo Finance: Dados históricos para gráficos
         *
         * Recursos:
         * - Cotações em tempo real com cache inteligente (5 minutos)
         * - Gráficos interativos com Chart.js
         * - Cálculo automático de rentabilidade
         * - Alocação de portfólio por tipo de investimento
         * - Insights de mercado (Ibovespa, Dólar, Selic)
         * - Interface responsiva e localizada em PT-BR
         *
         * Desenvolvido para o projeto FinançasJá
         * Última atualização: 2025
         * ========================================
         */

        // ========== CONFIGURAÇÕES E CONSTANTES ==========
        const TOKEN_BRAPI = '<?= BRAPI_TOKEN ?>';
        const URL_BRAPI = 'https://brapi.dev/api/quote';
        const totalInvestido = <?= (float)$total_invested ?>;
        let valorAtualTotal = 0;
        let cacheCotacoes = {};
        let instanciasGraficos = {};
        let instanciaGraficoAlocacao = null;

        // Mapeamento de criptomoedas para IDs do CoinGecko
        const MAPA_CRIPTOMOEDAS = {
            'BTC': 'bitcoin',
            'ETH': 'ethereum',
            'USDT': 'tether',
            'BNB': 'binancecoin',
            'XRP': 'ripple',
            'ADA': 'cardano',
            'DOGE': 'dogecoin',
            'SOL': 'solana',
            'DOT': 'polkadot',
            'MATIC': 'matic-network',
            'LTC': 'litecoin',
            'SHIB': 'shiba-inu',
            'AVAX': 'avalanche-2',
            'LINK': 'chainlink',
            'UNI': 'uniswap'
        };

        // Tempo de cache em milissegundos (5 minutos)
        const TEMPO_CACHE = 300000;

        // ========== COTAÇÕES DE MOEDAS ==========
        const URL_TAXA_CAMBIO = 'https://open.er-api.com/v6/latest/USD';

        /**
         * Busca e atualiza as cotações do Dólar (compra e venda)
         * Usa a API ExchangeRate para obter taxas em tempo real
         */
        async function obterCotacoesDolar() {
            try {
                const resposta = await fetch(URL_TAXA_CAMBIO);
                if (!resposta.ok) throw new Error(`Erro HTTP ${resposta.status}`);
                const dados = await resposta.json();

                if (dados.result === 'success' && dados.rates && dados.rates.BRL) {
                    const taxaCambio = dados.rates.BRL;
                    const spread = 0.02; // Spread de 2% simulando compra/venda
                    const valorCompra = taxaCambio * (1 - spread / 2);
                    const valorVenda = taxaCambio * (1 + spread / 2);

                    document.getElementById("dollar_compra").innerText = valorCompra.toFixed(2);
                    document.getElementById("dollar_venda").innerText = valorVenda.toFixed(2);

                    // Atualizar seção de insights de mercado
                    document.getElementById("market_dollar").innerText = 'R$ ' + taxaCambio.toFixed(2);
                } else {
                    throw new Error("Dados inválidos recebidos da API");
                }
            } catch (erro) {
                console.error("Erro ao obter cotação do Dólar:", erro);
                document.getElementById("dollar_compra").innerText = "Indisponível";
                document.getElementById("dollar_venda").innerText = "Indisponível";
                document.getElementById("market_dollar").innerText = "Erro ao carregar";
            } finally {
                // Atualizar horário da última atualização
                document.getElementById("dollar_update").innerText = new Date().toLocaleTimeString('pt-BR', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }

        /**
         * Busca e atualiza as cotações do Euro (compra e venda)
         * Calcula a taxa EUR/BRL através da conversão USD->EUR e USD->BRL
         */
        async function obterCotacoesEuro() {
            try {
                const resposta = await fetch(URL_TAXA_CAMBIO);
                if (!resposta.ok) throw new Error(`Erro HTTP ${resposta.status}`);
                const dados = await resposta.json();

                if (dados.result === 'success' && dados.rates && dados.rates.EUR && dados.rates.BRL) {
                    const taxaUsdParaBrl = dados.rates.BRL;
                    const taxaUsdParaEur = dados.rates.EUR;
                    const taxaEurParaBrl = taxaUsdParaBrl / taxaUsdParaEur;
                    const spread = 0.02; // Spread de 2%
                    const valorCompra = taxaEurParaBrl * (1 - spread / 2);
                    const valorVenda = taxaEurParaBrl * (1 + spread / 2);

                    document.getElementById("euro_compra").innerText = valorCompra.toFixed(2);
                    document.getElementById("euro_venda").innerText = valorVenda.toFixed(2);
                } else {
                    throw new Error("Dados inválidos recebidos da API");
                }
            } catch (erro) {
                console.error("Erro ao obter cotação do Euro:", erro);
                document.getElementById("euro_compra").innerText = "Indisponível";
                document.getElementById("euro_venda").innerText = "Indisponível";
            } finally {
                document.getElementById("euro_update").innerText = new Date().toLocaleTimeString('pt-BR', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }

        /**
         * Busca e atualiza o preço do Bitcoin em Reais
         * Usa a API CoinGecko para obter cotação em tempo real
         */
        async function obterPrecoBitcoin() {
            const urlApi = "https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=brl";
            try {
                const resposta = await fetch(urlApi);
                if (!resposta.ok) throw new Error(`Erro HTTP ${resposta.status}`);
                const dados = await resposta.json();

                if (dados.bitcoin?.brl) {
                    document.getElementById("bitcoin_preco").innerText = dados.bitcoin.brl.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                } else {
                    throw new Error("Dados de Bitcoin não disponíveis");
                }
            } catch (erro) {
                console.error("Erro ao obter cotação do Bitcoin:", erro);
                document.getElementById("bitcoin_preco").innerText = "Indisponível";
            } finally {
                document.getElementById("bitcoin_update").innerText = new Date().toLocaleTimeString('pt-BR', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }

        /**
         * Atualiza todas as cotações de moedas (Dólar, Euro e Bitcoin)
         */
        function atualizarCotacoesMoedas() {
            obterCotacoesDolar();
            obterCotacoesEuro();
            obterPrecoBitcoin();
        }

        // ========== INSIGHTS DE MERCADO ==========
        /**
         * Carrega e atualiza os insights de mercado (Ibovespa)
         * Usa a API BRAPI para obter dados da bolsa brasileira
         */
        async function carregarInsightsMercado() {
            try {
                // Buscar dados do Ibovespa (índice principal da B3)
                const respostaIbov = await fetch(`${URL_BRAPI}/^BVSP?token=${TOKEN_BRAPI}`);
                if (respostaIbov.ok) {
                    const dadosIbov = await respostaIbov.json();
                    if (dadosIbov.results && dadosIbov.results[0]) {
                        const ibovespa = dadosIbov.results[0];
                        const valorAtual = ibovespa.regularMarketPrice.toLocaleString('pt-BR', {maximumFractionDigits: 0});
                        const variacao = ibovespa.regularMarketChangePercent;

                        // Atualizar valor do Ibovespa
                        document.getElementById('ibovespa_value').textContent = valorAtual + ' pts';

                        // Atualizar variação com indicador visual
                        const elementoVariacao = document.getElementById('ibovespa_change');
                        elementoVariacao.textContent = (variacao >= 0 ? '+' : '') + variacao.toFixed(2) + '%';
                        elementoVariacao.className = 'trend-indicator ' + (variacao >= 0 ? 'trend-up' : 'trend-down');
                        elementoVariacao.innerHTML = `<i class="bi bi-arrow-${variacao >= 0 ? 'up' : 'down'} me-1"></i>` + elementoVariacao.textContent;
                    }
                }
            } catch (erro) {
                console.error('Erro ao carregar insights de mercado:', erro);
                document.getElementById('ibovespa_value').textContent = 'Dados indisponíveis no momento';
                document.getElementById('ibovespa_change').textContent = '--';
            }
        }

        // ========== FUNÇÕES DE COTAÇÃO DE PREÇOS ==========
        /**
         * Busca o preço de uma ação ou FII brasileiro na B3
         * Implementa cache de 5 minutos para reduzir chamadas à API
         * @param {string} simbolo - Código do ativo (ex: PETR4, VALE3)
         * @returns {number|null} - Preço atual ou null se não disponível
         */
        async function buscarPrecoAcaoBrasileira(simbolo) {
            // Verificar se existe no cache e se ainda é válido
            if (cacheCotacoes[simbolo]) {
                const idadeCache = Date.now() - cacheCotacoes[simbolo].timestamp;
                if (idadeCache < TEMPO_CACHE) {
                    console.log(`Usando cache para ${simbolo}`);
                    return cacheCotacoes[simbolo].preco;
                }
            }

            try {
                const resposta = await fetch(`${URL_BRAPI}/${simbolo}?token=${TOKEN_BRAPI}`);
                if (!resposta.ok) {
                    console.warn(`BRAPI retornou status ${resposta.status} para ${simbolo}`);
                    return null;
                }
                const dados = await resposta.json();

                if (dados.results && dados.results[0]?.regularMarketPrice) {
                    const preco = dados.results[0].regularMarketPrice;
                    // Armazenar no cache
                    cacheCotacoes[simbolo] = {
                        preco,
                        timestamp: Date.now()
                    };
                    return preco;
                }
            } catch (erro) {
                console.error(`Erro ao buscar preço de ${simbolo} na BRAPI:`, erro);
            }
            return null;
        }

        /**
         * Busca o preço de uma criptomoeda
         * Implementa cache de 5 minutos para reduzir chamadas à API
         * @param {string} simbolo - Código da cripto (ex: BTC, ETH)
         * @returns {number|null} - Preço atual em BRL ou null se não disponível
         */
        async function buscarPrecoCriptomoeda(simbolo) {
            const idCripto = MAPA_CRIPTOMOEDAS[simbolo] || simbolo.toLowerCase();

            // Verificar cache
            if (cacheCotacoes[simbolo]) {
                const idadeCache = Date.now() - cacheCotacoes[simbolo].timestamp;
                if (idadeCache < TEMPO_CACHE) {
                    console.log(`Usando cache para ${simbolo}`);
                    return cacheCotacoes[simbolo].preco;
                }
            }

            try {
                const urlApi = `https://api.coingecko.com/api/v3/simple/price?ids=${idCripto}&vs_currencies=brl`;
                const resposta = await fetch(urlApi);
                if (!resposta.ok) {
                    console.warn(`CoinGecko retornou status ${resposta.status} para ${simbolo}`);
                    return null;
                }
                const dados = await resposta.json();

                if (dados[idCripto]?.brl) {
                    const preco = dados[idCripto].brl;
                    // Armazenar no cache
                    cacheCotacoes[simbolo] = {
                        preco,
                        timestamp: Date.now()
                    };
                    return preco;
                }
            } catch (erro) {
                console.error(`Erro ao buscar preço de ${simbolo} no CoinGecko:`, erro);
            }
            return null;
        }

        /**
         * Busca o preço de um ativo baseado no tipo
         * @param {string} simbolo - Código do ativo
         * @param {string} tipo - Tipo do investimento (Ação, FII, Cripto, Renda Fixa)
         * @returns {number|null} - Preço atual ou null
         */
        async function buscarPrecoAtivo(simbolo, tipo) {
            if (tipo === 'Cripto') return await buscarPrecoCriptomoeda(simbolo);
            if (tipo === 'Ação' || tipo === 'FII') return await buscarPrecoAcaoBrasileira(simbolo);
            // Renda Fixa não tem cotação dinâmica
            return null;
        }

        // ========== ATUALIZAR INVESTIMENTOS DA CARTEIRA ==========
        /**
         * Atualiza uma linha de investimento com o preço atual e calcula rentabilidade
         * @param {HTMLElement} linha - Elemento TR da tabela
         */
        async function atualizarLinhaInvestimento(linha) {
            const simbolo = linha.dataset.symbol.toUpperCase().trim();
            const tipo = linha.dataset.type;
            const quantidade = parseFloat(linha.dataset.quantity);
            const precoCompra = parseFloat(linha.dataset.buyPrice);
            const valorInvestido = quantidade * precoCompra;

            let precoAtual = precoCompra;
            // Buscar cotação atual apenas se não for Renda Fixa
            if (tipo !== 'Renda Fixa') {
                precoAtual = await buscarPrecoAtivo(simbolo, tipo) || precoCompra;
            }

            const valorAtual = quantidade * precoAtual;
            const lucro = valorAtual - valorInvestido;
            const percentualLucro = valorInvestido > 0 ? (lucro / valorInvestido) * 100 : 0;

            // Atualizar preço atual na tabela
            linha.querySelector('.current-price').innerHTML = precoAtual === precoCompra && tipo !== 'Renda Fixa' ?
                `<span class="text-muted">R$ ${precoAtual.toFixed(2)}</span><br><small class="text-warning"><i class="bi bi-exclamation-circle"></i> Sem cotação</small>` :
                `R$ ${precoAtual.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

            // Atualizar valor atual
            linha.querySelector('.current-value').textContent = `R$ ${valorAtual.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

            // Atualizar resultado (lucro/prejuízo)
            const classeResultado = lucro >= 0 ? 'text-success' : 'text-danger';
            const iconeResultado = lucro >= 0 ? '↑' : '↓';
            const textoResultado = lucro >= 0 ? 'Alta' : 'Queda';
            linha.querySelector('.investment-result').innerHTML = `
                <div class="${classeResultado}">
                    <strong>${lucro >= 0 ? '+' : ''}R$ ${lucro.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong>
                    <br><small>${iconeResultado} ${textoResultado} ${percentualLucro.toFixed(2)}%</small>
                </div>
            `;

            valorAtualTotal += valorAtual;
        }

        /**
         * Atualiza todos os preços dos investimentos na carteira
         * Busca cotações atuais e recalcula rentabilidade total
         */
        async function atualizarPrecos() {
            valorAtualTotal = 0;
            cacheCotacoes = {}; // Limpar cache para forçar atualização
            const linhas = document.querySelectorAll('tbody tr[data-investment-id]');
            if (linhas.length === 0) return;

            // Mostrar spinners de carregamento
            for (const linha of linhas) {
                linha.querySelector('.current-price').innerHTML = '<span class="spinner-modern" style="border-color: #6c757d; border-top-color: #667eea;"></span>';
                linha.querySelector('.current-value').innerHTML = '<span class="spinner-modern" style="border-color: #6c757d; border-top-color: #667eea;"></span>';
                linha.querySelector('.investment-result').innerHTML = '<span class="spinner-modern" style="border-color: #6c757d; border-top-color: #667eea;"></span>';
            }

            document.getElementById('currentValue').innerHTML = '<span class="spinner-modern"></span>';
            document.getElementById('profitability').innerHTML = '<span class="spinner-modern"></span>';

            // Atualizar cada investimento
            for (const linha of linhas) {
                await atualizarLinhaInvestimento(linha);
                await new Promise(r => setTimeout(r, 100)); // Pequeno delay entre chamadas
            }

            // Calcular rentabilidade total
            const lucroTotal = valorAtualTotal - totalInvestido;
            const percentualLucroTotal = totalInvestido > 0 ? (lucroTotal / totalInvestido) * 100 : 0;

            // Atualizar valor atual total
            document.getElementById('currentValue').textContent = `R$ ${valorAtualTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

            // Atualizar rentabilidade com cores e ícones
            const classeLucro = lucroTotal >= 0 ? 'text-success' : 'text-danger';
            const iconeLucro = lucroTotal >= 0 ? '↑' : '↓';
            const textoLucro = lucroTotal >= 0 ? 'Alta' : 'Queda';
            document.getElementById('profitability').innerHTML = `
                <span class="${classeLucro}">
                    ${iconeLucro} ${textoLucro} ${lucroTotal >= 0 ? '+' : ''}${percentualLucroTotal.toFixed(2)}%
                </span>
            `;

            // Atualizar gráficos de alocação e performance
            atualizarGraficoAlocacao();
            atualizarPerformancePorTipo();
        }

        // ========== GRÁFICO DE ALOCAÇÃO DO PORTFÓLIO ==========
        /**
         * Atualiza o gráfico de pizza mostrando a alocação por tipo de investimento
         */
        function atualizarGraficoAlocacao() {
            const linhas = document.querySelectorAll('tbody tr[data-investment-id]');
            const dadosPorTipo = {};

            // Somar valores investidos por tipo
            linhas.forEach(linha => {
                const tipo = linha.dataset.type;
                const quantidade = parseFloat(linha.dataset.quantity);
                const precoCompra = parseFloat(linha.dataset.buyPrice);
                const valorInvestido = quantidade * precoCompra;

                if (!dadosPorTipo[tipo]) {
                    dadosPorTipo[tipo] = 0;
                }
                dadosPorTipo[tipo] += valorInvestido;
            });

            const rotulos = Object.keys(dadosPorTipo);
            const valores = Object.values(dadosPorTipo);

            // Cores por tipo de investimento
            const coresPorTipo = {
                'Ação': '#667eea',
                'FII': '#28a745',
                'Cripto': '#f7931a'
            };

            const coresFundo = rotulos.map(rotulo => coresPorTipo[rotulo] || '#6c757d');

            const elementoCanvas = document.getElementById('allocationChart');
            if (!elementoCanvas) return;

            // Destruir instância anterior se existir
            if (instanciaGraficoAlocacao) {
                instanciaGraficoAlocacao.destroy();
            }

            // Criar novo gráfico
            instanciaGraficoAlocacao = new Chart(elementoCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: rotulos,
                    datasets: [{
                        data: valores,
                        backgroundColor: coresFundo,
                        borderColor: '#1a1a2e',
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: 'white',
                                padding: 20,
                                font: { size: 13 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(contexto) {
                                    const rotulo = contexto.label || '';
                                    const valor = contexto.parsed;
                                    const total = contexto.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentual = ((valor / total) * 100).toFixed(1);
                                    return `${rotulo}: R$ ${valor.toLocaleString('pt-BR', {minimumFractionDigits: 2})} (${percentual}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // ========== PERFORMANCE POR TIPO DE INVESTIMENTO ==========
        /**
         * Calcula e exibe a performance agregada por tipo de investimento
         * Mostra ganhos e perdas separados por categoria (Ação, FII, Cripto, etc.)
         */
        async function atualizarPerformancePorTipo() {
            const linhas = document.querySelectorAll('tbody tr[data-investment-id]');
            const performancePorTipo = {};

            // Coletar dados de cada investimento
            for (const linha of linhas) {
                const tipo = linha.dataset.type;
                const elementoResultado = linha.querySelector('.investment-result');

                if (!performancePorTipo[tipo]) {
                    performancePorTipo[tipo] = { ganho: 0, perda: 0, quantidade: 0 };
                }

                performancePorTipo[tipo].quantidade++;

                // Extrair valor do resultado (lucro ou prejuízo)
                const textoResultado = elementoResultado.textContent;
                const match = textoResultado.match(/([+-])?R\$\s*([\d.,]+)/);
                if (match) {
                    const valor = parseFloat(match[2].replace(/\./g, '').replace(',', '.'));
                    const ehPositivo = match[1] !== '-';

                    if (ehPositivo) {
                        performancePorTipo[tipo].ganho += valor;
                    } else {
                        performancePorTipo[tipo].perda += valor;
                    }
                }
            }

            // Gerar HTML para exibição
            const container = document.getElementById('performanceByType');
            let htmlConteudo = '';

            Object.keys(performancePorTipo).forEach(tipo => {
                const perf = performancePorTipo[tipo];
                const resultadoTotal = perf.ganho - perf.perda;
                const ehPositivo = resultadoTotal >= 0;
                const corTexto = ehPositivo ? 'success' : 'danger';
                const icone = ehPositivo ? 'arrow-up' : 'arrow-down';

                htmlConteudo += `
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <div>
                            <h6 class="mb-1">${tipo}</h6>
                            <small class="text-muted">${perf.quantidade} ativo(s)</small>
                        </div>
                        <div class="text-end">
                            <h5 class="text-${corTexto} mb-0">
                                <i class="bi bi-${icone} me-1"></i>
                                ${ehPositivo ? '+' : ''}R$ ${resultadoTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                            </h5>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = htmlConteudo || '<p class="text-muted text-center">Nenhum dado disponível</p>';
        }

        // ========== FUNÇÕES DE EDIÇÃO ==========
        function openEditModal(investment) {
            document.getElementById('edit_investment_id').value = investment.id;
            document.getElementById('edit_tipo_investimento').value = investment.tipo_investimento;
            document.getElementById('edit_nome_ativo').value = investment.nome_ativo;
            document.getElementById('edit_quantidade').value = investment.quantidade;
            
            const precoFormatado = parseFloat(investment.preco_compra).toFixed(2).replace('.', ',');
            document.getElementById('edit_preco_compra').value = precoFormatado;
            
            document.getElementById('edit_data_compra').value = investment.data_compra;
            
            const modal = new bootstrap.Modal(document.getElementById('editInvestmentModal'));
            modal.show();
        }

        // ========== FORMATAÇÃO DO CAMPO DE PREÇO ==========
        function setupPriceInput(inputId) {
            const precoInput = document.getElementById(inputId);
            
            if (precoInput) {
                precoInput.addEventListener('input', function(e) {
                    let value = e.target.value;
                    value = value.replace(/\D/g, '');

                    if (value.length > 0) {
                        value = (parseInt(value) / 100).toFixed(2);
                        value = value.replace('.', ',');
                        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    }

                    e.target.value = value;
                });

                precoInput.closest('form').addEventListener('submit', function(e) {
                    const valorFormatado = precoInput.value;
                    const valorNumerico = valorFormatado.replace(/\./g, '').replace(',', '.');

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'preco_compra';
                    hiddenInput.value = valorNumerico;
                    this.appendChild(hiddenInput);

                    precoInput.removeAttribute('name');
                });
            }
        }

        // ========== INICIALIZAÇÃO DO SISTEMA ==========
        /**
         * Inicializa a página quando o DOM estiver pronto
         * Configura inputs, inicia atualizações automáticas e listeners de eventos
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar formatação de entrada de preços nos formulários
            setupPriceInput('preco_compra');
            setupPriceInput('edit_preco_compra');

            // Carregar cotações de moedas imediatamente e atualizar a cada 1 minuto
            atualizarCotacoesMoedas();
            setInterval(atualizarCotacoesMoedas, 60000);

            // Carregar insights de mercado e atualizar a cada 5 minutos
            carregarInsightsMercado();
            setInterval(carregarInsightsMercado, 300000);

            // Se existem investimentos na carteira, atualizar preços
            const linhas = document.querySelectorAll('tbody tr[data-investment-id]');
            if (linhas.length > 0) {
                atualizarPrecos();
                // Atualizar cotações automaticamente a cada 5 minutos
                setInterval(atualizarPrecos, 300000);
            }

            // Melhorar o botão de atualização de preços com feedback visual
            const botaoAtualizar = document.querySelector('button[onclick="atualizarPrecos()"]');
            if (botaoAtualizar) {
                botaoAtualizar.onclick = (evento) => {
                    evento.preventDefault();
                    botaoAtualizar.disabled = true;
                    botaoAtualizar.innerHTML = '<span class="spinner-modern me-2"></span>Atualizando...';
                    atualizarPrecos().finally(() => {
                        botaoAtualizar.disabled = false;
                        botaoAtualizar.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Atualizar Cotações';
                    });
                };
            }

            console.log('Sistema de investimentos inicializado com sucesso!');
            console.log(`Total investido: R$ ${totalInvestido.toFixed(2)}`);
            console.log(`APIs integradas: BRAPI (B3), CoinGecko (Cripto), ExchangeRate (Câmbio)`);
        });
    </script>

    <!-- Footer -->
    <footer class="footer-modern">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand mb-3">FinançasJá</div>
                    <p>Sua plataforma completa para gestão financeira pessoal com inteligência artificial.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold mb-3">Plataforma</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="dashboard.php" class="footer-link">Dashboard</a></li>
                        <li class="mb-2"><a href="investments.php" class="footer-link">Investimentos</a></li>
                        <li class="mb-2"><a href="calculators.php" class="footer-link">Calculadoras</a></li>
                        <li class="mb-2"><a href="conversabot.php" class="footer-link">Assistente IA</a></li>
                        <li class="mb-2"><a href="education.php" class="footer-link">Academia</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold mb-3">Recursos</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="quiz.php" class="footer-link">Quiz Financeiro</a></li>
                        <li class="mb-2"><a href="exercicios.php" class="footer-link">Exercícios</a></li>
                        <li class="mb-2"><a href="plans.php" class="footer-link">Planos</a></li>
                        <li class="mb-2"><a href="ativos.php" class="footer-link">Lista B3</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold mb-3">Suporte</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="support.php" class="footer-link">Central de Ajuda</a></li>
                        <li class="mb-2"><a href="support1.php" class="footer-link">Contato</a></li>
                        <li class="mb-2"><a href="about.php" class="footer-link">Sobre Nós</a></li>
                        <li class="mb-2"><a href="about1.php" class="footer-link">Nossa História</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold mb-3">Conta</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="login.php" class="footer-link">Login</a></li>
                        <li class="mb-2"><a href="register.php" class="footer-link">Criar Conta</a></li>
                        <li class="mb-2"><a href="logout.php" class="footer-link">Sair</a></li>
                        <li class="mb-2"><a href="home.php" class="footer-link">Início</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(138, 43, 226, 0.2);">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2025 FinançasJá. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Feito com <i class="bi bi-heart-fill text-danger"></i> para sua liberdade financeira</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>