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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Ledger | Finance Empirez</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .ledger-row:hover { background-color: #f1f5f9; }
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
            <a href="reports.php" class="flex items-center gap-4 px-4 py-3.5 bg-blue-50 text-blue-600 rounded-2xl font-bold transition">
                <i class="fa-solid fa-chart-pie w-5"></i> Financial Reports
            </a>
        </nav>
    </aside>

    <main class="flex-grow ml-72 p-10">
        <header class="mb-10">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">General Ledger</h1>
            <p class="text-slate-500 mt-1">Classified view of all transactions grouped by account.</p>
        </header>

        <?php if ($accounts_result->num_rows > 0): ?>
            <?php while($account = $accounts_result->fetch_assoc()): 
                $current_account = $account['account_title'];
                
                // Fetch entries for this specific account
                $ledger_sql = "
                    SELECT Day, Month, Year, debit_account_title, credit_account_title, transaction_amount, comment 
                    FROM Journal 
                    WHERE email = '$user_email' 
                    AND (debit_account_title = '$current_account' OR credit_account_title = '$current_account')
                    ORDER BY Year ASC, Month ASC, Day ASC";
                
                $ledger_result = $conn->query($ledger_sql);
                $total_debit = 0;
                $total_credit = 0;
            ?>

            <div class="mb-12 bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="bg-slate-900 p-6 flex justify-between items-center">
                    <h2 class="text-white font-bold text-lg tracking-wide uppercase"><?php echo htmlspecialchars($current_account); ?></h2>
                    <span class="text-slate-400 text-xs font-medium">Account Ledger</span>
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-400 text-xs uppercase tracking-widest border-b border-slate-50">
                            <th class="px-8 py-4 font-bold">Date</th>
                            <th class="px-8 py-4 font-bold">Particulars / Description</th>
                            <th class="px-8 py-4 font-bold text-right">Debit ($)</th>
                            <th class="px-8 py-4 font-bold text-right">Credit ($)</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-600 text-sm">
                        <?php while($row = $ledger_result->fetch_assoc()): 
                            $is_debit = ($row['debit_account_title'] == $current_account);
                            $amount = $row['transaction_amount'];
                            if($is_debit) $total_debit += $amount; else $total_credit += $amount;
                        ?>
                        <tr class="ledger-row border-b border-slate-50 transition-colors">
                            <td class="px-8 py-4 font-medium">
                                <?php echo sprintf("%02d/%02d/%d", $row['Day'], $row['Month'], $row['Year']); ?>
                            </td>
                            <td class="px-8 py-4">
                                <div class="font-bold text-slate-800">
                                    <?php echo $is_debit ? "To " . htmlspecialchars($row['credit_account_title']) : "By " . htmlspecialchars($row['debit_account_title']); ?>
                                </div>
                                <div class="text-xs text-slate-400 mt-1"><?php echo htmlspecialchars($row['comment']); ?></div>
                            </td>
                            <td class="px-8 py-4 text-right font-semibold <?php echo $is_debit ? 'text-emerald-600' : 'text-slate-300'; ?>">
                                <?php echo $is_debit ? number_format($amount, 2) : '-'; ?>
                            </td>
                            <td class="px-8 py-4 text-right font-semibold <?php echo !$is_debit ? 'text-red-500' : 'text-slate-300'; ?>">
                                <?php echo !$is_debit ? number_format($amount, 2) : '-'; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50/50 border-t-2 border-slate-100">
                            <td colspan="2" class="px-8 py-6 text-slate-900 font-extrabold text-right uppercase tracking-tighter">Total Footing</td>
                            <td class="px-8 py-6 text-right font-extrabold text-emerald-600 text-base border-r border-slate-100">
                                <?php echo number_format($total_debit, 2); ?>
                            </td>
                            <td class="px-8 py-6 text-right font-extrabold text-red-500 text-base">
                                <?php echo number_format($total_credit, 2); ?>
                            </td>
                        </tr>
                        <tr class="bg-blue-600 text-white">
                            <td colspan="2" class="px-8 py-4 font-bold text-right">Closing Balance</td>
                            <td colspan="2" class="px-8 py-4 text-right font-black text-lg">
                                <?php 
                                    $balance = $total_debit - $total_credit;
                                    echo ($balance >= 0 ? "Dr " : "Cr ") . "$" . number_format(abs($balance), 2); 
                                ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="bg-white p-20 rounded-[2.5rem] border border-dashed border-slate-300 text-center">
                <i class="fa-solid fa-folder-open text-slate-200 text-6xl mb-6"></i>
                <h3 class="text-xl font-bold text-slate-400">No ledger data available.</h3>
                <p class="text-slate-400 mt-2">Post some journal entries to see your accounts here.</p>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>