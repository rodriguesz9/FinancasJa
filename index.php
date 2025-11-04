<?php
session_start();


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinançasJá - Sua Liberdade Financeira</title>
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
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-gem me-2"></i>FinançasJá
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Funcionalidades</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="plans1.php">Planos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about1.php">Sobre</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-purple btn-modern ms-2" href="home.php">
                                <i class="bi bi-speedometer2 me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Sair</a>
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
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="display-3 fw-bold mb-4 fade-in-up">
                            Sua <span class="text-gradient">Liberdade Financeira</span> Começa Aqui
                        </h1>
                        <p class="lead mb-5 fade-in-up" style="animation-delay: 0.2s;">
                            Plataforma completa de gestão financeira com inteligência artificial. 
                            Controle receitas, despesas, investimentos e aprenda finanças de forma inteligente.
                        </p>
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center fade-in-up" style="animation-delay: 0.4s;">
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <a href="register.php" class="btn btn-purple btn-modern btn-lg">
                                    <i class="bi bi-rocket-takeoff me-2"></i>Começar Grátis
                                </a>
                                <a href="login.php" class="btn btn-outline-purple btn-modern btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Fazer Login
                                </a>
                            <?php else: ?>
                                <a href="home.php" class="btn btn-purple btn-modern btn-lg">
                                    <i class="bi bi-speedometer2 me-2"></i>Ir para Dashboard
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4 fade-in-up" style="animation-delay: 0.6s;">
                            <div class="d-flex justify-content-center align-items-center gap-4 text-white-50">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    <span>Grátis para sempre</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-shield-check text-info me-2"></i>
                                    <span>100% Seguro</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-robot text-warning me-2"></i>
                                    <span>IA Integrada</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="position-relative fade-in-up" style="animation-delay: 0.8s;">
                            <!-- Mockup do App -->
                            <div class="card-modern p-4" style="max-width: 400px; margin: 0 auto;">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="me-3">
                                        <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(45deg, var(--primary-purple), var(--accent-purple)); display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-person-fill text-white fs-5"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Olá, Maria! 👋</h6>
                                        <small class="text-muted">Bem-vinda de volta</small>
                                    </div>
                                </div>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="stat-card-modern success p-3">
                                            <small class="text-muted">Receitas</small>
                                            <h6 class="text-success mb-0">R$ 8.500</h6>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-card-modern danger p-3">
                                            <small class="text-muted">Despesas</small>
                                            <h6 class="text-danger mb-0">R$ 6.200</h6>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Investimentos</h6>
                                    <span class="badge bg-success">+12,5%</span>
                                </div>
                                
                                <div class="progress mb-4" style="height: 8px;">
                                    <div class="progress-bar gradient-purple" style="width: 75%;"></div>
                                </div>
                                
                                <div class="d-grid">
                                    <button href="conversabot.php" class="btn btn-purple btn-modern">
                                        <i class="bi bi-robot me-2" ></i>Perguntar à IA
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5" style="background: rgba(138, 43, 226, 0.05);">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <div class="card-modern h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-people-fill text-purple" style="font-size: 3rem;"></i>
                            <h3 class="mt-3 mb-2">50+</h3>
                            <p class="text-muted mb-0">Usuários Ativos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-modern h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-graph-up-arrow text-success" style="font-size: 3rem;"></i>
                            <h3 class="mt-3 mb-2">R$ 200k+</h3>
                            <p class="text-muted mb-0">Patrimônio Gerenciado</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-modern h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-star-fill text-warning" style="font-size: 3rem;"></i>
                            <h3 class="mt-3 mb-2">4.9/5</h3>
                            <p class="text-muted mb-0">Avaliação dos Usuários</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-modern h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-shield-check text-info" style="font-size: 3rem;"></i>
                            <h3 class="mt-3 mb-2">100%</h3>
                            <p class="text-muted mb-0">Dados Seguros</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-gradient">Funcionalidades Completas</h2>
                <p class="text-muted">Tudo que você precisa para transformar sua vida financeira</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card-modern h-100 text-center">
                        <div class="card-body p-4">
                            <div class="text-purple mb-3" style="font-size: 4rem;">
                                <i class="bi bi-speedometer2"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Dashboard Inteligente</h4>
                            <p class="text-muted mb-4">Visualize todas suas finanças em tempo real com gráficos interativos e insights personalizados.</p>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Controle de receitas e despesas</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Gráficos dinâmicos</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Categorização automática</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card-modern h-100 text-center">
                        <div class="card-body p-4">
                            <div class="text-success mb-3" style="font-size: 4rem;">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Investimentos Pro</h4>
                            <p class="text-muted mb-4">Acompanhe seus investimentos com cotações em tempo real e análises avançadas.</p>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Cotações em tempo real</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Análise de performance</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Diversificação inteligente</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card-modern h-100 text-center">
                        <div class="card-body p-4">
                            <div class="text-info mb-3" style="font-size: 4rem;">
                                <i class="bi bi-robot"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Assistente IA</h4>
                            <p class="text-muted mb-4">Consultoria financeira 24/7 com inteligência artificial do Gemini para respostas precisas.</p>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Powered by Gemini AI</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Disponível 24/7</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Respostas personalizadas</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-4">
                <div class="col-lg-6">
                    <div class="card-modern h-100">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center">
                                    <i class="bi bi-mortarboard text-warning" style="font-size: 4rem;"></i>
                                </div>
                                <div class="col-md-8">
                                    <h4 class="fw-bold mb-3">Academia Financeira</h4>
                                    <p class="text-muted mb-3">Aprenda finanças com cursos estruturados e certificações reconhecidas.</p>
                                    <ul class="list-unstyled">
                                        <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>3 módulos completos</li>
                                        <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Certificados digitais</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Exercícios práticos</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card-modern h-100">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center">
                                    <i class="bi bi-shield-check text-info" style="font-size: 4rem;"></i>
                                </div>
                                <div class="col-md-8">
                                    <h4 class="fw-bold mb-3">Segurança Total</h4>
                                    <p class="text-muted mb-3">Seus dados financeiros protegidos com criptografia de nível bancário.</p>
                                    <ul class="list-unstyled">
                                        <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Criptografia SSL</li>
                                        <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Dados na nuvem</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Backup automático</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--primary-purple), var(--accent-purple));">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="text-white fw-bold mb-3">Pronto para Transformar sua Vida Financeira?</h2>
                    <p class="text-white-50 mb-0">Junte-se a milhares de pessoas que já conquistaram sua liberdade financeira conosco.</p>
                </div>
                <div class="col-lg-4 text-end">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn btn-light btn-modern btn-lg">
                            <i class="bi bi-rocket-takeoff me-2"></i>Começar Agora
                        </a>
                    <?php else: ?>
                        <a href="home.php" class="btn btn-light btn-modern btn-lg">
                            <i class="bi bi-speedometer2 me-2"></i>Acessar Dashboard
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
                    <li class="mb-2"><a href="dashboard.php" class="footer-link"></a></li>
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
    <script>
        // Animações de entrada
        document.addEventListener('DOMContentLoaded', function() {
            // Animar elementos na entrada da viewport
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            // Observar todos os cards
            document.querySelectorAll('.card-modern').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.8s ease';
                observer.observe(card);
            });

            // Parallax suave no hero
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const rate = scrolled * -0.5;
                const hero = document.querySelector('.hero-modern');
                if (hero) {
                    hero.style.transform = `translateY(${rate}px)`;
                }
            });
        });

        // Scroll suave para links internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
