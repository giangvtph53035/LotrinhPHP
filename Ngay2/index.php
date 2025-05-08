<?php
// filepath: c:\laragon\www\LotrinhPHP\Ngay2\index.php

// Dữ liệu người dùng
$users = [
    1 => ['name' => 'Alice', 'referrer_id' => null],
    2 => ['name' => 'Bob', 'referrer_id' => 1],
    3 => ['name' => 'Charlie', 'referrer_id' => 2],
    4 => ['name' => 'David', 'referrer_id' => 3],
    5 => ['name' => 'Eva', 'referrer_id' => 1],
];

// Dữ liệu đơn hàng
$orders = [
    ['order_id' => 101, 'user_id' => 4, 'amount' => 2000000.0], // 2,000,000 VND
    ['order_id' => 102, 'user_id' => 3, 'amount' => 1500000.0], // 1,500,000 VND
    ['order_id' => 103, 'user_id' => 5, 'amount' => 3000000.0], // 3,000,000 VND
];

// Tỷ lệ hoa hồng theo cấp
$commissionRates = [
    1 => 0.10,
    2 => 0.05,
    3 => 0.02,
];

/**
 * Hàm đệ quy để tìm chuỗi giới thiệu từ người mua lên cấp trên.
 */
function getReferrerChain(int $userId, array $users, int $level = 1, int $maxLevel = 3): array {
    if ($level > $maxLevel || !isset($users[$userId]['referrer_id']) || $users[$userId]['referrer_id'] === null) {
        return [];
    }
    $referrerId = $users[$userId]['referrer_id'];
    return [$level => $referrerId] + getReferrerChain($referrerId, $users, $level + 1, $maxLevel);
}

/**
 * Hàm tính hoa hồng cho từng đơn hàng.
 */
function calculateCommission(array $orders, array $users, array $commissionRates): array {
    $commissions = [];

    foreach ($orders as $order) {
        $userId = $order['user_id'];
        $amount = $order['amount'];
        $referrerChain = getReferrerChain($userId, $users);

        foreach ($referrerChain as $level => $referrerId) {
            $commissionAmount = $amount * ($commissionRates[$level] ?? 0);
            if (!isset($commissions[$referrerId])) {
                $commissions[$referrerId] = [];
            }
            $commissions[$referrerId][] = [
                'order_id' => $order['order_id'],
                'buyer_id' => $userId,
                'level' => $level,
                'commission' => $commissionAmount,
            ];
        }
    }

    return $commissions;
}

/**
 * Hàm tổng hợp hoa hồng cho từng người dùng.
 */
function summarizeCommissions(array $commissions): array {
    $summary = [];
    foreach ($commissions as $userId => $details) {
        $total = array_reduce($details, function ($carry, $item) {
            return $carry + $item['commission'];
        }, 0);
        $summary[$userId] = $total;
    }
    return $summary;
}

// Tính toán hoa hồng
$commissions = calculateCommission($orders, $users, $commissionRates);
$summary = summarizeCommissions($commissions);

// Xuất kết quả
echo "Chi tiết hoa hồng:<br>";
foreach ($commissions as $userId => $details) {
    echo "Người dùng {$users[$userId]['name']} nhận được:<br>";
    foreach ($details as $detail) {
        echo "- Đơn hàng {$detail['order_id']} (người mua: {$users[$detail['buyer_id']]['name']}), cấp {$detail['level']}, hoa hồng: " . number_format($detail['commission'], 0, '.', ',') . " VND<br>";
    }
}

echo "<br>Tổng hoa hồng:<br>";
foreach ($summary as $userId => $total) {
    echo "- {$users[$userId]['name']}: " . number_format($total, 0, '.', ',') . " VNĐ<br>";
}
?>