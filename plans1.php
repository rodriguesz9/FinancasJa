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
    <title>Planos e Preços - FinançasJá</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <style>
        .plan-card {
            position: relative;
            transition: all 0.3s ease;
        }
        .plan-card:hover {
            transform: translateY(-10px);
        }
        .plan-badge {
            position: absolute;
            top: -15px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(118, 75, 162, 0.3);
        }
        .price-tag {
            font-size: 3rem;
            font-weight: 700;
            line-height: 1;
        }
        .feature-check {
            width: 24px;
            height: 24px;
            background: rgba(40, 167, 69, 0.1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }
        @media (max-width: 991px) {
            .plan-card { margin-bottom: 2rem; }
        }
    </style>
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
                            <a class="nav-link" href="home.php"><i class="bi bi-house-fill me-1"></i>Início</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="investments.php"><i class="bi bi-graph-up me-1"></i>Investimentos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="conversabot.php"><i class="bi bi-robot me-1"></i>Assistente IA</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="education.php"><i class="bi bi-mortarboard me-1"></i>Academia</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="index.php" role="button">Voltar</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="about.php"><i class="bi bi-info-circle me-2"></i>Sobre</a></li>
                            <li><a class="dropdown-item active" href="plans.php"><i class="bi bi-star me-2"></i>Planos</a></li>
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
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-purple btn-modern ms-2" href="register.php">Começar Grátis</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero-modern py-5 text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Plano Gratuito Completo</h1>
            <p class="lead mb-4">Aproveite todas as funcionalidades da plataforma sem pagar nada</p>
        </div>
    </section>

    <!-- Plano Gratuito Centralizado -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card-modern plan-card h-100 text-center position-relative p-4">
                        <span class="plan-badge"><i class="bi bi-star-fill me-1"></i>100% GRATUITO</span>
                        <i class="bi bi-gift text-success" style="font-size: 3rem;"></i>
                        <h3 class="fw-bold mt-3 mb-2">Gratuito</h3>
                        <div class="price-tag text-success mb-2">R$ 0</div>
                        <p class="text-muted mb-4">Para sempre</p>

                        <a href="<?= $is_logged_in ? 'home.php' : 'register.php' ?>" class="btn btn-outline-success btn-lg mb-4">
                            <?= $is_logged_in ? 'Seu Plano Atual' : 'Começar Grátis' ?>
                        </a>

                        <ul class="list-unstyled text-start">
                            <?php
                            $features = [
                                "Dashboard completo",
                                "Controle de receitas e despesas",
                                "Gráficos e relatórios avançados",
                                "Academia financeira completa",
                                "Assistente de IA ilimitado",
                                "Suporte por e-mail",
                                "Cotações em tempo real",
                                "Segurança total de dados"
                            ];
                            foreach ($features as $feature):
                            ?>
                                <li class="mb-2 d-flex align-items-center">
                                    <span class="feature-check"><i class="bi bi-check text-success"></i></span>
                                    <span><?= $feature ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="fw-bold text-center mb-4">Perguntas Frequentes</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item card-modern mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    O plano gratuito é realmente 100% grátis?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sim! O plano gratuito é completamente grátis para sempre, sem período de teste ou necessidade de cartão de crédito.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item card-modern mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Meus dados estão seguros?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Absolutamente! Utilizamos criptografia SSL de nível bancário e seguimos as melhores práticas de segurança da LGPD.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


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
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="footer-link">Privacidade</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Termos</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Segurança</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold mb-3">Contato</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2 text-muted"><i class="bi bi-envelope me-2"></i>contato@financasja.com</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(138, 43, 226, 0.2);">
            <div class="text-center">
                <p class="text-muted mb-0">&copy; 2024 FinançasJá. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            document.querySelectorAll('.plan-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.8s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>