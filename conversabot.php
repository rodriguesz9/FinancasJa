<?php
session_start();
require_once 'config/database.php';
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistente IA - FinançasJá</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <style>
        .chat-container {
            height: 60vh;
            overflow-y: auto;
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            margin: 5px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .message {
            margin-bottom: 1.5rem;
        }
        .message.user {
            text-align: right;
        }
        .message.bot {
            text-align: left;
        }
        .message-bubble {
            display: inline-block;
            max-width: 80%;
            padding: 1rem 1.5rem;
            border-radius: 1.5rem;
            margin: 0 1rem;
            position: relative;
        }
        .message.user .message-bubble {
            background: linear-gradient(45deg, var(--primary-purple), var(--accent-purple));
            color: white;
            box-shadow: var(--shadow-purple);
        }
        .message.bot .message-bubble {
            background: rgba(255, 255, 255, 0.05);
            color: var(--light-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .message-bubble strong {
            font-weight: bold !important;
        }
        .quick-questions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        .quick-question-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--primary-purple);
            color: var(--primary-purple);
            padding: 0.75rem 1.25rem;
            border-radius: var(--border-radius-lg);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }
        .quick-question-btn:hover {
            background: var(--primary-purple);
            color: var(--light-bg);
            transform: translateY(-2px);
        }
        .typing-dots {
            display: inline-block;
        }
        .typing-dots span {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary-purple);
            margin: 0 2px;
            animation: typing 1.4s infinite;
        }
        .typing-dots span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-dots span:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
                opacity: 0.4;
            }
            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }
        
        .typing-cursor {
            animation: blink 1s infinite;
            color: var(--primary-purple);
            font-weight: bold;
        }
        
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
        
        .scroll-indicator {
            position: absolute;
            bottom: 80px;
            right: 20px;
            background: rgba(138, 43, 226, 0.9);
            color: white;
            padding: 8px 12px;
            border-radius: var(--border-radius-lg);
            font-size: 12px;
            cursor: pointer;
            transition: opacity 0.3s;
            opacity: 0;
            pointer-events: none;
            z-index: 10;
        }
        
        .scroll-indicator.show {
            opacity: 1;
            pointer-events: all;
        }

        .chat-input-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-lg);
            padding: 1rem;
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
        <section class="hero-modern py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-5 fw-bold mb-3">
                            <i class="bi bi-robot me-3"></i>Assistente Financeiro IA
                        </h1>
                        <p class="lead mb-0">Consultoria financeira inteligente disponível 24/7 para você</p>
                    </div>
                    <div class="col-lg-4 text-end">
                        <button class="btn btn-outline-purple btn-modern" onclick="clearChat()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Limpar Conversa
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Chat Section -->
        <section class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <!-- Chat Container -->
                        <div class="chat-container position-relative" id="chatContainer">
                            <!-- Welcome Message -->
                            <div class="message bot">
                                <div class="message-bubble">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(45deg, var(--primary-purple), var(--accent-purple)); display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-robot text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <strong>Assistente Financeiro IA</strong>
                                            <br><small class="text-muted">Especialista em Finanças Pessoais</small>
                                        </div>
                                    </div>
                                    <p class="mb-3">Olá! Sou seu consultor financeiro pessoal com IA. Posso te ajudar com:</p>
                                    <ul class="mb-0">
                                        <li>Planejamento financeiro personalizado</li>
                                        <li>Estratégias de investimento</li>
                                        <li>Análise de orçamento e gastos</li>
                                        <li>Dicas para economia e poupança</li>
                                        <li>Educação financeira avançada</li>
                                    </ul>
                                    <div class="mt-3 p-3" style="background: rgba(138, 43, 226, 0.1); border-radius: 0.5rem;">
                                        <small><i class="bi bi-info-circle me-2"></i>As respostas nem sempre são 100% precisas, procure fontes para confirma-las</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Scroll Indicator -->
                            <div class="scroll-indicator" id="scrollIndicator" onclick="scrollToBottom()">
                                <i class="bi bi-arrow-down"></i> Rolar para baixo
                            </div>
                        </div>
                        
                        <!-- Quick Questions -->
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">💡 Perguntas Populares</h6>
                            <div class="quick-questions">
                                <div class="quick-question-btn" onclick="sendQuickQuestion('Como começar a investir com pouco dinheiro?')">
                                    💰 Como investir com pouco dinheiro?
                                </div>
                                <div class="quick-question-btn" onclick="sendQuickQuestion('Qual a melhor estratégia para quitar dívidas?')">
                                    💳 Estratégia para quitar dívidas
                                </div>
                                <div class="quick-question-btn" onclick="sendQuickQuestion('Como criar um orçamento familiar eficiente?')">
                                    📊 Orçamento familiar eficiente
                                </div>
                                <div class="quick-question-btn" onclick="sendQuickQuestion('Vale a pena investir em criptomoedas?')">
                                    ₿ Investir em criptomoedas?
                                </div>
                                <div class="quick-question-btn" onclick="sendQuickQuestion('Como calcular minha reserva de emergência?')">
                                    🛡️ Reserva de emergência
                                </div>
                                <div class="quick-question-btn" onclick="sendQuickQuestion('Qual a diferença entre CDB e Tesouro Direto?')">
                                    🏦 CDB vs Tesouro Direto
                                </div>
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div class="chat-input-container mt-4">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-modern" id="messageInput" 
                                       placeholder="Digite sua pergunta sobre finanças..." onkeypress="handleKeyPress(event)"
                                       style="border: none; background: transparent; color: white;">
                                <button class="btn btn-purple btn-modern" onclick="sendMessage()">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </div>

                        <!-- AI Features -->
                        <div class="row mt-5 g-4">
                            <div class="col-md-4">
                                <div class="card-modern text-center">
                                    <div class="card-body p-4">
                                        <i class="bi bi-brain text-purple" style="font-size: 3rem;"></i>
                                        <h6 class="fw-bold mt-3">IA Avançada</h6>
                                        <p class="text-muted small mb-0">Criada para respostas precisas e contextualizadas, porém a IA também pode cometer erros, procure fontes para confirmar as informações em casos de dúvida</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-modern text-center">
                                    <div class="card-body p-4">
                                        <i class="bi bi-clock-history text-success" style="font-size: 3rem;"></i>
                                        <h6 class="fw-bold mt-3">24/7 Disponível</h6>
                                        <p class="text-muted small mb-0">Consultoria financeira a qualquer hora do dia ou da noite</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-modern text-center">
                                    <div class="card-body p-4">
                                        <i class="bi bi-shield-check text-info" style="font-size: 3rem;"></i>
                                        <h6 class="fw-bold mt-3">Informações Seguras</h6>
                                        <p class="text-muted small mb-0">Suas conversas são privadas e dados protegidos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

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
    <script>
        // Banco de conhecimento financeiro
        const financialKnowledge = {
            "orçamento": {
                keywords: ["orçamento", "planilha", "controle", "gastos", "receitas"],
                response: "Para fazer um orçamento mensal eficaz:\n\n1. **Liste todas suas receitas** (salário, freelances, etc.)\n2. **Anote todos os gastos fixos** (aluguel, financiamentos, etc.)\n3. **Controle gastos variáveis** (alimentação, transporte, lazer)\n4. **Use a regra 50-30-20**: 50% necessidades, 30% desejos, 20% poupança\n5. **Revise mensalmente** e ajuste conforme necessário\n\nDica: Use aplicativos ou planilhas para facilitar o controle!"
            },
            "investimentos": {
                keywords: ["investir", "investimento", "aplicação", "renda fixa", "ações"],
                response: "Para começar a investir:\n\n1. **Quite dívidas caras** (cartão de crédito, cheque especial)\n2. **Monte sua reserva de emergência** (6 meses de gastos)\n3. **Defina seus objetivos** (curto, médio e longo prazo)\n4. **Conheça seu perfil** (conservador, moderado, arrojado)\n5. **Comece pela renda fixa** (CDB, Tesouro Direto)\n6. **Diversifique gradualmente** (ações, fundos, REITs)\n\nLembre-se: invista apenas o que pode se dar ao luxo de perder!"
            },
            "cdb": {
                keywords: ["cdb", "poupança", "renda fixa", "banco"],
                response: "**CDB vs Poupança:**\n\n**CDB (Certificado de Depósito Bancário):**\n• Rentabilidade: Geralmente 100% a 130% do CDI\n• Tributação: IR regressivo (22,5% a 15%)\n• Liquidez: Pode ter carência\n• Garantia: FGC até R$ 250.000\n\n**Poupança:**\n• Rentabilidade: 70% da Selic (quando Selic ≤ 8,5%)\n• Tributação: Isenta\n• Liquidez: Diária\n• Garantia: FGC até R$ 250.000\n\n**Conclusão:** CDB geralmente rende mais que a poupança!"
            },
            "diversificação": {
                keywords: ["diversificar", "diversificação", "carteira", "risco"],
                response: "**Diversificação é fundamental!**\n\n**Por que diversificar?**\n• Reduz riscos da carteira\n• Protege contra volatilidade\n• Melhora relação risco-retorno\n\n**Como diversificar:**\n1. **Por classes de ativos** (renda fixa, ações, imóveis)\n2. **Por setores** (tecnologia, bancos, consumo)\n3. **Por geografia** (Brasil, EUA, Europa)\n4. **Por prazo** (curto, médio, longo)\n\n**Regra prática:** Não coloque mais de 5-10% em um único ativo!"
            },
            "reserva": {
                keywords: ["reserva", "emergência", "segurança"],
                response: "**Reserva de Emergência - Sua Base Financeira!**\n\n**Quanto guardar?**\n• 3-6 meses de gastos (pessoa física)\n• 6-12 meses (autônomos/empresários)\n\n**Onde investir:**\n• Poupança (liquidez imediata)\n• CDB com liquidez diária\n• Fundos DI\n• Tesouro Selic\n\n**Dicas importantes:**\n• Priorize liquidez sobre rentabilidade\n• Mantenha em conta separada\n• Use apenas para emergências reais\n• Reponha sempre que usar!"
            }
        };

        function findBestResponse(message) {
            const messageLower = message.toLowerCase();
            let bestMatch = null;
            let maxMatches = 0;

            for (const [topic, data] of Object.entries(financialKnowledge)) {
                const matches = data.keywords.filter(keyword => 
                    messageLower.includes(keyword.toLowerCase())
                ).length;

                if (matches > maxMatches) {
                    maxMatches = matches;
                    bestMatch = data.response;
                }
            }

            return bestMatch || "Desculpe, não tenho informações específicas sobre isso. Posso te ajudar com:\n\n• Orçamento e planejamento financeiro\n• Investimentos básicos\n• Reserva de emergência\n• Diversificação de carteira\n• Comparação entre produtos financeiros\n\nTente reformular sua pergunta ou escolha um dos tópicos acima!";
        }

        // Variáveis para controle de scroll
        let userIsScrolling = false;
        let scrollTimeout;

        function addMessage(message, isUser = false, animate = false) {
            const chatContainer = document.getElementById('chatContainer');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isUser ? 'user' : 'bot'}`;
            
            const bubbleDiv = document.createElement('div');
            bubbleDiv.className = 'message-bubble';
            
            if (isUser) {
                bubbleDiv.textContent = message;
                messageDiv.appendChild(bubbleDiv);
                chatContainer.appendChild(messageDiv);
                scrollToBottom();
            } else {
                // Para mensagens do bot, usar efeito typewriter se animate = true
                if (animate) {
                    bubbleDiv.innerHTML = '<span class="typing-cursor">|</span>';
                    messageDiv.appendChild(bubbleDiv);
                    chatContainer.appendChild(messageDiv);
                    
                    // Começar animação de digitação
                    typeWriter(bubbleDiv, message, 0);
                } else {
                    // Processar formatação: quebras de linha e negritos (** ou *)
                    const formattedMessage = formatMessage(message);
                    bubbleDiv.innerHTML = formattedMessage;
                    messageDiv.appendChild(bubbleDiv);
                    chatContainer.appendChild(messageDiv);
                    scrollToBottom();
                }
            }
        }

        // Função para formatar mensagem (processar ** e * para negrito)
        function formatMessage(text) {
            // Substituir quebras de linha
            let formatted = text.replace(/\n/g, '<br>');
            
            // Substituir **texto** por <strong>texto</strong>
            formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            
            // Substituir *texto* por <strong>texto</strong> (apenas se não foi processado antes)
            formatted = formatted.replace(/\*([^\*]+?)\*/g, '<strong>$1</strong>');
            
            return formatted;
        }

        function typeWriter(element, text, index) {
            // Configurações do efeito typewriter
            const typingSpeed = 20; // Velocidade em ms (menor = mais rápido)
            const pauseAtPunctuation = 100; // Pausa extra em pontuações
            
            if (index < text.length) {
                const currentChar = text.charAt(index);
                
                // Remover cursor temporário
                let currentText = element.innerHTML.replace('<span class="typing-cursor">|</span>', '');
                
                // Processar formatação especial (**, *, <br>)
                if (currentChar === '*') {
                    // Verificar se é ** (negrito duplo)
                    if (text.charAt(index + 1) === '*') {
                        // É ** - encontrar o próximo **
                        const endIndex = text.indexOf('**', index + 2);
                        if (endIndex !== -1 && endIndex !== index) {
                            const boldText = text.substring(index + 2, endIndex);
                            currentText += '<strong>' + boldText + '</strong>';
                            element.innerHTML = currentText + '<span class="typing-cursor">|</span>';
                            
                            // Pular para depois do ** final
                            setTimeout(() => typeWriter(element, text, endIndex + 2), 10);
                            return;
                        }
                    }
                    // Se não encontrou par de **, verificar * simples
                    else {
                        // É * simples - encontrar o próximo * (mas não **)
                        let nextAsterisk = index + 1;
                        while (nextAsterisk < text.length) {
                            if (text.charAt(nextAsterisk) === '*') {
                                // Verificar se não é **
                                if (text.charAt(nextAsterisk + 1) !== '*' && 
                                    (nextAsterisk === 0 || text.charAt(nextAsterisk - 1) !== '*')) {
                                    const boldText = text.substring(index + 1, nextAsterisk);
                                    currentText += '<strong>' + boldText + '</strong>';
                                    element.innerHTML = currentText + '<span class="typing-cursor">|</span>';
                                    setTimeout(() => typeWriter(element, text, nextAsterisk + 1), 10);
                                    return;
                                }
                            }
                            nextAsterisk++;
                        }
                    }
                }
                
                // Processar quebra de linha
                if (currentChar === '\n') {
                    currentText += '<br>';
                    element.innerHTML = currentText + '<span class="typing-cursor">|</span>';
                    setTimeout(() => typeWriter(element, text, index + 1), 10);
                    return;
                }
                
                // Processar HTML tags para não quebrar a formatação
                if (currentChar === '<') {
                    const tagEnd = text.indexOf('>', index);
                    if (tagEnd !== -1) {
                        const htmlTag = text.substring(index, tagEnd + 1);
                        currentText += htmlTag;
                        element.innerHTML = currentText + '<span class="typing-cursor">|</span>';
                        setTimeout(() => typeWriter(element, text, tagEnd + 1), 10);
                        return;
                    }
                }
                
                currentText += currentChar;
                element.innerHTML = currentText + '<span class="typing-cursor">|</span>';
                
                // Scroll suave durante digitação (apenas se próximo do final)
                const chatContainer = document.getElementById('chatContainer');
                const isNearBottom = chatContainer.scrollHeight - chatContainer.scrollTop - chatContainer.clientHeight < 100;
                if (isNearBottom && !userIsScrolling) {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }
                
                // Determinar velocidade baseada no caractere
                let speed = typingSpeed;
                if (['.', '!', '?', ':', ';'].includes(currentChar)) {
                    speed += pauseAtPunctuation;
                } else if ([','].includes(currentChar)) {
                    speed += pauseAtPunctuation / 2;
                }
                
                setTimeout(() => typeWriter(element, text, index + 1), speed);
            } else {
                // Remover cursor final e fazer scroll final
                element.innerHTML = element.innerHTML.replace('<span class="typing-cursor">|</span>', '');
                
                // Scroll final suave
                setTimeout(() => {
                    if (!userIsScrolling) {
                        scrollToBottom(true);
                    }
                }, 200);
            }
        }

        function scrollToBottom(smooth = true) {
            const chatContainer = document.getElementById('chatContainer');
            if (smooth) {
                chatContainer.scrollTo({
                    top: chatContainer.scrollHeight,
                    behavior: 'smooth'
                });
            } else {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }

        function handleScroll() {
            userIsScrolling = true;
            clearTimeout(scrollTimeout);
            
            // Depois de 2 segundos sem scroll, considera que parou
            scrollTimeout = setTimeout(() => {
                userIsScrolling = false;
            }, 2000);
            
            updateScrollIndicator();
        }

        function updateScrollIndicator() {
            const chatContainer = document.getElementById('chatContainer');
            const scrollIndicator = document.getElementById('scrollIndicator');
            
            if (chatContainer && scrollIndicator) {
                const isAtBottom = chatContainer.scrollHeight - chatContainer.scrollTop - chatContainer.clientHeight < 50;
                
                if (userIsScrolling && !isAtBottom) {
                    scrollIndicator.classList.add('show');
                } else {
                    scrollIndicator.classList.remove('show');
                }
            }
        }

        function showTyping() {
            const chatContainer = document.getElementById('chatContainer');
            
            // Criar elemento de typing indicator dentro do chat
            const typingDiv = document.createElement('div');
            typingDiv.className = 'message bot';
            typingDiv.id = 'typingMessage';
            
            const bubbleDiv = document.createElement('div');
            bubbleDiv.className = 'message-bubble';
            
            bubbleDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(138, 43, 226, 0.2); display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-robot text-purple"></i>
                        </div>
                    </div>
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            `;
            
            typingDiv.appendChild(bubbleDiv);
            chatContainer.appendChild(typingDiv);
            
            if (!userIsScrolling) {
                scrollToBottom();
            }
        }

        function hideTyping() {
            const typingMessage = document.getElementById('typingMessage');
            if (typingMessage) {
                typingMessage.remove();
            }
        }

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (message === '') return;
            
            // Add user message
            addMessage(message, true);
            messageInput.value = '';
            
            // Show typing indicator dentro do chat
            showTyping();
            
            // Send to API
            fetch('api/chatbot.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                hideTyping();
                if (data.success) {
                    let response = data.response;
                    
                    // Adicionar badge da fonte
                    if (data.source === 'gemini') {
                        response += '<br><small class="badge bg-success mt-2"><i class="bi bi-robot"></i></small>';
                    } else if (data.source === 'local') {
                        response += '<br><small class="badge bg-secondary mt-2"><i class="bi bi-cpu"></i> Respostas Locais</small>';
                    } else if (data.source === 'fallback_gemini_error') {
                        response += '<br><small class="badge bg-warning mt-2"><i class="bi bi-exclamation-triangle"></i> Backup (Erro Gemini)</small>';
                        console.warn('Erro na IA:', data.error_msg);
                    } else {
                        response += '<br><small class="badge bg-danger mt-2"><i class="bi bi-x-circle"></i> Sistema de Backup</small>';
                    }
                    
                    // Usar efeito typewriter para mensagens do bot
                    addMessage(response, false, true);
                } else {
                    addMessage('Desculpe, ocorreu um erro. Tente novamente em alguns instantes, ou digite uma pergunta relacionada aos temas.', false, true);
                }
            })
            .catch(error => {
                hideTyping();
                console.error('Erro:', error);
                addMessage('Erro de conexão. Verifique sua internet e tente novamente.', false, true);
            });
        }

        function sendQuickQuestion(question) {
            document.getElementById('messageInput').value = question;
            sendMessage();
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }

        function clearChat() {
            const chatContainer = document.getElementById('chatContainer');
            chatContainer.innerHTML = `
                <div class="message bot">
                    <div class="message-bubble">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(45deg, var(--primary-purple), var(--accent-purple)); display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-robot text-white"></i>
                                </div>
                            </div>
                            <div>
                                <strong>Assistente Financeiro IA</strong>
                                <br><small class="text-muted">Especialista em Finanças Pessoais</small>
                            </div>
                        </div>
                        <p class="mb-3">Olá! Sou seu consultor financeiro pessoal com IA. Posso te ajudar com:</p>
                        <ul class="mb-0">
                            <li>Planejamento financeiro personalizado</li>
                            <li>Estratégias de investimento</li>
                            <li>Análise de orçamento e gastos</li>
                            <li>Dicas para economia e poupança</li>
                            <li>Educação financeira avançada</li>
                        </ul>
                        <div class="mt-3 p-3" style="background: rgba(138, 43, 226, 0.1); border-radius: 0.5rem;">
                            <small><i class="bi bi-info-circle me-2"></i>As respostas nem sempre são 100% precisas, procure fontes para confirma-las</small>
                        </div>
                    </div>
                </div>
                <div class="scroll-indicator" id="scrollIndicator" onclick="scrollToBottom()">
                    <i class="bi bi-arrow-down"></i> Rolar para baixo
                </div>
            `;
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Adicionar listener de scroll
            const chatContainer = document.getElementById('chatContainer');
            chatContainer.addEventListener('scroll', handleScroll);
            
            // Auto-focus on input
            document.getElementById('messageInput').focus();
            
            // Atualizar indicador de scroll periodicamente
            setInterval(updateScrollIndicator, 500);
        });
    </script>
</body>
</html>