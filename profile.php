<?php
session_start();
require_once 'config/database.php';
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'] ?? 'usuario@email.com';

requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - FinançasJá</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <style>
        .profile-header {
            background: linear-gradient(135deg, var(--primary-purple), var(--accent-purple));
            padding: 3rem 0;
            border-radius: var(--border-radius-lg);
            margin-bottom: 2rem;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 1rem;
            border: 4px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-purple);
            box-shadow: 0 10px 30px rgba(138, 43, 226, 0.3);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .info-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-lg);
            padding: 2rem;
        }
        
        .info-item {
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .badge-premium {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: #000;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            display: inline-block;
        }
        
        .achievement-badge {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .achievement-badge:hover {
            transform: scale(1.1) rotate(5deg);
            border-color: var(--primary-purple);
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
        <!-- Profile Header -->
        <div class="profile-header text-center">
            <div class="profile-avatar" title="Alterar foto">
                <i class="bi bi-person-circle"></i>
            </div>
            <h1 class="text-white mb-2"><?= htmlspecialchars($user_name) ?></h1>
            <p class="text-white-50 mb-3"><?= htmlspecialchars($user_email) ?></p>
            <span class="badge-premium">
                <i class="bi bi-star-fill me-2"></i>Membro Premium
            </span>
        </div>

        <!-- Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon mx-auto" style="background: linear-gradient(45deg, #4CAF50, #8BC34A);">
                        <i class="bi bi-calendar-check text-white"></i>
                    </div>
                    <h3 class="mb-1">120</h3>
                    <p class="text-muted mb-0">Dias Ativos</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon mx-auto" style="background: linear-gradient(45deg, #2196F3, #03A9F4);">
                        <i class="bi bi-graph-up-arrow text-white"></i>
                    </div>
                    <h3 class="mb-1">R$ 45.2K</h3>
                    <p class="text-muted mb-0">Patrimônio</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon mx-auto" style="background: linear-gradient(45deg, #FF9800, #FF5722);">
                        <i class="bi bi-trophy text-white"></i>
                    </div>
                    <h3 class="mb-1">15</h3>
                    <p class="text-muted mb-0">Conquistas</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon mx-auto" style="background: linear-gradient(45deg, #9C27B0, #E91E63);">
                        <i class="bi bi-lightning-charge text-white"></i>
                    </div>
                    <h3 class="mb-1">850</h3>
                    <p class="text-muted mb-0">Pontos XP</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Informações Pessoais -->
            <div class="col-lg-6">
                <div class="info-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="bi bi-person-badge me-2"></i>Informações Pessoais
                        </h4>
                        <button class="btn btn-sm btn-outline-purple">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </button>
                    </div>
                    
                    <div class="info-item">
                        <div class="row">
                            <div class="col-4 text-muted">Nome:</div>
                            <div class="col-8"><strong><?= htmlspecialchars($user_name) ?></strong></div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="row">
                            <div class="col-4 text-muted">Email:</div>
                            <div class="col-8"><?= htmlspecialchars($user_email) ?></div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="row">
                            <div class="col-4 text-muted">Membro desde:</div>
                            <div class="col-8">Janeiro de 2024</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="row">
                            <div class="col-4 text-muted">Plano:</div>
                            <div class="col-8">
                                <span class="badge bg-warning text-dark">Premium Anual</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="row">
                            <div class="col-4 text-muted">ID do Usuário:</div>
                            <div class="col-8"><code><?= $user_id ?></code></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conquistas -->
            <div class="col-lg-6">
                <div class="info-card">
                    <h4 class="mb-4">
                        <i class="bi bi-trophy me-2"></i>Conquistas Recentes
                    </h4>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-4 text-center">
                            <div class="achievement-badge mx-auto mb-2" style="background: linear-gradient(45deg, #FFD700, #FFA500);">
                                🏆
                            </div>
                            <small>Primeiro<br>Investimento</small>
                        </div>
                        <div class="col-4 text-center">
                            <div class="achievement-badge mx-auto mb-2" style="background: linear-gradient(45deg, #4CAF50, #8BC34A);">
                                💰
                            </div>
                            <small>Economizador<br>Nível 3</small>
                        </div>
                        <div class="col-4 text-center">
                            <div class="achievement-badge mx-auto mb-2" style="background: linear-gradient(45deg, #2196F3, #03A9F4);">
                                📚
                            </div>
                            <small>Estudante<br>Dedicado</small>
                        </div>
                        <div class="col-4 text-center">
                            <div class="achievement-badge mx-auto mb-2" style="background: linear-gradient(45deg, #9C27B0, #E91E63);">
                                🎯
                            </div>
                            <small>Meta<br>Atingida</small>
                        </div>
                        <div class="col-4 text-center">
                            <div class="achievement-badge mx-auto mb-2" style="background: linear-gradient(45deg, #FF5722, #F44336);">
                                🔥
                            </div>
                            <small>Sequência<br>30 Dias</small>
                        </div>
                        <div class="col-4 text-center">
                            <div class="achievement-badge mx-auto mb-2" style="background: linear-gradient(45deg, #00BCD4, #009688);">
                                💎
                            </div>
                            <small>Investidor<br>Premium</small>
                        </div>
                    </div>

                    <div class="mt-4 p-3 rounded" style="background: rgba(138, 43, 226, 0.1);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Próxima Conquista</strong>
                                <p class="mb-0 text-muted small">Diversificador Expert</p>
                            </div>
                            <div class="text-end">
                                <div class="progress" style="width: 120px; height: 8px;">
                                    <div class="progress-bar bg-purple" style="width: 75%"></div>
                                </div>
                                <small class="text-muted">75% completo</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Atividade Recente -->
        <div class="info-card mt-4">
            <h4 class="mb-4">
                <i class="bi bi-clock-history me-2"></i>Atividade Recente
            </h4>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 rounded" style="background: rgba(255, 255, 255, 0.03);">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(45deg, #4CAF50, #8BC34A); display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-plus-lg text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <strong>Novo Investimento</strong>
                                <p class="mb-0 text-muted small">CDB Banco XYZ - R$ 5.000</p>
                            </div>
                            <small class="text-muted">2h atrás</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="p-3 rounded" style="background: rgba(255, 255, 255, 0.03);">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(45deg, #2196F3, #03A9F4); display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-book text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <strong>Curso Concluído</strong>
                                <p class="mb-0 text-muted small">Fundamentos de Renda Fixa</p>
                            </div>
                            <small class="text-muted">1d atrás</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="p-3 rounded" style="background: rgba(255, 255, 255, 0.03);">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(45deg, #FF9800, #FF5722); display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-trophy text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <strong>Conquista Desbloqueada</strong>
                                <p class="mb-0 text-muted small">Sequência de 30 dias</p>
                            </div>
                            <small class="text-muted">2d atrás</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="p-3 rounded" style="background: rgba(255, 255, 255, 0.03);">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(45deg, #9C27B0, #E91E63); display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-chat-dots text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <strong>Consultoria IA</strong>
                                <p class="mb-0 text-muted small">15 perguntas respondidas</p>
                            </div>
                            <small class="text-muted">3d atrás</small>
                        </div>
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
</body>
</html>