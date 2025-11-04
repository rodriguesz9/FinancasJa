<?php
session_start();
require_once 'config/database.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Matéria e perguntas (mesmo conteúdo do código original)
$materia = [
    1 => "Noções básicas de finanças pessoais: orçamento, economia, despesas e prioridades financeiras.",
    2 => "Planejamento financeiro intermediário: poupança, reserva de emergência e juros simples.",
    3 => "Investimentos iniciais: CDB, LCI, LCA e conceitos de renda fixa.",
    4 => "Conceitos de renda variável: ações, fundos, riscos e diversificação.",
    5 => "Planejamento financeiro de longo prazo: aposentadoria, diversificação e estratégias avançadas.",
    6 => "Finanças avançadas: análise de risco e retorno, volatilidade e estratégias complexas de investimento."
];

$perguntas = [
    1 => [
        ["Qual é a importância de um orçamento pessoal?", "Para gastar sem limite", "Para controlar gastos e economizar", "Para aumentar dívidas", "Não é importante", 2],
        ["O que é uma despesa fixa?", "Gasto que varia todo mês", "Gasto mensal constante", "Investimento", "Doação", 2],
        ["Como economizar no dia a dia?", "Evitar gastos desnecessários", "Comprar tudo novo", "Não anotar despesas", "Aumentar cartão de crédito", 1],
        ["O que é prioridade financeira?", "Gastar primeiro em lazer", "Pagar dívidas e necessidades básicas", "Investir em luxo", "Ignorar contas", 2],
        ["O que significa poupar?", "Guardar dinheiro para futuro", "Gastar imediatamente", "Pedir empréstimo", "Ignorar salário", 1],
    ],
    2 => [
        ["O que é reserva de emergência?", "Dinheiro guardado para imprevistos", "Gastos com lazer", "Investimentos arriscados", "Doações", 1],
        ["Juros simples são calculados sobre:", "Montante total acumulado", "Valor inicial apenas", "Valor que cresce todo mês", "Não existe", 2],
        ["Poupança é indicada para:", "Curto prazo e segurança", "Renda alta imediata", "Gastos supérfluos", "Não serve para nada", 1],
        ["Planejamento financeiro ajuda a:", "Controlar gastos e alcançar metas", "Aumentar dívidas", "Evitar investimentos", "Viver sem contas", 1],
        ["Uma boa prática é:", "Anotar receitas e despesas", "Ignorar contas", "Comprar sem planejar", "Endividar-se", 1],
    ],
    3 => [
        ["Qual é uma característica do CDB?", "Renda fixa", "Renda variável", "Imobiliário", "Criptomoeda", 1],
        ["LCI e LCA são isentos de qual imposto?", "IPTU", "IR (Imposto de Renda)", "IPVA", "IOF", 2],
        ["Investir em renda fixa é indicado para:", "Baixo risco", "Alto risco", "Só especulação", "Não indicado", 1],
        ["Qual o principal objetivo de investir em CDB/LCI/LCA?", "Preservar e aumentar o capital", "Gastar rapidamente", "Evitar planejamento", "Endividar-se", 1],
        ["O que é rentabilidade?", "Lucro gerado pelo investimento", "Valor da dívida", "Salário mensal", "Gasto fixo", 1],
    ],
    4 => [
        ["O que caracteriza a renda variável?", "Retorno fixo", "Retorno que pode variar", "Sem risco", "Garantia de lucro", 2],
        ["Investir em ações envolve:", "Possibilidade de ganhos e perdas", "Lucro garantido", "Sem risco", "Imposto fixo", 1],
        ["O que é um fundo de investimento?", "Conjunto de recursos aplicados coletivamente", "Dinheiro guardado em casa", "Conta corrente", "Cheque especial", 1],
        ["Risco x Retorno significa:", "Quanto maior o risco, maior potencial de retorno", "Quanto menor o risco, maior o retorno sempre", "Não há relação", "Só se aplica a renda fixa", 1],
        ["O que é diversificação?", "Distribuir investimentos em diferentes produtos", "Investir tudo em um só", "Não investir", "Gastar sem controle", 1],
    ],
    5 => [
        ["Planejamento de longo prazo envolve:", "Aposentadoria e metas financeiras", "Só despesas do mês", "Ignorar investimentos", "Gastar o que sobra", 1],
        ["Diversificação ajuda a:", "Reduzir riscos", "Aumentar riscos", "Evitar lucros", "Gastar mais rápido", 1],
        ["Qual é a vantagem de investir a longo prazo?", "Potencial de maiores retornos", "Perda certa de dinheiro", "Sem vantagem", "Não há diferença", 1],
        ["Investimentos de longo prazo podem incluir:", "Ações, fundos, previdência privada", "Só poupança", "Só dinheiro em casa", "Crédito rotativo", 1],
        ["Planejar aposentadoria é importante porque:", "Garante segurança financeira futura", "Não é importante", "Só serve para ricos", "Gasta dinheiro desnecessário", 1],
    ],
    6 => [
        ["O que significa analisar risco x retorno?", "Comparar risco do investimento com potencial de lucro", "Não há risco", "Investimento garantido", "Só aplicar em poupança", 1],
        ["Investimentos complexos exigem:", "Conhecimento sobre mercado financeiro", "Ignorar informações", "Sorte", "Só dinheiro", 1],
        ["Risco sistemático é:", "Aquele que afeta todo o mercado", "Risco isolado de uma empresa", "Não existe", "Garantia de lucro", 1],
        ["O que é volatilidade?", "Variação dos preços do investimento", "Ganho certo", "Valor fixo", "Imposto cobrado", 1],
        ["Para minimizar riscos, um investidor deve:", "Diversificar investimentos", "Concentrar tudo em um só", "Ignorar mercado", "Endividar-se", 1],
    ],
];

$nivel = isset($_GET['nivel']) ? (int)$_GET['nivel'] : 1;
$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acertos = 0;
    $respostas_usuario = [];
    
    foreach($perguntas[$nivel] as $i => $q) {
        $resposta_correta = $q[5];
        $resposta_usuario = isset($_POST['resposta'][$i]) ? (int)$_POST['resposta'][$i] : 0;
        $respostas_usuario[$i] = [
            'usuario' => $resposta_usuario,
            'correta' => $resposta_correta,
            'acertou' => $resposta_usuario == $resposta_correta
        ];
        
        if ($resposta_usuario == $resposta_correta) {
            $acertos++;
        }
    }
    
    $resultado = $acertos;

    // Salvar resultado no banco
    $stmt = $pdo->prepare("INSERT INTO quiz_results (user_id, nivel, acertos, total, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $nivel, $acertos, count($perguntas[$nivel])]);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Nível <?= $nivel ?> - FinanceApp</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <style>
        .question-card {
            transition: all 0.3s ease;
        }
        
        .question-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .resultado-badge {
            font-size: 5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-modern sticky-top">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <i class="bi bi-gem me-2"></i>FinanceApp
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
                        <a class="nav-link active" href="education.php">
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

    <div class="container mt-5 mb-5">
        <?php if ($resultado === null): ?>
            <!-- Cabeçalho do Quiz -->
            <div class="text-center mb-5">
                <span class="badge badge-purple fs-5 mb-3">Nível <?= $nivel ?></span>
                <h1 class="display-5 fw-bold mb-3">Quiz de Finanças</h1>
                <p class="lead" style="color: #6c757d;"><?= $materia[$nivel] ?></p>
            </div>

            <!-- Formulário -->
            <form method="POST">
                <?php foreach($perguntas[$nivel] as $i => $q): ?>
                <div class="card-modern question-card mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <span class="badge bg-purple me-2"><?= $i+1 ?></span>
                            <?= $q[0] ?>
                        </h5>
                        
                        <?php for($j=1;$j<=4;$j++): ?>
                        <div class="form-check mb-3 p-3" style="border: 2px solid #e9ecef; border-radius: 10px; transition: all 0.3s ease;">
                            <input class="form-check-input" type="radio" name="resposta[<?= $i ?>]" id="q<?= $i ?>_<?= $j ?>" value="<?= $j ?>" required>
                            <label class="form-check-label w-100" for="q<?= $i ?>_<?= $j ?>" style="cursor: pointer;">
                                <?= $q[$j] ?>
                            </label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-purple btn-modern btn-lg px-5">
                        <i class="bi bi-check-circle me-2"></i>Enviar Respostas
                    </button>
                </div>
            </form>
            
        <?php else: ?>
            <!-- Resultado -->
            <div class="text-center">
                <div class="card-modern p-5">
                    <?php 
                    $percentual = ($resultado / count($perguntas[$nivel])) * 100;
                    $emoji = $percentual >= 80 ? '🎉' : ($percentual >= 60 ? '👍' : '📚');
                    $cor = $percentual >= 80 ? 'success' : ($percentual >= 60 ? 'warning' : 'danger');
                    ?>
                    
                    <div class="resultado-badge"><?= $emoji ?></div>
                    <h1 class="display-3 fw-bold text-<?= $cor ?> mb-4">
                        <?= $resultado ?>/<?= count($perguntas[$nivel]) ?>
                    </h1>
                    <h3 class="fw-bold mb-3">
                        <?php if($percentual >= 80): ?>
                            Excelente! Você domina o assunto!
                        <?php elseif($percentual >= 60): ?>
                            Bom trabalho! Continue estudando!
                        <?php else: ?>
                            Continue praticando! Revise as aulas.
                        <?php endif; ?>
                    </h3>
                    <p class="lead mb-4" style="color: #6c757d;">
                        Sua pontuação: <strong><?= $percentual ?>%</strong>
                    </p>
                    
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="exercicios.php" class="btn btn-outline-purple btn-modern">
                            <i class="bi bi-arrow-left me-2"></i>Voltar aos Exercícios
                        </a>
                        <a href="quiz.php?nivel=<?= $nivel ?>" class="btn btn-purple btn-modern">
                            <i class="bi bi-arrow-repeat me-2"></i>Tentar Novamente
                        </a>
                        <?php if($nivel < 6): ?>
                        <a href="quiz.php?nivel=<?= $nivel + 1 ?>" class="btn btn-success btn-modern">
                            <i class="bi bi-arrow-right me-2"></i>Próximo Nível
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Botão Voltar -->
        <div class="text-center mt-4">
            <a href="exercicios.php" class="btn btn-outline-secondary btn-modern">
                <i class="bi bi-arrow-left me-2"></i>Voltar para Exercícios
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Efeito hover nos radio buttons
        document.querySelectorAll('.form-check').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.borderColor = '#667eea';
                this.style.backgroundColor = 'rgba(102, 126, 234, 0.05)';
            });
            item.addEventListener('mouseleave', function() {
                if(!this.querySelector('input').checked) {
                    this.style.borderColor = '#e9ecef';
                    this.style.backgroundColor = 'transparent';
                }
            });
        });
    </script>
</body>
</html>