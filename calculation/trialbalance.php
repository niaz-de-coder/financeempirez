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

// 1. Fetch all distinct account titles involved in transactions for this user
$accounts_query = "
    SELECT DISTINCT account_title FROM (
        SELECT debit_account_title AS account_title FROM Journal WHERE email = '$user_email'
        UNION
        SELECT credit_account_title AS account_title FROM Journal WHERE email = '$user_email'
    ) as combined_accounts ORDER BY account_title ASC";

$accounts_result = $conn->query($accounts_query);

$grand_total_debit = 0;
$grand_total_credit = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Balance | Finance Empirez</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .tb-row:hover { background-color: #f1f5f9; }
    </style>
</head>
<body class="flex min-h-screen">

    <aside class="w-72 bg-white border-r border-slate-100 flex flex-col fixed h-full z-10">
        <div class="p-8">
            <div class="text-xl font-extrabold tracking-tighter flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white text-sm">F</div>
                <span>FINANCE<span class="text-blue-600">Empirez</span></span>
            </div>
        </div>
        <nav class="flex-grow px-4 space-y-2">
            <a href="dashboard.php" class="flex items-center gap-4 px-4 py-3.5 text-slate-500 rounded-2xl font-semibold transition">
                <i class="fa-solid fa-house-chimney w-5"></i> Dashboard
            </a>
            <a href="ledger.php" class="flex items-center gap-4 px-4 py-3.5 text-slate-500 rounded-2xl font-semibold transition">
                <i class="fa-solid fa-book w-5"></i> General Ledger
            </a>
            <a href="trialbalance.php" class="flex items-center gap-4 px-4 py-3.5 bg-blue-50 text-blue-600 rounded-2xl font-bold transition">
                <i class="fa-solid fa-scale-balanced w-5"></i> Trial Balance
            </a>
        </nav>
    </aside>

    <main class="flex-grow ml-72 p-10">
        <header class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Trial Balance</h1>
                <p class="text-slate-500 mt-1">Condensed summary of all ledger account balances.</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Financial Status</p>
                <p class="font-bold text-slate-900">Live Ledger Feed</p>
            </div>
        </header>

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white uppercase text-xs tracking-widest">
                        <th class="px-10 py-5 font-bold">Account Title</th>
                        <th class="px-10 py-5 font-bold text-right">Debit ($)</th>
                        <th class="px-10 py-5 font-bold text-right">Credit ($)</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600 text-sm">
                    <?php if ($accounts_result && $accounts_result->num_rows > 0): ?>
                        <?php while($account = $accounts_result->fetch_assoc()): 
                            $title = $account['account_title'];

                            // Calculate Total Debits for this account
                            $d_query = "SELECT SUM(transaction_amount) as d_total FROM Journal WHERE email = '$user_email' AND debit_account_title = '$title'";
                            $d_res = $conn->query($d_query);
                            $d_val = $d_res->fetch_assoc()['d_total'] ?? 0;

                            // Calculate Total Credits for this account
                            $c_query = "SELECT SUM(transaction_amount) as c_total FROM Journal WHERE email = '$user_email' AND credit_account_title = '$title'";
                            $c_res = $conn->query($c_query);
                            $c_val = $c_res->fetch_assoc()['c_total'] ?? 0;

                            // Calculate Net Balance for Trial Balance display
                            $net = $d_val - $c_val;
                            $final_d = ($net > 0) ? $net : 0;
                            $final_c = ($net < 0) ? abs($net) : 0;

                            $grand_total_debit += $final_d;
                            $grand_total_credit += $final_c;
                        ?>
                        <tr class="tb-row border-b border-slate-50 transition-colors">
                            <td class="px-10 py-5 font-bold text-slate-800 italic"><?php echo htmlspecialchars($title); ?></td>
                            <td class="px-10 py-5 text-right font-semibold text-emerald-600">
                                <?php echo $final_d > 0 ? number_format($final_d, 2) : '-'; ?>
                            </td>
                            <td class="px-10 py-5 text-right font-semibold text-red-500">
                                <?php echo $final_c > 0 ? number_format($final_c, 2) : '-'; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="px-10 py-20 text-center text-slate-400">No account data found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-blue-600 text-white">
                        <td class="px-10 py-6 text-lg font-black uppercase tracking-tighter">Grand Total</td>
                        <td class="px-10 py-6 text-right font-black text-xl border-r border-white/20">
                            <?php echo number_format($grand_total_debit, 2); ?>
                        </td>
                        <td class="px-10 py-6 text-right font-black text-xl">
                            <?php echo number_format($grand_total_credit, 2); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-8 p-6 rounded-3xl border flex items-center gap-4 <?php echo (round($grand_total_debit, 2) == round($grand_total_credit, 2)) ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700'; ?>">
            <?php if (round($grand_total_debit, 2) == round($grand_total_credit, 2)): ?>
                <i class="fa-solid fa-circle-check text-2xl"></i>
                <p class="font-bold">The Trial Balance is in agreement. Total Debits match Total Credits.</p>
            <?php else: ?>
                <i class="fa-solid fa-circle-xmark text-2xl"></i>
                <p class="font-bold">Error: Trial Balance Mismatch. Please review your journal entries for discrepancies.</p>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>