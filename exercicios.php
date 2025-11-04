<?php
session_start();
require_once 'config/database.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Buscar resultados anteriores (nível, melhor nota e última tentativa)
$stmt = $pdo->prepare("
    SELECT nivel, MAX(acertos) AS melhor_nota, MAX(created_at) AS ultima_tentativa
    FROM quiz_results
    WHERE user_id = ?
    GROUP BY nivel
");
$stmt->execute([$user_id]);
$resultados_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Converter resultados em array indexado por nível
$resultados = [];
foreach ($resultados_raw as $r) {
    $resultados[$r['nivel']] = $r['melhor_nota'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercícios - FinançasJá</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <style>
        .nivel-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        .nivel-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.3);
            border-color: #667eea;
        }
        .nivel-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }
        .badge-nota {
            position: absolute;
            top: -10px;
            right: -10px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
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
                    <li class="nav-item"><a class="nav-link" href="home.php"><i class="bi bi-house-fill me-1"></i>Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="investments.php"><i class="bi bi-graph-up me-1"></i>Investimentos</a></li>
                    <li class="nav-item"><a class="nav-link" href="conversabot.php"><i class="bi bi-robot me-1"></i>Assistente IA</a></li>
                    <li class="nav-item"><a class="nav-link active" href="education.php"><i class="bi bi-mortarboard me-1"></i>Academia</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Mais</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="plans.php"><i class="bi bi-star me-2"></i>Planos</a></li>
                            <li><a class="dropdown-item" href="support1.php"><i class="bi bi-headset me-2"></i>Suporte</a></li>
                            <li><a class="dropdown-item" href="about.php"><i class="bi bi-info-circle me-2"></i>Sobre</a></li>
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
        <div class="container text-center">
            <div class="hero-content">
                <h1 class="display-5 fw-bold mb-3">Exercícios Financeiros</h1>
                <p class="lead mb-4">Teste seus conhecimentos e acompanhe sua evolução</p>
            </div>
        </div>
    </section>

    <div class="container mt-5 mb-5">
        <!-- Voltar -->
        <div class="mb-4">
            <a href="education.php" class="btn btn-outline-purple btn-modern">
                <i class="bi bi-arrow-left me-2"></i>Voltar para Academia
            </a>
        </div>

        <div class="row g-4">
            <?php
            $niveis_info = [
                1 => ['titulo'=>'Nível 1 - Iniciante','desc'=>'Noções básicas de finanças pessoais.','icon'=>'bi-1-circle-fill','color'=>'success'],
                2 => ['titulo'=>'Nível 2 - Básico','desc'=>'Planejamento e reserva de emergência.','icon'=>'bi-2-circle-fill','color'=>'info'],
                3 => ['titulo'=>'Nível 3 - Intermediário','desc'=>'Investimentos iniciais: renda fixa.','icon'=>'bi-3-circle-fill','color'=>'warning'],
                4 => ['titulo'=>'Nível 4 - Avançado','desc'=>'Renda variável e fundos.','icon'=>'bi-4-circle-fill','color'=>'danger'],
                5 => ['titulo'=>'Nível 5 - Expert','desc'=>'Diversificação e aposentadoria.','icon'=>'bi-5-circle-fill','color'=>'primary'],
                6 => ['titulo'=>'Nível 6 - Master','desc'=>'Finanças avançadas e análise de risco.','icon'=>'bi-6-circle-fill','color'=>'dark']
            ];

            foreach($niveis_info as $nivel => $info):
                $melhor_nota = $resultados[$nivel] ?? null;
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card-modern nivel-card h-100 position-relative" onclick="window.location.href='quiz.php?nivel=<?= $nivel ?>'">
                    <?php if ($melhor_nota !== null): ?>
                        <span class="badge-nota">
                            <i class="bi bi-star-fill me-1"></i><?= $melhor_nota ?>/5
                        </span>
                    <?php endif; ?>
                    <div class="card-body text-center p-4">
                        <div class="nivel-icon bg-<?= $info['color'] ?> bg-opacity-10">
                            <i class="<?= $info['icon'] ?> text-<?= $info['color'] ?>"></i>
                        </div>
                        <h5 class="fw-bold mb-3"><?= $info['titulo'] ?></h5>
                        <p class="text-muted mb-3"><?= $info['desc'] ?></p>
                        <div class="d-flex justify-content-center gap-3 mb-3">
                            <span class="badge bg-<?= $info['color'] ?>"><i class="bi bi-question-circle me-1"></i>5 perguntas</span>
                            <?php if ($melhor_nota !== null): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Concluído</span>
                            <?php endif; ?>
                        </div>
                        <button class="btn btn-<?= $info['color'] ?> btn-modern w-100">
                            <i class="bi bi-play-fill me-2"></i>Iniciar Quiz
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Estatísticas -->
        <div class="row mt-5 g-4">
            <div class="col-md-4">
                <div class="card-modern text-center p-4" style="background: linear-gradient(135deg,#667eea,#764ba2);color:white;">
                    <i class="bi bi-trophy-fill fs-1 mb-3"></i>
                    <h3 class="fw-bold mb-2"><?= count($resultados) ?>/6</h3>
                    <p class="mb-0">Níveis Completados</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-modern text-center p-4" style="background: linear-gradient(135deg,#28a745,#20c997);color:white;">
                    <i class="bi bi-star-fill fs-1 mb-3"></i>
                    <h3 class="fw-bold mb-2"><?= array_sum($resultados) ?></h3>
                    <p class="mb-0">Total de Acertos</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-modern text-center p-4" style="background: linear-gradient(135deg,#ffc107,#ff9800);color:white;">
                    <i class="bi bi-lightning-fill fs-1 mb-3"></i>
                    <h3 class="fw-bold mb-2">
                        <?= count($resultados) > 0 ? round((array_sum($resultados) / (count($resultados) * 5)) * 100) : 0 ?>%
                    </h3>
                    <p class="mb-0">Taxa de Acerto</p>
                </div>
            </div>
        </div>

        <!-- Dicas -->
        <div class="card-modern mt-5" style="background: rgba(102,126,234,0.1); border-left:5px solid #667eea;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Dicas para os Exercícios</h5>
                <ul class="mb-0">
                    <li>Complete as aulas correspondentes antes de fazer o quiz.</li>
                    <li>Você pode refazer os exercícios quantas vezes quiser.</li>
                    <li>Sua melhor nota em cada nível fica salva automaticamente.</li>
                    <li>Use o conhecimento das aulas para responder corretamente.</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
