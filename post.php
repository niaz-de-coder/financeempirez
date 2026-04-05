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

$message = "";
$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['user_email']; // Automatically use logged-in user's email
    $day = (int)$_POST['day'];
    $month = (int)$_POST['month'];
    $year = (int)$_POST['year'];
    $debit_title = mysqli_real_escape_string($conn, $_POST['debit_account_title']);
    $credit_title = mysqli_real_escape_string($conn, $_POST['credit_account_title']);
    $amount = (float)$_POST['transaction_amount'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    // PDF Handling
    $pdf_path = "";
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == 0) {
        $target_dir = "assets/invoice/";
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES["pdf"]["name"], PATHINFO_EXTENSION);
        $file_name = "INV_" . time() . "_" . uniqid() . "." . $file_extension;
        $pdf_path = $target_dir . $file_name;
        move_uploaded_file($_FILES["pdf"]["tmp_name"], $pdf_path);
    }

    $sql = "INSERT INTO Journal (email, Day, Month, Year, debit_account_title, credit_account_title, transaction_amount, comment, pdf) 
            VALUES ('$email', $day, $month, $year, '$debit_title', '$credit_title', $amount, '$comment', '$pdf_path')";

    if ($conn->query($sql) === TRUE) {
        $message = "Journal entry posted successfully!";
        $status = "success";
    } else {
        $message = "Error: " . $conn->error;
        $status = "error";
    }
}

// Account Titles Array
$account_titles = ["Supplies", "Cash", "Bank balances", "Petty cash", "Cash equivalents", "Accounts receivable", "Allowance for doubtful accounts", "Bad debts", "Inventory", "Closing stock", "Opening stock", "Raw materials", "Work-in-progress", "Finished goods", "Inventory loss", "Short-term investments", "Marketable securities", "Prepaid expenses", "Prepaid rent", "Prepaid insurance", "Office supplies", "Unused supplies", "Advance to suppliers", "Deposits", "Bills receivable", "Short-term loans given", "Accrued income", "Outstanding income", "Income received in advance", "Land", "Buildings", "Machinery", "Equipment", "Vehicles", "Furniture", "Fixtures", "Office equipment", "Computers", "Plant", "Tools", "Leasehold improvements", "Factory", "Investment property", "Long-term investments", "Capital work-in-progress", "Goodwill", "Patents", "Trademarks", "Copyrights", "Software", "Accumulated depreciation", "Accumulated amortization", "Depreciation on assets", "Amortization on assets", "Accounts payable", "Bills payable", "Short-term loans", "Bank overdraft", "Accrued expenses", "Outstanding expenses", "Expenses payable", "Unearned revenue", "Deferred revenue", "Income received in advance", "Taxes payable", "Provision for taxation", "VAT payable", "Withholding tax payable", "Salaries payable", "Wages payable", "Interest payable", "Dividends payable", "Current portion of long-term debt", "Long-term loans", "Bonds payable", "Debentures", "Mortgage payable", "Lease liabilities", "Deferred tax liabilities", "Pension liabilities", "Notes payable", "Provision for employee benefits", "Long-term bank loans", "Loans from financial institutions", "Employee provident fund payable", "Gratuity payable", "Ordinary share capital", "Preference share capital", "Authorized share capital", "Issued share capital", "Subscribed share capital", "Paid-up share capital", "Called-up share capital", "Uncalled share capital", "Reserve share capital", "Share premium", "Treasury shares", "Retained earnings", "General reserve", "Capital reserve", "Drawings", "Sales revenue", "Service revenue", "Interest income", "Rental income", "Commission income", "Dividend income", "Fees earned", "Royalties", "Subscription revenue", "Advertising revenue", "Consulting income", "Franchise income", "Gain on sale of assets", "Foreign exchange gain", "Revaluation gain", "Sales returns", "Sales discounts", "Cost of goods sold", "Purchase returns", "Discount received", "Discount allowed", "Salaries expense", "Wages expense", "Rent expense", "Utilities expense", "Office supplies expense", "Depreciation expense", "Amortization expense", "Insurance expense", "Advertising expense", "Transportation expense", "Maintenance expense", "Interest expense", "Telephone expense", "Internet expense", "Printing and stationery expense", "Tax expense", "Bad debts expense", "Loss on sale of assets", "Foreign exchange loss", "Bank charges", "Legal fees", "Audit fees", "Training expense", "Travel expense", "Entertainment expense", "Security expense", "Cleaning expense", "Factory overhead", "Direct labor", "Indirect labor", "Indirect materials", "Factory rent", "Factory utilities", "Provision for doubtful debts", "Provision for depreciation", "Inventory adjustment", "Accrued expense adjustment", "Prepaid expense adjustment", "Unearned revenue adjustment", "Accrued income adjustment", "Depreciation adjustment", "Bad debts adjustment", "Cash dividends", "Stock dividends", "Property dividends", "Scrip dividends", "Liquidating dividends", "Interim dividends", "Final dividends", "Preferred dividends", "Special dividends", "Bonus shares"];
sort($account_titles);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Entry | Finance Empirez</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
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
            <a href="dashboard.php" class="sidebar-item flex items-center gap-4 px-4 py-3.5 text-slate-500 rounded-2xl font-semibold transition">
                <i class="fa-solid fa-house-chimney w-5"></i> Dashboard
            </a>
            <a href="post.php" class="flex items-center gap-4 px-4 py-3.5 bg-blue-50 text-blue-600 rounded-2xl font-bold transition">
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
        <header class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">New Journal Entry</h1>
                <p class="text-slate-500 mt-1">Record a new financial transaction into your ledger.</p>
            </div>
            <a href="dashboard.php" class="text-sm font-bold text-slate-400 hover:text-blue-600 transition">Back to Overview</a>
        </header>

        <?php if($message): ?>
            <div class="mb-8 p-4 <?php echo $status == 'success' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-red-50 text-red-600 border-red-100'; ?> rounded-2xl border flex items-center gap-3 font-semibold">
                <i class="fa-solid <?php echo $status == 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="post.php" method="POST" enctype="multipart/form-data" class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-8">
            
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Day</label>
                    <input type="number" name="day" min="1" max="31" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Month</label>
                    <input type="number" name="month" min="1" max="12" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Year</label>
                    <input type="number" name="year" min="1900" max="2200" value="2026" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 text-emerald-600">Debit Account</label>
                    <select name="debit_account_title" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-50 outline-none">
                        <?php foreach($account_titles as $title) echo "<option value='$title'>$title</option>"; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 text-red-600">Credit Account</label>
                    <select name="credit_account_title" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-50 outline-none">
                        <?php foreach($account_titles as $title) echo "<option value='$title'>$title</option>"; ?>
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Transaction Amount ($)</label>
                    <input type="number" step="0.01" name="transaction_amount" required placeholder="0.00" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-50 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Attachment (PDF Invoice)</label>
                    <input type="file" name="pdf" accept="application/pdf" class="w-full px-5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Transaction Note / Comment</label>
                <textarea name="comment" rows="3" placeholder="Briefly describe this transaction..." class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-50 outline-none"></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Post to Ledger
                </button>
            </div>
        </form>
    </main>

</body>
</html>