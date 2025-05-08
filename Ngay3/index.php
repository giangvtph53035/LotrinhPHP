<?php


// Dữ liệu giả định
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

// Hàm tính ngày công thực tế
function calculateWorkingDays($timesheet) {
    return array_map('count', $timesheet);
}

// Hàm tính lương thực lĩnh
function calculateNetSalary($employees, $workingDays, $adjustments) {
    return array_map(function ($employee) use ($workingDays, $adjustments) {
        $id = $employee['id'];
        $baseSalary = $employee['base_salary'];
        $daysWorked = $workingDays[$id] ?? 0;
        $allowance = $adjustments[$id]['allowance'] ?? 0;
        $deduction = $adjustments[$id]['deduction'] ?? 0;

        $dailySalary = $baseSalary / 22;
        $netSalary = round($dailySalary * $daysWorked + $allowance - $deduction);

        return [
            'id' => $id,
            'name' => $employee['name'],
            'working_days' => $daysWorked,
            'base_salary' => $baseSalary,
            'allowance' => $allowance,
            'deduction' => $deduction,
            'net_salary' => $netSalary,
        ];
    }, $employees);
}

// Hàm hiển thị dữ liệu lương qua bảng
function generatePayrollTable($payroll) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr>
            <th>Mã NV</th>
            <th>Họ tên</th>
            <th>Ngày công</th>
            <th>Lương cơ bản</th>
            <th>Phụ cấp</th>
            <th>Khấu trừ</th>
            <th>Lương thực lĩnh</th>
          </tr>";
    foreach ($payroll as $entry) {
        echo "<tr>
                <td>{$entry['id']}</td>
                <td>{$entry['name']}</td>
                <td>{$entry['working_days']}</td>
                <td>" . number_format($entry['base_salary']) . "</td>
                <td>" . number_format($entry['allowance']) . "</td>
                <td>" . number_format($entry['deduction']) . "</td>
                <td>" . number_format($entry['net_salary']) . "</td>
              </tr>";
    }
    echo "</table>";
}

// Hàm tìm nhân viên có ngày công cao nhất và thấp nhất
function findMinMaxWorkingDays($payroll) {
    usort($payroll, function ($a, $b) {
        return $b['working_days'] <=> $a['working_days'];
    });

    $max = $payroll[0];
    $min = $payroll[count($payroll) - 1];

    return ['max' => $max, 'min' => $min];
}

// Hàm lọc nhân viên theo số ngày công
function filterEmployeesByWorkingDays($payroll, $minDays) {
    return array_filter($payroll, function ($entry) use ($minDays) {
        return $entry['working_days'] >= $minDays;
    });
}

// Hàm kiểm tra dữ liệu
function checkData($timesheet, $adjustments, $employeeId, $date) {
    $workedOnDate = in_array($date, $timesheet[$employeeId] ?? []);
    $hasAdjustments = array_key_exists($employeeId, $adjustments);

    return [
        'worked_on_date' => $workedOnDate,
        'has_adjustments' => $hasAdjustments,
    ];
}

// Hàm làm sạch dữ liệu chấm công
function cleanTimesheet($timesheet) {
    return array_map('array_unique', $timesheet);
}

// Xử lý dữ liệu
$workingDays = calculateWorkingDays($timesheet);
$payroll = calculateNetSalary($employees, $workingDays, $adjustments);

// Báo cáo tổng hợp
echo "Bảng lương tổng hợp:";
generatePayrollTable($payroll);

// Tìm nhân viên có ngày công cao nhất và thấp nhất
$minMax = findMinMaxWorkingDays($payroll);
echo "<br>";
echo "Nhân viên làm nhiều nhất: {$minMax['max']['name']} ({$minMax['max']['working_days']} ngày công)";
echo "<br>";
echo "Nhân viên làm ít nhất: {$minMax['min']['name']} ({$minMax['min']['working_days']} ngày công)";
echo "<br>";

// Lọc nhân viên đủ điều kiện xét thưởng
$filteredEmployees = filterEmployeesByWorkingDays($payroll, 4);
echo "<br>";
echo "Danh sách nhân viên đủ điều kiện xét thưởng:";
echo "<br>";
foreach ($filteredEmployees as $employee) {
    echo "- {$employee['name']} ({$employee['working_days']} ngày công)";
    echo "<br>";
}

// Kiểm tra dữ liệu
$check = checkData($timesheet, $adjustments, 102, '2025-03-03');
echo "<br>";
echo "Trần Thị B có đi làm vào ngày 2025-03-03: " . ($check['worked_on_date'] ? 'Có' : 'Không');
echo "<br>";
echo "Thông tin phụ cấp của nhân viên 101 tồn tại: " . ($check['has_adjustments'] ? 'Có' : 'Không');
echo "<br>";


$timesheet = cleanTimesheet($timesheet);
?>