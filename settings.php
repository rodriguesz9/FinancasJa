<?php
session_start();
require_once 'config/database.php';
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'] ?? 'usuario@email.com';

requireLogin();
?> <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - FinançasJá</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <style>
        .settings-nav {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-lg);
            padding: 1rem;
        }
        
        .settings-nav-item {
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            color: var(--light-bg);
        }
        
        .settings-nav-item:hover {
            background: rgba(138, 43, 226, 0.1);
            transform: translateX(5px);
        }
        
        .settings-nav-item.active {
            background: linear-gradient(45deg, var(--primary-purple), var(--accent-purple));
            color: white;
        }
        
        .settings-nav-item i {
            font-size: 1.2rem;
            margin-right: 1rem;
            width: 24px;
        }
        
        .settings-content {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-lg);
            padding: 2rem;
        }
        
        .settings-section {
            padding: 1.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .settings-section:last-child {
            border-bottom: none;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.2);
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background: linear-gradient(45deg, var(--primary-purple), var(--accent-purple));
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .danger-zone {
            background: rgba(244, 67, 54, 0.1);
            border: 1px solid rgba(244, 67, 54, 0.3);
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-modern sticky-top">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <i class="bi bi-gem me-2"></i>FinancasJá
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

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col">
                <h1 class="display-5 fw-bold">
                    <i class="bi bi-gear me-3"></i>Configurações
                </h1>
                <p class="lead">Personalize sua experiência no FinançasJá</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3">
                <div class="settings-nav sticky-top" style="top: 100px;">
                    <div class="settings-nav-item active" onclick="showSection('account')">
                        <i class="bi bi-person-circle"></i>
                        <span>Conta</span>
                    </div>
                    <div class="settings-nav-item" onclick="showSection('security')">
                        <i class="bi bi-shield-lock"></i>
                        <span>Segurança</span>
                    </div>
                    <div class="settings-nav-item" onclick="showSection('notifications')">
                        <i class="bi bi-bell"></i>
                        <span>Notificações</span>
                    </div>
                    <div class="settings-nav-item" onclick="showSection('privacy')">
                        <i class="bi bi-eye-slash"></i>
                        <span>Privacidade</span>
                    </div>
                    <div class="settings-nav-item" onclick="showSection('appearance')">
                        <i class="bi bi-palette"></i>
                        <span>Aparência</span>
                    </div>
                    <div class="settings-nav-item" onclick="showSection('subscription')">
                        <i class="bi bi-star"></i>
                        <span>Assinatura</span>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-9">
                <!-- Account Section -->
                <div class="settings-content" id="account-section">
                    <h3 class="mb-4"><i class="bi bi-person-circle me-2"></i>Configurações da Conta</h3>
                    
                    <div class="settings-section">
                        <h5>Informações Pessoais</h5>
                        <p class="text-muted">Atualize suas informações básicas</p>
                        
                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Nome Completo</label>
                                <input type="text" class="form-control form-control-modern" value="<?= htmlspecialchars($user_name) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control form-control-modern" value="<?= htmlspecialchars($user_email) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telefone</label>
                                <input type="tel" class="form-control form-control-modern" placeholder="(00) 00000-0000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data de Nascimento</label>
                                <input type="date" class="form-control form-control-modern">
                            </div>
                        </div>
                        
                        <button class="btn btn-purple mt-3">
                            <i class="bi bi-check-lg me-2"></i>Salvar Alterações
                        </button>
                    </div>
                    
                    <div class="settings-section">
                        <h5>Idioma e Região</h5>
                        <p class="text-muted">Configure seu idioma e localização</p>
                        
                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Idioma</label>
                                <select class="form-select form-control-modern">
                                    <option selected>Português (Brasil)</option>
                                    <option>English (US)</option>
                                    <option>Español</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fuso Horário</label>
                                <select class="form-select form-control-modern">
                                    <option selected>GMT-3 (Brasília)</option>
                                    <option>GMT-5 (New York)</option>
                                    <option>GMT+0 (London)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Section -->
                <div class="settings-content d-none" id="security-section">
                    <h3 class="mb-4"><i class="bi bi-shield-lock me-2"></i>Segurança</h3>
                    
                    <div class="settings-section">
                        <h5>Alterar Senha</h5>
                        <p class="text-muted">Mantenha sua conta segura com uma senha forte</p>
                        
                        <div class="row g-3 mt-3">
                            <div class="col-md-12">
                                <label class="form-label">Senha Atual</label>
                                <input type="password" class="form-control form-control-modern">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nova Senha</label>
                                <input type="password" class="form-control form-control-modern">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control form-control-modern">
                            </div>
                        </div>
                        
                        <button class="btn btn-purple mt-3">
                            <i class="bi bi-key me-2"></i>Alterar Senha
                        </button>
                    </div>
                    
                    <div class="settings-section">
                        <h5>Autenticação de Dois Fatores</h5>
                        <p class="text-muted">Adicione uma camada extra de segurança</p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <strong>Ativar 2FA</strong>
                                <p class="text-muted small mb-0">Usar app autenticador (Google Authenticator)</p>
                            </div>
                            <label class="switch">
                                <input type="checkbox">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h5>Sessões Ativas</h5>
                        <p class="text-muted">Gerencie seus dispositivos conectados</p>
                        
                        <div class="mt-3">
                            <div class="p-3 rounded mb-2" style="background: rgba(255, 255, 255, 0.05);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-laptop me-2"></i>
                                        <strong>Windows PC</strong>
                                        <p class="text-muted small mb-0">Último acesso: Agora</p>
                                    </div>
                                    <span class="badge bg-success">Atual</span>
                                </div>
                            </div>
                            <div class="p-3 rounded mb-2" style="background: rgba(255, 255, 255, 0.05);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-phone me-2"></i>
                                        <strong>iPhone 13</strong>
                                        <p class="text-muted small mb-0">Último acesso: 2 horas atrás</p>
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger">Encerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications Section -->
                <div class="settings-content d-none" id="notifications-section">
                    <h3 class="mb-4"><i class="bi bi-bell me-2"></i>Notificações</h3>
                    
                    <div class="settings-section">
                        <h5>Notificações por Email</h5>
                        
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <strong>Newsletter Semanal</strong>
                                    <p class="text-muted small mb-0">Receba resumo semanal de seus investimentos</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <strong>Alertas de Mercado</strong>
                                    <p class="text-muted small mb-0">Notificações sobre mudanças no mercado</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <strong>Dicas Educacionais</strong>
                                    <p class="text-muted small mb-0">Conteúdo educativo sobre finanças</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h5>Notificações Push</h5>
                        
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <strong>Transações</strong>
                                    <p class="text-muted small mb-0">Notificar sobre novas transações</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <strong>Metas Atingidas</strong>
                                    <p class="text-muted small mb-0">Quando você alcançar suas metas</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Privacy Section -->
                <div class="settings-content d-none" id="privacy-section">
                    <h3 class="mb-4"><i class="bi bi-eye-slash me-2"></i>Privacidade</h3>
                    
                    <div class="settings-section">
                        <h5>Controle de Dados</h5>
                        
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <strong>Perfil Público</strong>
                                    <p class="text-muted small mb-0">Permitir que outros vejam seu perfil</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox">
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <strong>Compartilhar Estatísticas</strong>
                                    <p class="text-muted small mb-0">Permitir análise anônima de dados</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h5>Exportar Dados</h5>
                        <p class="text-muted">Baixe uma cópia de todos os seus dados</p>
                        
                        <button class="btn btn-outline-purple mt-3">
                            <i class="bi bi-download me-2"></i>Solicitar Exportação
                        </button>
                    </div>
                </div>

                <!-- Appearance Section -->
                <div class="settings-content d-none" id="appearance-section">
                    <h3 class="mb-4"><i class="bi bi-palette me-2"></i>Aparência</h3>
                    
                    <div class="settings-section">
                        <h5>Tema</h5>
                        <p class="text-muted">Escolha o tema da interface</p>
                        
                        <div class="row g-3 mt-3">
                            <div class="col-md-4">
                                <div class="p-3 rounded text-center" style="background: rgba(255, 255, 255, 0.05); cursor: pointer; border: 2px solid var(--primary-purple);">
                                    <i class="bi bi-moon-stars" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0"><strong>Escuro</strong></p>
                                    <small class="text-muted">Atual</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded text-center" style="background: rgba(255, 255, 255, 0.05); cursor: pointer;">
                                    <i class="bi bi-brightness-high" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0"><strong>Claro</strong></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded text-center" style="background: rgba(255, 255, 255, 0.05); cursor: pointer;">
                                    <i class="bi bi-circle-half" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0"><strong>Auto</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h5>Cor de Destaque</h5>
                        <p class="text-muted">Personalize a cor principal</p>
                        
                        <div class="d-flex gap-3 mt-3">
                            <div style="width: 50px; height: 50px; border-radius: 10px; background: linear-gradient(45deg, #8a2be2, #9370db); cursor: pointer; border: 3px solid white;"></div>
                            <div style="width: 50px; height: 50px; border-radius: 10px; background: linear-gradient(45deg, #2196F3, #03A9F4); cursor: pointer;"></div>
                            <div style="width: 50px; height: 50px; border-radius: 10px; background: linear-gradient(45deg, #4CAF50, #8BC34A); cursor: pointer;"></div>
                            <div style="width: 50px; height: 50px; border-radius: 10px; background: linear-gradient(45deg, #FF9800, #FF5722); cursor: pointer;"></div>
                            <div style="width: 50px; height: 50px; border-radius: 10px; background: linear-gradient(45deg, #E91E63, #F44336); cursor: pointer;"></div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Section -->
                <div class="settings-content d-none" id="subscription-section">
                    <h3 class="mb-4"><i class="bi bi-star me-2"></i>Assinatura</h3>
                    
                    <div class="settings-section">
                        <h5>Plano Atual</h5>
                        
                        <div class="mt-3 p-4 rounded" style="background: linear-gradient(135deg, #FFD700, #FFA500);">
                            <div class="d-flex justify-content-between align-items-center text-dark">
                                <div>
                                    <h4 class="mb-1">Premium Anual</h4>
                                    <p class="mb-0">Válido até 15 de Outubro de 2025</p>
                                </div>
                                <div class="text-end">
                                    <h3 class="mb-0">R$ 199,90</h3>
                                    <small>/ano</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button class="btn btn-outline-purple me-2">
                                <i class="bi bi-arrow-up-circle me-2"></i>Fazer Upgrade
                            </button>
                            <button class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-repeat me-2"></i>Alterar Plano
                            </button>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h5>Histórico de Pagamentos</h5>
                        
                        <div class="mt-3">
                            <div class="p-3 rounded mb-2" style="background: rgba(255, 255, 255, 0.05);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Premium Anual</strong>
                                        <p class="text-muted small mb-0">15 de Outubro de 2024</p>
                                    </div>
                                    <div class="text-end">
                                        <strong>R$ 199,90</strong>
                                        <p class="text-success small mb-0"><i class="bi bi-check-circle me-1"></i>Pago</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="danger-zone">
                        <h5 class="text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Cancelar Assinatura</h5>
                        <p class="text-muted">Você perderá acesso a todos os recursos premium</p>
                        <button class="btn btn-danger">
                            <i class="bi bi-x-circle me-2"></i>Cancelar Plano Premium
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-modern mt-5">
        <div class="container">
            <div class="text-center py-4">
                <p class="mb-0">&copy; 2025 FinançasJá. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showSection(sectionName) {
            // Hide all sections
            document.querySelectorAll('.settings-content').forEach(section => {
                section.classList.add('d-none');
            });
            
            // Remove active class from all nav items
            document.querySelectorAll('.settings-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(sectionName + '-section').classList.remove('d-none');
            
            // Add active class to clicked nav item
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>