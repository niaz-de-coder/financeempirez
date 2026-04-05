<?php
session_start();

// Authentication Check
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

$user_email = $_SESSION['user_email'];
$message = "";
$status = "";

// Handle Update Request
if (isset($_POST['update_entry'])) {
    $id = (int)$_POST['entry_id'];
    $day = (int)$_POST['day'];
    $month = (int)$_POST['month'];
    $year = (int)$_POST['year'];
    $debit = mysqli_real_escape_string($conn, $_POST['debit_account_title']);
    $credit = mysqli_real_escape_string($conn, $_POST['credit_account_title']);
    $amount = (float)$_POST['transaction_amount'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    // PDF Update Logic
    $pdf_sql = "";
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == 0) {
        $target_dir = "assets/invoice/";
        $file_name = "INV_UPD_" . time() . "_" . uniqid() . ".pdf";
        $pdf_path = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["pdf"]["tmp_name"], $pdf_path)) {
            $pdf_sql = ", pdf = '$pdf_path'";
        }
    }

    $sql = "UPDATE Journal SET Day=$day, Month=$month, Year=$year, debit_account_title='$debit', 
            credit_account_title='$credit', transaction_amount=$amount, comment='$comment' $pdf_sql 
            WHERE id=$id AND email='$user_email'";

    if ($conn->query($sql) === TRUE) {
        $message = "Entry updated successfully!";
        $status = "success";
    } else {
        $message = "Error updating: " . $conn->error;
        $status = "error";
    }
}

// Account Titles List
$account_titles = ["Supplies", "Cash", "Bank balances", "Petty cash", "Cash equivalents", "Accounts receivable", "Allowance for doubtful accounts", "Bad debts", "Inventory", "Closing stock", "Opening stock", "Raw materials", "Work-in-progress", "Finished goods", "Inventory loss", "Short-term investments", "Marketable securities", "Prepaid expenses", "Prepaid rent", "Prepaid insurance", "Office supplies", "Unused supplies", "Advance to suppliers", "Deposits", "Bills receivable", "Short-term loans given", "Accrued income", "Outstanding income", "Income received in advance", "Land", "Buildings", "Machinery", "Equipment", "Vehicles", "Furniture", "Fixtures", "Office equipment", "Computers", "Plant", "Tools", "Leasehold improvements", "Factory", "Investment property", "Long-term investments", "Capital work-in-progress", "Goodwill", "Patents", "Trademarks", "Copyrights", "Software", "Accumulated depreciation", "Accumulated amortization", "Depreciation on assets", "Amortization on assets", "Accounts payable", "Bills payable", "Short-term loans", "Bank overdraft", "Accrued expenses", "Outstanding expenses", "Expenses payable", "Unearned revenue", "Deferred revenue", "Income received in advance", "Taxes payable", "Provision for taxation", "VAT payable", "Withholding tax payable", "Salaries payable", "Wages payable", "Interest payable", "Dividends payable", "Current portion of long-term debt", "Long-term loans", "Bonds payable", "Debentures", "Mortgage payable", "Lease liabilities", "Deferred tax liabilities", "Pension liabilities", "Notes payable", "Provision for employee benefits", "Long-term bank loans", "Loans from financial institutions", "Employee provident fund payable", "Gratuity payable", "Ordinary share capital", "Preference share capital", "Authorized share capital", "Issued share capital", "Subscribed share capital", "Paid-up share capital", "Called-up share capital", "Uncalled share capital", "Reserve share capital", "Share premium", "Treasury shares", "Retained earnings", "General reserve", "Capital reserve", "Drawings", "Sales revenue", "Service revenue", "Interest income", "Rental income", "Commission income", "Dividend income", "Fees earned", "Royalties", "Subscription revenue", "Advertising revenue", "Consulting income", "Franchise income", "Gain on sale of assets", "Foreign exchange gain", "Revaluation gain", "Sales returns", "Sales discounts", "Cost of goods sold", "Purchase returns", "Discount received", "Discount allowed", "Salaries expense", "Wages expense", "Rent expense", "Utilities expense", "Office supplies expense", "Depreciation expense", "Amortization expense", "Insurance expense", "Advertising expense", "Transportation expense", "Maintenance expense", "Interest expense", "Telephone expense", "Internet expense", "Printing and stationery expense", "Tax expense", "Bad debts expense", "Loss on sale of assets", "Foreign exchange loss", "Bank charges", "Legal fees", "Audit fees", "Training expense", "Travel expense", "Entertainment expense", "Security expense", "Cleaning expense", "Factory overhead", "Direct labor", "Indirect labor", "Indirect materials", "Factory rent", "Factory utilities", "Provision for doubtful debts", "Provision for depreciation", "Inventory adjustment", "Accrued expense adjustment", "Prepaid expense adjustment", "Unearned revenue adjustment", "Accrued income adjustment", "Depreciation adjustment", "Bad debts adjustment", "Cash dividends", "Stock dividends", "Property dividends", "Scrip dividends", "Liquidating dividends", "Interim dividends", "Final dividends", "Preferred dividends", "Special dividends", "Bonus shares"];
sort($account_titles);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Journal | Finance Empirez</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; overflow-x: hidden; }
        .glass-modal { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
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
            <a href="post.php" class="sidebar-item flex items-center gap-4 px-4 py-3.5 text-slate-500 rounded-2xl font-semibold transition">
                <i class="fa-solid fa-plus-circle w-5"></i> Post Journal
            </a>
            <a href="edit.php" class="flex items-center gap-4 px-4 py-3.5 bg-blue-50 text-blue-600 rounded-2xl font-bold transition">
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
        <?php if (!isset($_POST['find_entries']) && !isset($_POST['edit_selected'])): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm">
            <div class="bg-white w-full max-w-md p-10 rounded-[2.5rem] shadow-2xl border border-slate-100">
                <h3 class="text-2xl font-extrabold mb-2">Find Entries</h3>
                <p class="text-slate-500 mb-8 text-sm">Select the date to find journal entries.</p>
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-3 gap-4">
                        <input type="number" name="f_day" placeholder="DD" min="1" max="31" required class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-blue-50">
                        <input type="number" name="f_month" placeholder="MM" min="1" max="12" required class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-blue-50">
                        <input type="number" name="f_year" placeholder="YYYY" min="1900" max="2200" value="2026" required class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-blue-50">
                    </div>
                    <button type="submit" name="find_entries" class="w-full py-4 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">Search Records</button>
                    <a href="dashboard.php" class="block text-center text-xs font-bold text-slate-400 hover:underline mt-4">Cancel</a>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php 
        if (isset($_POST['find_entries'])): 
            $f_day = (int)$_POST['f_day'];
            $f_month = (int)$_POST['f_month'];
            $f_year = (int)$_POST['f_year'];
            $res = $conn->query("SELECT * FROM Journal WHERE email='$user_email' AND Day=$f_day AND Month=$f_month AND Year=$f_year");
        ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm">
            <div class="bg-white w-full max-w-2xl p-10 rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col max-h-[80vh]">
                <h3 class="text-2xl font-extrabold mb-6">Entries for <?php echo "$f_day/$f_month/$f_year"; ?></h3>
                <div class="overflow-y-auto pr-2 space-y-4">
                    <?php if ($res->num_rows > 0): while($row = $res->fetch_assoc()): ?>
                        <form method="POST" class="p-6 bg-slate-50 rounded-3xl border border-slate-100 flex justify-between items-center group hover:border-blue-300 transition">
                            <input type="hidden" name="selected_id" value="<?php echo $row['id']; ?>">
                            <div>
                                <p class="text-xs font-bold text-blue-600 uppercase mb-1"><?php echo $row['debit_account_title']; ?> / <?php echo $row['credit_account_title']; ?></p>
                                <p class="text-lg font-bold text-slate-800">$<?php echo number_format($row['transaction_amount'], 2); ?></p>
                                <p class="text-sm text-slate-500 italic">"<?php echo $row['comment']; ?>"</p>
                            </div>
                            <button type="submit" name="edit_selected" class="bg-white text-slate-900 px-6 py-2.5 rounded-xl font-bold border border-slate-200 hover:bg-blue-600 hover:text-white transition">Edit</button>
                        </form>
                    <?php endwhile; else: ?>
                        <p class="text-center py-10 text-slate-400 font-medium italic">No entries found for this date.</p>
                        <a href="edit.php" class="block text-center py-3 bg-slate-100 rounded-xl font-bold">Try Another Date</a>
                    <?php endif; ?>
                </div>
                <a href="edit.php" class="mt-6 text-center text-sm font-bold text-slate-400 hover:text-blue-600 transition">Close</a>
            </div>
        </div>
        <?php endif; ?>

        <?php 
        if (isset($_POST['edit_selected'])): 
            $sid = (int)$_POST['selected_id'];
            $entry = $conn->query("SELECT * FROM Journal WHERE id=$sid AND email='$user_email'")->fetch_assoc();
        ?>
        <div class="max-w-4xl mx-auto">
            <header class="mb-8">
                <h2 class="text-3xl font-extrabold text-slate-900">Modify Entry #<?php echo $sid; ?></h2>
                <p class="text-slate-500 mt-1">Updates will reflect immediately in financial reports.</p>
            </header>

            <form method="POST" enctype="multipart/form-data" class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-8">
                <input type="hidden" name="entry_id" value="<?php echo $sid; ?>">
                
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Day</label>
                        <input type="number" name="day" value="<?php echo $entry['Day']; ?>" min="1" max="31" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Month</label>
                        <input type="number" name="month" value="<?php echo $entry['Month']; ?>" min="1" max="12" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Year</label>
                        <input type="number" name="year" value="<?php echo $entry['Year']; ?>" min="1900" max="2200" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-bold text-emerald-600 mb-2">Debit Account</label>
                        <select name="debit_account_title" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <?php foreach($account_titles as $t) echo "<option value='$t' ".($entry['debit_account_title']==$t ? 'selected':'').">$t</option>"; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-red-600 mb-2">Credit Account</label>
                        <select name="credit_account_title" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <?php foreach($account_titles as $t) echo "<option value='$t' ".($entry['credit_account_title']==$t ? 'selected':'').">$t</option>"; ?>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Amount ($)</label>
                        <input type="number" step="0.01" name="transaction_amount" value="<?php echo $entry['transaction_amount']; ?>" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">New PDF (Optional)</label>
                        <input type="file" name="pdf" accept="application/pdf" class="w-full text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Comment</label>
                    <textarea name="comment" rows="3" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl"><?php echo $entry['comment']; ?></textarea>
                </div>

                <div class="flex gap-4">
                    <button type="submit" name="update_entry" class="flex-grow py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-blue-600 transition shadow-lg">Save Changes</button>
                    <a href="edit.php" class="px-8 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold hover:bg-slate-200 transition">Cancel</a>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <?php if($message): ?>
            <div class="mt-8 p-6 bg-emerald-50 text-emerald-700 rounded-[2rem] border border-emerald-100 text-center font-bold">
                <i class="fa-solid fa-circle-check mr-2"></i> <?php echo $message; ?>
                <br><a href="dashboard.php" class="text-xs underline mt-2 block">Return to Dashboard</a>
            </div>
        <?php endif; ?>

    </main>

</body>
</html>