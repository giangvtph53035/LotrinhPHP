<?php
function handleFileUpload($file) {
    $maxSize = 2048 * 1024  ; // 2MB
    $allowedTypes = ['jpg', 'png', 'pdf'];
    $uploadDir = 'uploads/';
    
    // Kiểm tra lỗi upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "Lỗi upload: " . getUploadErrorMessage($file['error']);
    }

    // Kiểm tra kích thước
    if ($file['size'] > $maxSize) {
        return "Lỗi: Kích thước file vượt quá 2MB.";
    }

    // Kiểm tra định dạng
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        return "Lỗi: Chỉ chấp nhận file .jpg, .png, .pdf.";
    }

    // Đổi tên file với timestamp
    $newName = 'upload_' . time() . '.' . $ext;
    $destination = $uploadDir . $newName;

    // Di chuyển file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $newName;
    } else {
        return "Lỗi: Không thể lưu file. Kiểm tra quyền thư mục uploads/";
    }
}

// Hàm lấy thông báo lỗi upload
function getUploadErrorMessage($errorCode) {
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return "File quá lớn.";
        case UPLOAD_ERR_PARTIAL:
            return "File chỉ được tải lên một phần.";
        case UPLOAD_ERR_NO_FILE:
            return "Không có file được chọn.";
        case UPLOAD_ERR_NO_TMP_DIR:
            return "Thiếu thư mục tạm.";
        case UPLOAD_ERR_CANT_WRITE:
            return "Không thể ghi file.";
        default:
            return "Lỗi không xác định.";
    }
}
?>