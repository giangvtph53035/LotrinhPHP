<?php
function logAction($action, $ip, $file = null) {
    $date = date('Y-m-d');
    $logFile = "logs/log_$date.txt";
    $timestamp = date('Y-m-d H:i:s');
    
    $logEntry = "[$timestamp] IP: $ip | Hành động: $action";
    if ($file) {
        $logEntry .= " | File: $file";
    }
    $logEntry .= PHP_EOL;

    // Ghi vào file, tự động tạo nếu chưa tồn tại
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
?>