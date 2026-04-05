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

// --- 1. DEFINE CATEGORIES FROM PROMPT ---
$current_assets = ["Supplies","Cash","Bank balances","Petty cash","Cash equivalents","Accounts receivable","Allowance for doubtful accounts","Inventory","Closing stock","Opening stock","Raw materials","Work-in-progress","Finished goods","Short-term investments","Marketable securities","Prepaid expenses","Prepaid rent","Prepaid insurance","Office supplies","Unused supplies","Advance to suppliers","Deposits","Bills receivable","Short-term loans given","Accrued income","Outstanding income"];
$fixed_assets = ["Land","Buildings","Machinery","Equipment","Vehicles","Furniture","Fixtures","Office equipment","Computers","Plant","Tools","Leasehold improvements","Factory","Investment property","Long-term investments","Capital work-in-progress","Goodwill","Patents","Trademarks","Copyrights","Software","Accumulated depreciation","Accumulated amortization"];
$current_liabilities = ["Accounts payable","Bills payable","Short-term loans","Bank overdraft","Accrued expenses","Outstanding expenses","Expenses payable","Unearned revenue","Deferred revenue","Income received in advance","Taxes payable","Provision for taxation","VAT payable","Withholding tax payable","Salaries payable","Wages payable","Interest payable","Dividends payable","Current portion of long-term debt"];
$long_term_liabilities = ["Long-term loans","Bonds payable","Debentures","Mortgage payable","Lease liabilities","Deferred tax liabilities","Pension liabilities","Notes payable","Provision for employee benefits","Long-term bank loans","Loans from financial institutions","Employee provident fund payable","Gratuity payable"];
$share_capital = ["Ordinary share capital","Preference share capital","Authorized share capital","Issued share capital","Subscribed share capital","Paid-up share capital","Called-up share capital","Uncalled share capital","Reserve share capital","Share premium","Treasury shares","Retained earnings","General reserve","Capital reserve","Bonus shares"];
$revenues = ["Sales revenue","Service revenue","Interest income","Rental income","Commission income","Dividend income","Fees earned","Royalties","Subscription revenue","Advertising revenue","Consulting income","Franchise income","Gain on sale of assets","Foreign exchange gain","Revaluation gain","Discount received"];
$expenses = ["Bad debts","Inventory loss","Sales returns","Sales discounts","Cost of goods sold","Purchase returns","Discount allowed","Salaries expense","Wages expense","Rent expense","Utilities expense","Office supplies expense","Depreciation expense","Amortization expense","Insurance expense","Advertising expense","Transportation expense","Maintenance expense","Interest expense","Telephone expense","Internet expense","Printing and stationery expense","Tax expense","Bad debts expense","Loss on sale of assets","Foreign exchange loss","Bank charges","Legal fees","Audit fees","Training expense","Travel expense","Entertainment expense","Security expense","Cleaning expense","Factory overhead","Direct labor","Indirect labor","Indirect materials","Factory rent","Factory utilities","Depreciation on assets","Amortization on assets"];
$dividends = ["Drawings","Cash dividends","Stock dividends","Property dividends","Scrip dividends","Liquidating dividends","Interim dividends","Final dividends","Preferred dividends","Special dividends"];

// --- 2. FETCH ALL ACCOUNT BALANCES ---
function getBalances($conn, $email, $category_list) {
    if (empty($category_list)) return [];
    $list = "'" . implode("','", array_map(array($conn, 'real_escape_string'), $category_list)) . "'";
    
    $sql = "SELECT account, SUM(debit) - SUM(credit) as balance FROM (
                SELECT debit_account_title as account, transaction_amount as debit, 0 as credit FROM Journal WHERE email = '$email'
                UNION ALL
                SELECT credit_account_title as account, 0 as debit, transaction_amount as credit FROM Journal WHERE email = '$email'
            ) as t WHERE account IN ($list) GROUP BY account HAVING balance != 0";
    
    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[$row['account']] = (float)$row['balance'];
    }
    return $data;
}

$ca_data = getBalances($conn, $user_email, $current_assets);
$fa_data = getBalances($conn, $user_email, $fixed_assets);
$cl_data = getBalances($conn, $user_email, $current_liabilities);
$ll_data = getBalances($conn, $user_email, $long_term_liabilities);
$eq_data = getBalances($conn, $user_email, $share_capital);
$rev_data = getBalances($conn, $user_email, $revenues);
$exp_data = getBalances($conn, $user_email, $expenses);
$div_data = getBalances($conn, $user_email, $dividends);

// --- 3. CALCULATE TOTALS & KPI ---
$total_ca = array_sum($ca_data);
$total_fa = array_sum($fa_data);
$total_assets = $total_ca + $total_fa;

$total_cl = abs(array_sum($cl_data));
$total_ll = abs(array_sum($ll_data));
$total_liabilities = $total_cl + $total_ll;

$total_rev = abs(array_sum($rev_data));
$total_exp = array_sum($exp_data);
$net_income = $total_rev - $total_exp;

$total_div = array_sum($div_data);
$retained_for_period = $net_income - abs($total_div);
$total_equity = abs(array_sum($eq_data)) + $retained_for_period;

// KPI Calculations
$income_percentage = ($total_rev > 0) ? ($net_income / $total_rev) * 100 : 0;
$risk_percentage = ($total_assets > 0) ? ($total_liabilities / $total_assets) * 100 : 0;
$retention_percentage = ($net_income > 0) ? ($retained_for_period / $net_income) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Financial Position | Finance Empirez</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 font-sans pb-20">

    <main class="max-w-6xl mx-auto px-6 pt-12">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Statement of Financial Position</h1>
                <p class="text-slate-500 font-medium mt-2">As of <?php echo date('F d, Y'); ?></p>
            </div>
            <button onclick="window.print()" class="bg-white border border-slate-200 px-6 py-3 rounded-2xl font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                <i class="fa-solid fa-print mr-2"></i> Print Report
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase mb-2">Total Revenue</p>
                <h3 class="text-2xl font-black text-blue-600">$<?php echo number_format($total_rev, 2); ?></h3>
            </div>
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase mb-2">Income %</p>
                <h3 class="text-2xl font-black text-emerald-500"><?php echo number_format($income_percentage, 1); ?>%</h3>
            </div>
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase mb-2">Risk (L/A)</p>
                <h3 class="text-2xl font-black text-red-500"><?php echo number_format($risk_percentage, 1); ?>%</h3>
            </div>
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase mb-2">Retention %</p>
                <h3 class="text-2xl font-black text-indigo-600"><?php echo number_format($retention_percentage, 1); ?>%</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            
            <div class="space-y-8">
                <section class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                    <h2 class="text-xl font-black text-slate-900 mb-6 flex items-center">
                        <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3 text-sm">A</span> Assets
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Current Assets</h4>
                            <?php foreach($ca_data as $title => $val): ?>
                            <div class="flex justify-between py-2 border-b border-slate-50 text-sm">
                                <span class="text-slate-600"><?php echo $title; ?></span>
                                <span class="font-bold text-slate-900"><?php echo number_format($val, 2); ?></span>
                            </div>
                            <?php endforeach; ?>
                            <div class="flex justify-between mt-2 font-bold text-blue-600">
                                <span>Total Current Assets</span>
                                <span>$<?php echo number_format($total_ca, 2); ?></span>
                            </div>
                        </div>

                        <div class="pt-4">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Non-Current Assets</h4>
                            <?php foreach($fa_data as $title => $val): ?>
                            <div class="flex justify-between py-2 border-b border-slate-50 text-sm">
                                <span class="text-slate-600"><?php echo $title; ?></span>
                                <span class="font-bold text-slate-900"><?php echo number_format($val, 2); ?></span>
                            </div>
                            <?php endforeach; ?>
                            <div class="flex justify-between mt-2 font-bold text-blue-600">
                                <span>Total Fixed Assets</span>
                                <span>$<?php echo number_format($total_fa, 2); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t-4 border-double border-slate-100 flex justify-between items-center">
                        <span class="text-lg font-black uppercase">Total Assets</span>
                        <span class="text-2xl font-black text-blue-600">$<?php echo number_format($total_assets, 2); ?></span>
                    </div>
                </section>
            </div>

            <div class="space-y-8">
                <section class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                    <h2 class="text-xl font-black text-slate-900 mb-6 flex items-center">
                        <span class="w-8 h-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center mr-3 text-sm">L</span> Liabilities
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Current Liabilities</h4>
                            <?php foreach($cl_data as $title => $val): ?>
                            <div class="flex justify-between py-2 border-b border-slate-50 text-sm">
                                <span class="text-slate-600"><?php echo $title; ?></span>
                                <span class="font-bold text-slate-900"><?php echo number_format(abs($val), 2); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div>
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Long-term Liabilities</h4>
                            <?php foreach($ll_data as $title => $val): ?>
                            <div class="flex justify-between py-2 border-b border-slate-50 text-sm">
                                <span class="text-slate-600"><?php echo $title; ?></span>
                                <span class="font-bold text-slate-900"><?php echo number_format(abs($val), 2); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t-2 border-slate-100 flex justify-between items-center text-red-600 font-bold">
                        <span class="uppercase text-sm">Total Liabilities</span>
                        <span class="text-lg">$<?php echo number_format($total_liabilities, 2); ?></span>
                    </div>
                </section>

                <section class="bg-slate-900 rounded-[2.5rem] p-8 shadow-xl text-white">
                    <h2 class="text-xl font-black mb-6 flex items-center">
                        <span class="w-8 h-8 bg-slate-800 text-white rounded-lg flex items-center justify-center mr-3 text-sm">E</span> Equity
                    </h2>
                    
                    <div class="space-y-4">
                        <?php foreach($eq_data as $title => $val): ?>
                        <div class="flex justify-between text-sm opacity-80">
                            <span><?php echo $title; ?></span>
                            <span><?php echo number_format(abs($val), 2); ?></span>
                        </div>
                        <?php endforeach; ?>
                        <div class="flex justify-between text-sm border-t border-slate-800 pt-4">
                            <span>Retained Earnings (Current Period)</span>
                            <span class="text-emerald-400 font-bold"><?php echo number_format($retained_for_period, 2); ?></span>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t-4 border-double border-slate-700 flex justify-between items-center">
                        <span class="text-lg font-black uppercase">Total Equity & Liabilities</span>
                        <span class="text-2xl font-black text-white">$<?php echo number_format($total_equity + $total_liabilities, 2); ?></span>
                    </div>
                </section>

                <?php if (round($total_assets, 2) == round($total_equity + $total_liabilities, 2)): ?>
                <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-center gap-3 text-emerald-700">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <p class="text-sm font-bold">Your Balance Sheet is balanced.</p>
                </div>
                <?php else: ?>
                <div class="bg-red-50 border border-red-200 p-4 rounded-2xl flex items-center gap-3 text-red-700">
                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    <p class="text-sm font-bold">Balance Mismatch: Difference of $<?php echo number_format(abs($total_assets - ($total_equity + $total_liabilities)), 2); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>