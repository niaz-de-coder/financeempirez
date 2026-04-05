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

// Fetching basic stats for the user (Example: Total Transactions)
$user_email = $_SESSION['user_email'];
$count_query = "SELECT COUNT(*) as total FROM Journal WHERE email = '$user_email'";
$count_result = $conn->query($count_query);
$total_entries = ($count_result) ? $count_result->fetch_assoc()['total'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Finance Empirez</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .sidebar-item:hover { background-color: rgba(37, 99, 235, 0.05); color: #2563eb; }
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
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
            <a href="dashboard.php" class="flex items-center gap-4 px-4 py-3.5 bg-blue-50 text-blue-600 rounded-2xl font-bold transition">
                <i class="fa-solid fa-house-chimney w-5"></i> Dashboard
            </a>
            <a href="post.php" class="sidebar-item flex items-center gap-4 px-4 py-3.5 text-slate-500 rounded-2xl font-semibold transition">
                <i class="fa-solid fa-plus-circle w-5"></i> Post Journal
            </a>
            <a href="edit.php" class="sidebar-item flex items-center gap-4 px-4 py-3.5 text-slate-500 rounded-2xl font-semibold transition">
                <i class="fa-solid fa-pen-to-square w-5"></i> Edit Entries
            </a>
            <a href="reports.php" class="sidebar-item flex items-center gap-4 px-4 py-3.5 text-slate-500 rounded-2xl font-semibold transition">
                <i class="fa-solid fa-chart-pie w-5"></i> Financial Reports
            </a>
            <div class="pt-6 pb-2 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Account</div>
            <a href="settings.php" class="sidebar-item flex items-center gap-4 px-4 py-3.5 text-slate-500 rounded-2xl font-semibold transition">
                <i class="fa-solid fa-gear w-5"></i> User Settings
            </a>
        </nav>

        <div class="p-4 mt-auto">
            <a href="logout.php" class="flex items-center gap-4 px-4 py-4 text-red-500 bg-red-50 rounded-2xl font-bold hover:bg-red-100 transition">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </a>
        </div>
    </aside>

    <main class="flex-grow ml-72 p-10">
        <header class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Welcome back, <span class="text-blue-600"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span> 👋
                </h1>
                <p class="text-slate-500 mt-1">Here is what's happening with your accounts today.</p>
            </div>
            <div class="flex items-center gap-4">
                <button class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-blue-600 transition">
                    <i class="fa-solid fa-bell"></i>
                </button>
                <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </div>
            </div>
        </header>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <!-- <div class="stat-card bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-6">
                    <i class="fa-solid fa-list-check text-xl"></i>
                </div>
                <p class="text-slate-500 font-semibold text-sm">Total Journal Entries</p>
                <h3 class="text-4xl font-extrabold mt-2"><?php echo $total_entries; ?></h3>
            </div>
            
            <div class="stat-card bg-slate-900 p-8 rounded-[2rem] shadow-lg shadow-slate-200 text-white">
                <div class="w-12 h-12 bg-white/10 text-white rounded-xl flex items-center justify-center mb-6">
                    <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                </div>
                <p class="text-slate-400 font-semibold text-sm">Active Reports</p>
                <h3 class="text-4xl font-extrabold mt-2">3</h3>
                <p class="text-xs text-slate-400 mt-4 font-medium italic">Balance Sheet, P&L, Ledger</p>
            </div>

            <div class="stat-card bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-6">
                    <i class="fa-solid fa-shield-halved text-xl"></i>
                </div>
                <p class="text-slate-500 font-semibold text-sm">Security Status</p>
                <h3 class="text-2xl font-extrabold mt-2 text-emerald-600">Encrypted</h3>
                <div class="mt-4 flex gap-1">
                    <div class="h-1.5 w-8 bg-emerald-500 rounded-full"></div>
                    <div class="h-1.5 w-8 bg-emerald-500 rounded-full"></div>
                    <div class="h-1.5 w-8 bg-emerald-500 rounded-full"></div>
                </div>
            </div> -->
        </section>

        <section class="grid lg:grid-cols-2 gap-8">
            <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h4 class="text-xl font-bold mb-6">Quick Actions</h4>
                <div class="grid grid-cols-2 gap-4">
                    <a href="post.php" class="p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:border-blue-200 group transition">
                        <i class="fa-solid fa-plus text-blue-600 mb-3 block group-hover:scale-110 transition"></i>
                        <span class="font-bold text-slate-800">New Entry</span>
                    </a>
                    <a href="reports.php" class="p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:border-blue-200 group transition">
                        <i class="fa-solid fa-download text-emerald-600 mb-3 block group-hover:scale-110 transition"></i>
                        <span class="font-bold text-slate-800">Export Reports</span>
                    </a>
                </div>
            </div>

            <div class="bg-blue-600 p-10 rounded-[2.5rem] text-white flex flex-col justify-center relative overflow-hidden">
                <div class="relative z-10">
                    <h4 class="text-2xl font-bold mb-4">Automation v2.0 is Live!</h4>
                    <p class="opacity-80 leading-relaxed mb-6">You can now upload PDF invoices directly to your journal entries for better digital record keeping.</p>
                    <button class="bg-white text-blue-600 px-6 py-3 rounded-xl font-bold hover:bg-blue-50 transition">Learn More</button>
                </div>
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white opacity-10 rounded-full"></div>
            </div>
        </section>
    </main>

</body>
</html>