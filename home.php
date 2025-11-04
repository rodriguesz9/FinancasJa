<?php
session_start();
require_once 'config/database.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Buscar dados resumidos do usuário
$stmt = $pdo->prepare("SELECT 
    COALESCE(SUM(CASE WHEN tipo = 'receita' THEN valor END), 0) as total_receitas,
    COALESCE(SUM(CASE WHEN tipo = 'despesa' THEN valor END), 0) as total_despesas,
    COUNT(*) as total_transacoes
    FROM transactions WHERE user_id = ? AND MONTH(data_transacao) = MONTH(CURRENT_DATE())");
$stmt->execute([$user_id]);
$financial_summary = $stmt->fetch();

$saldo_mensal = $financial_summary['total_receitas'] - $financial_summary['total_despesas'];

// Buscar investimentos
$stmt = $pdo->prepare("SELECT COUNT(*) as total_investimentos, 
    COALESCE(SUM(quantidade * preco_compra), 0) as total_investido 
    FROM investments WHERE user_id = ?");
$stmt->execute([$user_id]);
$investment_summary = $stmt->fetch();

// Buscar última transação
$stmt = $pdo->prepare("SELECT categoria, valor, tipo, data_transacao FROM transactions 
    WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$last_transaction = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - FinançasJá</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
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
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configurações</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <div class="container-fluid p-0">
        <!-- Hero Section -->
        <section class="hero-modern">
            <div class="container">
                <div class="hero-content text-center">
                    <div class="row align-items-center">
                        <div class="col-lg-8 mx-auto">
                            <h1 class="display-4 fw-bold mb-4 fade-in-up">
                                Olá, <?= htmlspecialchars(explode(' ', $user_name)[0]) ?>!
                            </h1>
                            <p class="lead mb-4 fade-in-up" style="animation-delay: 0.2s;">
                                Sua vida financeira em um só lugar. Controle, invista e aprenda com inteligência artificial.
                            </p>
                            <div class="d-flex gap-3 justify-content-center fade-in-up" style="animation-delay: 0.4s;">
                                <a href="dashboard.php" class="btn btn-purple btn-modern">
                                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                </a>
                                <a href="conversabot.php" class="btn btn-outline-purple btn-modern">
                                    <i class="bi bi-robot me-2"></i>Assistente IA
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Adicione esta seção APÓS a seção de Stats Cards em home.php -->
<!-- Dicas Financeiras - Slideshow Full Width -->
<section class="py-5" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); overflow: hidden;">
    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-2">
                        <i class="bi bi-lightbulb-fill text-warning me-2"></i>
                        Dicas Financeiras do Dia
                    </h3>
                    <p class="text-muted">Aprenda algo novo todos os dias</p>
                </div>

                <!-- Slideshow Container -->
                <div class="tips-slideshow-container">
                    <!-- Slides -->
                    <div class="tips-slides-wrapper" id="tipsSlider">
                        <!-- Os slides serão inseridos aqui via JavaScript -->
                    </div>

                    <!-- Controles de Navegação -->
                    <button class="tip-nav-btn tip-prev" onclick="changeTip(-1)">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="tip-nav-btn tip-next" onclick="changeTip(1)">
                        <i class="bi bi-chevron-right"></i>
                    </button>

                    <!-- Indicadores (dots) -->
                    <div class="tips-dots" id="tipsDots">
                        <!-- Os dots serão inseridos aqui via JavaScript -->
                    </div>

                    <!-- Controle de Auto-play -->
                    <div class="tips-controls">
                        <button class="btn btn-sm btn-outline-purple" onclick="toggleAutoPlay()" id="playPauseBtn">
                            <i class="bi bi-pause-fill" id="playPauseIcon"></i>
                            <span id="playPauseText">Pausar</span>
                        </button>
                        <span class="badge badge-purple ms-2">
                            <i class="bi bi-clock me-1"></i>
                            <span id="tipTimer">10s</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Container Principal */
.tips-slideshow-container {
    position: relative;
    max-width: 100%;
    margin: 0 auto;
    background: linear-gradient(135deg, #2e0352ff 0%, #5b179eff 100%);
    border-radius: 20px;
    padding: 60px 80px;
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
    overflow: hidden;
}

/* Wrapper dos Slides */
.tips-slides-wrapper {
    position: relative;
    width: 100%;
    min-height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Cada Slide */
.tip-slide {
    position: absolute;
    width: 100%;
    opacity: 0;
    transform: scale(0.9);
    transition: opacity 0.6s ease, transform 0.6s ease;
    text-align: center;
    color: white;
}

.tip-slide.active {
    opacity: 1;
    transform: scale(1);
    position: relative;
}

/* Ícone do Slide */
.tip-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Texto da Dica */
.tip-text {
    font-size: 1.5rem;
    font-weight: 500;
    line-height: 1.6;
    margin-bottom: 1.5rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
}

/* Categoria da Dica */
.tip-category {
    display: inline-block;
    padding: 8px 20px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

/* Botões de Navegação */
.tip-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.5);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    z-index: 10;
}

.tip-nav-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-50%) scale(1.1);
}

.tip-prev {
    left: 20px;
}

.tip-next {
    right: 20px;
}

.tip-nav-btn i {
    font-size: 1.5rem;
}

/* Indicadores (Dots) */
.tips-dots {
    text-align: center;
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
    z-index: 10;
}

.tip-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.tip-dot:hover {
    background: rgba(255, 255, 255, 0.5);
    transform: scale(1.2);
}

.tip-dot.active {
    background: white;
    width: 30px;
    border-radius: 10px;
}

/* Controles */
.tips-controls {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 10;
}

.tips-controls .btn {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    backdrop-filter: blur(10px);
}

.tips-controls .btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: white;
}

.tips-controls .badge {
    background: rgba(255, 255, 255, 0.2) !important;
    backdrop-filter: blur(10px);
}

/* Responsividade */
@media (max-width: 768px) {
    .tips-slideshow-container {
        padding: 40px 60px;
    }
    
    .tip-text {
        font-size: 1.2rem;
    }
    
    .tip-icon {
        font-size: 3rem;
    }
    
    .tip-nav-btn {
        width: 40px;
        height: 40px;
    }
    
    .tip-prev {
        left: 10px;
    }
    
    .tip-next {
        right: 10px;
    }
    
    .tips-controls {
        top: 10px;
        right: 10px;
    }
}

@media (max-width: 576px) {
    .tips-slideshow-container {
        padding: 30px 20px;
        border-radius: 15px;
    }
    
    .tip-text {
        font-size: 1rem;
    }
    
    .tip-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .tip-nav-btn {
        display: none;
    }
    
    .tips-controls {
        position: static;
        justify-content: center;
        margin-top: 1rem;
    }
}
</style>

<script>
// Array de dicas financeiras com categorias
const financialTips = [
    {
        icon: "💰",
        text: "Invista pelo menos 15% da sua renda mensal. Comece pequeno, mas comece hoje!",
        category: "Investimentos"
    },
    {
        icon: "📊",
        text: "A regra dos 50-30-20: 50% necessidades, 30% desejos, 20% poupança/investimentos.",
        category: "Orçamento"
    },
    {
        icon: "🛡️",
        text: "Crie uma reserva de emergência equivalente a 6 meses de despesas antes de investir.",
        category: "Segurança"
    },
    {
        icon: "🎯",
        text: "Diversifique seus investimentos: não coloque todos os ovos na mesma cesta.",
        category: "Estratégia"
    },
    {
        icon: "⚡",
        text: "Juros compostos são a 8ª maravilha do mundo. Quanto antes começar, melhor!",
        category: "Investimentos"
    },
    {
        icon: "⚠️",
        text: "Evite dívidas com juros altos como cartão de crédito e cheque especial.",
        category: "Dívidas"
    },
    {
        icon: "📱",
        text: "Acompanhe seus gastos diariamente. O que não é medido, não é controlado.",
        category: "Controle"
    },
    {
        icon: "📚",
        text: "Invista em você mesmo: educação financeira é o melhor investimento.",
        category: "Educação"
    },
    {
        icon: "🤝",
        text: "Negocie sempre que possível: contas, seguros, assinaturas. Economize sem perder qualidade.",
        category: "Economia"
    },
    {
        icon: "⏰",
        text: "Antes de comprar algo, espere 24 horas. Compras por impulso são inimigas do orçamento.",
        category: "Consumo"
    },
    {
        icon: "🤖",
        text: "Automatize suas economias: programe transferências automáticas para investimentos.",
        category: "Automação"
    },
    {
        icon: "🔍",
        text: "Compare preços e use cashback sempre que possível. Pequenas economias somam muito.",
        category: "Economia"
    },
    {
        icon: "🎯",
        text: "Tenha objetivos financeiros claros: casa própria, aposentadoria, viagem. Visualize seus sonhos!",
        category: "Metas"
    },
    {
        icon: "💵",
        text: "Invista em ativos que geram renda passiva: FIIs, dividendos, aluguéis.",
        category: "Renda Passiva"
    },
    {
        icon: "📋",
        text: "Revise seu orçamento mensalmente. Ajuste categorias conforme necessário.",
        category: "Planejamento"
    }
];

let currentTipIndex = 0;
let autoPlayInterval;
let isPlaying = true;
let countdown = 10;
let countdownInterval;

// Inicializar slideshow
function initTipsSlideshow() {
    const slider = document.getElementById('tipsSlider');
    const dotsContainer = document.getElementById('tipsDots');
    
    // Criar slides
    financialTips.forEach((tip, index) => {
        const slide = document.createElement('div');
        slide.className = 'tip-slide';
        if (index === 0) slide.classList.add('active');
        
        slide.innerHTML = `
            <div class="tip-icon">${tip.icon}</div>
            <p class="tip-text">${tip.text}</p>
            <span class="tip-category">${tip.category}</span>
        `;
        
        slider.appendChild(slide);
        
        // Criar dot
        const dot = document.createElement('span');
        dot.className = 'tip-dot';
        if (index === 0) dot.classList.add('active');
        dot.onclick = () => goToTip(index);
        dotsContainer.appendChild(dot);
    });
    
    // Iniciar auto-play
    startAutoPlay();
}

// Mudar dica
function changeTip(direction) {
    currentTipIndex += direction;
    
    if (currentTipIndex >= financialTips.length) {
        currentTipIndex = 0;
    } else if (currentTipIndex < 0) {
        currentTipIndex = financialTips.length - 1;
    }
    
    showTip(currentTipIndex);
    resetCountdown();
}

// Ir para dica específica
function goToTip(index) {
    currentTipIndex = index;
    showTip(currentTipIndex);
    resetCountdown();
}

// Mostrar dica
function showTip(index) {
    const slides = document.querySelectorAll('.tip-slide');
    const dots = document.querySelectorAll('.tip-dot');
    
    slides.forEach((slide, i) => {
        if (i === index) {
            slide.classList.add('active');
        } else {
            slide.classList.remove('active');
        }
    });
    
    dots.forEach((dot, i) => {
        if (i === index) {
            dot.classList.add('active');
        } else {
            dot.classList.remove('active');
        }
    });
}

// Auto-play
function startAutoPlay() {
    if (autoPlayInterval) clearInterval(autoPlayInterval);
    if (countdownInterval) clearInterval(countdownInterval);
    
    countdown = 10;
    updateTimer();
    
    countdownInterval = setInterval(() => {
        countdown--;
        updateTimer();
        if (countdown <= 0) {
            countdown = 10;
        }
    }, 1000);
    
    autoPlayInterval = setInterval(() => {
        changeTip(1);
    }, 10000);
}

function stopAutoPlay() {
    if (autoPlayInterval) clearInterval(autoPlayInterval);
    if (countdownInterval) clearInterval(countdownInterval);
}

function toggleAutoPlay() {
    isPlaying = !isPlaying;
    const icon = document.getElementById('playPauseIcon');
    const text = document.getElementById('playPauseText');
    
    if (isPlaying) {
        icon.className = 'bi bi-pause-fill';
        text.textContent = 'Pausar';
        startAutoPlay();
    } else {
        icon.className = 'bi bi-play-fill';
        text.textContent = 'Continuar';
        stopAutoPlay();
    }
}

function resetCountdown() {
    if (isPlaying) {
        stopAutoPlay();
        startAutoPlay();
    }
}

function updateTimer() {
    document.getElementById('tipTimer').textContent = countdown + 's';
}

// Suporte para teclado
document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') {
        changeTip(-1);
    } else if (e.key === 'ArrowRight') {
        changeTip(1);
    } else if (e.key === ' ') {
        e.preventDefault();
        toggleAutoPlay();
    }
});

// Suporte para touch (swipe)
let touchStartX = 0;
let touchEndX = 0;

document.addEventListener('DOMContentLoaded', function() {
    initTipsSlideshow();
    
    const slider = document.querySelector('.tips-slideshow-container');
    
    slider.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    slider.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
});

function handleSwipe() {
    if (touchEndX < touchStartX - 50) {
        changeTip(1); // Swipe left
    }
    if (touchEndX > touchStartX + 50) {
        changeTip(-1); // Swipe right
    }
}
</script>
        <!-- Stats Cards -->
        <section class="py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="stat-card-modern success">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Receitas (Mês)</h6>
                                    <h3 class="text-success mb-0">R$ <?= number_format($financial_summary['total_receitas'], 2, ',', '.') ?></h3>
                                </div>
                                <div class="text-success" style="font-size: 2.5rem;">
                                    <i class="bi bi-arrow-up-circle-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card-modern danger">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Despesas (Mês)</h6>
                                    <h3 class="text-danger mb-0">R$ <?= number_format($financial_summary['total_despesas'], 2, ',', '.') ?></h3>
                                </div>
                                <div class="text-danger" style="font-size: 2.5rem;">
                                    <i class="bi bi-arrow-down-circle-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card-modern <?= $saldo_mensal >= 0 ? 'info' : 'warning' ?>">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Saldo (Mês)</h6>
                                    <h3 class="<?= $saldo_mensal >= 0 ? 'text-info' : 'text-warning' ?> mb-0">
                                        R$ <?= number_format($saldo_mensal, 2, ',', '.') ?>
                                    </h3>
                                </div>
                                <div class="<?= $saldo_mensal >= 0 ? 'text-info' : 'text-warning' ?>" style="font-size: 2.5rem;">
                                    <i class="bi bi-wallet-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card-modern">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Investido</h6>
                                    <h3 class="text-purple mb-0">R$ <?= number_format($investment_summary['total_investido'], 2, ',', '.') ?></h3>
                                </div>
                                <div class="text-purple" style="font-size: 2.5rem;">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Features -->
        <section class="py-5">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-gradient">Suas Funcionalidades</h2>
                    <p class="text-muted">Tudo que você precisa para controlar suas finanças</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card-modern h-100" onclick="window.location.href='dashboard.php'" style="cursor: pointer;">
                            <div class="card-body text-center p-4">
                                <div class="text-purple mb-3" style="font-size: 3rem;">
                                    <i class="bi bi-speedometer2"></i>
                                </div>
                                <h5 class="fw-bold mb-3">Gerenciador Financeiro</h5>
                                <p class="text-muted mb-4">Controle completo de receitas e despesas com análises em tempo real.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-purple"><?= $financial_summary['total_transacoes'] ?> transações</span>
                                    <i class="bi bi-arrow-right text-purple"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card-modern h-100" onclick="window.location.href='investments.php'" style="cursor: pointer;">
                            <div class="card-body text-center p-4">
                                <div class="text-success mb-3" style="font-size: 3rem;">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                                <h5 class="fw-bold mb-3">Carteira de Investimentos</h5>
                                <p class="text-muted mb-4">Acompanhe ações, fundos e criptomoedas em tempo real com APIs integradas.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-modern" style="background: var(--success);"><?= $investment_summary['total_investimentos'] ?> ativos</span>
                                    <i class="bi bi-arrow-right text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card-modern h-100" onclick="window.location.href='conversabot.php'" style="cursor: pointer;">
                            <div class="card-body text-center p-4">
                                <div class="text-info mb-3" style="font-size: 3rem;">
                                    <i class="bi bi-robot"></i>
                                </div>
                                <h5 class="fw-bold mb-3">Assistente IA</h5>
                                <p class="text-muted mb-4">Consultoria financeira 24/7 com inteligência artificial avançada.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-modern" style="background: var(--info);">Online</span>
                                    <i class="bi bi-arrow-right text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Academia Financeira -->
        <section class="py-5" style="background: rgba(138, 43, 226, 0.05);">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h2 class="fw-bold mb-4">Academia Financeira</h2>
                        <p class="text-muted mb-4">Aprenda sobre investimentos, planejamento financeiro e muito mais com nossos cursos especializados.</p>
                        <div class="d-flex gap-3">
                            <a href="education.php" class="btn btn-purple btn-modern">
                                <i class="bi bi-mortarboard me-2"></i>Começar Agora
                            </a>
                            <div class="d-flex align-items-center text-muted">
                                <i class="bi bi-star-fill text-warning me-1"></i>
                                <span>4.9/5 avaliação</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="card-modern text-center p-3">
                                    <i class="bi bi-book-fill text-purple fs-2 mb-2"></i>
                                    <h6>Módulo 1</h6>
                                    <small class="text-muted">Fundamentos</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card-modern text-center p-3">
                                    <i class="bi bi-graph-up text-success fs-2 mb-2"></i>
                                    <h6>Módulo 2</h6>
                                    <small class="text-muted">Investimentos</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="py-5">
            <div class="container">
                <h3 class="fw-bold mb-4">Ações Rápidas</h3>
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="dashboard.php" class="card-modern d-block text-decoration-none">
                            <div class="card-body d-flex align-items-center">
                                <i class="bi bi-plus-circle-fill text-success fs-3 me-3"></i>
                                <div>
                                    <h6 class="mb-0">Nova Transação</h6>
                                    <small class="text-muted">Adicionar receita/despesa</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="investments.php" class="card-modern d-block text-decoration-none">
                            <div class="card-body d-flex align-items-center">
                                <i class="bi bi-graph-up-arrow text-info fs-3 me-3"></i>
                                <div>
                                    <h6 class="mb-0">Novo Investimento</h6>
                                    <small class="text-muted">Adicionar à carteira</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="conversabot.php" class="card-modern d-block text-decoration-none">
                            <div class="card-body d-flex align-items-center">
                                <i class="bi bi-chat-dots-fill text-purple fs-3 me-3"></i>
                                <div>
                                    <h6 class="mb-0">Consultar IA</h6>
                                    <small class="text-muted">Tirar dúvidas financeiras</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="education.php" class="card-modern d-block text-decoration-none">
                            <div class="card-body d-flex align-items-center">
                                <i class="bi bi-book-fill text-warning fs-3 me-3"></i>
                                <div>
                                    <h6 class="mb-0">Aprender</h6>
                                    <small class="text-muted">Cursos e materiais</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
<section class="py-5" style="background: rgba(138, 43, 226, 0.05);">
    <div class="container">
        <div class="text-center mb-5">
            <i class="bi bi-question-circle text-purple" style="font-size: 3rem;"></i>
            <h2 class="fw-bold mt-3 mb-2">Perguntas Frequentes</h2>
            <p class="text-muted">Tire suas dúvidas sobre a plataforma</p>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <!-- Pergunta 1 -->
                    <div class="accordion-item mb-3" style="border: 1px solid rgba(138, 43, 226, 0.2); border-radius: 10px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="background: white; color: #333; font-weight: 600;">
                                <i class="bi bi-plus-circle text-purple me-2"></i>
                                Como adicionar uma transação?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="background: white;">
                                <p class="mb-3">Para adicionar uma transação, siga os passos:</p>
                                <ol>
                                    <li><strong>Acesse o Dashboard</strong> no menu principal</li>
                                    <li>Clique no botão <strong>"Nova Transação"</strong> no topo da página</li>
                                    <li>Escolha o <strong>tipo</strong>: Receita (entrada de dinheiro) ou Despesa (saída de dinheiro)</li>
                                    <li>Selecione a <strong>categoria</strong> apropriada (Alimentação, Transporte, etc.)</li>
                                    <li>Preencha a <strong>descrição</strong>, <strong>valor</strong> e <strong>data</strong></li>
                                    <li>Clique em <strong>"Salvar Transação"</strong></li>
                                </ol>
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-lightbulb me-2"></i>
                                    <strong>Dica:</strong> Use descrições claras para facilitar o acompanhamento futuro!
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pergunta 2 -->
                    <div class="accordion-item mb-3" style="border: 1px solid rgba(138, 43, 226, 0.2); border-radius: 10px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="background: white; color: #333; font-weight: 600;">
                                <i class="bi bi-graph-up text-success me-2"></i>
                                Como acompanhar meus investimentos?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="background: white;">
                                <p class="mb-3">A plataforma oferece acompanhamento completo de investimentos:</p>
                                <ul>
                                    <li><strong>Acesse "Investimentos"</strong> no menu</li>
                                    <li>Clique em <strong>"Novo Investimento"</strong></li>
                                    <li>Adicione: tipo (Ação, FII, Cripto), nome do ativo, quantidade, preço de compra e data</li>
                                    <li>O sistema atualiza as <strong>cotações automaticamente</strong></li>
                                    <li>Visualize <strong>gráficos de 7 dias</strong> clicando na seta ao lado de cada ativo</li>
                                    <li>Veja rentabilidade, lucro/prejuízo e valor atual da carteira</li>
                                </ul>
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Importante:</strong> As cotações são informativas. Consulte fontes oficiais para operações reais.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pergunta 3 -->
                    <div class="accordion-item mb-3" style="border: 1px solid rgba(138, 43, 226, 0.2); border-radius: 10px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="background: white; color: #333; font-weight: 600;">
                                <i class="bi bi-robot text-info me-2"></i>
                                Como usar o Assistente IA?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="background: white;">
                                <p class="mb-3">O Assistente IA está disponível 24/7 para ajudar:</p>
                                <ul>
                                    <li>Clique em <strong>"Assistente IA"</strong> no menu</li>
                                    <li>Digite sua pergunta sobre finanças na caixa de texto</li>
                                    <li>A IA responde instantaneamente com informações personalizadas</li>
                                </ul>
                                <p class="mb-3"><strong>Exemplos de perguntas:</strong></p>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge bg-purple">Como fazer um orçamento?</span>
                                    <span class="badge bg-success">O que é Tesouro Direto?</span>
                                    <span class="badge bg-info">Como investir com pouco dinheiro?</span>
                                    <span class="badge bg-warning">Devo pagar dívidas ou investir?</span>
                                </div>
                                <div class="alert alert-success mb-0">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <strong>Benefício:</strong> Respostas imediatas sem precisar agendar consultorias!
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pergunta 4 -->
                    <div class="accordion-item mb-3" style="border: 1px solid rgba(138, 43, 226, 0.2); border-radius: 10px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" style="background: white; color: #333; font-weight: 600;">
                                <i class="bi bi-wallet2 text-warning me-2"></i>
                                Como criar um orçamento mensal?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="background: white;">
                                <p class="mb-3">Use o recurso de Planejamento de Orçamento:</p>
                                <ol>
                                    <li>No <strong>Dashboard</strong>, encontre a seção "Planejamento de Orçamento"</li>
                                    <li>Clique em <strong>"Nova Meta"</strong></li>
                                    <li>Escolha a <strong>categoria</strong> (Alimentação, Transporte, etc.)</li>
                                    <li>Defina o <strong>valor máximo</strong> que deseja gastar no mês</li>
                                    <li>Selecione o <strong>mês</strong></li>
                                    <li>O sistema mostra uma <strong>barra de progresso</strong> conforme você gasta</li>
                                </ol>
                                <div class="alert alert-info mb-3">
                                    <i class="bi bi-lightbulb me-2"></i>
                                    <strong>Dica:</strong> A barra fica verde (ok), amarela (atenção 80%+) ou vermelha (estourou o orçamento)
                                </div>
                                <p class="mb-0"><strong>Regra 50-30-20:</strong> 50% necessidades, 30% desejos, 20% poupança</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pergunta 5 -->
                    <div class="accordion-item mb-3" style="border: 1px solid rgba(138, 43, 226, 0.2); border-radius: 10px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" style="background: white; color: #333; font-weight: 600;">
                                <i class="bi bi-shield-check text-success me-2"></i>
                                Meus dados estão seguros?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="background: white;">
                                <p class="mb-3">Sim! Levamos a segurança muito a sério:</p>
                                <ul>
                                    <li><i class="bi bi-check-circle text-success me-2"></i><strong>Criptografia SSL</strong> de nível bancário em todas as conexões</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i><strong>Senhas criptografadas</strong> com algoritmos modernos</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i><strong>Conformidade com LGPD</strong> (Lei Geral de Proteção de Dados)</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i><strong>Backups automáticos</strong> diários</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i><strong>Sem compartilhamento</strong> de dados com terceiros</li>
                                </ul>
                                <div class="alert alert-success mb-0">
                                    <i class="bi bi-shield-fill-check me-2"></i>
                                    <strong>Garantia:</strong> Seus dados financeiros estão protegidos 24/7!
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pergunta 6 -->
                    <div class="accordion-item mb-3" style="border: 1px solid rgba(138, 43, 226, 0.2); border-radius: 10px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6" style="background: white; color: #333; font-weight: 600;">
                                <i class="bi bi-download text-primary me-2"></i>
                                Posso exportar meus dados?
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="background: white;">
                                <p class="mb-3">Sim! Você pode exportar seus dados a qualquer momento:</p>
                                <ul>
                                    <li>No <strong>Dashboard</strong>, clique em "Exportar Relatório"</li>
                                    <li>O sistema gera um <strong>arquivo CSV</strong> com todas as transações</li>
                                    <li>Abra no Excel, Google Sheets ou qualquer planilha</li>
                                    <li>Dados incluem: ID, Tipo, Categoria, Descrição, Valor, Data</li>
                                </ul>
                                <div class="alert alert-primary mb-0">
                                    <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                                    <strong>Formato CSV:</strong> Universal e compatível com qualquer ferramenta de análise!
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pergunta 7 -->
                    <div class="accordion-item" style="border: 1px solid rgba(138, 43, 226, 0.2); border-radius: 10px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7" style="background: white; color: #333; font-weight: 600;">
                                <i class="bi bi-mortarboard text-purple me-2"></i>
                                O que é a Academia Financeira?
                            </button>
                        </h2>
                        <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="background: white;">
                                <p class="mb-3">A Academia oferece educação financeira completa:</p>
                                <ul>
                                    <li><strong>Módulos estruturados</strong> desde básico até avançado</li>
                                    <li><strong>Quizzes interativos</strong> para testar conhecimento</li>
                                    <li><strong>Exercícios práticos</strong> com casos reais</li>
                                    <li><strong>Certificados</strong> ao completar módulos</li>
                                    <li>Conteúdo sobre: orçamento, investimentos, aposentadoria, impostos e mais</li>
                                </ul>
                                <div class="alert alert-success mb-0">
                                    <i class="bi bi-star-fill text-warning me-2"></i>
                                    <strong>Avaliação 4.9/5:</strong> Mais de 10.000 alunos satisfeitos!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA para mais ajuda -->
                <div class="text-center mt-5">
                    <p class="text-muted mb-3">Não encontrou sua resposta?</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="conversabot.php" class="btn btn-purple btn-modern">
                            <i class="bi bi-robot me-2"></i>Perguntar à IA
                        </a>
                        <a href="support1.php" class="btn btn-outline-purple btn-modern">
                            <i class="bi bi-headset me-2"></i>Falar com Suporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)) !important;
    color: var(--primary-purple) !important;
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(138, 43, 226, 0.3);
}

.accordion-button::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%238a2be2'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}

.accordion-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.accordion-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(138, 43, 226, 0.15);
}
</style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animações suaves nos cards
            const cards = document.querySelectorAll('.card-modern');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, index * 100);
                    }
                });
            }, { threshold: 0.1 });

            cards.forEach((card) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });

            // Atalhos de teclado
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    switch(e.key) {
                        case '1':
                            e.preventDefault();
                            window.location.href = 'dashboard.php';
                            break;
                        case '2':
                            e.preventDefault();
                            window.location.href = 'investments.php';
                            break;
                        case '3':
                            e.preventDefault();
                            window.location.href = 'conversabot.php';
                            break;
                        case '4':
                            e.preventDefault();
                            window.location.href = 'education.php';
                            break;
                    }
                }
            });
        });
    </script>
    <!-- Footer -->
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

            <!-- Plataforma -->
            <div class="col-lg-2">
                <h6 class="fw-bold mb-3">Plataforma</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="dashboard.php" class="footer-link">Dashboard</a></li>
                    <li class="mb-2"><a href="investments.php" class="footer-link">Investimentos</a></li>
                    <li class="mb-2"><a href="conversabot.php" class="footer-link">Assistente IA</a></li>
                    <li class="mb-2"><a href="education.php" class="footer-link">Academia</a></li>
                </ul>
            </div>

            <!-- Recursos -->
            <div class="col-lg-2">
                <h6 class="fw-bold mb-3">Recursos</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="quiz.php" class="footer-link">Quiz Financeiro</a></li>
                    <li class="mb-2"><a href="exercicios.php" class="footer-link">Exercícios</a></li>
                    <li class="mb-2"><a href="plans.php" class="footer-link">Planos</a></li>
                    <li class="mb-2"><a href="plans1.php" class="footer-link">Planos Premium</a></li>
                </ul>
            </div>

            <!-- Suporte -->
            <div class="col-lg-2">
                <h6 class="fw-bold mb-3">Suporte</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="support1.php" class="footer-link">Central de Ajuda</a></li>
                    <li class="mb-2"><a href="support1.php" class="footer-link">Contato</a></li>
                    <li class="mb-2"><a href="about.php" class="footer-link">Sobre Nós</a></li>
                    <li class="mb-2"><a href="about1.php" class="footer-link">Nossa História</a></li>
                </ul>
            </div>

            <!-- Legal -->
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