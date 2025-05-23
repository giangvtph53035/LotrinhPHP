<?php
require 'functions.php';

// Thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    if ($name && $price > 0 && $stock >= 0) {
        addProduct($name, $price, $stock);
        header("Location: index.php");
        exit;
    }
}

// Xóa sản phẩm
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    deleteProduct($id);
    header("Location: index.php");
    exit;
}

// Sửa sản phẩm
$editProduct = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    foreach (getAllProducts() as $p) {
        if ($p['id'] == $id) {
            $editProduct = $p;
            break;
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    updateProduct($id, $price, $stock);
    header("Location: index.php");
    exit;
}

$products = getLatestProducts();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm - TechFactory</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans">

    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold text-blue-600 mb-6 flex items-center">
            <i class="bi bi-box-seam me-2"></i> Danh sách sản phẩm
        </h2>

        <!-- Form thêm sản phẩm -->
        <form method="post" class="bg-white p-6 rounded-lg shadow-md mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tên sản phẩm</label>
                    <input type="text" name="name" placeholder="Tên sản phẩm" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Giá (VND)</label>
                    <input type="number" name="price" placeholder="Giá" min="0" step="1000" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Số lượng</label>
                    <input type="number" name="stock" placeholder="Số lượng" min="0" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" name="add" class="w-full bg-green-500 text-white p-2 rounded-md hover:bg-green-600 flex items-center justify-center">
                        <i class="bi bi-plus-circle me-2"></i> Thêm sản phẩm
                    </button>
                </div>
            </div>
        </form>

        <!-- Form sửa sản phẩm -->
        <?php if ($editProduct): ?>
            <form method="post" class="bg-yellow-50 p-6 rounded-lg shadow-md mb-6">
                <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tên sản phẩm</label>
                        <input type="text" value="<?= htmlspecialchars($editProduct['product_name']) ?>" disabled class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Giá (VND)</label>
                        <input type="number" name="price" value="<?= $editProduct['unit_price'] ?>" min="0" step="1000" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Số lượng</label>
                        <input type="number" name="stock" value="<?= $editProduct['stock_quantity'] ?>" min="0" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" name="update" class="w-full bg-yellow-500 text-white p-2 rounded-md hover:bg-yellow-600 flex items-center justify-center">
                            <i class="bi bi-pencil-square me-2"></i> Cập nhật
                        </button>
                        <a href="index.php" class="w-full bg-gray-500 text-white p-2 rounded-md hover:bg-gray-600 flex items-center justify-center text-center">
                            <i class="bi bi-x-circle me-2"></i> Hủy
                        </a>
                    </div>
                </div>
            </form>
        <?php endif; ?>

        <!-- Bảng sản phẩm -->
        <div class="bg-white rounded-lg shadow-md overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">ID</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Tên sản phẩm</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Giá (VND)</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Số lượng tồn</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Ngày tạo</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border-t"><?= $p['id'] ?></td>
                            <td class="px-4 py-2 border-t"><?= htmlspecialchars($p['product_name']) ?></td>
                            <td class="px-4 py-2 border-t text-green-600 font-semibold"><?= number_format($p['unit_price'], 0, ',', '.') ?></td>
                            <td class="px-4 py-2 border-t"><?= $p['stock_quantity'] ?></td>
                            <td class="px-4 py-2 border-t"><?= $p['created_at'] ?></td>
                            <td class="px-4 py-2 border-t">
                                <a href="?edit=<?= $p['id'] ?>" class="inline-block bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600">
                                    <i class="bi bi-pencil me-1"></i> Sửa
                                </a>
                                <a href="?delete=<?= $p['id'] ?>" class="inline-block bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                                    <i class="bi bi-trash me-1"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap 5 JS (for potential future interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>