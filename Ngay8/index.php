<?php
require_once 'src/Accounts/BankAccount.php';
require_once 'src/Accounts/InterestBearing.php';
require_once 'src/Accounts/TransactionLogger.php';
require_once 'src/Accounts/SavingsAccount.php';
require_once 'src/Accounts/CheckingAccount.php';
require_once 'src/Accounts/Bank.php';
require_once 'src/Accounts/AccountCollection.php';

use XYZBank\Accounts\AccountCollection;
use XYZBank\Accounts\Bank;
use XYZBank\Accounts\CheckingAccount;
use XYZBank\Accounts\SavingsAccount;

// Khởi tạo collection
$collection = new AccountCollection();

// Thêm các tài khoản mẫu
$savings = new SavingsAccount("10201122", "Nguyễn Thị A", 20000000);
$collection->addAccount($savings);

$checking1 = new CheckingAccount("20301123", "Lê Văn B", 8000000);
$checking2 = new CheckingAccount("20401124", "Trần Minh C", 12000000);
$collection->addAccount($checking1);
$collection->addAccount($checking2);

// Thực hiện giao dịch mẫu
$checking1->deposit(5000000);
$checking2->withdraw(2000000);

// Xử lý tạo tài khoản mới từ form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['account_type'] ?? '';
    $number = $_POST['account_number'] ?? '';
    $owner = $_POST['owner_name'] ?? '';
    $balance = (int)($_POST['balance'] ?? 0);

    if ($type && $number && $owner && $balance > 0) {
        if ($type === 'savings') {
            $newAccount = new SavingsAccount($number, $owner, $balance);
        } else {
            $newAccount = new CheckingAccount($number, $owner, $balance);
        }
        $collection->addAccount($newAccount);
        $successMsg = "Tạo tài khoản mới thành công!";
    } else {
        $errorMsg = "Vui lòng nhập đầy đủ và hợp lệ thông tin!";
    }
}

// Lấy tất cả tài khoản tiết kiệm và tính tổng lãi
$savingsAccounts = [];
$totalInterest = 0;
foreach ($collection as $account) {
    if ($account instanceof SavingsAccount) {
        $savingsAccounts[] = $account;
        $totalInterest += $account->calculateAnnualInterest();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản ngân hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        body {
            background: #f8fafc;
            font-family: 'Inter', sans-serif;
        }

        .glow-card {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glow-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.1);
        }

        .table-row-hover:hover td {
            background-color: #f8fafc;
        }

        .balance-cell {
            font-feature-settings: 'tnum';
            font-variant-numeric: tabular-nums;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Header -->
        <header class="flex items-center mb-8 space-x-4">
            <div class="p-3 bg-blue-600 rounded-lg shadow-lg">
                <i class="fas fa-university text-2xl text-white"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <?php echo htmlspecialchars(Bank::getBankName()); ?>
                </h1>
                <p class="text-gray-500 mt-1">Hệ thống quản lý tài khoản</p>
            </div>
        </header>

        <!-- Thông báo -->
        <?php if (!empty($successMsg)): ?>
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded"><?php echo $successMsg; ?></div>
        <?php endif; ?>
        <?php if (!empty($errorMsg)): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded"><?php echo $errorMsg; ?></div>
        <?php endif; ?>

        <!-- Form tạo tài khoản mới -->
        <div class="bg-white glow-card rounded-xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Tạo tài khoản mới</h3>
            <form method="post" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tài khoản</label>
                    <input name="account_number" required class="w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chủ tài khoản</label>
                    <input name="owner_name" required class="w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số dư ban đầu</label>
                    <input name="balance" type="number" min="1000" required class="w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại tài khoản</label>
                    <select name="account_type" class="w-full border rounded px-3 py-2">
                        <option value="savings">Tiết kiệm</option>
                        <option value="checking">Thanh toán</option>
                    </select>
                </div>
                <div class="md:col-span-4">
                    <button type="submit" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Tạo mới
                    </button>
                </div>
            </form>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Bank Summary Card -->
            <div class="bg-white glow-card rounded-xl p-6">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                        <i class="fas fa-chart-line text-blue-600 text-lg"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700">Tổng quan</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Tổng tài khoản</span>
                        <span class="font-medium text-blue-600">
                            <?php echo Bank::getTotalAccounts(); ?>
                        </span>
                    </div>
                    
                </div>
            </div>

            <!-- Interest Card -->
            <div class="bg-white glow-card rounded-xl p-6">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-purple-100 rounded-lg mr-3">
                        <i class="fas fa-coins text-purple-600 text-lg"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700">Lãi suất</h3>
                </div>
                <?php if (count($savingsAccounts) > 0): ?>
                    <div class="mb-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-gray-500">
                                    <th class="text-left py-1">Số TK</th>
                                    <th class="text-left py-1">Chủ tài khoản</th>
                                    <th class="text-right py-1">Số dư</th>
                                    <th class="text-right py-1">Lãi hàng năm</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($savingsAccounts as $acc): ?>
                                <tr>
                                    <td class="py-1"><?php echo htmlspecialchars($acc->getAccountNumber()); ?></td>
                                    <td class="py-1"><?php echo htmlspecialchars($acc->getOwnerName()); ?></td>
                                    <td class="py-1 text-right"><?php echo number_format($acc->getBalance(), 0, ',', '.'); ?></td>
                                    <td class="py-1 text-right text-purple-700 font-semibold">
                                        <?php echo number_format($acc->calculateAnnualInterest(), 0, ',', '.'); ?> VNĐ
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-end mt-2">
                        <span class="text-gray-700 font-semibold mr-2">Tổng lãi suất:</span>
                        <span class="font-bold text-purple-700"><?php echo number_format($totalInterest, 0, ',', '.'); ?> VNĐ</span>
                    </div>
                <?php else: ?>
                    <div class="text-gray-500">Chưa có tài khoản tiết kiệm nào.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Accounts Table -->
        <div class="bg-white glow-card rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-700">Danh sách tài khoản</h3>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-gray-500 text-sm font-medium">
                        <tr>
                            <th class="px-6 py-4 text-left">Số TK</th>
                            <th class="px-6 py-4 text-left">Chủ tài khoản</th>
                            <th class="px-6 py-4 text-left">Loại TK</th>
                            <th class="px-6 py-4 text-right">Số dư (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        <?php foreach ($collection as $account): ?>
                        <tr class="table-row-hover">
                            <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($account->getAccountNumber()); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($account->getOwnerName()); ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    <?php echo $account->getAccountType() === 'Tiết kiệm' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' ?>">
                                    <?php echo htmlspecialchars($account->getAccountType()); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right balance-cell font-medium text-gray-900">
                                <?php echo number_format($account->getBalance(), 0, ',', '.'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-8 text-center text-sm text-gray-500">
            <p>© 2024 <?php echo htmlspecialchars(Bank::getBankName()); ?>. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>