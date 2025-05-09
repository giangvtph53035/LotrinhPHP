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
                $style = strpos($line, 'thất bại') !== false ? 'color: red;' : '';
                $logContent .= "<li style='$style'>" . htmlspecialchars($line) . "</li>";
            }
        }
        fclose($file);
    } else {
        $logContent = '<p>Không có nhật ký cho ngày này.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xem nhật ký</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Xem nhật ký hoạt động</h1>
    <form method="POST">
        <label for="log_date">Chọn ngày:</label>
        <input type="date" id="log_date" name="log_date" value="<?php echo $selectedDate; ?>" required>
        <button type="submit">Xem</button>
    </form>
    <h2>Nhật ký ngày <?php echo $selectedDate; ?></h2>
    <ul>
        <?php echo $logContent; ?>
    </ul>
    <p><a href="index.php">Quay lại ghi log</a></p>
</body>
</html>