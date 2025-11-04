<?php
session_start();
require_once 'config/database.php';

requireLogin();

$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre - FinançasJá</title>
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
                            <li><a class="dropdown-item active" href="about.php"><i class="bi bi-info-circle me-2"></i>Sobre</a></li>
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

    <!-- Hero Section -->
    <section class="hero-modern">
        <div class="container">
            <div class="hero-content text-center">
                <div class="row align-items-center">
                    <div class="col-lg-8 mx-auto">
                        <h1 class="display-4 fw-bold mb-4 fade-in-up">
                            Sobre o FinançasJá
                        </h1>
                        <p class="lead mb-4 fade-in-up" style="animation-delay: 0.2s;">
                            Transformando a forma como você gerencia suas finanças
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Nossa História -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Nossa História</h2>
                    <p class="text-muted mb-3">
                        O FinancasJa nasceu da necessidade de tornar a gestão financeira acessível e simples para todos. 
                        Fundada em 2025, nossa plataforma combina tecnologia de ponta com educação financeira de qualidade.
                    </p>
                    <p class="text-muted mb-4">
                        Acreditamos que todos merecem ter controle total sobre suas finanças, e por isso desenvolvemos 
                        ferramentas intuitivas que ajudam você a tomar decisões mais inteligentes sobre seu dinheiro.
                    </p>
                    <div class="d-flex gap-4">
                        <div>
                            <h3 class="text-purple fw-bold mb-0">50</h3>
                            <small class="text-muted">Usuários Ativos</small>
                        </div>
                        <div>
                            
                        </div>
                        <div>
                            <h3 class="text-purple fw-bold mb-0">4.9★</h3>
                            <small class="text-muted">Avaliação</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-modern p-5 text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-gem text-white" style="font-size: 5rem;"></i>
                        <h4 class="text-white mt-4 mb-3">Nossa Missão</h4>
                        <p class="text-white opacity-75">
                            Democratizar o acesso à educação e gestão financeira através da tecnologia
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Valores -->
    <section class="py-5" style="background: rgba(138, 43, 226, 0.05);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-gradient">Nossos Valores</h2>
                <p class="text-muted">O que nos move todos os dias</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card-modern text-center h-100 p-4">
                        <i class="bi bi-shield-check text-success fs-1 mb-3"></i>
                        <h5 class="fw-bold mb-3">Segurança</h5>
                        <p class="text-muted">
                            Seus dados são criptografados e protegidos com os mais altos padrões de segurança do mercado.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-modern text-center h-100 p-4">
                        <i class="bi bi-lightning-charge text-warning fs-1 mb-3"></i>
                        <h5 class="fw-bold mb-3">Inovação</h5>
                        <p class="text-muted">
                            Utilizamos IA e tecnologias de ponta para oferecer a melhor experiência de gestão financeira.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-modern text-center h-100 p-4">
                        <i class="bi bi-people text-info fs-1 mb-3"></i>
                        <h5 class="fw-bold mb-3">Transparência</h5>
                        <p class="text-muted">
                            Sem taxas ocultas, sem surpresas. Você sempre sabe exatamente o que está pagando e recebendo.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recursos -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">O que nos torna diferentes?</h2>
                <p class="text-muted">Recursos que você não encontra em outros lugares</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card-modern p-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-purple bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-robot text-purple fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Assistente de IA Avançado</h5>
                                <p class="text-muted mb-0">
                                    Consultoria financeira 24/7 com inteligência artificial que aprende com seu comportamento 
                                    e oferece recomendações personalizadas.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-modern p-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-graph-up-arrow text-success fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Investimentos em Tempo Real</h5>
                                <p class="text-muted mb-0">
                                    Acompanhe sua carteira com cotações em tempo real de ações, fundos e criptomoedas 
                                    integradas diretamente de APIs oficiais.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-modern p-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-info bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-mortarboard text-info fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Academia Financeira</h5>
                                <p class="text-muted mb-0">
                                    Cursos completos sobre investimentos, planejamento financeiro e economia 
                                    criados por especialistas certificados.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-modern p-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-warning bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-pie-chart text-warning fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Análises Inteligentes</h5>
                                <p class="text-muted mb-0">
                                    Relatórios detalhados e visualizações interativas que transformam seus dados 
                                    financeiros em insights acionáveis.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="text-white fw-bold mb-3">Pronto para transformar suas finanças?</h2>
                    <p class="text-white opacity-75 mb-0">
                        Junte-se a milhares de usuários que já estão no controle de seu futuro financeiro
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="dashboard.php" class="btn btn-light btn-modern btn-lg">
                        <i class="bi bi-star me-2"></i>Ver Dashboard
                    </a>
                </div>
            </div>
        </div>
    </section>
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
                    <a href="https://www.instagram.com/financasja_/" class="social-icon"><i class="bi bi-instagram"></i></a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>