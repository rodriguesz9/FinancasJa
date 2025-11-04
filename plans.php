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
    <title>Planos - FinançasJá</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <style>
        .pricing-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .pricing-card.featured {
            border: 3px solid var(--purple);
            position: relative;
        }
        .badge-featured {
            position: absolute;
            top: -15px;
            right: 20px;
            background: linear-gradient(135deg, #000931ff 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
        }
        .accordion-body{
            color: white;
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Mais
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="about.php"><i class="bi bi-info-circle me-2"></i>Sobre</a></li>
                            <li><a class="dropdown-item active" href="plans.php"><i class="bi bi-star me-2"></i>Planos</a></li>
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
                            Plano 100% Gratuito
                        </h1>
                        <p class="lead mb-4 fade-in-up" style="animation-delay: 0.2s;">
                            Todas as funcionalidades, sem custo algum!
                        </p>
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <span class="badge badge-modern" style="background: var(--success);">
                                <i class="bi bi-check-circle me-1"></i>Sem taxas
                            </span>
                            <span class="badge badge-modern" style="background: var(--info);">
                                <i class="bi bi-infinity me-1"></i>Ilimitado
                            </span>
                            <span class="badge badge-modern" style="background: var(--warning);">
                                <i class="bi bi-shield-check me-1"></i>Sem cartão de crédito
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Plano Gratuito -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card-modern pricing-card featured position-relative">
                        <span class="badge-featured">
                            <i class="bi bi-star-fill me-1"></i>100% GRATUITO
                        </span>
                        
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i class="bi bi-gem text-purple" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="fw-bold mb-3">Plano Completo</h2>
                            <div class="my-5">
                                <span class="display-2 fw-bold text-purple">R$ 0</span>
                                <span class="fs-4 text-muted">/para sempre</span>
                            </div>
                            <p class="lead mb-5" style="color: #6c757d;">
                                Acesso total e ilimitado a todas as funcionalidades da plataforma
                            </p>
                            
                            <button class="btn btn-purple btn-modern btn-lg px-5" disabled>
                                <i class="bi bi-check-circle me-2"></i>Você já está usando!
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Funcionalidades Completas -->
    <section class="py-5" style="background: rgba(138, 43, 226, 0.05);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-gradient">O que está incluído</h2>
                <p class="text-muted">Todas as ferramentas para gerenciar suas finanças</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card-modern h-100 p-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-infinity text-success fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Transações Ilimitadas</h5>
                                <p class="text-muted mb-0">
                                    Registre quantas receitas e despesas quiser, sem limites.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card-modern h-100 p-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-info bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-graph-up-arrow text-info fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Carteira de Investimentos</h5>
                                <p class="text-muted mb-0">
                                    Acompanhe ações, FIIs e criptomoedas com cotações em tempo real.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card-modern h-100 p-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-warning bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-robot text-warning fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Assistente de IA Ilimitado</h5>
                                <p class="text-muted mb-0">
                                    Consultoria financeira 24/7 com inteligência artificial avançada.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card-modern h-100 p-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-danger bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-bar-chart-fill text-danger fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Dashboard Completo</h5>
                                <p class="text-muted mb-0">
                                    Visualize seus dados financeiros com gráficos e relatórios avançados.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card-modern h-100 p-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-mortarboard text-primary fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Academia Financeira</h5>
                                <p class="text-muted mb-0">
                                    44 aulas em vídeo sobre finanças pessoais e investimentos.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card-modern h-100 p-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-clipboard-check text-success fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Exercícios e Quizzes</h5>
                                <p class="text-muted mb-0">
                                    Teste seus conhecimentos com 6 níveis de exercícios práticos.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card-modern h-100 p-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-info bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-cash-coin text-info fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Cotações em Tempo Real</h5>
                                <p class="text-muted mb-0">
                                    Dólar, Euro e Bitcoin atualizados automaticamente.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card-modern h-100 p-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-dark bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-shield-check text-dark fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Segurança Total</h5>
                                <p class="text-muted mb-0">
                                    Seus dados protegidos com criptografia de ponta a ponta.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Por que é gratuito? -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Por que é 100% gratuito?</h2>
                    <p class="text-muted mb-3">
                        Acreditamos que educação financeira e ferramentas de gestão devem ser 
                        acessíveis a todos, independente da situação financeira.
                    </p>
                    <p class="text-muted mb-4">
                        Nossa missão é democratizar o acesso ao conhecimento financeiro e ajudar 
                        o máximo de pessoas possível a alcançar seus objetivos.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Sem taxas ocultas</strong> - Realmente gratuito
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Sem anúncios</strong> - Experiência limpa e focada
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Sem limite de tempo</strong> - Use para sempre
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Todas as funcionalidades</strong> - Sem restrições
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="card-modern p-5 text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-heart-fill text-white" style="font-size: 5rem;"></i>
                        <h4 class="text-white mt-4 mb-3">Feito com ❤️</h4>
                        <p class="text-white opacity-75 mb-0">
                            Desenvolvido pensando em você e no seu futuro financeiro
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-5" style="background: rgba(138, 43, 226, 0.05);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Perguntas Frequentes</h2>
                <p class="text-muted">Tire suas dúvidas sobre o plano gratuito</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item border-0 mb-3 card-modern">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    É realmente gratuito para sempre?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sim! Não há pegadinhas. O FinanceApp é 100% gratuito e sempre será. 
                                    Você tem acesso completo a todas as funcionalidades sem pagar nada.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item border-0 mb-3 card-modern">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Preciso cadastrar cartão de crédito?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Não! Não pedimos dados de cartão de crédito. Basta criar sua conta 
                                    com email e senha para começar a usar imediatamente.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item border-0 mb-3 card-modern">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Existe limite de uso?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Não! Você pode adicionar quantas transações, investimentos e usar 
                                    o assistente de IA quantas vezes quiser. Tudo é ilimitado.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item border-0 mb-3 card-modern">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Meus dados estão seguros?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sim! Utilizamos criptografia de ponta a ponta e nunca compartilhamos 
                                    seus dados com terceiros. Sua privacidade é nossa prioridade.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-3 card-modern">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    Haverá planos pagos no futuro?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    O plano gratuito sempre existirá com todas as funcionalidades atuais. 
                                    Se criarmos recursos extras no futuro, serão opcionais e o plano gratuito 
                                    continuará completo como está.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-5" style="background: rgba(138, 43, 226, 0.05);">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Perguntas Frequentes</h2>
      <p class="text-muted">Tire suas dúvidas sobre o plano gratuito e planos pagos</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="accordion" id="faqAccordion">

          <!-- FAQ 1 -->
          <div class="accordion-item border-0 mb-3 card-modern">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                É realmente gratuito para sempre?
              </button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Sim! Não há pegadinhas. O FinanceApp é 100% gratuito e sempre será.
                Você tem acesso completo a todas as funcionalidades sem pagar nada.
              </div>
            </div>
          </div>

          <!-- FAQ 2 -->
          <div class="accordion-item border-0 mb-3 card-modern">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                Preciso cadastrar cartão de crédito?
              </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Não! Não pedimos dados de cartão de crédito. Basta criar sua conta
                com e-mail e senha para começar a usar imediatamente.
              </div>
            </div>
          </div>

          <!-- FAQ 3 -->
          <div class="accordion-item border-0 mb-3 card-modern">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                Existe limite de uso?
              </button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Não! Você pode adicionar quantas transações, investimentos e usar
                o assistente de IA quantas vezes quiser. Tudo é ilimitado.
              </div>
            </div>
          </div>

          <!-- FAQ 4 -->
          <div class="accordion-item border-0 mb-3 card-modern">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                Meus dados estão seguros?
              </button>
            </h2>
            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Sim! Utilizamos criptografia de ponta a ponta e nunca compartilhamos
                seus dados com terceiros. Sua privacidade é nossa prioridade.
              </div>
            </div>
          </div>

          <!-- FAQ 5 -->
          <div class="accordion-item border-0 mb-3 card-modern">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                Haverá planos pagos no futuro?
              </button>
            </h2>
            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                O plano gratuito sempre existirá com todas as funcionalidades atuais.
                Se criarmos recursos extras no futuro, serão opcionais e o plano gratuito
                continuará completo como está.
              </div>
            </div>
          </div>

          <!-- FAQ 6 -->
          <div class="accordion-item border-0 mb-3 card-modern">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                Como funciona a garantia de 30 dias?
              </button>
            </h2>
            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Se você não ficar satisfeito com o plano Pro ou Premium nos primeiros 30 dias,
                devolvemos 100% do seu dinheiro, sem perguntas.
              </div>
            </div>
          </div>

          <!-- FAQ 7 -->
          <div class="accordion-item border-0 mb-3 card-modern">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                Quais formas de pagamento são aceitas?
              </button>
            </h2>
            <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Aceitamos cartão de crédito, débito, PIX e boleto bancário.
                Todos os pagamentos são processados de forma segura.
              </div>
            </div>
          </div>

          <!-- FAQ 8 -->
          <div class="accordion-item border-0 mb-3 card-modern">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                Como vocês protegem meus dados financeiros?
              </button>
            </h2>
            <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Absolutamente! Utilizamos criptografia de ponta a ponta e seguimos os mais
                rigorosos padrões de segurança bancária (PCI DSS Level 1).
              </div>
            </div>
          </div>

        </div><!-- /.accordion -->
      </div>
    </div>
  </div>
</section>



    <!-- CTA Final -->
    <section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="text-center text-white">
                <i class="bi bi-rocket-takeoff-fill" style="font-size: 4rem;"></i>
                <h2 class="fw-bold mb-3 mt-3">Comece Agora Mesmo!</h2>
                <p class="mb-4 opacity-75 lead">
                    Todas as funcionalidades estão esperando por você
                </p>
                <a href="home.php" class="btn btn-light btn-modern btn-lg px-5">
                    <i class="bi bi-arrow-right me-2"></i>Ir para Dashboard
                </a>
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