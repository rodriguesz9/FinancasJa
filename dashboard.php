<?php
session_start();
require_once 'config/database.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Exportar relatório
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    $stmt = $pdo->prepare("SELECT id, tipo, categoria, descricao, valor, data_transacao, created_at FROM transactions WHERE user_id = ? ORDER BY data_transacao DESC");
    $stmt->execute([$user_id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="relatorio_transacoes.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Tipo', 'Categoria', 'Descrição', 'Valor', 'Data Transação', 'Criado Em']);

    foreach ($transactions as $row) {
        fputcsv($output, [
            $row['id'],
            $row['tipo'],
            $row['categoria'],
            $row['descricao'],
            $row['valor'],
            $row['data_transacao'],
            $row['created_at']
        ]);
    }
    exit();
}

// Processar exclusão de transação
if (isset($_GET['delete_transaction'])) {
    $transaction_id = (int)$_GET['delete_transaction'];
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
    $stmt->execute([$transaction_id, $user_id]);
    header('Location: dashboard.php');
    exit();
}

// ============================================
// SISTEMA DE DESPESAS FIXAS
// ============================================

// Processar exclusão de despesa fixa
if (isset($_GET['delete_expense'])) {
    $expense_id = (int)$_GET['delete_expense'];
    $stmt = $pdo->prepare("DELETE FROM fixed_expenses WHERE id = ? AND user_id = ?");
    $stmt->execute([$expense_id, $user_id]);
    $_SESSION['success_message'] = 'Despesa fixa excluída com sucesso!';
    header('Location: dashboard.php');
    exit();
}

// Processar nova transação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_transaction'])) {
    $tipo = $_POST['tipo'];
    $categoria = $_POST['categoria'];
    $descricao = $_POST['descricao'];
    $valor = (float)$_POST['valor'];
    $data = $_POST['data'];

    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, tipo, categoria, descricao, valor, data_transacao) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $tipo, $categoria, $descricao, $valor, $data])) {
        $_SESSION['success_message'] = 'Transação adicionada com sucesso!';
        header('Location: dashboard.php');
        exit();
    }
}

// Processar edição de transação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_transaction'])) {
    $transaction_id = (int)$_POST['transaction_id'];
    $tipo = $_POST['tipo'];
    $categoria = $_POST['categoria'];
    $descricao = $_POST['descricao'];
    $valor = (float)$_POST['valor'];
    $data = $_POST['data'];

    $stmt = $pdo->prepare("UPDATE transactions SET tipo = ?, categoria = ?, descricao = ?, valor = ?, data_transacao = ? WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$tipo, $categoria, $descricao, $valor, $data, $transaction_id, $user_id])) {
        $_SESSION['success_message'] = 'Transação atualizada com sucesso!';
        header('Location: dashboard.php');
        exit();
    }
}

// Processar nova despesa fixa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_fixed_expense'])) {
    $nome_despesa = $_POST['nome_despesa'];
    $descricao = $_POST['descricao'] ?? null;
    $valor = (float)$_POST['valor'];
    $categoria = $_POST['categoria'];
    $dia_vencimento = (int)$_POST['dia_vencimento'];
    $dias_aviso = (int)$_POST['dias_aviso'];
    $icone = $_POST['icone'];
    $cor = $_POST['cor'];

    $stmt = $pdo->prepare("INSERT INTO fixed_expenses (user_id, nome_despesa, descricao, valor, categoria, dia_vencimento, dias_aviso, icone, cor)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $nome_despesa, $descricao, $valor, $categoria, $dia_vencimento, $dias_aviso, $icone, $cor])) {
        $_SESSION['success_message'] = 'Despesa fixa adicionada com sucesso!';
        header('Location: dashboard.php');
        exit();
    }
}

// Processar edição de despesa fixa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_fixed_expense'])) {
    $expense_id = (int)$_POST['expense_id'];
    $nome_despesa = $_POST['nome_despesa'];
    $descricao = $_POST['descricao'] ?? null;
    $valor = (float)$_POST['valor'];
    $categoria = $_POST['categoria'];
    $dia_vencimento = (int)$_POST['dia_vencimento'];
    $dias_aviso = (int)$_POST['dias_aviso'];
    $icone = $_POST['icone'];
    $cor = $_POST['cor'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE fixed_expenses SET nome_despesa = ?, descricao = ?, valor = ?, categoria = ?, dia_vencimento = ?, dias_aviso = ?, icone = ?, cor = ?, status = ?
                           WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$nome_despesa, $descricao, $valor, $categoria, $dia_vencimento, $dias_aviso, $icone, $cor, $status, $expense_id, $user_id])) {
        $_SESSION['success_message'] = 'Despesa fixa atualizada com sucesso!';
        header('Location: dashboard.php');
        exit();
    }
}

// Marcar despesa como paga
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_as_paid'])) {
    $expense_id = (int)$_POST['expense_id'];
    $data_pagamento = date('Y-m-d');

    $stmt = $pdo->prepare("UPDATE fixed_expenses SET status = 'paga', data_ultimo_pagamento = ? WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$data_pagamento, $expense_id, $user_id])) {
        $_SESSION['success_message'] = 'Despesa marcada como paga!';
        header('Location: dashboard.php');
        exit();
    }
}

// Calcular totais
$stmt = $pdo->prepare("SELECT 
    COALESCE(SUM(CASE WHEN tipo = 'receita' THEN valor END), 0) as total_receitas,
    COALESCE(SUM(CASE WHEN tipo = 'despesa' THEN valor END), 0) as total_despesas
    FROM transactions WHERE user_id = ? AND MONTH(data_transacao) = MONTH(CURRENT_DATE())");
$stmt->execute([$user_id]);
$totals = $stmt->fetch();

$saldo = $totals['total_receitas'] - $totals['total_despesas'];

// Buscar últimas transações
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY data_transacao DESC, created_at DESC LIMIT 10");
$stmt->execute([$user_id]);
$recent_transactions = $stmt->fetchAll();

// Buscar gastos por categoria (últimos 30 dias)
$stmt = $pdo->prepare("SELECT categoria, SUM(valor) as total FROM transactions 
    WHERE user_id = ? AND tipo = 'despesa' AND data_transacao >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY) 
    GROUP BY categoria ORDER BY total DESC LIMIT 5");
$stmt->execute([$user_id]);
$expenses_by_category = $stmt->fetchAll();

// Buscar despesas fixas ativas do usuário
$stmt = $pdo->prepare("SELECT * FROM fixed_expenses WHERE user_id = ? AND status IN ('ativa', 'paga', 'atrasada') ORDER BY dia_vencimento ASC");
$stmt->execute([$user_id]);
$fixed_expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular notificações pendentes e status das despesas
$notificacoes_pendentes = [];
$total_despesas_mes = 0;
$dia_atual = (int)date('d');
$mes_atual = date('Y-m');

foreach ($fixed_expenses as &$expense) {
    $dia_vencimento = (int)$expense['dia_vencimento'];
    $dias_aviso = (int)$expense['dias_aviso'];
    $dias_ate_vencimento = $dia_vencimento - $dia_atual;

    // Verificar se já pagou este mês
    $ultimo_pagamento = $expense['data_ultimo_pagamento'];
    $pagou_este_mes = false;
    if ($ultimo_pagamento) {
        $mes_pagamento = date('Y-m', strtotime($ultimo_pagamento));
        $pagou_este_mes = ($mes_pagamento === $mes_atual);
    }

    // Atualizar status baseado no vencimento
    if ($pagou_este_mes) {
        $expense['status_calculado'] = 'paga';
        $expense['dias_ate_vencimento'] = null;
    } elseif ($dias_ate_vencimento < 0) {
        $expense['status_calculado'] = 'atrasada';
        $expense['dias_ate_vencimento'] = abs($dias_ate_vencimento);
        // Adicionar à lista de notificações
        $notificacoes_pendentes[] = [
            'tipo' => 'atrasada',
            'despesa' => $expense['nome_despesa'],
            'dias' => abs($dias_ate_vencimento),
            'valor' => $expense['valor']
        ];
    } elseif ($dias_ate_vencimento <= $dias_aviso) {
        $expense['status_calculado'] = 'proxima';
        $expense['dias_ate_vencimento'] = $dias_ate_vencimento;
        // Adicionar à lista de notificações
        $notificacoes_pendentes[] = [
            'tipo' => 'proxima',
            'despesa' => $expense['nome_despesa'],
            'dias' => $dias_ate_vencimento,
            'valor' => $expense['valor']
        ];
    } else {
        $expense['status_calculado'] = 'ativa';
        $expense['dias_ate_vencimento'] = $dias_ate_vencimento;
    }

    // Somar ao total do mês (exceto as já pagas)
    if (!$pagou_este_mes) {
        $total_despesas_mes += $expense['valor'];
    }
}
unset($expense); // Quebrar referência

// ============================================
// SISTEMA DE METAS FINANCEIRAS
// ============================================

// Processar nova meta financeira
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_goal'])) {
    $nome_meta = $_POST['nome_meta'];
    $descricao = $_POST['descricao'] ?? null;
    $valor_objetivo = (float)$_POST['valor_objetivo'];
    $data_inicio = $_POST['data_inicio'];
    $data_objetivo = $_POST['data_objetivo'];
    $categoria = $_POST['categoria_meta'];
    $icone = $_POST['icone'];
    $cor = $_POST['cor'];
    $prioridade = $_POST['prioridade'];

    $stmt = $pdo->prepare("INSERT INTO financial_goals (user_id, nome_meta, descricao, valor_objetivo, data_inicio, data_objetivo, categoria, icone, cor, prioridade)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $nome_meta, $descricao, $valor_objetivo, $data_inicio, $data_objetivo, $categoria, $icone, $cor, $prioridade])) {
        $_SESSION['success_message'] = 'Meta criada com sucesso!';
        header('Location: dashboard.php');
        exit();
    }
}

// Processar edição de meta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_goal'])) {
    $goal_id = (int)$_POST['goal_id'];
    $nome_meta = $_POST['nome_meta'];
    $descricao = $_POST['descricao'] ?? null;
    $valor_objetivo = (float)$_POST['valor_objetivo'];
    $data_objetivo = $_POST['data_objetivo'];
    $categoria = $_POST['categoria_meta'];
    $icone = $_POST['icone'];
    $cor = $_POST['cor'];
    $prioridade = $_POST['prioridade'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE financial_goals SET nome_meta = ?, descricao = ?, valor_objetivo = ?, data_objetivo = ?, categoria = ?, icone = ?, cor = ?, prioridade = ?, status = ?
                           WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$nome_meta, $descricao, $valor_objetivo, $data_objetivo, $categoria, $icone, $cor, $prioridade, $status, $goal_id, $user_id])) {
        $_SESSION['success_message'] = 'Meta atualizada com sucesso!';
        header('Location: dashboard.php');
        exit();
    }
}

// Processar contribuição para meta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_contribution'])) {
    $goal_id = (int)$_POST['goal_id'];
    $valor = (float)$_POST['valor_contribuicao'];
    $descricao = $_POST['descricao_contribuicao'] ?? null;
    $data_contribuicao = $_POST['data_contribuicao'];
    $tipo = $_POST['tipo_contribuicao'];

    // Inserir contribuição
    $stmt = $pdo->prepare("INSERT INTO goal_contributions (goal_id, user_id, valor, descricao, data_contribuicao, tipo)
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$goal_id, $user_id, $valor, $descricao, $data_contribuicao, $tipo]);

    // Atualizar valor atual da meta
    $multiplier = ($tipo === 'deposito') ? 1 : -1;
    $stmt = $pdo->prepare("UPDATE financial_goals SET valor_atual = valor_atual + ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$valor * $multiplier, $goal_id, $user_id]);

    // Verificar se meta foi concluída
    $stmt = $pdo->prepare("SELECT valor_atual, valor_objetivo FROM financial_goals WHERE id = ? AND user_id = ?");
    $stmt->execute([$goal_id, $user_id]);
    $goal = $stmt->fetch();

    if ($goal && $goal['valor_atual'] >= $goal['valor_objetivo']) {
        $stmt = $pdo->prepare("UPDATE financial_goals SET status = 'concluida' WHERE id = ? AND user_id = ?");
        $stmt->execute([$goal_id, $user_id]);
        $_SESSION['success_message'] = 'Contribuição adicionada! Meta concluída! 🎉';
    } else {
        $_SESSION['success_message'] = 'Contribuição adicionada com sucesso!';
    }

    header('Location: dashboard.php');
    exit();
}

// Processar exclusão de meta
if (isset($_GET['delete_goal'])) {
    $goal_id = (int)$_GET['delete_goal'];
    $stmt = $pdo->prepare("DELETE FROM financial_goals WHERE id = ? AND user_id = ?");
    $stmt->execute([$goal_id, $user_id]);
    $_SESSION['success_message'] = 'Meta excluída com sucesso!';
    header('Location: dashboard.php');
    exit();
}

// Buscar metas ativas do usuário
$stmt = $pdo->prepare("SELECT * FROM financial_goals WHERE user_id = ? AND status IN ('ativa', 'concluida') ORDER BY
                       CASE
                           WHEN status = 'concluida' THEN 3
                           WHEN prioridade = 'alta' THEN 1
                           WHEN prioridade = 'media' THEN 2
                           ELSE 3
                       END,
                       data_objetivo ASC");
$stmt->execute([$user_id]);
$financial_goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FinançasJá</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <style>
        .opcoes {
            background-color: rgba(26, 26, 46, 0.95);
            color: white;
        }

        img {
            loading: lazy;
        }

        .progress {
            height: 20px;
        }

        .progress-bar {
            transition: width 0.6s ease;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .expense-item {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .expense-item:last-child {
            border-bottom: none;
        }

        .budget-section .card-body {
            padding: 2rem;
        }

        .budget-item {
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .budget-item:last-child {
            border-bottom: none;
        }

        #transr {
            margin: 15px;
        }

        .opc {
            color: white;
            background-color: rgba(26, 26, 46, 0.95);
        }

        .action-btn-group {
            display: flex;
            gap: 0.5rem;
        }
    </style>
</head>

<body>
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
                        <a class="nav-link active" href="dashboard.php">
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

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['success_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <div class="container-fluid p-0">
        <section class="hero-modern py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-5 fw-bold mb-3">Dashboard Financeiro</h1>
                        <p class="lead mb-0">Controle completo das suas receitas e despesas em tempo real</p>
                    </div>
                    <div class="col-lg-4 text-end action-buttons">
                        <button class="btn btn-purple btn-modern" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                            <i class="bi bi-plus-lg me-2"></i>Nova Transação
                        </button>
                        <a href="dashboard.php?export=csv" class="btn btn-outline-purple btn-modern">
                            <i class="bi bi-download me-2"></i>Exportar Relatório
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-4">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="stat-card-modern success">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Receitas (Mês)</h6>
                                    <h3 class="text-success mb-0">R$ <?= number_format($totals['total_receitas'], 2, ',', '.') ?></h3>
                                </div>
                                <div class="text-success" style="font-size: 2.5rem;">
                                    <i class="bi bi-arrow-up-circle-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card-modern danger">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Despesas (Mês)</h6>
                                    <h3 class="text-danger mb-0">R$ <?= number_format($totals['total_despesas'], 2, ',', '.') ?></h3>
                                </div>
                                <div class="text-danger" style="font-size: 2.5rem;">
                                    <i class="bi bi-arrow-down-circle-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card-modern <?= $saldo >= 0 ? 'info' : 'warning' ?>">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Saldo (Mês)</h6>
                                    <h3 class="<?= $saldo >= 0 ? 'text-info' : 'text-warning' ?> mb-0">
                                        R$ <?= number_format($saldo, 2, ',', '.') ?>
                                    </h3>
                                    <small class="text-muted"><?= $saldo >= 0 ? 'Superávit' : 'Déficit' ?></small>
                                </div>
                                <div class="<?= $saldo >= 0 ? 'text-info' : 'text-warning' ?>" style="font-size: 2.5rem;">
                                    <i class="bi bi-wallet-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Adicione esta seção em dashboard.php APÓS a seção "Visão Geral Mensal" -->
<!-- Gráfico de Saldo Diário - VERSÃO COM DADOS REAIS -->
<section class="py-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-12">
                <div class="card-modern">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="fw-bold mb-1">
                                    <i class="bi bi-graph-up-arrow text-info me-2"></i>
                                    Evolução do Saldo
                                </h4>
                                <p class="text-muted mb-0">Acompanhe seu saldo ao longo do tempo</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-purple btn-sm" id="btn-week" onclick="changeBalancePeriod('week')">
                                    <i class="bi bi-calendar-week me-1"></i>7 dias
                                </button>
                                <button class="btn btn-purple btn-sm active" id="btn-month" onclick="changeBalancePeriod('month')">
                                    <i class="bi bi-calendar-month me-1"></i>30 dias
                                </button>
                                <button class="btn btn-outline-purple btn-sm" id="btn-year" onclick="changeBalancePeriod('year')">
                                    <i class="bi bi-calendar-range me-1"></i>Ano
                                </button>
                            </div>
                        </div>

                        <!-- Stats do Saldo -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="p-3 rounded" style="background: rgba(102, 126, 234, 0.1);">
                                    <small class="text-muted d-block mb-1">Saldo Inicial</small>
                                    <h5 class="mb-0 text-info" id="balance_start">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 rounded" style="background: rgba(40, 167, 69, 0.1);">
                                    <small class="text-muted d-block mb-1">Saldo Atual</small>
                                    <h5 class="mb-0 text-success" id="balance_current">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 rounded" style="background: rgba(255, 193, 7, 0.1);">
                                    <small class="text-muted d-block mb-1">Variação</small>
                                    <h5 class="mb-0 text-warning" id="balance_variation">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 rounded" style="background: rgba(138, 43, 226, 0.1);">
                                    <small class="text-muted d-block mb-1">Crescimento</small>
                                    <h5 class="mb-0 text-purple" id="balance_growth">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico -->
                        <div style="height: 350px; position: relative;">
                            <canvas id="balanceChart"></canvas>
                        </div>

                        <!-- Legenda e Insights -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div class="d-flex align-items-center">
                                        <div style="width: 16px; height: 16px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 3px;" class="me-2"></div>
                                        <small class="text-muted">Saldo Acumulado</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 16px; height: 16px; background: rgba(40, 167, 69, 0.2); border: 2px solid #28a745; border-radius: 3px;" class="me-2"></div>
                                        <small class="text-muted">Receitas</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 16px; height: 16px; background: rgba(220, 53, 69, 0.2); border: 2px solid #dc3545; border-radius: 3px;" class="me-2"></div>
                                        <small class="text-muted">Despesas</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Dados baseados nas suas transações reais
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
let balanceChart;
let currentPeriod = 'month';

// Função para buscar dados REAIS do backend
async function fetchBalanceData(period) {
    try {
        const response = await fetch(`get_balance_data.php?period=${period}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Erro ao buscar dados');
        }
        
        return data;
    } catch (error) {
        console.error('Erro ao buscar dados:', error);
        
        // Mostrar mensagem de erro ao usuário
        showErrorMessage('Erro ao carregar dados do gráfico. Tente novamente.');
        
        // Retornar dados vazios para não quebrar o gráfico
        return {
            labels: [],
            balanceData: [],
            incomeData: [],
            expenseData: [],
            startBalance: 0,
            currentBalance: 0
        };
    }
}

// Mostrar mensagem de erro
function showErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-warning alert-dismissible fade show';
    errorDiv.innerHTML = `
        <i class="bi bi-exclamation-triangle me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.card-modern .card-body');
    container.insertBefore(errorDiv, container.firstChild);
    
    setTimeout(() => errorDiv.remove(), 5000);
}

// Criar/Atualizar gráfico
async function updateBalanceChart(period = 'month') {
    currentPeriod = period;
    
    // Mostrar loading
    showLoading(true);
    
    try {
        // Buscar dados do backend
        const data = await fetchBalanceData(period);
        
        // Verificar se tem dados
        if (!data.labels || data.labels.length === 0) {
            showEmptyState();
            return;
        }
        
        // Atualizar stats
        updateStats(data);
        
        // Criar/atualizar gráfico
        createChart(data);
        
        // Atualizar botões ativos
        updateActiveButton(period);
        
    } catch (error) {
        console.error('Erro ao atualizar gráfico:', error);
        showErrorMessage('Erro ao atualizar gráfico.');
    } finally {
        showLoading(false);
    }
}

// Mostrar/ocultar loading
function showLoading(show) {
    const canvas = document.getElementById('balanceChart');
    if (show) {
        canvas.style.opacity = '0.3';
    } else {
        canvas.style.opacity = '1';
    }
}

// Atualizar estatísticas
function updateStats(data) {
    const startBalance = data.startBalance || 0;
    const currentBalance = data.currentBalance || 0;
    const variation = currentBalance - startBalance;
    const growthPercent = startBalance !== 0 ? ((variation / Math.abs(startBalance)) * 100) : 0;
    
    // Formatar valores
    const formatMoney = (value) => 'R$ ' + value.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    document.getElementById('balance_start').textContent = formatMoney(startBalance);
    document.getElementById('balance_current').textContent = formatMoney(currentBalance);
    document.getElementById('balance_variation').textContent = (variation >= 0 ? '+' : '') + formatMoney(variation);
    document.getElementById('balance_growth').textContent = (growthPercent >= 0 ? '+' : '') + growthPercent.toFixed(2) + '%';
    
    // Atualizar cores
    const variationElement = document.getElementById('balance_variation');
    const growthElement = document.getElementById('balance_growth');
    
    if (variation >= 0) {
        variationElement.className = 'mb-0 text-success';
        growthElement.className = 'mb-0 text-success';
    } else {
        variationElement.className = 'mb-0 text-danger';
        growthElement.className = 'mb-0 text-danger';
    }
}

// Criar gráfico
function createChart(data) {
    // Destruir gráfico existente
    if (balanceChart) {
        balanceChart.destroy();
    }
    
    const ctx = document.getElementById('balanceChart').getContext('2d');
    
    balanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Saldo',
                    data: data.balanceData,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#764ba2',
                    pointHoverBorderColor: '#fff'
                },
                {
                    label: 'Receitas',
                    data: data.incomeData,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4,
                    pointRadius: 2,
                    pointHoverRadius: 4
                },
                {
                    label: 'Despesas',
                    data: data.expenseData,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4,
                    pointRadius: 2,
                    pointHoverRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: 'white',
                        padding: 15,
                        font: {
                            size: 12
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        title: function(context) {
                            return 'Data: ' + context[0].label;
                        },
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += 'R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                            return label;
                        },
                        footer: function(context) {
                            const index = context[0].dataIndex;
                            if (index > 0) {
                                const current = context[0].parsed.y;
                                const previous = context[0].chart.data.datasets[0].data[index - 1];
                                const diff = current - previous;
                                const diffText = (diff >= 0 ? '↑ +' : '↓ ') + 'R$ ' + Math.abs(diff).toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                                return 'Variação: ' + diffText;
                            }
                            return '';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        color: 'white',
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        }
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)',
                        drawBorder: false
                    }
                },
                x: {
                    ticks: {
                        color: 'white',
                        maxRotation: 45,
                        minRotation: 0
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)',
                        drawBorder: false
                    }
                }
            }
        }
    });
}

// Mostrar estado vazio
function showEmptyState() {
    const container = document.querySelector('#balanceChart').parentElement;
    container.innerHTML = `
        <div class="text-center py-5">
            <i class="bi bi-graph-up text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            <h5 class="text-muted mt-3">Nenhum dado disponível</h5>
            <p class="text-muted">Adicione transações para ver o gráfico de evolução do saldo</p>
            <button class="btn btn-purple btn-modern" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                <i class="bi bi-plus-lg me-2"></i>Adicionar Transação
            </button>
        </div>
    `;
    
    // Limpar stats
    document.getElementById('balance_start').textContent = 'R$ 0,00';
    document.getElementById('balance_current').textContent = 'R$ 0,00';
    document.getElementById('balance_variation').textContent = 'R$ 0,00';
    document.getElementById('balance_growth').textContent = '0%';
}

// Atualizar botão ativo
function updateActiveButton(period) {
    document.querySelectorAll('button[id^="btn-"]').forEach(btn => {
        btn.classList.remove('btn-purple', 'active');
        btn.classList.add('btn-outline-purple');
    });
    
    const activeBtn = document.getElementById(`btn-${period}`);
    if (activeBtn) {
        activeBtn.classList.remove('btn-outline-purple');
        activeBtn.classList.add('btn-purple', 'active');
    }
}

// Mudar período
function changeBalancePeriod(period) {
    updateBalanceChart(period);
}

// Inicializar ao carregar página
document.addEventListener('DOMContentLoaded', function() {
    // Aguardar Chart.js carregar
    if (typeof Chart !== 'undefined') {
        updateBalanceChart('month');
    } else {
        setTimeout(() => updateBalanceChart('month'), 500);
    }
});
</script>

<!-- ============================================ -->
<!-- SEÇÃO DE METAS FINANCEIRAS -->
<!-- ============================================ -->
<section class="py-4">
    <div class="container">
        <div class="card-modern">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">
                            <i class="bi bi-bullseye text-warning me-2"></i>
                            Metas Financeiras
                        </h4>
                        <p class="text-muted mb-0">Defina e acompanhe seus objetivos financeiros</p>
                    </div>
                    <button class="btn btn-purple btn-modern" data-bs-toggle="modal" data-bs-target="#addGoalModal">
                        <i class="bi bi-plus-lg me-2"></i>Nova Meta
                    </button>
                </div>

                <?php if (empty($financial_goals)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-bullseye text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h5 class="text-muted mt-3">Nenhuma meta cadastrada</h5>
                        <p class="text-muted">Crie sua primeira meta financeira e comece a economizar!</p>
                        <button class="btn btn-purple btn-modern" data-bs-toggle="modal" data-bs-target="#addGoalModal">
                            <i class="bi bi-plus-lg me-2"></i>Criar Primeira Meta
                        </button>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($financial_goals as $goal):
                            $progresso_percentual = $goal['valor_objetivo'] > 0 ? ($goal['valor_atual'] / $goal['valor_objetivo']) * 100 : 0;
                            $progresso_percentual = min($progresso_percentual, 100);

                            // Calcular dias restantes
                            $hoje = new DateTime();
                            $data_meta = new DateTime($goal['data_objetivo']);
                            $dias_restantes = $hoje->diff($data_meta)->days;
                            $dias_passaram = $data_meta < $hoje;

                            // Definir cor da barra de progresso
                            $cor_progresso = 'bg-success';
                            if ($progresso_percentual < 30) {
                                $cor_progresso = 'bg-danger';
                            } elseif ($progresso_percentual < 70) {
                                $cor_progresso = 'bg-warning';
                            }

                            $status_badge = match($goal['status']) {
                                'concluida' => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Concluída</span>',
                                'ativa' => '<span class="badge bg-primary"><i class="bi bi-play-circle me-1"></i>Ativa</span>',
                                'pausada' => '<span class="badge bg-warning"><i class="bi bi-pause-circle me-1"></i>Pausada</span>',
                                'cancelada' => '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Cancelada</span>',
                                default => ''
                            };

                            $prioridade_badge = match($goal['prioridade']) {
                                'alta' => '<span class="badge badge-modern" style="background: #dc3545;"><i class="bi bi-arrow-up-circle me-1"></i>Alta</span>',
                                'media' => '<span class="badge badge-modern" style="background: #ffc107;"><i class="bi bi-dash-circle me-1"></i>Média</span>',
                                'baixa' => '<span class="badge badge-modern" style="background: #6c757d;"><i class="bi bi-arrow-down-circle me-1"></i>Baixa</span>',
                                default => ''
                            };
                        ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); border: 1px solid <?= htmlspecialchars($goal['cor']) ?>; border-radius: 15px; backdrop-filter: blur(10px);">
                                    <div class="card-body p-4">
                                        <!-- Cabeçalho da Meta -->
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                <div style="width: 50px; height: 50px; background: <?= htmlspecialchars($goal['cor']) ?>; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                                    <i class="bi bi-<?= htmlspecialchars($goal['icone']) ?> text-white"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h5 class="mb-0 fw-bold"><?= htmlspecialchars($goal['nome_meta']) ?></h5>
                                                    <small class="text-muted"><?= htmlspecialchars($goal['categoria']) ?></small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Badges -->
                                        <div class="mb-3 d-flex gap-2 flex-wrap">
                                            <?= $status_badge ?>
                                            <?= $prioridade_badge ?>
                                        </div>

                                        <!-- Descrição (se houver) -->
                                        <?php if (!empty($goal['descricao'])): ?>
                                            <p class="text-muted small mb-3"><?= htmlspecialchars($goal['descricao']) ?></p>
                                        <?php endif; ?>

                                        <!-- Valores -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Progresso</span>
                                                <span class="fw-bold"><?= number_format($progresso_percentual, 1) ?>%</span>
                                            </div>
                                            <div class="progress mb-2" style="height: 12px; border-radius: 10px;">
                                                <div class="progress-bar <?= $cor_progresso ?>"
                                                     role="progressbar"
                                                     style="width: <?= $progresso_percentual ?>%; transition: width 0.6s ease;"
                                                     aria-valuenow="<?= $progresso_percentual ?>"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <small class="text-muted">Atual</small>
                                                    <div class="fw-bold text-success">R$ <?= number_format($goal['valor_atual'], 2, ',', '.') ?></div>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted">Objetivo</small>
                                                    <div class="fw-bold text-info">R$ <?= number_format($goal['valor_objetivo'], 2, ',', '.') ?></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Data Meta -->
                                        <div class="alert alert-info py-2 px-3 mb-3" style="background: rgba(23, 162, 184, 0.1); border: 1px solid rgba(23, 162, 184, 0.3); border-radius: 10px;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <i class="bi bi-calendar-event me-2"></i>
                                                    <small>
                                                        <?php if ($dias_passaram): ?>
                                                            <span class="text-danger">Prazo expirado há <?= $dias_restantes ?> dias</span>
                                                        <?php else: ?>
                                                            <span>Faltam <?= $dias_restantes ?> dias</span>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                                <small class="text-muted"><?= date('d/m/Y', strtotime($goal['data_objetivo'])) ?></small>
                                            </div>
                                        </div>

                                        <!-- Botões de Ação -->
                                        <div class="d-flex gap-2 justify-content-between">
                                            <button class="btn btn-success btn-sm flex-fill"
                                                    onclick="openContributionModal(<?= $goal['id'] ?>, '<?= htmlspecialchars($goal['nome_meta'], ENT_QUOTES) ?>')"
                                                    <?= $goal['status'] == 'concluida' ? 'disabled' : '' ?>>
                                                <i class="bi bi-plus-circle me-1"></i>Adicionar
                                            </button>
                                            <button class="btn btn-outline-info btn-sm"
                                                    onclick='editGoal(<?= json_encode($goal) ?>)'>
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm"
                                                    onclick="deleteGoal(<?= $goal['id'] ?>, '<?= htmlspecialchars($goal['nome_meta'], ENT_QUOTES) ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
/* Animação suave nos stats */
#balance_start, #balance_current, #balance_variation, #balance_growth {
    transition: all 0.5s ease;
}

/* Efeito hover nos botões de período */
button[id^="btn-"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

/* Loading state para o gráfico */
#balanceChart {
    transition: opacity 0.3s ease;
}

/* Responsividade */
@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
        width: 100%;
    }
    
    button[id^="btn-"] {
        width: 100%;
    }
}
</style>
        <section class="py-4">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card-modern">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4 class="fw-bold mb-0">Visão Geral Mensal</h4>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-purple btn-sm" onclick="changeChartType('bar')">
                                            <i class="bi bi-bar-chart-fill"></i>
                                        </button>
                                        <button class="btn btn-outline-purple btn-sm" onclick="changeChartType('doughnut')">
                                            <i class="bi bi-pie-chart-fill"></i>
                                        </button>
                                    </div>
                                </div>
                                <div style="height: 300px; position: relative;">
                                    <canvas id="monthlyChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card-modern">
                            <div class="card-body">
                                <h5 class="fw-bold mb-4">Gastos por Categoria</h5>
                                <?php if (empty($expenses_by_category)): ?>
                                    <div class="text-center text-muted">
                                        <i class="bi bi-pie-chart fs-1 mb-3"></i>
                                        <p>Nenhuma despesa ainda</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($expenses_by_category as $expense): ?>
                                        <div class="expense-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <?php
                                                    $icon = match ($expense['categoria']) {
                                                        'Alimentação' => 'bi-cup-hot-fill',
                                                        'Transporte' => 'bi-car-front-fill',
                                                        'Moradia' => 'bi-house-fill',
                                                        'Lazer' => 'bi-controller',
                                                        'Saúde' => 'bi-heart-pulse-fill',
                                                        'Educação' => 'bi-book-fill',
                                                        default => 'bi-circle-fill'
                                                    };

                                                    $color = match ($expense['categoria']) {
                                                        'Alimentação' => 'text-warning',
                                                        'Transporte' => 'text-info',
                                                        'Moradia' => 'text-primary',
                                                        'Lazer' => 'text-success',
                                                        'Saúde' => 'text-danger',
                                                        'Educação' => 'text-purple',
                                                        default => 'text-muted'
                                                    };
                                                    ?>
                                                    <i class="bi <?= $icon ?> <?= $color ?> fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1"><?= htmlspecialchars($expense['categoria']) ?></h6>
                                                    <small class="text-muted">Últimos 30 dias</small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <strong>R$ <?= number_format($expense['total'], 2, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Seção: Notificações de Despesas Próximas -->
        <?php if (!empty($notificacoes_pendentes)): ?>
        <section class="py-4">
            <div class="container">
                <div class="alert" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.15), rgba(255, 193, 7, 0.15)); border: 1px solid rgba(220, 53, 69, 0.3); border-radius: 15px;">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-bell-fill text-warning me-2" style="font-size: 1.5rem;"></i>
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                            Você tem <?= count($notificacoes_pendentes) ?> despesa(s) pendente(s)!
                        </h5>
                    </div>
                    <div class="row g-3">
                        <?php foreach ($notificacoes_pendentes as $notif): ?>
                            <div class="col-md-6">
                                <div class="p-3 rounded" style="background: rgba(26, 26, 46, 0.6); border-left: 4px solid <?= $notif['tipo'] === 'atrasada' ? '#dc3545' : '#ffc107' ?>;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($notif['despesa']) ?></h6>
                                            <p class="mb-0 small <?= $notif['tipo'] === 'atrasada' ? 'text-danger' : 'text-warning' ?>">
                                                <i class="bi bi-<?= $notif['tipo'] === 'atrasada' ? 'exclamation-circle' : 'clock' ?> me-1"></i>
                                                <?php if ($notif['tipo'] === 'atrasada'): ?>
                                                    Atrasada há <?= $notif['dias'] ?> dia(s)
                                                <?php else: ?>
                                                    Vence em <?= $notif['dias'] == 0 ? 'hoje' : $notif['dias'] . ' dia(s)' ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <h5 class="mb-0 text-white">R$ <?= number_format($notif['valor'], 2, ',', '.') ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Seção: Despesas Fixas -->
        <section class="py-4">
            <div class="container">
                <div class="card-modern">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="fw-bold mb-1">
                                    <i class="bi bi-calendar-check text-info me-2"></i>
                                    Despesas Fixas Recorrentes
                                </h4>
                                <p class="text-muted mb-0">Gerencie suas contas mensais e receba notificações de vencimento</p>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="text-end me-3">
                                    <small class="text-muted d-block">Total do Mês</small>
                                    <h5 class="mb-0 text-danger">R$ <?= number_format($total_despesas_mes, 2, ',', '.') ?></h5>
                                </div>
                                <button class="btn btn-purple btn-modern" data-bs-toggle="modal" data-bs-target="#addFixedExpenseModal">
                                    <i class="bi bi-plus-lg me-2"></i>Nova Despesa
                                </button>
                            </div>
                        </div>

                        <?php if (empty($fixed_expenses)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                                <h5 class="text-muted mt-3">Nenhuma despesa fixa cadastrada</h5>
                                <p class="text-muted">Adicione suas contas recorrentes e nunca mais esqueça um vencimento!</p>
                                <button class="btn btn-purple btn-modern" data-bs-toggle="modal" data-bs-target="#addFixedExpenseModal">
                                    <i class="bi bi-plus-lg me-2"></i>Adicionar Primeira Despesa
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($fixed_expenses as $expense):
                                    $status_calc = $expense['status_calculado'];
                                    $badge_color = match($status_calc) {
                                        'paga' => 'success',
                                        'atrasada' => 'danger',
                                        'proxima' => 'warning',
                                        default => 'info'
                                    };
                                    $badge_text = match($status_calc) {
                                        'paga' => 'Paga',
                                        'atrasada' => 'Atrasada',
                                        'proxima' => 'Próxima',
                                        default => 'Ativa'
                                    };
                                ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); border: 1px solid <?= htmlspecialchars($expense['cor']) ?>; border-radius: 15px;">
                                            <div class="card-body p-3">
                                                <!-- Cabeçalho -->
                                                <div class="d-flex align-items-start mb-3">
                                                    <div style="width: 45px; height: 45px; background: <?= htmlspecialchars($expense['cor']) ?>; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="bi bi-<?= htmlspecialchars($expense['icone']) ?> text-white fs-5"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($expense['nome_despesa']) ?></h6>
                                                        <small class="text-muted"><?= htmlspecialchars($expense['categoria']) ?></small>
                                                    </div>
                                                    <span class="badge bg-<?= $badge_color ?>"><?= $badge_text ?></span>
                                                </div>

                                                <!-- Descrição -->
                                                <?php if (!empty($expense['descricao'])): ?>
                                                    <p class="text-muted small mb-3"><?= htmlspecialchars($expense['descricao']) ?></p>
                                                <?php endif; ?>

                                                <!-- Valor e Vencimento -->
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted">Valor</span>
                                                        <h5 class="mb-0 text-white">R$ <?= number_format($expense['valor'], 2, ',', '.') ?></h5>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-muted">Vencimento</span>
                                                        <span class="fw-bold">Dia <?= $expense['dia_vencimento'] ?></span>
                                                    </div>
                                                    <?php if ($expense['dias_ate_vencimento'] !== null): ?>
                                                        <div class="mt-2 p-2 rounded text-center" style="background: rgba(<?= $status_calc === 'atrasada' ? '220, 53, 69' : '255, 193, 7' ?>, 0.2);">
                                                            <small class="<?= $status_calc === 'atrasada' ? 'text-danger' : 'text-warning' ?>">
                                                                <?php if ($status_calc === 'atrasada'): ?>
                                                                    <i class="bi bi-exclamation-triangle me-1"></i>Atrasada há <?= $expense['dias_ate_vencimento'] ?> dia(s)
                                                                <?php else: ?>
                                                                    <i class="bi bi-clock me-1"></i>Vence em <?= $expense['dias_ate_vencimento'] == 0 ? 'hoje!' : $expense['dias_ate_vencimento'] . ' dia(s)' ?>
                                                                <?php endif; ?>
                                                            </small>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Botões de Ação -->
                                                <div class="d-flex gap-2">
                                                    <?php if ($status_calc !== 'paga'): ?>
                                                        <form method="POST" class="flex-grow-1" onsubmit="return confirm('Marcar como paga?');">
                                                            <input type="hidden" name="expense_id" value="<?= $expense['id'] ?>">
                                                            <button type="submit" name="mark_as_paid" class="btn btn-success btn-sm w-100">
                                                                <i class="bi bi-check-circle me-1"></i>Pagar
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <button class="btn btn-outline-info btn-sm" onclick='editFixedExpense(<?= json_encode($expense) ?>)'>
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteFixedExpense(<?= $expense['id'] ?>, '<?= htmlspecialchars($expense['nome_despesa'], ENT_QUOTES) ?>')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-4">
            <div class="container">
                <div class="card-modern">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold mb-0" id="transr">Transações Recentes</h4>
                        </div>

                        <?php if (empty($recent_transactions)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                                <h4 class="text-muted mt-3">Nenhuma transação ainda</h4>
                                <p class="text-muted mb-4">Comece adicionando sua primeira receita ou despesa</p>
                                <button class="btn btn-purple btn-modern" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                                    <i class="bi bi-plus-lg me-2"></i>Primeira Transação
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Data</th>
                                            <th>Descrição</th>
                                            <th>Categoria</th>
                                            <th>Tipo</th>
                                            <th class="text-end">Valor</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_transactions as $transaction): ?>
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong><?= date('d/m/Y', strtotime($transaction['data_transacao'])) ?></strong>
                                                        <br><small class="text-muted"><?= date('H:i', strtotime($transaction['created_at'])) ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($transaction['descricao']) ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge badge-modern badge-purple"><?= htmlspecialchars($transaction['categoria']) ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $transaction['tipo'] == 'receita' ? 'bg-success' : 'bg-danger' ?>">
                                                        <i class="bi <?= $transaction['tipo'] == 'receita' ? 'bi-arrow-up' : 'bi-arrow-down' ?> me-1"></i>
                                                        <?= ucfirst($transaction['tipo']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="<?= $transaction['tipo'] == 'receita' ? 'text-success' : 'text-danger' ?>">
                                                        <?= $transaction['tipo'] == 'receita' ? '+' : '-' ?>R$ <?= number_format($transaction['valor'], 2, ',', '.') ?>
                                                    </strong>
                                                </td>
                                                <td>
                                                    <div class="action-btn-group">
                                                        <button class="btn btn-sm btn-outline-info" 
                                                            onclick='editTransaction(<?= json_encode($transaction) ?>)'>
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteTransaction(<?= $transaction['id'] ?>)">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-4 mb-5">
            <div class="container">
                <h4 class="fw-bold mb-4">Ações Rápidas</h4>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card-modern text-center" style="cursor: pointer;" onclick="openTransactionModal('receita')">
                            <div class="card-body p-4">
                                <i class="bi bi-plus-circle-fill text-success fs-1 mb-3"></i>
                                <h6 class="fw-bold">Nova Receita</h6>
                                <p class="text-muted small mb-0">Adicionar entrada de dinheiro</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-modern text-center" style="cursor: pointer;" onclick="openTransactionModal('despesa')">
                            <div class="card-body p-4">
                                <i class="bi bi-dash-circle-fill text-danger fs-1 mb-3"></i>
                                <h6 class="fw-bold">Nova Despesa</h6>
                                <p class="text-muted small mb-0">Registrar um gasto</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-modern text-center" style="cursor: pointer;" onclick="window.location.href='investments.php'">
                            <div class="card-body p-4">
                                <i class="bi bi-graph-up text-info fs-1 mb-3"></i>
                                <h6 class="fw-bold">Ver Investimentos</h6>
                                <p class="text-muted small mb-0">Acompanhar carteira</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-modern text-center" style="cursor: pointer;" onclick="window.location.href='conversabot.php'">
                            <div class="card-body p-4">
                                <i class="bi bi-robot text-purple fs-1 mb-3"></i>
                                <h6 class="fw-bold">Consultoria IA</h6>
                                <p class="text-muted small mb-0">Tirar dúvidas financeiras</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal para Nova Transação -->
    <div class="modal fade" id="addTransactionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="background: var(--dark-bg); color: white; border: 1px solid var(--primary-purple);">
                <div class="modal-header" style="border-bottom: 1px solid var(--primary-purple);">
                    <h5 class="modal-title">Nova Transação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="dashboard.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipo" id="receita" value="receita" required>
                                <label class="btn btn-outline-success" for="receita">
                                    <i class="bi bi-arrow-up me-2"></i>Receita
                                </label>

                                <input type="radio" class="btn-check" name="tipo" id="despesa" value="despesa" required>
                                <label class="btn btn-outline-danger" for="despesa">
                                    <i class="bi bi-arrow-down me-2"></i>Despesa
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Categoria</label>
                            <select class="form-control-modern" name="categoria" required>
                                <option value="">Selecione o tipo primeiro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" class="form-control-modern" name="descricao" placeholder="Ex: Almoço no restaurante" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor (R$)</label>
                            <input type="number" step="0.01" class="form-control-modern" name="valor" placeholder="0,00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data</label>
                            <input type="date" class="form-control-modern" name="data" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--primary-purple);">
                        <button type="button" class="btn btn-outline-purple btn-modern" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="add_transaction" class="btn btn-purple btn-modern">
                            <i class="bi bi-check-lg me-2"></i>Salvar Transação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Transação -->
    <div class="modal fade" id="editTransactionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="background: var(--dark-bg); color: white; border: 1px solid var(--primary-purple);">
                <div class="modal-header" style="border-bottom: 1px solid var(--primary-purple);">
                    <h5 class="modal-title">Editar Transação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="dashboard.php">
                    <input type="hidden" name="transaction_id" id="edit_transaction_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipo" id="edit_receita" value="receita" required>
                                <label class="btn btn-outline-success" for="edit_receita">
                                    <i class="bi bi-arrow-up me-2"></i>Receita
                                </label>

                                <input type="radio" class="btn-check" name="tipo" id="edit_despesa" value="despesa" required>
                                <label class="btn btn-outline-danger" for="edit_despesa">
                                    <i class="bi bi-arrow-down me-2"></i>Despesa
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Categoria</label>
                            <select class="form-control-modern" name="categoria" id="edit_categoria" required>
                                <option value="">Selecione o tipo primeiro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" class="form-control-modern" name="descricao" id="edit_descricao" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor (R$)</label>
                            <input type="number" step="0.01" class="form-control-modern" name="valor" id="edit_valor" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data</label>
                            <input type="date" class="form-control-modern" name="data" id="edit_data" required>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--primary-purple);">
                        <button type="button" class="btn btn-outline-purple btn-modern" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="edit_transaction" class="btn btn-purple btn-modern">
                            <i class="bi bi-check-lg me-2"></i>Atualizar Transação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAIS PARA DESPESAS FIXAS -->
    <!-- ============================================ -->

    <!-- Modal para Nova Despesa Fixa -->
    <div class="modal fade" id="addFixedExpenseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: var(--dark-bg); color: white; border: 1px solid var(--primary-purple);">
                <div class="modal-header" style="border-bottom: 1px solid var(--primary-purple);">
                    <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Nova Despesa Fixa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="dashboard.php">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Nome da Despesa *</label>
                                    <input type="text" class="form-control-modern" name="nome_despesa"
                                           placeholder="Ex: Aluguel, Condomínio, Netflix, Seguro..." required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Dia do Vencimento *</label>
                                    <input type="number" class="form-control-modern" name="dia_vencimento"
                                           min="1" max="31" placeholder="1-31" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição (opcional)</label>
                            <input type="text" class="form-control-modern" name="descricao"
                                   placeholder="Adicione detalhes sobre esta despesa...">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Categoria *</label>
                                    <select class="form-control-modern" name="categoria" required>
                                        <option value="">Selecione</option>
                                        <option value="Moradia">🏠 Moradia</option>
                                        <option value="Transporte">🚗 Transporte</option>
                                        <option value="Alimentação">🍽️ Alimentação</option>
                                        <option value="Saúde">❤️ Saúde</option>
                                        <option value="Educação">📚 Educação</option>
                                        <option value="Lazer">🎮 Lazer</option>
                                        <option value="Serviços">🔧 Serviços</option>
                                        <option value="Assinaturas">📺 Assinaturas</option>
                                        <option value="Seguros">🛡️ Seguros</option>
                                        <option value="Outros">📌 Outros</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Valor (R$) *</label>
                                    <input type="number" step="0.01" class="form-control-modern" name="valor"
                                           placeholder="0,00" min="0.01" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ícone *</label>
                                    <select class="form-control-modern" name="icone" required>
                                        <option value="receipt">🧾 Recibo (padrão)</option>
                                        <option value="house">🏠 Casa</option>
                                        <option value="car-front">🚗 Carro</option>
                                        <option value="lightning-charge">⚡ Energia</option>
                                        <option value="droplet">💧 Água</option>
                                        <option value="wifi">📶 Internet</option>
                                        <option value="phone">📱 Telefone</option>
                                        <option value="tv">📺 TV/Streaming</option>
                                        <option value="credit-card">💳 Cartão</option>
                                        <option value="shield-check">🛡️ Seguro</option>
                                        <option value="hospital">🏥 Plano de Saúde</option>
                                        <option value="bank">🏦 Banco</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Cor *</label>
                                    <select class="form-control-modern" name="cor" required>
                                        <option value="#dc3545">🔴 Vermelho (padrão)</option>
                                        <option value="#667eea">🔵 Roxo</option>
                                        <option value="#28a745">🟢 Verde</option>
                                        <option value="#17a2b8">🔵 Azul</option>
                                        <option value="#ffc107">🟡 Amarelo</option>
                                        <option value="#6c757d">⚫ Cinza</option>
                                        <option value="#f7931a">🟠 Laranja</option>
                                        <option value="#e83e8c">🩷 Rosa</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Avisar com quantos dias de antecedência? *</label>
                            <select class="form-control-modern" name="dias_aviso" required>
                                <option value="1">1 dia antes</option>
                                <option value="2">2 dias antes</option>
                                <option value="3" selected>3 dias antes</option>
                                <option value="5">5 dias antes</option>
                                <option value="7">7 dias antes</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--primary-purple);">
                        <button type="button" class="btn btn-outline-purple btn-modern" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="add_fixed_expense" class="btn btn-purple btn-modern">
                            <i class="bi bi-check-lg me-2"></i>Adicionar Despesa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Despesa Fixa -->
    <div class="modal fade" id="editFixedExpenseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: var(--dark-bg); color: white; border: 1px solid var(--primary-purple);">
                <div class="modal-header" style="border-bottom: 1px solid var(--primary-purple);">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Despesa Fixa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="dashboard.php">
                    <input type="hidden" name="expense_id" id="edit_expense_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Nome da Despesa *</label>
                                    <input type="text" class="form-control-modern" name="nome_despesa" id="edit_expense_nome" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Dia do Vencimento *</label>
                                    <input type="number" class="form-control-modern" name="dia_vencimento"
                                           id="edit_expense_dia" min="1" max="31" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" class="form-control-modern" name="descricao" id="edit_expense_descricao">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Categoria *</label>
                                    <select class="form-control-modern" name="categoria" id="edit_expense_categoria" required>
                                        <option value="Moradia">🏠 Moradia</option>
                                        <option value="Transporte">🚗 Transporte</option>
                                        <option value="Alimentação">🍽️ Alimentação</option>
                                        <option value="Saúde">❤️ Saúde</option>
                                        <option value="Educação">📚 Educação</option>
                                        <option value="Lazer">🎮 Lazer</option>
                                        <option value="Serviços">🔧 Serviços</option>
                                        <option value="Assinaturas">📺 Assinaturas</option>
                                        <option value="Seguros">🛡️ Seguros</option>
                                        <option value="Outros">📌 Outros</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Valor (R$) *</label>
                                    <input type="number" step="0.01" class="form-control-modern" name="valor"
                                           id="edit_expense_valor" min="0.01" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Ícone *</label>
                                    <select class="form-control-modern" name="icone" id="edit_expense_icone" required>
                                        <option value="receipt">🧾 Recibo</option>
                                        <option value="house">🏠 Casa</option>
                                        <option value="car-front">🚗 Carro</option>
                                        <option value="lightning-charge">⚡ Energia</option>
                                        <option value="droplet">💧 Água</option>
                                        <option value="wifi">📶 Internet</option>
                                        <option value="phone">📱 Telefone</option>
                                        <option value="tv">📺 TV/Streaming</option>
                                        <option value="credit-card">💳 Cartão</option>
                                        <option value="shield-check">🛡️ Seguro</option>
                                        <option value="hospital">🏥 Plano de Saúde</option>
                                        <option value="bank">🏦 Banco</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Cor *</label>
                                    <select class="form-control-modern" name="cor" id="edit_expense_cor" required>
                                        <option value="#dc3545">🔴 Vermelho</option>
                                        <option value="#667eea">🔵 Roxo</option>
                                        <option value="#28a745">🟢 Verde</option>
                                        <option value="#17a2b8">🔵 Azul</option>
                                        <option value="#ffc107">🟡 Amarelo</option>
                                        <option value="#6c757d">⚫ Cinza</option>
                                        <option value="#f7931a">🟠 Laranja</option>
                                        <option value="#e83e8c">🩷 Rosa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Status *</label>
                                    <select class="form-control-modern" name="status" id="edit_expense_status" required>
                                        <option value="ativa">Ativa</option>
                                        <option value="paga">Paga</option>
                                        <option value="atrasada">Atrasada</option>
                                        <option value="inativa">Inativa</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Avisar com quantos dias de antecedência? *</label>
                            <select class="form-control-modern" name="dias_aviso" id="edit_expense_dias_aviso" required>
                                <option value="1">1 dia antes</option>
                                <option value="2">2 dias antes</option>
                                <option value="3">3 dias antes</option>
                                <option value="5">5 dias antes</option>
                                <option value="7">7 dias antes</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--primary-purple);">
                        <button type="button" class="btn btn-outline-purple btn-modern" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="edit_fixed_expense" class="btn btn-purple btn-modern">
                            <i class="bi bi-check-lg me-2"></i>Atualizar Despesa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAIS PARA METAS FINANCEIRAS -->
    <!-- ============================================ -->

    <!-- Modal para Nova Meta -->
    <div class="modal fade" id="addGoalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: var(--dark-bg); color: white; border: 1px solid var(--primary-purple);">
                <div class="modal-header" style="border-bottom: 1px solid var(--primary-purple);">
                    <h5 class="modal-title"><i class="bi bi-bullseye me-2"></i>Nova Meta Financeira</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="dashboard.php">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Nome da Meta *</label>
                                    <input type="text" class="form-control-modern" name="nome_meta"
                                           placeholder="Ex: Viagem para Europa, Comprar Carro, Reserva de Emergência..." required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Prioridade</label>
                                    <select class="form-control-modern" name="prioridade" required>
                                        <option value="media" selected>Média</option>
                                        <option value="alta">Alta</option>
                                        <option value="baixa">Baixa</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição (opcional)</label>
                            <textarea class="form-control-modern" name="descricao" rows="2"
                                      placeholder="Descreva brevemente sua meta..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Categoria *</label>
                                    <select class="form-control-modern" name="categoria_meta" required>
                                        <option value="">Selecione</option>
                                        <option value="Reserva de Emergência">🛡️ Reserva de Emergência</option>
                                        <option value="Viagem">✈️ Viagem</option>
                                        <option value="Aposentadoria">👴 Aposentadoria</option>
                                        <option value="Compra">🛒 Compra</option>
                                        <option value="Educação">📚 Educação</option>
                                        <option value="Investimento">📈 Investimento</option>
                                        <option value="Outro">📌 Outro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Valor Objetivo (R$) *</label>
                                    <input type="number" step="0.01" class="form-control-modern" name="valor_objetivo"
                                           placeholder="0,00" min="0.01" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Data de Início *</label>
                                    <input type="date" class="form-control-modern" name="data_inicio"
                                           value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Data Objetivo *</label>
                                    <input type="date" class="form-control-modern" name="data_objetivo"
                                           min="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ícone *</label>
                                    <select class="form-control-modern" name="icone" id="icone_select" required>
                                        <option value="bullseye">🎯 Alvo (padrão)</option>
                                        <option value="airplane">✈️ Avião</option>
                                        <option value="car-front">🚗 Carro</option>
                                        <option value="house">🏠 Casa</option>
                                        <option value="piggy-bank">🐷 Cofre</option>
                                        <option value="wallet2">💼 Carteira</option>
                                        <option value="gift">🎁 Presente</option>
                                        <option value="heart">❤️ Coração</option>
                                        <option value="star">⭐ Estrela</option>
                                        <option value="trophy">🏆 Troféu</option>
                                        <option value="cash-coin">💰 Dinheiro</option>
                                        <option value="graph-up-arrow">📈 Investimento</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Cor *</label>
                                    <select class="form-control-modern" name="cor" id="cor_select" required>
                                        <option value="#667eea">🔵 Roxo (padrão)</option>
                                        <option value="#28a745">🟢 Verde</option>
                                        <option value="#17a2b8">🔵 Azul</option>
                                        <option value="#ffc107">🟡 Amarelo</option>
                                        <option value="#dc3545">🔴 Vermelho</option>
                                        <option value="#6c757d">⚫ Cinza</option>
                                        <option value="#f7931a">🟠 Laranja</option>
                                        <option value="#e83e8c">🩷 Rosa</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--primary-purple);">
                        <button type="button" class="btn btn-outline-purple btn-modern" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="add_goal" class="btn btn-purple btn-modern">
                            <i class="bi bi-check-lg me-2"></i>Criar Meta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Meta -->
    <div class="modal fade" id="editGoalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: var(--dark-bg); color: white; border: 1px solid var(--primary-purple);">
                <div class="modal-header" style="border-bottom: 1px solid var(--primary-purple);">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Meta Financeira</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="dashboard.php">
                    <input type="hidden" name="goal_id" id="edit_goal_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Nome da Meta *</label>
                                    <input type="text" class="form-control-modern" name="nome_meta" id="edit_goal_nome" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Prioridade</label>
                                    <select class="form-control-modern" name="prioridade" id="edit_goal_prioridade" required>
                                        <option value="baixa">Baixa</option>
                                        <option value="media">Média</option>
                                        <option value="alta">Alta</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control-modern" name="descricao" id="edit_goal_descricao" rows="2"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Categoria *</label>
                                    <select class="form-control-modern" name="categoria_meta" id="edit_goal_categoria" required>
                                        <option value="Reserva de Emergência">🛡️ Reserva de Emergência</option>
                                        <option value="Viagem">✈️ Viagem</option>
                                        <option value="Aposentadoria">👴 Aposentadoria</option>
                                        <option value="Compra">🛒 Compra</option>
                                        <option value="Educação">📚 Educação</option>
                                        <option value="Investimento">📈 Investimento</option>
                                        <option value="Outro">📌 Outro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Valor Objetivo (R$) *</label>
                                    <input type="number" step="0.01" class="form-control-modern" name="valor_objetivo"
                                           id="edit_goal_valor" min="0.01" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Data Objetivo *</label>
                                    <input type="date" class="form-control-modern" name="data_objetivo"
                                           id="edit_goal_data" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status *</label>
                                    <select class="form-control-modern" name="status" id="edit_goal_status" required>
                                        <option value="ativa">Ativa</option>
                                        <option value="pausada">Pausada</option>
                                        <option value="concluida">Concluída</option>
                                        <option value="cancelada">Cancelada</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ícone *</label>
                                    <select class="form-control-modern" name="icone" id="edit_goal_icone" required>
                                        <option value="bullseye">🎯 Alvo</option>
                                        <option value="airplane">✈️ Avião</option>
                                        <option value="car-front">🚗 Carro</option>
                                        <option value="house">🏠 Casa</option>
                                        <option value="piggy-bank">🐷 Cofre</option>
                                        <option value="wallet2">💼 Carteira</option>
                                        <option value="gift">🎁 Presente</option>
                                        <option value="heart">❤️ Coração</option>
                                        <option value="star">⭐ Estrela</option>
                                        <option value="trophy">🏆 Troféu</option>
                                        <option value="cash-coin">💰 Dinheiro</option>
                                        <option value="graph-up-arrow">📈 Investimento</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Cor *</label>
                                    <select class="form-control-modern" name="cor" id="edit_goal_cor" required>
                                        <option value="#667eea">🔵 Roxo</option>
                                        <option value="#28a745">🟢 Verde</option>
                                        <option value="#17a2b8">🔵 Azul</option>
                                        <option value="#ffc107">🟡 Amarelo</option>
                                        <option value="#dc3545">🔴 Vermelho</option>
                                        <option value="#6c757d">⚫ Cinza</option>
                                        <option value="#f7931a">🟠 Laranja</option>
                                        <option value="#e83e8c">🩷 Rosa</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--primary-purple);">
                        <button type="button" class="btn btn-outline-purple btn-modern" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="edit_goal" class="btn btn-purple btn-modern">
                            <i class="bi bi-check-lg me-2"></i>Atualizar Meta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Adicionar Contribuição -->
    <div class="modal fade" id="addContributionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="background: var(--dark-bg); color: white; border: 1px solid var(--primary-purple);">
                <div class="modal-header" style="border-bottom: 1px solid var(--primary-purple);">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Adicionar Valor à Meta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="dashboard.php">
                    <input type="hidden" name="goal_id" id="contribution_goal_id">
                    <div class="modal-body">
                        <div class="alert alert-info" style="background: rgba(23, 162, 184, 0.1); border: 1px solid rgba(23, 162, 184, 0.3);">
                            <i class="bi bi-info-circle me-2"></i>
                            <span id="contribution_goal_name"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipo de Operação *</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipo_contribuicao" id="tipo_deposito" value="deposito" checked>
                                <label class="btn btn-outline-success" for="tipo_deposito">
                                    <i class="bi bi-plus-circle me-1"></i>Depósito
                                </label>
                                <input type="radio" class="btn-check" name="tipo_contribuicao" id="tipo_retirada" value="retirada">
                                <label class="btn btn-outline-danger" for="tipo_retirada">
                                    <i class="bi bi-dash-circle me-1"></i>Retirada
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Valor (R$) *</label>
                            <input type="number" step="0.01" class="form-control-modern" name="valor_contribuicao"
                                   placeholder="0,00" min="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição (opcional)</label>
                            <input type="text" class="form-control-modern" name="descricao_contribuicao"
                                   placeholder="Ex: Salário do mês, Bônus de final de ano...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Data *</label>
                            <input type="date" class="form-control-modern" name="data_contribuicao"
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--primary-purple);">
                        <button type="button" class="btn btn-outline-purple btn-modern" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="add_contribution" class="btn btn-success btn-modern">
                            <i class="bi bi-check-lg me-2"></i>Adicionar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let monthlyChart;

            // Definir categorias separadas por tipo
            const categoriasReceita = [
                { value: "Salário", text: "💼 Salário" },
                { value: "13º Salário", text: "🎁 13º Salário" },
                { value: "Aluguel de Imóveis", text: "🏡 Aluguel de Imóveis" },
                { value: "Freelance", text: "💻 Freelance" },
                { value: "Investimentos", text: "📈 Investimentos" },
                { value: "Outras Receitas", text: "💰 Outras Receitas" }
            ];

            const categoriasDespesa = [
                { value: "Alimentação", text: "🍽️ Alimentação" },
                { value: "Transporte", text: "🚗 Transporte" },
                { value: "Moradia", text: "🏠 Moradia" },
                { value: "Lazer", text: "🎮 Lazer" },
                { value: "Saúde", text: "❤️ Saúde" },
                { value: "Educação", text: "📚 Educação" },
                { value: "Contas", text: "🧾 Contas (água, luz, internet)" },
                { value: "Compras", text: "🛍️ Compras" },
                { value: "Outras Despesas", text: "📦 Outras Despesas" }
            ];

            // Função para atualizar as opções de categoria
            function atualizarCategorias(modalId = 'addTransactionModal') {
                const modal = document.getElementById(modalId);
                const tipoSelecionado = modal.querySelector('input[name="tipo"]:checked');
                const selectCategoria = modal.querySelector('select[name="categoria"]');

                if (!selectCategoria) return;

                if (!tipoSelecionado) {
                    selectCategoria.innerHTML = '<option value="">Selecione o tipo primeiro</option>';
                    return;
                }

                // Limpar opções existentes
                selectCategoria.innerHTML = '<option value="">Selecione a categoria</option>';

                // Adicionar opções baseadas no tipo selecionado
                const categorias = tipoSelecionado.value === 'receita' ? categoriasReceita : categoriasDespesa;

                categorias.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.value;
                    option.textContent = cat.text;
                    option.className = 'opcoes';
                    selectCategoria.appendChild(option);
                });
            }

            // Adicionar event listeners aos radio buttons (modal de adicionar)
            const radiosTipo = document.querySelectorAll('#addTransactionModal input[name="tipo"]');
            radiosTipo.forEach(radio => {
                radio.addEventListener('change', () => atualizarCategorias('addTransactionModal'));
            });

            // Adicionar event listeners aos radio buttons (modal de editar)
            const radiosTipoEdit = document.querySelectorAll('#editTransactionModal input[name="tipo"]');
            radiosTipoEdit.forEach(radio => {
                radio.addEventListener('change', () => atualizarCategorias('editTransactionModal'));
            });

            // Função para abrir modal com tipo pré-selecionado
            window.openTransactionModal = function(tipo) {
                const modal = new bootstrap.Modal(document.getElementById('addTransactionModal'));
                modal.show();
                setTimeout(() => {
                    document.getElementById(tipo).checked = true;
                    atualizarCategorias('addTransactionModal');
                }, 200);
            };

            // Função para editar transação
            window.editTransaction = function(transaction) {
                document.getElementById('edit_transaction_id').value = transaction.id;
                
                // Selecionar tipo
                if (transaction.tipo === 'receita') {
                    document.getElementById('edit_receita').checked = true;
                } else {
                    document.getElementById('edit_despesa').checked = true;
                }
                
                // Atualizar categorias e selecionar categoria
                atualizarCategorias('editTransactionModal');
                setTimeout(() => {
                    document.getElementById('edit_categoria').value = transaction.categoria;
                }, 100);
                
                document.getElementById('edit_descricao').value = transaction.descricao;
                document.getElementById('edit_valor').value = transaction.valor;
                document.getElementById('edit_data').value = transaction.data_transacao;
                
                const modal = new bootstrap.Modal(document.getElementById('editTransactionModal'));
                modal.show();
            };


            // Dados para o gráfico
            const chartData = {
                labels: ['Receitas', 'Despesas'],
                datasets: [{
                    data: [<?= $totals['total_receitas'] ?>, <?= $totals['total_despesas'] ?>],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 2
                }]
            };

            // Inicializar gráfico
            function initChart() {
                const ctx = document.getElementById('monthlyChart').getContext('2d');
                monthlyChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: 'white',
                                    padding: 20,
                                    font: {
                                        size: 14
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Alterar tipo do gráfico
            window.changeChartType = function(type) {
                monthlyChart.destroy();
                const ctx = document.getElementById('monthlyChart').getContext('2d');
                monthlyChart = new Chart(ctx, {
                    type: type,
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: 'white',
                                    padding: 20,
                                    font: {
                                        size: 14
                                    }
                                }
                            }
                        },
                        scales: type === 'bar' ? {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: 'white'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: 'white'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            }
                        } : {}
                    }
                });
            };

            // Excluir transação
            window.deleteTransaction = function(id) {
                if (confirm('Tem certeza que deseja excluir esta transação?')) {
                    window.location.href = `dashboard.php?delete_transaction=${id}`;
                }
            };

            // Inicializar gráfico
            initChart();

            // ============================================
            // FUNÇÕES PARA METAS FINANCEIRAS
            // ============================================

            // Abrir modal de contribuição
            window.openContributionModal = function(goalId, goalName) {
                document.getElementById('contribution_goal_id').value = goalId;
                document.getElementById('contribution_goal_name').textContent = 'Meta: ' + goalName;

                const modal = new bootstrap.Modal(document.getElementById('addContributionModal'));
                modal.show();
            };

            // Editar meta
            window.editGoal = function(goal) {
                document.getElementById('edit_goal_id').value = goal.id;
                document.getElementById('edit_goal_nome').value = goal.nome_meta;
                document.getElementById('edit_goal_descricao').value = goal.descricao || '';
                document.getElementById('edit_goal_categoria').value = goal.categoria;
                document.getElementById('edit_goal_valor').value = goal.valor_objetivo;
                document.getElementById('edit_goal_data').value = goal.data_objetivo;
                document.getElementById('edit_goal_prioridade').value = goal.prioridade;
                document.getElementById('edit_goal_status').value = goal.status;
                document.getElementById('edit_goal_icone').value = goal.icone;
                document.getElementById('edit_goal_cor').value = goal.cor;

                const modal = new bootstrap.Modal(document.getElementById('editGoalModal'));
                modal.show();
            };

            // Excluir meta
            window.deleteGoal = function(goalId, goalName) {
                if (confirm(`Tem certeza que deseja excluir a meta "${goalName}"?\n\nEsta ação não pode ser desfeita e todas as contribuições relacionadas serão perdidas.`)) {
                    window.location.href = `dashboard.php?delete_goal=${goalId}`;
                }
            };

            // ============================================
            // FUNÇÕES PARA DESPESAS FIXAS
            // ============================================

            // Editar despesa fixa
            window.editFixedExpense = function(expense) {
                document.getElementById('edit_expense_id').value = expense.id;
                document.getElementById('edit_expense_nome').value = expense.nome_despesa;
                document.getElementById('edit_expense_descricao').value = expense.descricao || '';
                document.getElementById('edit_expense_valor').value = expense.valor;
                document.getElementById('edit_expense_categoria').value = expense.categoria;
                document.getElementById('edit_expense_dia').value = expense.dia_vencimento;
                document.getElementById('edit_expense_dias_aviso').value = expense.dias_aviso;
                document.getElementById('edit_expense_icone').value = expense.icone;
                document.getElementById('edit_expense_cor').value = expense.cor;
                document.getElementById('edit_expense_status').value = expense.status;

                const modal = new bootstrap.Modal(document.getElementById('editFixedExpenseModal'));
                modal.show();
            };

            // Excluir despesa fixa
            window.deleteFixedExpense = function(expenseId, expenseName) {
                if (confirm(`Tem certeza que deseja excluir a despesa fixa "${expenseName}"?\n\nEsta ação não pode ser desfeita.`)) {
                    window.location.href = `dashboard.php?delete_expense=${expenseId}`;
                }
            };

            // Animações dos cards
            const cards = document.querySelectorAll('.stat-card-modern, .card-modern');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease';

                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>

    <footer class="footer-modern">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand mb-3">FinançasJá</div>
                    <p>
                        Sua plataforma completa para gestão financeira pessoal com inteligência artificial.
                    </p>
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
                        <li class="mb-2"><a href="plans1.php" class="footer-link">Planos Premium</a></li>
                    </ul>
                </div>

                <div class="col-lg-2">
                    <h6 class="fw-bold mb-3">Suporte</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="support1.php" class="footer-link">Central de Ajuda</a></li>
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