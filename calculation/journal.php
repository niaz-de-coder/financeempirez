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

// Fetch Journal Entries for the logged-in user
$sql = "SELECT * FROM Journal WHERE email = '$user_email' ORDER BY Year DESC, Month DESC, Day DESC, id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Journal | Finance Empirez</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .journal-table th { background-color: #f1f5f9; color: #475569; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        .credit-row { padding-left: 2.5rem; }
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
            <a href="dashboard.php" class="sidebar-item flex items-center gap-4 px-4 py-3.5 text-slate-500 rounded-2xl font-semibold transition hover:bg-slate-50">
                <i class="fa-solid fa-house-chimney w-5"></i> Dashboard
            </a>
            <a href="post.php" class="sidebar-item flex items-center gap-4 px-4 py-3.5 text-slate-500 rounded-2xl font-semibold transition hover:bg-slate-50">
                <i class="fa-solid fa-plus-circle w-5"></i> Post Journal
            </a>
            <a href="journal.php" class="flex items-center gap-4 px-4 py-3.5 bg-blue-50 text-blue-600 rounded-2xl font-bold transition">
                <i class="fa-solid fa-book w-5"></i> General Journal
            </a>
            <a href="reports.php" class="sidebar-item flex items-center gap-4 px-4 py-3.5 text-slate-500 rounded-2xl font-semibold transition hover:bg-slate-50">
                <i class="fa-solid fa-chart-pie w-5"></i> Financial Reports
            </a>
        </nav>

        <div class="p-4 mt-auto">
            <a href="logout.php" class="flex items-center gap-4 px-4 py-4 text-red-500 bg-red-50 rounded-2xl font-bold hover:bg-red-100 transition">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </a>
        </div>
    </aside>

    <main class="flex-grow ml-72 p-10">
        <header class="mb-10 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">General Journal</h1>
                <p class="text-slate-500 mt-1">Detailed chronological record of all financial transactions.</p>
            </div>
            <button onclick="window.print()" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50 transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-print"></i> Print Journal
            </button>
        </header>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-left border-collapse journal-table">
                <thead>
                    <tr>
                        <th class="px-8 py-5 border-b border-slate-100">Date</th>
                        <th class="px-6 py-5 border-b border-slate-100">Account Titles & Explanation</th>
                        <th class="px-6 py-5 border-b border-slate-100 text-right">Debit ($)</th>
                        <th class="px-6 py-5 border-b border-slate-100 text-right">Credit ($)</th>
                        <th class="px-8 py-5 border-b border-slate-100 text-center">Docs</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-8 py-6 align-top">
                                    <div class="font-bold text-slate-900"><?php echo sprintf("%02d", $row['Day']); ?></div>
                                    <div class="text-xs text-slate-400 font-semibold uppercase">
                                        <?php echo date("M", mktime(0, 0, 0, $row['Month'], 10)); ?> <?php echo $row['Year']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="font-bold text-slate-800"><?php echo $row['debit_account_title']; ?></div>
                                    
                                    <div class="credit-row font-bold text-slate-600 mt-1">
                                        <?php echo $row['credit_account_title']; ?>
                                    </div>
                                    
                                    <?php if($row['comment']): ?>
                                        <div class="text-sm text-slate-400 italic mt-3 flex items-start gap-2">
                                            <span class="text-blue-400 font-bold">(</span>
                                            <?php echo htmlspecialchars($row['comment']); ?>
                                            <span class="text-blue-400 font-bold">)</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-6 text-right align-top font-bold text-emerald-600">
                                    <?php echo number_format($row['transaction_amount'], 2); ?>
                                </td>
                                <td class="px-6 py-6 text-right align-top font-bold text-red-500">
                                    <div class="mt-7"> <?php echo number_format($row['transaction_amount'], 2); ?>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center align-top">
                                    <?php if($row['pdf']): ?>
                                        <a href="<?php echo $row['pdf']; ?>" target="_blank" class="w-10 h-10 inline-flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition">
                                            <i class="fa-solid fa-file-pdf text-lg"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-slate-200"><i class="fa-solid fa-minus"></i></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                                    <i class="fa-solid fa-folder-open text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900">No entries found</h3>
                                <p class="text-slate-500">You haven't posted any transactions to your journal yet.</p>
                                <a href="post.php" class="inline-block mt-6 px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition">Post Your First Entry</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if ($result->num_rows > 0): ?>
                <tfoot>
                    <tr class="bg-slate-50/50">
                        <td colspan="2" class="px-8 py-6 text-right font-extrabold text-slate-500 uppercase tracking-widest text-xs">Total Journaled</td>
                        <?php
                            // Quick calculation for totals (in a real scenario, you'd sum in SQL)
                            $result->data_seek(0);
                            $total = 0;
                            while($sum = $result->fetch_assoc()) { $total += $sum['transaction_amount']; }
                        ?>
                        <td class="px-6 py-6 text-right font-extrabold text-slate-900 border-t-2 border-slate-200">
                            $<?php echo number_format($total, 2); ?>
                        </td>
                        <td class="px-6 py-6 text-right font-extrabold text-slate-900 border-t-2 border-slate-200">
                            $<?php echo number_format($total, 2); ?>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </main>

</body>
</html>