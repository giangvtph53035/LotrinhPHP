<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Thương mại điện tử</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-100 font-sans">
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">E-Commerce</h1>
            <nav class="flex items-center space-x-4">
                <a href="#" class="hover:underline">Trang chủ</a>
                <div class="relative">
                    <span id="cart-count" class="bg-red-500 text-white rounded-full px-2 py-1 text-xs absolute -top-2 -right-2">0</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </nav>
        </div>
    </header>

    <main class="container mx-auto my-8 px-4">
        <section class="mb-8">
            <h2 class="text-3xl font-semibold mb-4">Danh sách sản phẩm</h2>
            <ul id="product-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php
                include 'includes/db.php';
                try {
                    $stmt = $pdo->query("SELECT id, name FROM products");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<li class='bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition'>
                                <a href='#' class='product-link text-blue-600 hover:underline' data-id='{$row['id']}'>{$row['name']}</a>
                                <button class='add-to-cart mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition' data-id='{$row['id']}'>Thêm vào giỏ</button>
                              </li>";
                    }
                } catch (PDOException $e) {
                    echo "<p class='text-red-500'>Lỗi truy vấn: " . $e->getMessage() . "</p>";
                }
                ?>
            </ul>
        </section>

        <section class="mb-8 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Chi tiết sản phẩm</h2>
            <div id="product-details"></div>
            <button id="show-reviews" class="mt-4 bg-gray-200 px-4 py-2 rounded hover:bg-gray-300 transition" data-id="">Xem đánh giá</button>
            <div id="reviews" class="mt-4"></div>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-4">Lọc theo ngành hàng</h2>
            <div class="flex space-x-4">
                <select id="category" class="border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Chọn ngành hàng</option>
                    <option value="Điện tử">Điện tử</option>
                    <option value="Thời trang">Thời trang</option>
                </select>
                <select id="brand" class="border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></select>
            </div>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-4">Tìm kiếm sản phẩm</h2>
            <div class="relative">
                <input type="text" id="search" placeholder="Tìm kiếm sản phẩm..." class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <div id="search-results" class="mt-4"></div>
        </section>

        <section class="mb-8 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Bình chọn cải thiện</h2>
            <form id="poll-form" class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="option" value="Giao diện" class="mr-2"> Giao diện
                </label>
                <label class="flex items-center">
                    <input type="radio" name="option" value="Tốc độ" class="mr-2"> Tốc độ
                </label>
                <label class="flex items-center">
                    <input type="radio" name="option" value="Dịch vụ khách hàng" class="mr-2"> Dịch vụ khách hàng
                </label>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">Gửi</button>
            </form>
            <div id="poll-results" class="mt-4"></div>
        </section>
    </main>

    <footer class="bg-gray-800 text-white p-4 text-center">
        <p>&copy; 2025 E-Commerce. All rights reserved.</p>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>