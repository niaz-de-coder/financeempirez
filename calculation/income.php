<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Database Connection
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'finance';

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_email = $_SESSION['user_email'];

// Define Revenue and Expense categories based on your list
$revenue_titles = [
    "Sales revenue", "Service revenue", "Interest income", "Rental income",
    "Commission income", "Dividend income", "Fees earned", "Royalties",
    "Subscription revenue", "Advertising revenue", "Consulting income",
    "Franchise income", "Gain on sale of assets", "Foreign exchange gain",
    "Revaluation gain", "Discount received"
];

$expense_titles = [
    "Bad debts", "Inventory loss", "Sales returns", "Sales discounts", "Cost of goods sold",
    "Purchase returns", "Discount allowed", "Salaries expense", "Wages expense", "Rent expense",
    "Utilities expense", "Office supplies expense", "Depreciation expense", "Amortization expense",
    "Insurance expense", "Advertising expense", "Transportation expense", "Maintenance expense",
    "Interest expense", "Telephone expense", "Internet expense", "Printing and stationery expense",
    "Tax expense", "Bad debts expense", "Loss on sale of assets", "Foreign exchange loss",
    "Bank charges", "Legal fees", "Audit fees", "Training expense", "Travel expense",
    "Entertainment expense", "Security expense", "Cleaning expense", "Factory overhead",
    "Direct labor", "Indirect labor", "Indirect materials", "Factory rent", "Factory utilities",
    "Depreciation on assets", "Amortization on assets"
];

// Helper function to get net balance for a specific account
function getAccountBalance($conn, $email, $title) {
    $title_esc = mysqli_real_escape_string($conn, $title);
    $d_query = "SELECT SUM(transaction_amount) as d_total FROM Journal WHERE email = '$email' AND debit_account_title = '$title_esc'";
    $c_query = "SELECT SUM(transaction_amount) as c_total FROM Journal WHERE email = '$email' AND credit_account_title = '$title_esc'";
    
    $d_val = $conn->query($d_query)->fetch_assoc()['d_total'] ?? 0;
    $c_val = $conn->query($c_query)->fetch_assoc()['c_total'] ?? 0;
    
    return $d_val - $c_val;
}

$total_revenue = 0;
$total_expense = 0;
$revenue_data = [];
$expense_data = [];

// Calculate Revenues
foreach ($revenue_titles as $title) {
    $balance = getAccountBalance($conn, $user_email, $title);
    // Revenues usually have Credit balances (Negative in our Dr-Cr logic)
    if ($balance != 0) {
        $abs_balance = abs($balance);
        $revenue_data[$title] = $abs_balance;
        $total_revenue += $abs_balance;
    }
}

// Calculate Expenses
foreach ($expense_titles as $title) {
    $balance = getAccountBalance($conn, $user_email, $title);
    // Expenses usually have Debit balances (Positive in our Dr-Cr logic)
    if ($balance != 0) {
        $abs_balance = abs($balance);
        $expense_data[$title] = $abs_balance;
        $total_expense += $abs_balance;
    }
}

$net_income = $total_revenue - $total_expense;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Statement | Finance Empirez</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="flex min-h-screen">

    <aside class="w-72 bg-white border-r border-slate-100 flex flex-col fixed h-full z-10">
        <div class="p-8 text-xl font-extrabold tracking-tighter flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white text-sm">F</div>
            <span>FINANCE<span class="text-blue-600">Empirez</span></span>
        </div>
        <nav class="flex-grow px-4 space-y-2">
            <a href="dashboard.php" class="flex items-center gap-4 px-4 py-3.5 text-slate-500 rounded-2xl font-semibold hover:bg-slate-50 transition">
                <i class="fa-solid fa-house-chimney w-5"></i> Dashboard
            </a>
            <a href="trialbalance.php" class="flex items-center gap-4 px-4 py-3.5 text-slate-500 rounded-2xl font-semibold hover:bg-slate-50 transition">
                <i class="fa-solid fa-scale-balanced w-5"></i> Trial Balance
            </a>
            <a href="income.php" class="flex items-center gap-4 px-4 py-3.5 bg-blue-50 text-blue-600 rounded-2xl font-bold transition">
                <i class="fa-solid fa-chart-line w-5"></i> Income Statement
            </a>
        </nav>
    </aside>

    <main class="flex-grow ml-72 p-10">
        <header class="mb-10 flex justify-between items-end border-b border-slate-200 pb-6">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Income Statement</h1>
                <p class="text-slate-500 mt-1">Performance report for the current period.</p>
            </div>
            <div class="bg-slate-900 text-white px-6 py-3 rounded-2xl text-sm font-bold shadow-xl">
                Fiscal Year 2025-26
            </div>
        </header>

        <div class="max-w-4xl mx-auto bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden p-12">
            
            <section class="mb-12">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-blue-600 mb-6 flex items-center gap-3">
                    <span class="w-8 h-[2px] bg-blue-600"></span> Operating Revenues
                </h2>
                <div class="space-y-4">
                    <?php foreach ($revenue_data as $title => $amount): ?>
                    <div class="flex justify-between items-center text-slate-700">
                        <span class="font-medium"><?php echo $title; ?></span>
                        <span class="font-bold">$<?php echo number_format($amount, 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                        <span class="text-lg font-bold text-slate-900">Total Revenues</span>
                        <span class="text-lg font-black text-slate-900 underline decoration-4 decoration-blue-200">$<?php echo number_format($total_revenue, 2); ?></span>
                    </div>
                </div>
            </section>

            <section class="mb-12">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-red-500 mb-6 flex items-center gap-3">
                    <span class="w-8 h-[2px] bg-red-500"></span> Operating Expenses
                </h2>
                <div class="space-y-4">
                    <?php foreach ($expense_data as $title => $amount): ?>
                    <div class="flex justify-between items-center text-slate-600">
                        <span><?php echo $title; ?></span>
                        <span class="font-semibold text-red-400">($<?php echo number_format($amount, 2); ?>)</span>
                    </div>
                    <?php endforeach; ?>
                    <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                        <span class="text-lg font-bold text-slate-900">Total Expenses</span>
                        <span class="text-lg font-black text-red-600 underline decoration-4 decoration-red-100">($<?php echo number_format($total_expense, 2); ?>)</span>
                    </div>
                </div>
            </section>

            <div class="<?php echo $net_income >= 0 ? 'bg-emerald-600' : 'bg-red-600'; ?> p-8 rounded-3xl text-white shadow-2xl flex justify-between items-center">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest opacity-80 mb-1">Final Calculation</p>
                    <h3 class="text-3xl font-black italic uppercase"><?php echo $net_income >= 0 ? 'Net Income' : 'Net Loss'; ?></h3>
                </div>
                <div class="text-right">
                    <span class="text-4xl font-black tracking-tighter">
                        $<?php echo number_format(abs($net_income), 2); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center text-slate-400 text-xs font-medium uppercase tracking-widest">
            Generated by Finance Empirez Accounting Engine
        </div>
    </main>

</body>
</html>