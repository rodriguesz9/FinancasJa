<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - FinançasJá</title>
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
            <a class="navbar-brand" href="<?= $is_logged_in ? 'home.php' : 'index.php' ?>">
                <i class="bi bi-gem me-2"></i>FinançasJá
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if ($is_logged_in): ?>
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
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Mais
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item active" href="about1.php"><i class="bi bi-info-circle me-2"></i>Sobre</a></li>
                            <li><a class="dropdown-item" href="plans1.php"><i class="bi bi-star me-2"></i>Planos</a></li>
                            <li><a class="dropdown-item" href="support1.php"><i class="bi bi-headset me-2"></i>Suporte</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($is_logged_in): ?>
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
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-purple btn-modern ms-2" href="register.php">
                                Começar Grátis
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-modern">
        <div class="container">
            <div class="hero-content text-center">
                <h1 class="display-4 fw-bold mb-4 fade-in-up">Sobre o FinançasJá</h1>
                <p class="lead mb-0 fade-in-up" style="animation-delay: 0.2s;">
                    Transformando a educação financeira no Brasil com tecnologia e inovação
                </p>
            </div>
        </div>
    </section>

    <!-- Missão, Visão e Valores -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card-modern text-center h-100">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="bi bi-bullseye text-purple" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Nossa Missão</h4>
                            <p class="text-muted">
                                Democratizar o acesso à educação financeira e ferramentas de gestão, 
                                capacitando pessoas a conquistarem sua liberdade financeira através 
                                de tecnologia acessível e intuitiva.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-modern text-center h-100">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="bi bi-eye text-info" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Nossa Visão</h4>
                            <p class="text-muted">
                                Ser a principal plataforma de educação e gestão financeira do Brasil, 
                                reconhecida pela excelência em tecnologia, inteligência artificial 
                                e pela transformação positiva na vida de milhões de brasileiros.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-modern text-center h-100">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="bi bi-heart text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Nossos Valores</h4>
                            <p class="text-muted">
                                Transparência, inovação, educação de qualidade, segurança dos dados, 
                                acessibilidade, compromisso com resultados e foco total na 
                                satisfação e sucesso dos nossos usuários.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Nossa História -->
    <section class="py-5" style="background: rgba(138, 43, 226, 0.05);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Nossa História</h2>
                    <p class="text-muted mb-4">
                        O FinançasJá nasceu em 2025 com um objetivo claro: tornar a educação financeira 
                        acessível a todos os brasileiros. Fundada por especialistas em finanças e tecnologia, 
                        nossa plataforma combina o melhor da educação financeira com inteligência artificial 
                        de ponta.
                    </p>
                    <p class="text-muted mb-4">
                        Observamos que milhões de brasileiros enfrentam dificuldades na gestão de suas 
                        finanças pessoais, não por falta de vontade, mas por falta de ferramentas adequadas 
                        e conhecimento estruturado. Foi assim que decidimos criar uma solução completa.
                    </p>
                    <p class="text-muted mb-0">
                        Hoje, o FinançasJá conta com mais de 50  usuários ativos, gerenciando mais de 
                        R$ 100.000  em patrimônio, e continuamos crescendo exponencialmente, sempre 
                        com foco em entregar a melhor experiência possível.
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="card-modern">
                        <div class="card-body p-4">
                            <div class="row g-4 text-center">
                                <div class="col-6">
                                    <div class="p-3">
                                        <h2 class="text-purple fw-bold mb-2">2025</h2>
                                        <p class="text-muted mb-0">Ano de Fundação</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3">
                                        <h2 class="text-success fw-bold mb-2">50+</h2>
                                        <p class="text-muted mb-0">Usuários Ativos</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3">
                                        <h2 class="text-info fw-bold mb-2">R$ 200.000+</h2>
                                        <p class="text-muted mb-0">Patrimônio Gerenciado</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3">
                                        <h2 class="text-warning fw-bold mb-2">4.9/5</h2>
                                        <p class="text-muted mb-0">Avaliação Média</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tecnologia -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Tecnologia de Ponta</h2>
                <p class="text-muted">Utilizamos as melhores tecnologias do mercado para entregar resultados excepcionais</p>
            </div>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card-modern text-center h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-robot text-purple fs-1 mb-3"></i>
                            <h5 class="fw-bold mb-2">Gemini AI</h5>
                            <p class="text-muted small mb-0">Inteligência artificial do Google para consultoria financeira 24/7</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-modern text-center h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-shield-check text-success fs-1 mb-3"></i>
                            <h5 class="fw-bold mb-2">Segurança SSL</h5>
                            <p class="text-muted small mb-0">Criptografia de nível bancário para proteger seus dados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-modern text-center h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-cloud-check text-info fs-1 mb-3"></i>
                            <h5 class="fw-bold mb-2">Cloud Computing</h5>
                            <p class="text-muted small mb-0">Infraestrutura escalável e confiável na nuvem</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-modern text-center h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-graph-up-arrow text-warning fs-1 mb-3"></i>
                            <h5 class="fw-bold mb-2">APIs em Tempo Real</h5>
                            <p class="text-muted small mb-0">Cotações e dados financeiros atualizados instantaneamente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Diferenciais -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--primary-purple), var(--accent-purple));">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-white mb-3">Por Que Escolher o FinançasJá?</h2>
                <p class="text-white-50">Diferenciais que nos tornam únicos no mercado</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-check-circle-fill text-white fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-2">100% Gratuito</h5>
                            <p class="text-white-50 mb-0">Acesso completo a todas as funcionalidades sem custo algum</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-lightning-charge-fill text-white fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-2">Interface Intuitiva</h5>
                            <p class="text-white-50 mb-0">Design moderno e fácil de usar, sem complicações</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-phone-fill text-white fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-2">Multiplataforma</h5>
                            <p class="text-white-50 mb-0">Acesse de qualquer dispositivo, a qualquer hora</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-mortarboard-fill text-white fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-2">Educação Completa</h5>
                            <p class="text-white-50 mb-0">Cursos estruturados com certificação reconhecida</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-headset text-white fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-2">Suporte Dedicado</h5>
                            <p class="text-white-50 mb-0">Equipe pronta para ajudar quando você precisar</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-award-fill text-white fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-2">Resultados Comprovados</h5>
                            <p class="text-white-50 mb-0">Milhares de usuários já transformaram suas vidas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5">
        <div class="container">
            <div class="card-modern text-center">
                <div class="card-body p-5">
                    <h2 class="fw-bold mb-3">Pronto para Começar?</h2>
                    <p class="text-muted mb-4">
                        Junte-se a milhares de brasileiros que já estão no caminho da liberdade financeira
                    </p>
                    <?php if (!$is_logged_in): ?>
                        <a href="register.php" class="btn btn-purple btn-modern btn-lg">
                            <i class="bi bi-rocket-takeoff me-2"></i>Começar Agora Grátis
                        </a>
                    <?php else: ?>
                        <a href="home.php" class="btn btn-purple btn-modern btn-lg">
                            <i class="bi bi-speedometer2 me-2"></i>Ir para Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-modern">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand mb-3">
                        <i class="bi bi-gem me-2"></i>FinançasJá
                    </div>
                    <p class="text-muted">
                        Sua plataforma completa para gestão financeira pessoal com inteligência artificial.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="social-icon">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="bi bi-linkedin"></i>
                        </a>
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
                    <h6 class="fw-bold mb-3">Empresa</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="about.php" class="footer-link">Sobre</a></li>
                        <li class="mb-2"><a href="plans.php" class="footer-link">Planos</a></li>
                        <li class="mb-2"><a href="support1.php" class="footer-link">Suporte</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Blog</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="footer-link">Privacidade</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Termos</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Segurança</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Cookies</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold mb-3">Contato</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2 text-muted"><i class="bi bi-envelope me-2"></i>contato.financasja@gmail.com</li>
                        <li class="mb-2 text-muted"><i class="bi bi-telephone me-2"></i>(31) 9409-0721</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(138, 43, 226, 0.2);">
            <div class="text-center">
                <p class="text-muted mb-0">&copy; 2025 FinançasJá. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animações de entrada
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            document.querySelectorAll('.card-modern').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.8s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>