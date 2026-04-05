<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'finance';
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

$user_email = $_SESSION['user_email'];

// --- SECTION 1: CALCULATE NET INCOME (Reusing your Income Statement Logic) ---
$revenue_titles = [
    "Sales revenue", "Service revenue", "Interest income", "Rental income", "Commission income", 
    "Dividend income", "Fees earned", "Royalties", "Subscription revenue", "Advertising revenue", 
    "Consulting income", "Franchise income", "Gain on sale of assets", "Foreign exchange gain", 
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

function getBalance($conn, $email, $title) {
    $t = mysqli_real_escape_string($conn, $title);
    $d = $conn->query("SELECT SUM(transaction_amount) as total FROM Journal WHERE email = '$email' AND debit_account_title = '$t'")->fetch_assoc()['total'] ?? 0;
    $c = $conn->query("SELECT SUM(transaction_amount) as total FROM Journal WHERE email = '$email' AND credit_account_title = '$t'")->fetch_assoc()['total'] ?? 0;
    return $d - $c;
}

$net_revenue = 0;
foreach ($revenue_titles as $r) { $net_revenue += abs(getBalance($conn, $user_email, $r)); }

$net_expense = 0;
foreach ($expense_titles as $e) { $net_expense += abs(getBalance($conn, $user_email, $e)); }

$net_income = $net_revenue - $net_expense;

// --- SECTION 2: CALCULATE DIVIDENDS ---
$dividend_titles = [
    "Cash dividends", "Stock dividends", "Property dividends", "Scrip dividends",
    "Liquidating dividends", "Interim dividends", "Final dividends",
    "Preferred dividends", "Special dividends", "Drawings"
];

$total_dividends = 0;
$dividend_list = [];
foreach ($dividend_titles as $d_title) {
    $bal = abs(getBalance($conn, $user_email, $d_title));
    if ($bal > 0) {
        $dividend_list[$d_title] = $bal;
        $total_dividends += $bal;
    }
}

// Final Retained Earnings Calculation
// Note: In a full system, you would also fetch "Beginning Retained Earnings" from a balance sheet table.
$beginning_retained = 0; 
$ending_retained = ($beginning_retained + $net_income) - $total_dividends;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Retained Earnings | Finance Empirez</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 flex">

    <aside class="w-72 bg-white border-r border-slate-100 fixed h-full p-8">
        <div class="text-xl font-extrabold tracking-tighter mb-10">
            <span class="text-blue-600">F</span>INANCE Empirez
        </div>
        <nav class="space-y-2">
            <a href="dashboard.php" class="block px-4 py-3 text-slate-500 font-semibold hover:bg-slate-50 rounded-xl">Dashboard</a>
            <a href="income.php" class="block px-4 py-3 text-slate-500 font-semibold hover:bg-slate-50 rounded-xl">Income Statement</a>
            <a href="retained.php" class="block px-4 py-3 bg-blue-50 text-blue-600 font-bold rounded-xl">Retained Earnings</a>
        </nav>
    </aside>

    <main class="ml-72 flex-grow p-12">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-3xl font-black text-slate-900 uppercase tracking-widest">Statement of Retained Earnings</h1>
                <p class="text-slate-500 font-bold mt-2 italic">For the Period Ended <?php echo date('F d, Y'); ?></p>
            </div>

            <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-16">
                <table class="w-full text-left border-collapse">
                    <tbody class="text-slate-700">
                        <tr class="border-b border-slate-50">
                            <td class="py-6 font-semibold">Retained Earnings, Beginning</td>
                            <td class="py-6 text-right font-mono text-slate-400">$<?php echo number_format($beginning_retained, 2); ?></td>
                        </tr>

                        <tr class="border-b border-slate-50">
                            <td class="py-6 font-semibold flex items-center gap-3">
                                <i class="fa-solid fa-plus-circle text-emerald-500"></i> Add: Net Income
                            </td>
                            <td class="py-6 text-right font-mono text-emerald-600">
                                <?php echo number_format($net_income, 2); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Subtotal</td>
                            <td class="py-4 text-right font-mono font-bold">$<?php echo number_format($beginning_retained + $net_income, 2); ?></td>
                        </tr>

                        <?php if($total_dividends > 0): ?>
                            <?php foreach($dividend_list as $name => $amount): ?>
                            <tr class="text-sm text-slate-500">
                                <td class="py-2 pl-8 italic">Less: <?php echo $name; ?></td>
                                <td class="py-2 text-right font-mono">(<?php echo number_format($amount, 2); ?>)</td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="text-sm text-slate-400">
                                <td class="py-4 pl-8 italic">No dividends declared this period</td>
                                <td class="py-4 text-right font-mono">$0.00</td>
                            </tr>
                        <?php endif; ?>

                        <tr class="border-t-2 border-slate-900">
                            <td class="pt-8 pb-2 text-xl font-black text-slate-900">Retained Earnings, Ending</td>
                            <td class="pt-8 pb-2 text-right text-2xl font-black text-blue-600">
                                <span class="border-b-4 border-double border-blue-600">
                                    $<?php echo number_format($ending_retained, 2); ?>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-12 grid grid-cols-2 gap-6">
                <div class="bg-emerald-50 p-6 rounded-3xl border border-emerald-100">
                    <p class="text-xs font-bold text-emerald-600 uppercase mb-1">Profit Retention</p>
                    <p class="text-slate-700 text-sm leading-relaxed">
                        The business retained <strong><?php echo $net_income > 0 ? round(($ending_retained/$net_income)*100) : 0; ?>%</strong> of this period's earnings.
                    </p>
                </div>
                <div class="bg-blue-50 p-6 rounded-3xl border border-blue-100">
                    <p class="text-xs font-bold text-blue-600 uppercase mb-1">Accounting Tip</p>
                    <p class="text-slate-700 text-sm leading-relaxed">
                        This closing balance will be transferred to the <strong>Stockholders' Equity</strong> section of your Balance Sheet.
                    </p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>