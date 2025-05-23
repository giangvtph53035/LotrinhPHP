<?php
namespace XYZBank\Accounts;

/**
 * Lớp tài khoản thanh toán, kế thừa BankAccount, cho phép nạp/rút tiền và ghi log giao dịch
 */
class CheckingAccount extends BankAccount {
    use TransactionLogger;

    public function deposit(float $amount): void {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Số tiền gửi phải lớn hơn 0");
        }
        $this->balance += $amount;
        $this->logTransaction("Gửi tiền", $amount, $this->balance);
    }

    public function withdraw(float $amount): void {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Số tiền rút phải lớn hơn 0");
        }
        if ($amount > $this->balance) {
            throw new \RuntimeException("Số dư không đủ");
        }
        $this->balance -= $amount;
        $this->logTransaction("Rút tiền", $amount, $this->balance);
    }

    public function getAccountType(): string {
        return "Thanh toán";
    }
}
?>