<?php
require_once 'includes/header.php';

$logContent = '';
$selectedDate = date('Y-m-d');

// Xử lý form chọn ngày
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_date'])) {
    $selectedDate = htmlspecialchars($_POST['log_date']);
    $logFile = "logs/log_$selectedDate.txt";

    if (file_exists($logFile)) {
        $file = fopen($logFile, 'r');
        while (!feof($file)) {
            $line = fgets($file);
            if ($line) {
                // Đánh dấu hành động thất bại bằng màu đỏ
                $class = strpos($line, 'thất bại') !== false ? 'text-red-600' : 'text-gray-700';
                $logContent .= "<li class='$class'>" . htmlspecialchars($line) . "</li>";
            }
        }
        fclose($file);
    } else {
        $logContent = '<p class="text-gray-500">Không có nhật ký cho ngày này.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem nhật ký</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl w-full mx-auto p-6 bg-white rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Xem nhật ký hoạt động</h1>

        <!-- Form chọn ngày -->
        <form method="POST" class="flex items-center space-x-4 mb-6">
            <div class="flex-1">
                <label for="log_date" class="block text-sm font-medium text-gray-700">Chọn ngày:</label>
                <input type="date" id="log_date" name="log_date" value="<?php echo $selectedDate; ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <button type="submit" class="mt-6 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Xem</button>
        </form>

        <!-- Hiển thị nhật ký -->
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Nhật ký ngày <?php echo $selectedDate; ?></h2>
        <ul class="list-none space-y-2">
            <?php echo $logContent; ?>
        </ul>

        <!-- Liên kết quay lại -->
        <p class="mt-6 text-center">
            <a href="index.php" class="text-blue-600 hover:underline">Quay lại ghi log</a>
        </p>
    </div>
</body>
</html>