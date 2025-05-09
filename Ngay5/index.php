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
        echo '<p class="text-green-600 bg-green-100 border border-green-400 rounded-lg p-4 mb-4">Hành động đã được ghi log!</p>';
    } else {
        echo '<p class="text-red-600 bg-red-100 border border-red-400 rounded-lg p-4 mb-4">' . $error . '</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhật ký hoạt động</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto p-6 bg-white rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Ghi nhật ký hoạt động</h1>
        
        <!-- Thông báo lỗi hoặc thành công -->
        <?php if (isset($error)): ?>
            <p class="text-red-600 bg-red-100 border border-red-400 rounded-lg p-4 mb-4"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div class="form-group">
                <label for="action" class="block text-sm font-medium text-gray-700">Hành động:</label>
                <input type="text" id="action" name="action" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="form-group">
                <label for="evidence" class="block text-sm font-medium text-gray-700">File minh chứng (PDF, JPG, PNG):</label>
                <input type="file" id="evidence" name="evidence" accept=".pdf,.jpg,.png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Ghi log</button>
        </form>

        <!-- Liên kết xem nhật ký -->
        <p class="mt-4 text-center">
            <a href="view_log.php" class="text-blue-600 hover:underline">Xem nhật ký</a>
        </p>
    </div>
</body>
</html>