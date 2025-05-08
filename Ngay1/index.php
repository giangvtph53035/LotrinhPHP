<?php
// Định nghĩa hằng số
const TY_LE_HOA_HONG = 0.2; // Tỷ lệ hoa hồng 20%
const THUE_VAT = 0.1; // Thuế VAT 10%

// Dữ liệu đầu vào
$ten_chien_dich = "Spring Sale 2025";
$so_don_hang = 150;
$gia_san_pham = 99.99;
$loai_san_pham = "Thời trang";
$trang_thai = true; // true: kết thúc, false: đang chạy
$danh_sach_don = [
    "ID001" => 99,
    "ID002" => 49,
    "ID003" => 99,
    "ID004" => 79,
    "ID005" => 200
];

// Tính toán doanh thu từ danh sách đơn hàng
$doanh_thu = 0;
foreach ($danh_sach_don as $ma_don => $gia) {
    $doanh_thu += (float)$gia; // Chuyển đổi kiểu dữ liệu nếu cần
}

// Tính toán chi phí hoa hồng và lợi nhuận
$chi_phi_hoa_hong = $doanh_thu * TY_LE_HOA_HONG;
$chi_phi_thue = $doanh_thu * THUE_VAT;
$loi_nhuan = $doanh_thu - $chi_phi_hoa_hong - $chi_phi_thue;

// Đánh giá hiệu quả chiến dịch
$ket_qua = "";
if ($loi_nhuan > 0) {
    $ket_qua = "Chiến dịch thành công";
} elseif ($loi_nhuan == 0) {
    $ket_qua = "Chiến dịch hòa vốn";
} else {
    $ket_qua = "Chiến dịch thất bại";
}

// Thông báo theo loại sản phẩm
$thong_bao_sp = "";
switch ($loai_san_pham) {
    case "Thời trang":
        $thong_bao_sp = "Sản phẩm Thời trang có doanh thu ổn định";
        break;
    case "Điện tử":
        $thong_bao_sp = "Sản phẩm Điện tử có tiềm năng cao";
        break;
    case "Gia dụng":
        $thong_bao_sp = "Sản phẩm Gia dụng có nhu cầu ổn định";
        break;
    default:
        $thong_bao_sp = "Loại sản phẩm không xác định";
}

// Hiển thị kết quả
echo "Phân tích chiến dịch Affiliate Marketing";
echo "<br>";
echo "----------------------------------------";
echo "<br>";
echo "Tên chiến dịch: $ten_chien_dich";
echo "<br>";
echo "Trạng thái: " . ($trang_thai ? "Kết thúc" : "Đang chạy");
echo "<br>";
echo "Tổng doanh thu: $doanh_thu USD";
echo "<br>";
echo "Chi phí hoa hồng: $chi_phi_hoa_hong USD";
echo "<br>";
echo "Thuế VAT: $chi_phi_thue USD";
echo "<br>";
echo "Lợi nhuận: $loi_nhuan USD";
echo "<br>";
echo "Đánh giá: $ket_qua";
echo "<br>";
echo "Thông tin sản phẩm: $thong_bao_sp";
echo "<br>";
echo "Chi tiết đơn hàng:";
echo "<br>";
print_r($danh_sach_don);
echo "<br>";
echo "----------------------------------------";
echo "<br>";
echo "Chiến dịch $ten_chien_dich đã " . ($trang_thai ? "kết thúc" : "đang chạy") . " với lợi nhuận: $loi_nhuan USD";
echo "<br>";
// echo "Debug: File " . __FILE__ . " at line " . __LINE__;
// echo "<br>";
?>