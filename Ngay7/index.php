<?php
session_start();

// Lớp cơ bản cho cộng tác viên thường
class AffiliatePartner {
    const PLATFORM_NAME = "VietLink Affiliate";

    private string $name;
    private string $email;
    private float $commissionRate;
    private bool $isActive;

    public function __construct(string $name, string $email, float $commissionRate, bool $isActive = true) {
        $this->name = $name;
        $this->email = $email;
        $this->commissionRate = $commissionRate;
        $this->isActive = $isActive;
    }

    public function __destruct() {
        // Lưu thông báo vào session thay vì echo trực tiếp
        if (!isset($_SESSION['destruct_messages'])) {
            $_SESSION['destruct_messages'] = [];
        }
        $_SESSION['destruct_messages'][] = "Cộng tác viên {$this->name} đã được giải phóng khỏi bộ nhớ.";
    }

    public function calculateCommission(float $orderValue): float {
        return ($this->commissionRate / 100) * $orderValue;
    }

    public function getSummary(): string {
        $status = $this->isActive ? "Hoạt động" : "Không hoạt động";
        return "Tên: {$this->name}<br>Email: {$this->email}<br>Tỷ lệ hoa hồng: {$this->commissionRate}%<br>Trạng thái: {$status}<br>Nền tảng: " . self::PLATFORM_NAME . "<br>";
    }

    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getCommissionRate(): float { return $this->commissionRate; }
}

// Lớp kế thừa cho cộng tác viên cao cấp
class PremiumAffiliatePartner extends AffiliatePartner {
    private float $bonusPerOrder;

    public function __construct(string $name, string $email, float $commissionRate, float $bonusPerOrder, bool $isActive = true) {
        parent::__construct($name, $email, $commissionRate, $isActive);
        $this->bonusPerOrder = $bonusPerOrder;
    }

    public function calculateCommission(float $orderValue): float {
        $baseCommission = parent::calculateCommission($orderValue);
        return $baseCommission + $this->bonusPerOrder;
    }

    public function getBonusPerOrder(): float { return $this->bonusPerOrder; }
}

// Lớp quản lý cộng tác viên
class AffiliateManager {
    private array $partners = [];

    public function addPartner(AffiliatePartner $affiliate): void {
        $this->partners[] = $affiliate;
    }

    public function listPartners(): array {
        return $this->partners;
    }

    public function totalCommission(float $orderValue): float {
        $total = 0.0;
        foreach ($this->partners as $partner) {
            $total += $partner->calculateCommission($orderValue);
        }
        return $total;
    }
}

// Khởi tạo $manager và khôi phục từ session
$manager = new AffiliateManager();

// Khôi phục danh sách từ session nếu có
if (isset($_SESSION['partners'])) {
    foreach ($_SESSION['partners'] as $partnerData) {
        if ($partnerData['isPremium']) {
            $manager->addPartner(new PremiumAffiliatePartner($partnerData['name'], $partnerData['email'], $partnerData['commissionRate'], $partnerData['bonusPerOrder']));
        } else {
            $manager->addPartner(new AffiliatePartner($partnerData['name'], $partnerData['email'], $partnerData['commissionRate']));
        }
    }
}

// Thêm 3 cộng tác viên mặc định
$manager->addPartner(new AffiliatePartner("Nguyễn Văn A", "NguyenVanA@gmail.com", 5.0));
$manager->addPartner(new AffiliatePartner("Trần Thị B", "TranThiB@gmail.com", 3.0));
$manager->addPartner(new PremiumAffiliatePartner("Lê Văn C", "LevanC@gmail.com", 7.0, 50000));
$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_partner'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $commissionRate = filter_input(INPUT_POST, 'commission_rate', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0, 'max_range' => 100]]);
    $isPremium = isset($_POST['is_premium']);
    $bonusPerOrder = $isPremium ? filter_input(INPUT_POST, 'bonus_per_order', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]) : 0;

    if ($name && $email && $commissionRate !== false) {
        if ($isPremium && $bonusPerOrder !== false) {
            $partner = new PremiumAffiliatePartner($name, $email, $commissionRate, $bonusPerOrder);
        } else {
            $partner = new AffiliatePartner($name, $email, $commissionRate);
        }
        $manager->addPartner($partner);
        $messages[] = "Đã thêm cộng tác viên {$name} thành công!";

        // Lưu vào session
        $partnerData = [
            'name' => $name,
            'email' => $email,
            'commissionRate' => $commissionRate,
            'isPremium' => $isPremium,
            'bonusPerOrder' => $bonusPerOrder
        ];
        $_SESSION['partners'][] = $partnerData;
    } else {
        $messages[] = "Dữ liệu không hợp lệ. Vui lòng kiểm tra lại!";
    }
}

$orderValue = 2000000; // Giá trị đơn hàng mặc định
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Cộng tác viên - VietLink Affiliate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 900px;
            margin-top: 30px;
        }
        .header-title {
            color: #2c3e50;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-custom {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card-custom:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .form-label {
            font-weight: 500;
            color: #34495e;
        }
        .btn-primary {
            background-color: #3498db;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
        .table-custom {
            margin-top: 30px;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background-color: #3498db;
            color: white;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #ecf0f1;
        }
        .table-hover tbody tr:hover {
            background-color: #dfe6e9;
        }
        .table-success {
            background-color: #2ecc71;
            color: white;
        }
        .alert-custom {
            border-left: 5px solid #3498db;
            background-color: #e8f4f8;
        }
        .summary-cell {
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Logo" width="60" height="60" class="rounded-circle shadow">
                <div>
                    <h1 class="fw-bold mb-0" style="color:#2980b9;">VietLink Affiliate</h1>
                    <div class="text-muted fst-italic" style="font-size:1.1rem;">Nền tảng quản lý cộng tác viên chuyên nghiệp</div>
                </div>
            </div>
            <button class="btn btn-primary btn-lg shadow" data-bs-toggle="modal" data-bs-target="#addPartnerModal">
                <i class="bi bi-person-plus"></i> Thêm cộng tác viên
            </button>
        </div>

        <!-- Thông báo -->
        <?php if ($messages): ?>
            <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-info-circle"></i>
                <?php echo implode('<br>', $messages); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Modal Thêm cộng tác viên -->
        <div class="modal fade" id="addPartnerModal" tabindex="-1" aria-labelledby="addPartnerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 shadow">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title" id="addPartnerModalLabel"><i class="bi bi-person-plus"></i> Thêm Cộng tác viên</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" class="modal-body row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="name" name="name" required autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <label for="commission_rate" class="form-label">Tỷ lệ hoa hồng (%)</label>
                            <input type="number" step="0.1" class="form-control" id="commission_rate" name="commission_rate" min="0" max="100" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_premium" name="is_premium">
                                <label class="form-check-label" for="is_premium">Cộng tác viên cao cấp</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="bonus_per_order" class="form-label">Thưởng mỗi đơn hàng (VNĐ)</label>
                            <input type="number" class="form-control" id="bonus_per_order" name="bonus_per_order" min="0" disabled>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" name="add_partner" class="btn btn-primary px-4">
                                <i class="bi bi-plus-circle"></i> Thêm cộng tác viên
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Danh sách cộng tác viên -->
        <div class="card shadow-lg border-0 rounded-4 mt-4">
            <div class="card-header bg-gradient bg-primary text-white rounded-top-4 d-flex align-items-center">
                <i class="bi bi-people-fill me-2"></i>
                <h2 class="mb-0 fs-4">Danh sách Cộng tác viên</h2>
            </div>
            <div class="card-body p-0">
                <?php $partners = $manager->listPartners(); ?>
                <?php if (empty($partners)): ?>
                    <div class="alert alert-warning m-4" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> Chưa có cộng tác viên nào trong hệ thống.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Tên</th>
                                    <th>Email</th>
                                    <th>Tỷ lệ hoa hồng</th>
                                    <th>Thưởng</th>
                                    <th>Hoa hồng</th>
                                    <th>Trạng thái</th>
                                    <th>Tóm tắt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($partners as $index => $partner): 
                                    $commission = $partner->calculateCommission($orderValue);
                                    $bonus = ($partner instanceof PremiumAffiliatePartner) ? $partner->getBonusPerOrder() : 0;
                                    $isPremium = ($partner instanceof PremiumAffiliatePartner);
                                ?>
                                <tr>
                                    <td class="fw-bold text-primary"><?php echo $index + 1; ?></td>
                                    <td>
                                        <?php if ($isPremium): ?>
                                            <span class="badge bg-success me-1">VIP</span>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($partner->getName()); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($partner->getEmail()); ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark"><?php echo htmlspecialchars(number_format($partner->getCommissionRate(), 1)); ?>%</span>
                                    </td>
                                    <td>
                                        <?php if ($bonus): ?>
                                            <span class="badge bg-warning text-dark"><?php echo number_format($bonus); ?> VNĐ</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-success"><?php echo number_format($commission); ?> VNĐ</span>
                                    </td>
                                    <td>
                                        <?php
                                            $status = $partner->isActive ?? true;
                                            if (method_exists($partner, 'getSummary')) {
                                                $summary = $partner->getSummary();
                                                $isActive = strpos($summary, 'Hoạt động') !== false;
                                            } else {
                                                $isActive = true;
                                            }
                                        ?>
                                        <?php if ($isActive): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Không hoạt động</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="white-space: pre-line; max-width: 250px;">
                                        <span class="text-muted small"><?php echo $partner->getSummary(); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <td colspan="5" class="text-end fw-bold">Tổng hoa hồng</td>
                                    <td colspan="3" class="fw-bold fs-5 text-success">
                                        <?php echo number_format($manager->totalCommission($orderValue)); ?> VNĐ
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php
        if (!empty($_SESSION['destruct_messages'])):
        ?>
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
                <?php foreach ($_SESSION['destruct_messages'] as $msg): ?>
                    <div class="alert alert-secondary alert-dismissible fade show shadow-sm mb-2" role="alert" style="min-width: 300px;">
                        <i class="bi bi-info-circle"></i>
                        <?php echo htmlspecialchars($msg); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php
        unset($_SESSION['destruct_messages']);
        endif;
        ?>
    </div>

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('is_premium').addEventListener('change', function() {
            document.getElementById('bonus_per_order').disabled = !this.checked;
        });
    </script>
</body>
</html>