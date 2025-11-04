<?php
session_start();
require_once 'config/database.php';

// Verificar se está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit();
}

$user_id = $_SESSION['user_id'];
$period = $_GET['period'] ?? 'month';

// Calcular intervalo de datas baseado no período
switch ($period) {
    case 'week':
        $days = 7;
        break;
    case 'year':
        $days = 365;
        break;
    case 'month':
    default:
        $days = 30;
        break;
}

$start_date = date('Y-m-d', strtotime("-$days days"));
$today = date('Y-m-d');

try {
    // 1. Calcular saldo inicial (antes do período)
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END), 0) as total_receitas,
            COALESCE(SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END), 0) as total_despesas
        FROM transactions 
        WHERE user_id = ? AND data_transacao < ?
    ");
    $stmt->execute([$user_id, $start_date]);
    $initial = $stmt->fetch(PDO::FETCH_ASSOC);
    $startBalance = $initial['total_receitas'] - $initial['total_despesas'];

    // 2. Buscar transações do período agrupadas por dia
    $stmt = $pdo->prepare("
        SELECT 
            DATE(data_transacao) as date,
            SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END) as income,
            SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END) as expense
        FROM transactions 
        WHERE user_id = ? 
            AND data_transacao >= ? 
            AND data_transacao <= ?
        GROUP BY DATE(data_transacao)
        ORDER BY date ASC
    ");
    $stmt->execute([$user_id, $start_date, $today]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Criar array indexado por data
    $transactionsByDate = [];
    foreach ($transactions as $trans) {
        $transactionsByDate[$trans['date']] = [
            'income' => (float)$trans['income'],
            'expense' => (float)$trans['expense']
        ];
    }

    // 4. Gerar dados para todos os dias do período
    $labels = [];
    $balanceData = [];
    $incomeData = [];
    $expenseData = [];
    $currentBalance = $startBalance;

    $current = new DateTime($start_date);
    $end = new DateTime($today);
    $interval = new DateInterval('P1D');
    
    // Para o período anual, vamos agrupar por mês
    if ($period === 'year') {
        // Agrupar por mês
        $monthlyData = [];
        
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $monthKey = $current->format('Y-m');
            
            $trans = $transactionsByDate[$dateStr] ?? ['income' => 0, 'expense' => 0];
            $currentBalance += $trans['income'] - $trans['expense'];
            
            if (!isset($monthlyData[$monthKey])) {
                $monthlyData[$monthKey] = [
                    'label' => $current->format('M/y'),
                    'balance' => $currentBalance,
                    'income' => 0,
                    'expense' => 0
                ];
            }
            
            $monthlyData[$monthKey]['income'] += $trans['income'];
            $monthlyData[$monthKey]['expense'] += $trans['expense'];
            $monthlyData[$monthKey]['balance'] = $currentBalance;
            
            $current->add($interval);
        }
        
        foreach ($monthlyData as $data) {
            $labels[] = $data['label'];
            $balanceData[] = round($data['balance'], 2);
            $incomeData[] = round($data['income'], 2);
            $expenseData[] = round($data['expense'], 2);
        }
    } else {
        // Dados diários para semana e mês
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $trans = $transactionsByDate[$dateStr] ?? ['income' => 0, 'expense' => 0];
            
            $currentBalance += $trans['income'] - $trans['expense'];
            
            $labels[] = $current->format('d/m');
            $balanceData[] = round($currentBalance, 2);
            $incomeData[] = round($trans['income'], 2);
            $expenseData[] = round($trans['expense'], 2);
            
            $current->add($interval);
        }
    }

    // 5. Retornar JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'labels' => $labels,
        'balanceData' => $balanceData,
        'incomeData' => $incomeData,
        'expenseData' => $expenseData,
        'startBalance' => round($startBalance, 2),
        'currentBalance' => round($currentBalance, 2)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao buscar dados: ' . $e->getMessage()
    ]);
}
?>