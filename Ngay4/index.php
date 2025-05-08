<?php
// Bắt đầu session để lưu trữ giao dịch
// Sử dụng $_SESSION để lưu danh sách giao dịch tạm thời
session_start();

// Khởi tạo biến toàn cục trong $GLOBALS để lưu tổng thu, chi và cấu hình
// Sử dụng $GLOBALS để truy xuất toàn cục
$GLOBALS['config'] = [
    'currency' => 'VND',
    'sensitive_keywords' => ['nợ xấu', 'vay nóng', 'cho vay nặng lãi']
];
$GLOBALS['total_income'] = 0;
$GLOBALS['total_expense'] = 0;

// Khởi tạo mảng giao dịch trong session nếu chưa tồn tại
if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
}

// Thiết lập cookie cho phiên làm việc gần nhất nếu chưa tồn tại
if (!isset($_COOKIE['last_session'])) {
    setcookie('last_session', date('Y-m-d H:i:s'), time() + (86400 * 30)); // Lưu 30 ngày
}

// Hàm kiểm tra dữ liệu đầu vào
function validateInput($data) {
    $errors = [];
    
    // Kiểm tra tên giao dịch: chỉ chứa chữ, số, khoảng trắng
    // Sử dụng biểu thức chính quy: /^[a-zA-Z0-9\s]+$/
    if (empty($data['transaction_name']) || !preg_match('/^[a-zA-Z0-9\s]+$/', $data['transaction_name'])) {
        $errors[] = "Tên giao dịch không hợp lệ (chỉ chứa chữ, số, khoảng trắng)";
    }
    
    // Kiểm tra số tiền: số dương, đúng định dạng
    // Sử dụng biểu thức chính quy: /^[0-9]+(\.[0-9]{1,2})?$/
    if (empty($data['amount']) || !preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $data['amount']) || $data['amount'] <= 0) {
        $errors[] = "Số tiền phải là số dương, đúng định dạng (VD: 1000 hoặc 1000.50)";
    }
    
    // Kiểm tra ngày: định dạng YYYY-MM-DD (từ input type="date")
    if (empty($data['date']) || !preg_match('/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/', $data['date'])) {
        $errors[] = "Ngày không đúng định dạng (YYYY-MM-DD)";
    }
    
    return $errors;
}

// Hàm kiểm tra từ khóa nhạy cảm trong ghi chú
function checkSensitiveNote($note) {
    // Sử dụng $GLOBALS để truy xuất danh sách từ khóa nhạy cảm
    foreach ($GLOBALS['config']['sensitive_keywords'] as $keyword) {
        if (stripos($note, $keyword) !== false) {
            return "Cảnh báo: Ghi chú chứa từ khóa nhạy cảm '$keyword'";
        }
    }
    return "";
}

// Xử lý form khi nhận dữ liệu POST
// Sử dụng $_POST để lấy dữ liệu từ form
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sử dụng $_REQUEST để lấy dữ liệu (có thể từ POST hoặc GET)
    $formData = [
        'transaction_name' => isset($_REQUEST['transaction_name']) ? trim($_REQUEST['transaction_name']) : '',
        'amount' => isset($_REQUEST['amount']) ? floatval($_REQUEST['amount']) : 0,
        'type' => isset($_REQUEST['type']) ? $_REQUEST['type'] : '',
        'note' => isset($_REQUEST['note']) ? trim($_REQUEST['note']) : '',
        'date' => isset($_REQUEST['date']) ? trim($_REQUEST['date']) : ''
    ];
    
    // Kiểm tra dữ liệu đầu vào
    $errors = validateInput($formData);
    
    // Kiểm tra loại giao dịch
    if (!in_array($formData['type'], ['income', 'expense'])) {
        $errors[] = "Loại giao dịch không hợp lệ";
    }
    
    // Nếu không có lỗi, xử lý giao dịch
    if (empty($errors)) {
        // Kiểm tra từ khóa nhạy cảm
        $warning = checkSensitiveNote($formData['note']);
        
        // Lưu giao dịch vào session
        // Sử dụng $_SESSION để lưu trữ
        $_SESSION['transactions'][] = [
            'name' => $formData['transaction_name'],
            'amount' => $formData['amount'],
            'type' => $formData['type'],
            'note' => $formData['note'],
            'date' => $formData['date'],
            'warning' => $warning
        ];
        
        // Cập nhật tổng thu/chi trong $GLOBALS
        if ($formData['type'] === 'income') {
            $GLOBALS['total_income'] += $formData['amount'];
        } else {
            $GLOBALS['total_expense'] += $formData['amount'];
        }
    }
}

// Hàm hiển thị bảng giao dịch
function displayTransactions() {
    // Kiểm tra nếu không có giao dịch
    if (empty($_SESSION['transactions'])) {
        echo "<p class='text-gray-600 italic text-center'>Chưa có giao dịch nào.</p>";
        return;
    }
    
    // Tạo bảng giao dịch
    echo "<div class='overflow-x-auto'><table class='min-w-full bg-white shadow-md rounded-lg'>";
    echo "<thead class='bg-gradient-to-r from-blue-600 to-blue-800 text-white'><tr><th class='py-3 px-4'>Tên giao dịch</th><th class='py-3 px-4'>Số tiền</th><th class='py-3 px-4'>Loại</th><th class='py-3 px-4'>Ghi chú</th><th class='py-3 px-4'>Ngày</th><th class='py-3 px-4'>Cảnh báo</th></tr></thead>";
    echo "<tbody>";
    
    // Lặp qua danh sách giao dịch để hiển thị
    foreach ($_SESSION['transactions'] as $trans) {
        $amount = number_format($trans['amount']) . ' ' . $GLOBALS['config']['currency'];
        $type = $trans['type'] === 'income' ? '<span class="text-green-600">Thu</span>' : '<span class="text-red-600">Chi</span>';
        echo "<tr class='border-b hover:bg-gray-100'>";
        echo "<td class='py-3 px-4'>{$trans['name']}</td>";
        echo "<td class='py-3 px-4 text-right'>$amount</td>";
        echo "<td class='py-3 px-4'>$type</td>";
        echo "<td class='py-3 px-4'>{$trans['note']}</td>";
        echo "<td class='py-3 px-4'>{$trans['date']}</td>";
        echo "<td class='py-3 px-4 text-red-500'>{$trans['warning']}</td>";
        echo "</tr>";
    }
    
    echo "</tbody></table></div>";
    
    // Thống kê tổng thu, chi, số dư
    $balance = $GLOBALS['total_income'] - $GLOBALS['total_expense'];
    echo "<div class='mt-6 grid grid-cols-1 md:grid-cols-3 gap-4'>";
    echo "<div class='p-4 bg-green-100 rounded-lg text-center'>";
    echo "<p class='text-lg font-semibold'>Tổng thu</p>";
    echo "<p class='text-2xl text-green-600'>" . number_format($GLOBALS['total_income']) . " {$GLOBALS['config']['currency']}</p>";
    echo "</div>";
    echo "<div class='p-4 bg-red-100 rounded-lg text-center'>";
    echo "<p class='text-lg font-semibold'>Tổng chi</p>";
    echo "<p class='text-2xl text-red-600'>" . number_format($GLOBALS['total_expense']) . " {$GLOBALS['config']['currency']}</p>";
    echo "</div>";
    echo "<div class='p-4 bg-blue-100 rounded-lg text-center'>";
    echo "<p class='text-lg font-semibold'>Số dư</p>";
    echo "<p class='text-2xl text-blue-600'>" . number_format($balance) . " {$GLOBALS['config']['currency']}</p>";
    echo "</div>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài chính cá nhân</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Kiểm tra sơ bộ phía client
        function validateForm() {
            let name = document.getElementById('transaction_name').value;
            let amount = document.getElementById('amount').value;
            let date = document.getElementById('date').value;
            
            let nameRegex = /^[a-zA-Z0-9\s]+$/;
            let amountRegex = /^[0-9]+(\.[0-9]{1,2})?$/;
            let dateRegex = /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/;
            
            if (!nameRegex.test(name)) {
                alert("Tên giao dịch chỉ được chứa chữ, số, khoảng trắng");
                return false;
            }
            if (!amountRegex.test(amount) || parseFloat(amount) <= 0) {
                alert("Số tiền phải là số dương, đúng định dạng");
                return false;
            }
            if (!dateRegex.test(date)) {
                alert("Vui lòng chọn ngày hợp lệ");
                return false;
            }
            return true;
        }
    </script>
</head>
<body class="bg-gradient-to-b from-blue-50 to-white min-h-screen">
    <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-4">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold">Quản lý tài chính cá nhân</h1>
        </div>
    </header>
    
    <div class="container mx-auto max-w-5xl px-4 py-8">
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 hover:shadow-xl transition">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Nhập giao dịch mới</h2>
            
            <!-- Hiển thị lỗi nếu có -->
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                    <ul class="list-disc pl-5">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Form nhập giao dịch -->
            <!-- Sử dụng $_SERVER['PHP_SELF'] để gửi form về chính nó -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" onsubmit="return validateForm()" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="transaction_name" class="block text-sm font-medium text-gray-700">Tên giao dịch</label>
                    <input type="text" id="transaction_name" name="transaction_name" required class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Số tiền</label>
                    <input type="text" id="amount" name="amount" required class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Loại giao dịch</label>
                    <div class="mt-2 space-x-4">
                        <label><input type="radio" name="type" value="income" required class="mr-1"> Thu</label>
                        <label><input type="radio" name="type" value="expense" class="mr-1"> Chi</label>
                    </div>
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Ngày thực hiện</label>
                    <input type="date" id="date" name="date" required class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label for="note" class="block text-sm font-medium text-gray-700">Ghi chú</label>
                    <textarea name="note" id="note" class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-blue-500 focus:border-blue-500 h-24"></textarea>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 transition">Thêm giao dịch</button>
                </div>
            </form>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Danh sách giao dịch</h2>
            <?php displayTransactions(); ?>
        </div>
    </div>
</body>
</html>