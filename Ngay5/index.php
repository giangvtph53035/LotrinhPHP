<?php
require_once 'includes/header.php';
require_once 'includes/logger.php';
require_once 'includes/upload.php';

// Xử lý form gửi hành động
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = htmlspecialchars($_POST['action']);
    $ip = $_SERVER['REMOTE_ADDR'];
    $file = null;
    $error = null;

    // Xử lý upload file nếu có
    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] !== UPLOAD_ERR_NO_FILE) {
        $fileResult = handleFileUpload($_FILES['evidence']);
        if (strpos($fileResult, 'Lỗi') === 0) {
            $error = $fileResult;
        } else {
            $file = $fileResult;
        }
    }

    // Ghi log
    if (!$error) {
        logAction($action, $ip, $file);
        echo '<p class="success">Hành động đã được ghi log!</p>';
    } else {
        echo '<p class="error">' . $error . '</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhật ký hoạt động</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .form-group { margin-bottom: 15px; }
        input, select { padding: 5px; }
    </style>
</head>
<body>
    <h1>Ghi nhật ký hoạt động</h1>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="action">Hành động:</label>
            <input type="text" id="action" name="action" required>
        </div>
        <div class="form-group">
            <label for="evidence">File minh chứng (PDF, JPG, PNG):</label>
            <input type="file" id="evidence" name="evidence" accept=".pdf,.jpg,.png">
        </div>
        <button type="submit">Ghi log</button>
    </form>
    <p><a href="view_log.php">Xem nhật ký</a></p>
</body>
</html>