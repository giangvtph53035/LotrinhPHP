<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Thương mại điện tử</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light font-sans">
    <header class="bg-primary text-white shadow-lg">
        <div class="container py-4">
            <div class="d-flex align-items-center justify-content-between">
                <h1 class="text-3xl font-bold d-flex align-items-center">
                    <i class="bi bi-shop me-2"></i>E-Commerce
                </h1>
                <nav class="d-flex align-items-center gap-4">
                    <a href="#" class="text-white hover:text-gray-200 transition">Trang chủ</a>
                    <div class="position-relative">
                        <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                        <i class="bi bi-cart3 fs-4"></i>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <main class="container my-5">
        <section class="mb-5">
            <h2 class="text-2xl font-semibold mb-4 d-flex align-items-center">
                <i class="bi bi-list-ul me-2"></i>Danh sách sản phẩm
            </h2>
            <div id="product-list" class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                <?php
                include 'includes/db.php';
                try {
                    $stmt = $pdo->query("SELECT id, name FROM products");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<div class='col'>
                                <div class='card h-100 shadow-sm hover:shadow-lg transition'>
                                    <div class='card-body'>
                                        <h5 class='card-title'>
                                            <a href='#' class='product-link text-primary hover:underline' data-id='{$row['id']}'>{$row['name']}</a>
                                        </h5>
                                        <button class='add-to-cart btn btn-primary mt-2' data-id='{$row['id']}'>
                                            <i class='bi bi-cart-plus me-2'></i>Thêm vào giỏ
                                        </button>
                                    </div>
                                </div>
                              </div>";
                    }
                } catch (PDOException $e) {
                    echo "<div class='alert alert-danger'>Lỗi truy vấn: " . $e->getMessage() . "</div>";
                }
                ?>
            </div>
        </section>

        <section class="mb-5 card shadow-sm">
            <div class="card-body">
                <h2 class="text-2xl font-semibold mb-4 d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>Chi tiết sản phẩm
                </h2>
                <div id="product-details"></div>
                <button id="show-reviews" class="btn btn-outline-secondary mt-3" data-id="">
                    <i class="bi bi-chat-square-text me-2"></i>Xem đánh giá
                </button>
                <div id="reviews" class="mt-4"></div>
            </div>
        </section>

        <section class="mb-5">
            <h2 class="text-2xl font-semibold mb-4 d-flex align-items-center">
                <i class="bi bi-filter me-2"></i>Lọc theo ngành hàng
            </h2>
            <div class="d-flex gap-3">
                <select id="category" class="form-select border rounded focus:ring-2 focus:ring-primary">
                    <option value="">Chọn ngành hàng</option>
                    <option value="Điện tử">Điện tử</option>
                    <option value="Thời trang">Thời trang</option>
                </select>
                <select id="brand" class="form-select border rounded focus:ring-2 focus:ring-primary"></select>
            </div>
        </section>

        <section class="mb-5">
            <h2 class="text-2xl font-semibold mb-4 d-flex align-items-center">
                <i class="bi bi-search me-2"></i>Tìm kiếm sản phẩm
            </h2>
            <div class="position-relative">
                <input type="text" id="search" placeholder="Tìm kiếm sản phẩm..." class="form-control rounded px-4 py-2 focus:ring-2 focus:ring-primary">
                <i class="bi bi-search position-absolute end-0 top-50 translate-middle-y me-3"></i>
            </div>
            <div id="search-results" class="mt-4"></div>
        </section>

        <section class="mb-5 card shadow-sm">
            <div class="card-body">
                <h2 class="text-2xl font-semibold mb-4 d-flex align-items-center">
                    <i class="bi bi-bar-chart me-2"></i>Bình chọn cải thiện
                </h2>
                <form id="poll-form" class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="option" value="Giao diện" id="option1">
                        <label class="form-check-label" for="option1"><i class="bi bi-display me-2"></i>Giao diện</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="option" value="Tốc độ" id="option2">
                        <label class="form-check-label" for="option2"><i class="bi bi-speedometer2 me-2"></i>Tốc độ</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="option" value="Dịch vụ khách hàng" id="option3">
                        <label class="form-check-label" for="option3"><i class="bi bi-headset me-2"></i>Dịch vụ khách hàng</label>
                    </div>
                    <button type="submit" class="btn btn-success mt-2"><i class="bi bi-send me-2"></i>Gửi</button>
                </form>
                <div id="poll-results" class="mt-4"></div>
            </div>
        </section>
    </main>

    <footer class="bg-dark text-white text-center py-4">
        <p>© 2025 E-Commerce. All rights reserved.</p>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>