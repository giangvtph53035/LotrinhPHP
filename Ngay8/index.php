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

// Test scenario
$collection = new AccountCollection();

// Create savings account
$savings = new SavingsAccount("10201122", "Nguyễn Thị A", 20000000);
$collection->addAccount($savings);

// Create checking accounts
$checking1 = new CheckingAccount("20301123", "Lê Văn B", 8000000);
$checking2 = new CheckingAccount("20401124", "Trần Minh C", 12000000);
$collection->addAccount($checking1);
$collection->addAccount($checking2);

// Perform transactions
$checking1->deposit(5000000);
$checking2->withdraw(2000000);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản ngân hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center text-blue-800 mb-6">Quản lý tài khoản - <?php echo htmlspecialchars(Bank::getBankName()); ?></h1>

        <!-- Accounts Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Số tài khoản</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Chủ tài khoản</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Loại tài khoản</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Số dư (VNĐ)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($collection as $account): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($account->getAccountNumber()); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($account->getOwnerName()); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($account->getAccountType()); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo number_format($account->getBalance(), 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Savings Interest -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Thông tin lãi suất</h2>
            <p class="text-gray-700">Lãi suất hàng năm cho <?php echo htmlspecialchars($savings->getOwnerName()); ?>: 
                <span class="font-bold text-blue-600"><?php echo number_format($savings->calculateAnnualInterest(), 0, ',', '.'); ?> VNĐ</span>
            </p>
        </div>

        <!-- Bank Summary -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Tổng quan ngân hàng</h2>
            <p class="text-gray-700">Tên ngân hàng: <span class="font-bold text-blue-600"><?php echo htmlspecialchars(Bank::getBankName()); ?></span></p>
            <p class="text-gray-700">Tổng số tài khoản: <span class="font-bold text-blue-600"><?php echo Bank::getTotalAccounts(); ?></span></p>
        </div>
    </div>
</body>
</html>