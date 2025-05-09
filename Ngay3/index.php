<?php
// Dữ liệu mẫu
$employees = [
    ['id' => 101, 'name' => 'Nguyễn Văn A', 'base_salary' => 5000000],
    ['id' => 102, 'name' => 'Trần Thị B', 'base_salary' => 6000000],
    ['id' => 103, 'name' => 'Lê Văn C', 'base_salary' => 5500000],
];

$timesheet = [
    101 => ['2025-03-01', '2025-03-02', '2025-03-04', '2025-03-05'],
    102 => ['2025-03-01', '2025-03-03', '2025-03-04'],
    103 => ['2025-03-02', '2025-03-03', '2025-03-04', '2025-03-05', '2025-03-06'],
];

$adjustments = [
    101 => ['allowance' => 500000, 'deduction' => 200000],
    102 => ['allowance' => 300000, 'deduction' => 100000],
    103 => ['allowance' => 400000, 'deduction' => 150000],
];

// Số ngày làm việc chuẩn trong tháng
const STANDARD_DAYS = 22;

// Làm sạch dữ liệu chấm công (xóa ngày trùng lặp)
function cleanTimesheet(array $timesheet): void {
    foreach ($timesheet as $days) {
        $days = array_unique($days); // Xóa ngày trùng lặp
    }
}

// Tính số ngày làm việc thực tế
function calculateWorkingDays(array $timesheet): array {
    return array_map(function ($days) {
        return count($days); // Đếm số ngày làm việc
    }, $timesheet);
}

// Tính lương thực lĩnh
function calculateNetSalary(array $employee, int $workingDays, array $adjustments): float {
    $dailySalary = $employee['base_salary'] / STANDARD_DAYS;
    $salary = $dailySalary * $workingDays;
    $allowance = $adjustments[$employee['id']]['allowance'] ?? 0;
    $deduction = $adjustments[$employee['id']]['deduction'] ?? 0;
    return round($salary + $allowance - $deduction); // Làm tròn số
}

// Tạo bảng lương
function generatePayrollTable(array $employees, array $timesheet, array $adjustments): array {
    cleanTimesheet($timesheet); // Làm sạch dữ liệu chấm công
    $workingDays = calculateWorkingDays($timesheet);
    $payroll = [];

    foreach (array_keys($employees) as $index) {
        $employee = $employees[$index];
        $id = $employee['id'];
        $days = $workingDays[$id] ?? 0;
        $allowance = $adjustments[$id]['allowance'] ?? 0;
        $deduction = $adjustments[$id]['deduction'] ?? 0;
        $netSalary = calculateNetSalary($employee, $days, $adjustments);

        $payroll[] = compact('id') + [
            'name' => $employee['name'],
            'days' => $days,
            'base_salary' => $employee['base_salary'],
            'allowance' => $allowance,
            'deduction' => $deduction,
            'net_salary' => $netSalary
        ];
    }

    return $payroll;
}

// Tính tổng lương
function getTotalSalary(array $payroll): float {
    return array_sum(array_column($payroll, 'net_salary')); // Cộng tổng lương thực lĩnh
}

// Tìm nhân viên có ngày công cao nhất/thấp nhất
function findMaxMinWorkingDays(array $timesheet, array $employees): array {
    $workingDays = calculateWorkingDays($timesheet);
    $daysList = array_values($workingDays);
    sort($daysList);
    $minDays = $daysList[0];
    $maxDays = end($daysList);

    $employeeMap = array_column($employees, 'name', 'id');
    $result = ['max' => [], 'min' => []];

    foreach (array_keys($workingDays) as $id) {
        $days = $workingDays[$id];
        if ($days === $maxDays) {
            $result['max'][] = [$employeeMap[$id], $days];
        }
        if ($days === $minDays) {
            $result['min'][] = [$employeeMap[$id], $days];
        }
    }

    return $result;
}

// Lọc nhân viên có ngày công >= 4
function filterEmployeesByWorkingDays(array $timesheet, array $employees, int $threshold = 4): array {
    $workingDays = calculateWorkingDays($timesheet);
    $employeeMap = array_column($employees, 'name', 'id');

    return array_filter($workingDays, function ($days) use ($threshold) {
        return $days >= $threshold; // Lọc nhân viên có ngày công >= ngưỡng
    }, ARRAY_FILTER_USE_BOTH);
}

// Kiểm tra dữ liệu nhân viên
function checkEmployeeData(int $employeeId, string $date, array $timesheet, array $adjustments): array {
    $workedOnDate = in_array($date, $timesheet[$employeeId] ?? []); // Kiểm tra ngày làm việc
    $hasAdjustments = array_key_exists($employeeId, $adjustments); // Kiểm tra thông tin phụ cấp
    return ['worked' => $workedOnDate, 'has_adjustments' => $hasAdjustments];
}

// Cập nhật dữ liệu nhân viên và chấm công
function updateData(array &$employees, array &$timesheet): void {
    $newEmployees = [
        ['id' => 104, 'name' => 'Phạm Thị D', 'base_salary' => 5200000]
    ];
    $employees = array_merge($employees, $newEmployees); // Thêm nhân viên mới

    // Cập nhật chấm công
    if (isset($timesheet[101])) {
        array_push($timesheet[101], '2025-03-07'); // Thêm ngày vào cuối
    }
    if (isset($timesheet[102])) {
        array_unshift($timesheet[102], '2025-03-02'); // Thêm ngày vào đầu
    }
    if (isset($timesheet[103])) {
        array_pop($timesheet[103]); // Xóa ngày cuối
    }
    if (isset($timesheet[103]) && count($timesheet[103]) > 0) {
        array_shift($timesheet[103]); // Xóa ngày đầu
    }
}

// Thực thi logic
updateData($employees, $timesheet);
$payroll = generatePayrollTable($employees, $timesheet, $adjustments);
$totalSalary = getTotalSalary($payroll);
$maxMin = findMaxMinWorkingDays($timesheet, $employees);
$filteredEmployees = filterEmployeesByWorkingDays($timesheet, $employees);
$checkData = checkEmployeeData(102, '2025-03-03', $timesheet, $adjustments);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Chấm công và Tính lương</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .table-row:hover {
            transform: scale(1.01);
            transition: transform 0.2s ease-in-out;
        }
        .summary-card {
            transition: all 0.3s ease;
        }
        .summary-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <header class="text-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-800">HỆ THỐNG CHẤM CÔNG VÀ TÍNH LƯƠNG</h1>
            <p class="text-gray-600 mt-2">Báo cáo lương tháng 03/2025</p>
        </header>

        <section class="bg-white rounded-lg shadow-lg p-6 mb-8 max-w-6xl mx-auto">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4 text-center">Bảng lương tổng hợp</h2>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-indigo-600 text-white">
                            <th class="py-3 px-4 text-left">Mã NV</th>
                            <th class="py-3 px-4 text-left">Họ tên</th>
                            <th class="py-3 px-4 text-left">Ngày công</th>
                            <th class="py-3 px-4 text-left">Lương cơ bản</th>
                            <th class="py-3 px-4 text-left">Phụ cấp</th>
                            <th class="py-3 px-4 text-left">Khấu trừ</th>
                            <th class="py-3 px-4 text-left">Lương thực lĩnh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payroll as $row): ?>
                            <tr class="table-row border-b border-gray-200 bg-white">
                                <td class="py-3 px-4 text-gray-700"><?php echo $row['id']; ?></td>
                                <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="py-3 px-4 text-gray-700"><?php echo $row['days']; ?></td>
                                <td class="py-3 px-4 text-gray-700"><?php echo number_format($row['base_salary']); ?></td>
                                <td class="py-3 px-4 text-gray-700"><?php echo number_format($row['allowance']); ?></td>
                                <td class="py-3 px-4 text-gray-700"><?php echo number_format($row['deduction']); ?></td>
                                <td class="py-3 px-4 text-gray-700 font-semibold"><?php echo number_format($row['net_salary']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-lg p-6 summary-card">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Tổng quỹ lương</h3>
                <p class="text-gray-600">Tổng quỹ lương tháng 03/2025: <span class="font-bold text-indigo-600"><?php echo number_format($totalSalary); ?> VNĐ</span></p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 summary-card">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Ngày công cao nhất/thấp nhất</h3>
                <p class="text-gray-600">Nhân viên làm nhiều nhất: <span class="font-semibold"><?php echo implode(', ', array_map(fn($e) => "{$e[0]} ({$e[1]} ngày công)", $maxMin['max'])); ?></span></p>
                <p class="text-gray-600">Nhân viên làm ít nhất: <span class="font-semibold"><?php echo implode(', ', array_map(fn($e) => "{$e[0]} ({$e[1]} ngày công)", $maxMin['min'])); ?></span></p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 summary-card md:col-span-2">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Nhân viên có ngày công >= 4</h3>
                <ul class="list-disc pl-5 text-gray-600">
                    <?php
                    $employeeMap = array_column($employees, 'name', 'id');
                    foreach ($filteredEmployees as $id => $days): ?>
                        <li><?php echo htmlspecialchars($employeeMap[$id] ?? 'Không xác định'); ?> (<?php echo $days; ?> ngày công)</li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 summary-card md:col-span-2">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Kiểm tra dữ liệu</h3>
                <p class="text-gray-600">Trần Thị B có đi làm vào ngày 2025-03-03: <span class="font-semibold"><?php echo $checkData['worked'] ? 'Có' : 'Không'; ?></span></p>
                <p class="text-gray-600">Thông tin phụ cấp của nhân viên 102 tồn tại: <span class="font-semibold"><?php echo $checkData['has_adjustments'] ? 'Có' : 'Không'; ?></span></p>
            </div>
        </section>
    </div>
</body>
</html>