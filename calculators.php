<?php
session_start();
require_once 'config/database.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadoras Financeiras - FinançasJá | Planeje seus Investimentos</title>
    <meta name="description" content="Use nossas calculadoras financeiras gratuitas para planejar investimentos, calcular juros compostos, ROI e simular economia. Ferramentas profissionais para sua liberdade financeira.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .calculator-card {
            border-radius: 20px;
            padding: 30px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            border: 2px solid var(--primary-purple);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .calculator-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(130, 10, 209, 0.2);
        }
        
        .result-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 15px;
            padding: 25px;
            color: white;
            margin-top: 20px;
        }
        
        .result-value {
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .chart-container-calc {
            position: relative;
            height: 300px;
            margin-top: 20px;
        }
        
        .input-group-modern {
            margin-bottom: 20px;
        }
        
        .input-group-modern label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: white;
        }
        
        .input-group-modern input,
        .input-group-modern select {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: 2px solid rgba(130, 10, 209, 0.3);
            background: rgba(255, 255, 255, 0.05);
            color: white;
            transition: all 0.3s ease;
        }
        
        .input-group-modern input:focus,
        .input-group-modern select:focus {
            border-color: var(--primary-purple);
            outline: none;
            box-shadow: 0 0 0 3px rgba(130, 10, 209, 0.1);
        }
        
        .calc-icon {
            font-size: 3rem;
            color: var(--primary-purple);
            margin-bottom: 15px;
        }
        
        .toggle-theme {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }
        
        .info-tip {
            background: rgba(130, 10, 209, 0.1);
            border-left: 4px solid var(--primary-purple);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .comparison-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .comparison-box {
                grid-template-columns: 1fr;
            }
        }
        
        .strategy-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            border: 2px solid rgba(130, 10, 209, 0.2);
        }
        
        .nav-pills-modern .nav-link {
            color: white;
            border-radius: 10px;
            padding: 12px 25px;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .nav-pills-modern .nav-link.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        
        .nav-pills-modern .nav-link:hover:not(.active) {
            background: rgba(130, 10, 209, 0.2);
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
                        <a class="nav-link active" href="calculators.php">
                            <i class="bi bi-calculator me-1"></i>Calculadoras
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
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($user_name) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Perfil</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Configurações</a></li>
                            <li><hr class="dropdown-divider"></li>
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
            <div class="hero-content text-center">
                <h1 class="display-4 fw-bold mb-3">Calculadoras Financeiras</h1>
                <p class="lead mb-4">Ferramentas profissionais para planejar seu futuro financeiro</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="#juros-compostos" class="btn btn-purple btn-modern">
                        <i class="bi bi-calculator me-2"></i>Começar Agora
                    </a>
                    <a href="investments.php" class="btn btn-outline-purple btn-modern">
                        <i class="bi bi-graph-up me-2"></i>Ver Investimentos
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="container mt-5 mb-5">
        <!-- Navegação entre Calculadoras -->
        <ul class="nav nav-pills nav-pills-modern justify-content-center mb-5" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="pill" href="#juros-compostos">
                    <i class="bi bi-graph-up-arrow me-2"></i>Juros Compostos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="pill" href="#crescimento">
                    <i class="bi bi-calendar-range me-2"></i>Crescimento
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="pill" href="#roi">
                    <i class="bi bi-bar-chart-line me-2"></i>ROI
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="pill" href="#conversor">
                    <i class="bi bi-arrow-left-right me-2"></i>Conversor
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="pill" href="#meta">
                    <i class="bi bi-bullseye me-2"></i>Meta
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="pill" href="#comparacao">
                    <i class="bi bi-columns-gap me-2"></i>Comparar
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Calculadora de Juros Compostos -->
            <div class="tab-pane fade show active" id="juros-compostos">
                <div class="calculator-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-graph-up-arrow calc-icon"></i>
                        <h2 class="fw-bold">Calculadora de Juros Compostos</h2>
                        <p class="text-muted">Descubra o poder dos juros compostos ao longo do tempo</p>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group-modern">
                                <label>Valor Inicial (R$)</label>
                                <input type="number" id="comp_principal" value="10000" min="0" step="100">
                            </div>
                            <div class="input-group-modern">
                                <label>Aporte Mensal (R$)</label>
                                <input type="number" id="comp_monthly" value="500" min="0" step="50">
                            </div>
                            <div class="input-group-modern">
                                <label>Taxa de Juros Anual (%)</label>
                                <input type="number" id="comp_rate" value="10" min="0" max="100" step="0.1">
                                <small class="text-muted">Exemplo: Tesouro Selic ~13%, CDI ~13%, Poupança ~8%</small>
                            </div>
                            <div class="input-group-modern">
                                <label>Período (Meses)</label>
                                <input type="number" id="comp_months" value="120" min="1" max="600">
                                <small class="text-muted">Digite o período em meses (ex: 120 = 10 anos)</small>
                            </div>
                            <button class="btn btn-purple btn-modern w-100 mt-3" onclick="calculateCompound()">
                                <i class="bi bi-calculator me-2"></i>Calcular
                            </button>
                        </div>

                        <div class="col-lg-6">
                            <div class="result-box">
                                <div class="mb-3">
                                    <small>Total Investido</small>
                                    <h3 id="comp_invested">R$ 0,00</h3>
                                </div>
                                <div class="mb-3">
                                    <small>Total de Juros</small>
                                    <h3 id="comp_interest" class="text-warning">R$ 0,00</h3>
                                </div>
                                <hr style="border-color: rgba(255,255,255,0.3);">
                                <div>
                                    <small>Montante Final</small>
                                    <div class="result-value" id="comp_total">R$ 0,00</div>
                                </div>
                            </div>
                            
                            <div class="info-tip">
                                <i class="bi bi-lightbulb me-2"></i>
                                <strong>Dica:</strong> Quanto mais cedo você começar a investir, maior será o efeito dos juros compostos!
                            </div>
                        </div>
                    </div>

                    <div class="chart-container-calc mt-4">
                        <canvas id="compoundChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Calculadora de Crescimento -->
            <div class="tab-pane fade" id="crescimento">
                <div class="calculator-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-calendar-range calc-icon"></i>
                        <h2 class="fw-bold">Simulador de Crescimento</h2>
                        <p class="text-muted">Visualize como seu investimento cresce mês a mês</p>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group-modern">
                                <label>Investimento Inicial (R$)</label>
                                <input type="number" id="growth_initial" value="5000" min="0" step="100">
                            </div>
                            <div class="input-group-modern">
                                <label>Taxa Mensal (%)</label>
                                <input type="number" id="growth_rate" value="0.8" min="0" max="10" step="0.1">
                                <small class="text-muted">Taxa de retorno esperada por mês</small>
                            </div>
                            <div class="input-group-modern">
                                <label>Período (Meses)</label>
                                <input type="number" id="growth_period" value="36" min="1" max="600">
                            </div>
                            <button class="btn btn-purple btn-modern w-100 mt-3" onclick="calculateGrowth()">
                                <i class="bi bi-graph-up me-2"></i>Simular Crescimento
                            </button>
                        </div>

                        <div class="col-lg-6">
                            <div class="result-box">
                                <div class="mb-3">
                                    <small>Valor Inicial</small>
                                    <h3 id="growth_start">R$ 0,00</h3>
                                </div>
                                <div class="mb-3">
                                    <small>Crescimento Total</small>
                                    <h3 id="growth_gain" class="text-success">R$ 0,00</h3>
                                </div>
                                <hr style="border-color: rgba(255,255,255,0.3);">
                                <div>
                                    <small>Valor Final</small>
                                    <div class="result-value" id="growth_final">R$ 0,00</div>
                                </div>
                                <div class="mt-3">
                                    <small>Retorno Percentual</small>
                                    <h4 id="growth_percent" class="text-warning">0%</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chart-container-calc mt-4">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Calculadora de ROI -->
            <div class="tab-pane fade" id="roi">
                <div class="calculator-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-bar-chart-line calc-icon"></i>
                        <h2 class="fw-bold">Calculadora de ROI</h2>
                        <p class="text-muted">Calcule o Retorno sobre Investimento (Return on Investment)</p>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group-modern">
                                <label>Investimento Inicial (R$)</label>
                                <input type="number" id="roi_investment" value="10000" min="0" step="100">
                            </div>
                            <div class="input-group-modern">
                                <label>Valor Final (R$)</label>
                                <input type="number" id="roi_final" value="15000" min="0" step="100">
                            </div>
                            <div class="input-group-modern">
                                <label>Período (Meses)</label>
                                <input type="number" id="roi_period" value="12" min="1" max="600">
                            </div>
                            <button class="btn btn-purple btn-modern w-100 mt-3" onclick="calculateROI()">
                                <i class="bi bi-calculator-fill me-2"></i>Calcular ROI
                            </button>
                        </div>

                        <div class="col-lg-6">
                            <div class="result-box">
                                <div class="mb-3">
                                    <small>Lucro/Prejuízo</small>
                                    <h3 id="roi_profit">R$ 0,00</h3>
                                </div>
                                <div class="mb-3">
                                    <small>ROI (%)</small>
                                    <div class="result-value" id="roi_percent">0%</div>
                                </div>
                                <hr style="border-color: rgba(255,255,255,0.3);">
                                <div>
                                    <small>ROI Anualizado</small>
                                    <h3 id="roi_annual" class="text-warning">0%</h3>
                                </div>
                            </div>

                            <div class="info-tip">
                                <i class="bi bi-info-circle me-2"></i>
                                ROI = (Valor Final - Investimento Inicial) / Investimento Inicial × 100
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conversor de Moedas -->
            <div class="tab-pane fade" id="conversor">
                <div class="calculator-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-arrow-left-right calc-icon"></i>
                        <h2 class="fw-bold">Conversor de Moedas</h2>
                        <p class="text-muted">Converta entre diferentes moedas em tempo real</p>
                    </div>

                    <div class="row">
                        <div class="col-lg-8 mx-auto">
                            <div class="input-group-modern">
                                <label>Valor</label>
                                <input type="number" id="conv_amount" value="1000" min="0" step="0.01">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="input-group-modern">
                                        <label>De</label>
                                        <select id="conv_from" class="form-select-modern">
                                            <option value="BRL">BRL - Real Brasileiro</option>
                                            <option value="USD">USD - Dólar Americano</option>
                                            <option value="EUR">EUR - Euro</option>
                                            <option value="BTC">BTC - Bitcoin</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-center justify-content-center">
                                    <button class="btn btn-outline-purple btn-modern" onclick="swapCurrencies()">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </button>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group-modern">
                                        <label>Para</label>
                                        <select id="conv_to" class="form-select-modern">
                                            <option value="BRL">BRL - Real Brasileiro</option>
                                            <option value="USD" selected>USD - Dólar Americano</option>
                                            <option value="EUR">EUR - Euro</option>
                                            <option value="BTC">BTC - Bitcoin</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-purple btn-modern w-100 mt-3" onclick="convertCurrency()">
                                <i class="bi bi-currency-exchange me-2"></i>Converter
                            </button>

                            <div class="result-box mt-4">
                                <div class="text-center">
                                    <small>Resultado</small>
                                    <div class="result-value" id="conv_result">R$ 0,00</div>
                                    <small class="mt-2 d-block" id="conv_rate">Taxa: 1 = 1</small>
                                </div>
                            </div>

                            <div class="info-tip">
                                <i class="bi bi-clock-history me-2"></i>
                                Cotações atualizadas automaticamente
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Planejador de Meta de Economia -->
            <div class="tab-pane fade" id="meta">
                <div class="calculator-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-bullseye calc-icon"></i>
                        <h2 class="fw-bold">Planejador de Meta</h2>
                        <p class="text-muted">Descubra quanto economizar para atingir sua meta</p>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group-modern">
                                <label>Meta de Economia (R$)</label>
                                <input type="number" id="goal_target" value="50000" min="0" step="1000">
                            </div>
                            <div class="input-group-modern">
                                <label>Prazo (Meses)</label>
                                <input type="number" id="goal_months" value="60" min="1" max="600">
                            </div>
                            <div class="input-group-modern">
                                <label>Valor Inicial Já Economizado (R$)</label>
                                <input type="number" id="goal_initial" value="0" min="0" step="100">
                            </div>
                            <div class="input-group-modern">
                                <label>Taxa de Rendimento Mensal (%)</label>
                                <input type="number" id="goal_rate" value="0.5" min="0" max="5" step="0.1">
                            </div>
                            <button class="btn btn-purple btn-modern w-100 mt-3" onclick="calculateGoal()">
                                <i class="bi bi-calculator me-2"></i>Calcular Meta
                            </button>
                        </div>

                        <div class="col-lg-6">
                            <div class="result-box">
                                <div class="mb-3">
                                    <small>Aporte Mensal Necessário</small>
                                    <div class="result-value" id="goal_monthly">R$ 0,00</div>
                                </div>
                                <hr style="border-color: rgba(255,255,255,0.3);">
                                <div class="mb-3">
                                    <small>Total a Investir</small>
                                    <h3 id="goal_total_invest">R$ 0,00</h3>
                                </div>
                                <div>
                                    <small>Rendimento Estimado</small>
                                    <h3 id="goal_earnings" class="text-success">R$ 0,00</h3>
                                </div>
                            </div>

                            <div class="info-tip">
                                <i class="bi bi-trophy me-2"></i>
                                <strong>Dica:</strong> Revise sua meta regularmente e ajuste conforme necessário!
                            </div>
                        </div>
                    </div>

                    <div class="chart-container-calc mt-4">
                        <canvas id="goalChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Comparador de Estratégias -->
            <div class="tab-pane fade" id="comparacao">
                <div class="calculator-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-columns-gap calc-icon"></i>
                        <h2 class="fw-bold">Comparador de Estratégias</h2>
                        <p class="text-muted">Compare duas estratégias de investimento lado a lado</p>
                    </div>

                    <div class="comparison-box">
                        <!-- Estratégia 1 -->
                        <div class="strategy-card">
                            <h5 class="text-center mb-3 text-info">
                                <i class="bi bi-1-circle me-2"></i>Estratégia 1
                            </h5>
                            <div class="input-group-modern">
                                <label>Valor Inicial (R$)</label>
                                <input type="number" id="comp1_initial" value="10000" min="0" step="100">
                            </div>
                            <div class="input-group-modern">
                                <label>Aporte Mensal (R$)</label>
                                <input type="number" id="comp1_monthly" value="500" min="0" step="50">
                            </div>
                            <div class="input-group-modern">
                                <label>Taxa Anual (%)</label>
                                <input type="number" id="comp1_rate" value="10" min="0" max="100" step="0.1">
                            </div>
                        </div>

                        <!-- Estratégia 2 -->
                        <div class="strategy-card">
                            <h5 class="text-center mb-3 text-warning">
                                <i class="bi bi-2-circle me-2"></i>Estratégia 2
                            </h5>
                            <div class="input-group-modern">
                                <label>Valor Inicial (R$)</label>
                                <input type="number" id="comp2_initial" value="10000" min="0" step="100">
                            </div>
                            <div class="input-group-modern">
                                <label>Aporte Mensal (R$)</label>
                                <input type="number" id="comp2_monthly" value="1000" min="0" step="50">
                            </div>
                            <div class="input-group-modern">
                                <label>Taxa Anual (%)</label>
                                <input type="number" id="comp2_rate" value="8" min="0" max="100" step="0.1">
                            </div>
                        </div>
                    </div>

                    <div class="input-group-modern text-center mt-4">
                        <label>Período de Comparação (Meses)</label>
                        <input type="number" id="comp_period" value="120" min="1" max="600" class="w-50 mx-auto">
                    </div>

                    <button class="btn btn-purple btn-modern w-100 mt-3" onclick="compareStrategies()">
                        <i class="bi bi-graph-up-arrow me-2"></i>Comparar Estratégias
                    </button>

                    <div class="comparison-box mt-4">
                        <div class="result-box">
                            <small>Estratégia 1 - Resultado Final</small>
                            <div class="result-value" id="comp1_result">R$ 0,00</div>
                        </div>
                        <div class="result-box">
                            <small>Estratégia 2 - Resultado Final</small>
                            <div class="result-value" id="comp2_result">R$ 0,00</div>
                        </div>
                    </div>

                    <div class="chart-container-calc mt-4">
                        <canvas id="comparisonChart"></canvas>
                    </div>

                    <div class="info-tip mt-4">
                        <i class="bi bi-bar-chart me-2"></i>
                        <strong>Análise:</strong> <span id="comparison_analysis">Faça a comparação para ver a análise</span>
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
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold mb-3">Recursos</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="quiz.php" class="footer-link">Quiz Financeiro</a></li>
                        <li class="mb-2"><a href="education.php" class="footer-link">Academia</a></li>
                        <li class="mb-2"><a href="plans.php" class="footer-link">Planos</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold mb-3">Suporte</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="support1.php" class="footer-link">Central de Ajuda</a></li>
                        <li class="mb-2"><a href="about.php" class="footer-link">Sobre Nós</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="footer-link">Termos de Uso</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Privacidade</a></li>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variáveis globais para os gráficos
        let compoundChartInstance, growthChartInstance, goalChartInstance, comparisonChartInstance;

        // Função auxiliar para formatar moeda
        function formatCurrency(value) {
            return 'R$ ' + value.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Calculadora de Juros Compostos
        function calculateCompound() {
            const principal = parseFloat(document.getElementById('comp_principal').value) || 0;
            const monthly = parseFloat(document.getElementById('comp_monthly').value) || 0;
            const annualRate = parseFloat(document.getElementById('comp_rate').value) || 0;
            const months = parseInt(document.getElementById('comp_months').value) || 0;
            
            const monthlyRate = Math.pow(1 + annualRate/100, 1/12) - 1;
            
            let balance = principal;
            const totalInvested = principal + (monthly * months);
            const balanceData = [principal];
            const labels = ['Início'];
            
            for (let i = 1; i <= months; i++) {
                balance = balance * (1 + monthlyRate) + monthly;
                if (i % Math.max(1, Math.floor(months / 20)) === 0 || i === months) {
                    balanceData.push(balance);
                    labels.push(`${i}m`);
                }
            }
            
            const totalInterest = balance - totalInvested;
            
            document.getElementById('comp_invested').textContent = formatCurrency(totalInvested);
            document.getElementById('comp_interest').textContent = formatCurrency(totalInterest);
            document.getElementById('comp_total').textContent = formatCurrency(balance);
            
            updateCompoundChart(labels, balanceData);
        }

        function updateCompoundChart(labels, data) {
            const ctx = document.getElementById('compoundChart').getContext('2d');
            
            if (compoundChartInstance) {
                compoundChartInstance.destroy();
            }
            
            compoundChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Evolução do Patrimônio',
                        data: data,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Valor: ' + formatCurrency(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { 
                                color: 'white',
                                callback: function(value) {
                                    return formatCurrency(value);
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
        }

        // Calculadora de Crescimento
        function calculateGrowth() {
            const initial = parseFloat(document.getElementById('growth_initial').value) || 0;
            const monthlyRate = parseFloat(document.getElementById('growth_rate').value) / 100 || 0;
            const months = parseInt(document.getElementById('growth_period').value) || 0;
            
            const balanceData = [initial];
            const labels = ['Mês 0'];
            let balance = initial;
            
            for (let i = 1; i <= months; i++) {
                balance = balance * (1 + monthlyRate);
                balanceData.push(balance);
                labels.push(`Mês ${i}`);
            }
            
            const totalGain = balance - initial;
            const percentGain = (totalGain / initial) * 100;
            
            document.getElementById('growth_start').textContent = formatCurrency(initial);
            document.getElementById('growth_gain').textContent = formatCurrency(totalGain);
            document.getElementById('growth_final').textContent = formatCurrency(balance);
            document.getElementById('growth_percent').textContent = '+' + percentGain.toFixed(2) + '%';
            
            updateGrowthChart(labels, balanceData);
        }

        function updateGrowthChart(labels, data) {
            const ctx = document.getElementById('growthChart').getContext('2d');
            
            if (growthChartInstance) {
                growthChartInstance.destroy();
            }
            
            growthChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.filter((_, i) => i % Math.max(1, Math.floor(labels.length / 15)) === 0),
                    datasets: [{
                        label: 'Crescimento Mensal',
                        data: data.filter((_, i) => i % Math.max(1, Math.floor(data.length / 15)) === 0),
                        backgroundColor: 'rgba(102, 126, 234, 0.7)',
                        borderColor: '#667eea',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: 'white' } }
                    },
                    scales: {
                        y: {
                            ticks: { 
                                color: 'white',
                                callback: function(value) {
                                    return formatCurrency(value);
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
        }

        // Calculadora de ROI
        function calculateROI() {
            const investment = parseFloat(document.getElementById('roi_investment').value) || 0;
            const finalValue = parseFloat(document.getElementById('roi_final').value) || 0;
            const months = parseInt(document.getElementById('roi_period').value) || 1;
            
            const profit = finalValue - investment;
            const roiPercent = (profit / investment) * 100;
            const annualROI = (roiPercent / months) * 12;
            
            document.getElementById('roi_profit').textContent = formatCurrency(profit);
            document.getElementById('roi_profit').className = profit >= 0 ? 'text-success' : 'text-danger';
            
            document.getElementById('roi_percent').textContent = (roiPercent >= 0 ? '+' : '') + roiPercent.toFixed(2) + '%';
            document.getElementById('roi_annual').textContent = (annualROI >= 0 ? '+' : '') + annualROI.toFixed(2) + '% a.a.';
        }

        // Conversor de Moedas
        let exchangeRates = {};

        async function loadExchangeRates() {
            try {
                const response = await fetch('https://open.er-api.com/v6/latest/BRL');
                const data = await response.json();
                exchangeRates = data.rates;
            } catch (error) {
                console.error('Erro ao carregar taxas:', error);
                exchangeRates = { BRL: 1, USD: 0.20, EUR: 0.18, BTC: 0.000004 };
            }
        }

        function swapCurrencies() {
            const from = document.getElementById('conv_from').value;
            const to = document.getElementById('conv_to').value;
            document.getElementById('conv_from').value = to;
            document.getElementById('conv_to').value = from;
        }

        async function convertCurrency() {
            if (Object.keys(exchangeRates).length === 0) {
                await loadExchangeRates();
            }
            
            const amount = parseFloat(document.getElementById('conv_amount').value) || 0;
            const from = document.getElementById('conv_from').value;
            const to = document.getElementById('conv_to').value;
            
            let result;
            if (from === to) {
                result = amount;
            } else {
                const amountInBRL = from === 'BRL' ? amount : amount / exchangeRates[from];
                result = to === 'BRL' ? amountInBRL : amountInBRL * exchangeRates[to];
            }
            
            const rate = from === 'BRL' ? exchangeRates[to] : 1 / exchangeRates[from];
            
            document.getElementById('conv_result').textContent = result.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 8
            }) + ' ' + to;
            
            document.getElementById('conv_rate').textContent = 
                `Taxa: 1 ${from} = ${rate.toFixed(6)} ${to}`;
        }

        // Planejador de Meta
        function calculateGoal() {
            const target = parseFloat(document.getElementById('goal_target').value) || 0;
            const months = parseInt(document.getElementById('goal_months').value) || 1;
            const initial = parseFloat(document.getElementById('goal_initial').value) || 0;
            const monthlyRate = parseFloat(document.getElementById('goal_rate').value) / 100 || 0;
            
            let monthlyPayment;
            if (monthlyRate === 0) {
                monthlyPayment = (target - initial) / months;
            } else {
                const fv = target - (initial * Math.pow(1 + monthlyRate, months));
                monthlyPayment = fv / (((Math.pow(1 + monthlyRate, months) - 1) / monthlyRate));
            }
            
            const totalInvested = initial + (monthlyPayment * months);
            const earnings = target - totalInvested;
            
            document.getElementById('goal_monthly').textContent = formatCurrency(monthlyPayment);
            document.getElementById('goal_total_invest').textContent = formatCurrency(totalInvested);
            document.getElementById('goal_earnings').textContent = formatCurrency(earnings);
            
            updateGoalChart(months, initial, monthlyPayment, monthlyRate, target);
        }

        function updateGoalChart(months, initial, monthlyPayment, rate, target) {
            const ctx = document.getElementById('goalChart').getContext('2d');
            
            if (goalChartInstance) {
                goalChartInstance.destroy();
            }
            
            let balance = initial;
            const balanceData = [initial];
            const labels = ['Início'];
            
            for (let i = 1; i <= months; i++) {
                balance = balance * (1 + rate) + monthlyPayment;
                if (i % Math.max(1, Math.floor(months / 20)) === 0 || i === months) {
                    balanceData.push(balance);
                    labels.push(`${i}m`);
                }
            }
            
            goalChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Progresso da Meta',
                        data: balanceData,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Meta',
                        data: new Array(labels.length).fill(target),
                        borderColor: '#ffc107',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: 'white' } }
                    },
                    scales: {
                        y: {
                            ticks: { 
                                color: 'white',
                                callback: function(value) {
                                    return formatCurrency(value);
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
        }

        // Comparador de Estratégias
        function compareStrategies() {
            const initial1 = parseFloat(document.getElementById('comp1_initial').value) || 0;
            const monthly1 = parseFloat(document.getElementById('comp1_monthly').value) || 0;
            const rate1 = Math.pow(1 + parseFloat(document.getElementById('comp1_rate').value) / 100, 1/12) - 1;
            
            const initial2 = parseFloat(document.getElementById('comp2_initial').value) || 0;
            const monthly2 = parseFloat(document.getElementById('comp2_monthly').value) || 0;
            const rate2 = Math.pow(1 + parseFloat(document.getElementById('comp2_rate').value) / 100, 1/12) - 1;
            
            const months = parseInt(document.getElementById('comp_period').value) || 0;
            
            let balance1 = initial1;
            let balance2 = initial2;
            const data1 = [initial1];
            const data2 = [initial2];
            const labels = ['Início'];
            
            for (let i = 1; i <= months; i++) {
                balance1 = balance1 * (1 + rate1) + monthly1;
                balance2 = balance2 * (1 + rate2) + monthly2;
                
                if (i % Math.max(1, Math.floor(months / 20)) === 0 || i === months) {
                    data1.push(balance1);
                    data2.push(balance2);
                    labels.push(`${i}m`);
                }
            }
            
            document.getElementById('comp1_result').textContent = formatCurrency(balance1);
            document.getElementById('comp2_result').textContent = formatCurrency(balance2);
            
            const diff = Math.abs(balance1 - balance2);
            const winner = balance1 > balance2 ? 1 : 2;
            const percentDiff = ((diff / Math.min(balance1, balance2)) * 100).toFixed(2);
            
            document.getElementById('comparison_analysis').innerHTML = 
                `A <strong>Estratégia ${winner}</strong> resulta em <strong>${formatCurrency(diff)}</strong> a mais (${percentDiff}% de diferença)`;
            
            updateComparisonChart(labels, data1, data2);
        }

        function updateComparisonChart(labels, data1, data2) {
            const ctx = document.getElementById('comparisonChart').getContext('2d');
            
            if (comparisonChartInstance) {
                comparisonChartInstance.destroy();
            }
            
            comparisonChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Estratégia 1',
                        data: data1,
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Estratégia 2',
                        data: data2,
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: 'white' } }
                    },
                    scales: {
                        y: {
                            ticks: { 
                                color: 'white',
                                callback: function(value) {
                                    return formatCurrency(value);
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
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            calculateCompound();
            calculateGrowth();
            calculateROI();
            calculateGoal();
            loadExchangeRates();
            
            // Auto-calcular quando mudar inputs
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.addEventListener('change', function() {
                    const activeTab = document.querySelector('.tab-pane.active').id;
                    switch(activeTab) {
                        case 'juros-compostos':
                            calculateCompound();
                            break;
                        case 'crescimento':
                            calculateGrowth();
                            break;
                        case 'roi':
                            calculateROI();
                            break;
                        case 'meta':
                            calculateGoal();
                            break;
                    }
                });
            });
        });
    </script>
</body>
</html>