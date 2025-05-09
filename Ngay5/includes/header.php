<?php
// Thiết lập múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Đảm bảo các thư mục tồn tại
$dirs = ['logs', 'uploads'];
foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}
?>