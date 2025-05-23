<?php
// Trait dùng để ghi log các giao dịch tài khoản
namespace XYZBank\Accounts;
trait TransactionLogger {
    public function logTransaction(string $type, float $amount, float $newBalance): void {
        // $timestamp = date('Y-m-d H:i:s');
        // $formattedAmount = number_format($amount, 0, ',', '.') . ' VNĐ';
        // $formattedBalance = number_format($newBalance, 0, ',', '.') . ' VNĐ';
        // echo "[$timestamp] Giao dịch: $type $formattedAmount | Số dư mới: $formattedBalance\n";
    }
}
?>