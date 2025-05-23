<?php
namespace XYZBank\Accounts;

// Interface cho các tài khoản có chức năng tính lãi suất
interface InterestBearing {
    public function calculateAnnualInterest(): float;
}
?>