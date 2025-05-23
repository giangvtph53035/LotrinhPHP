document.addEventListener('DOMContentLoaded', () => {
    // Tính năng 1: Lấy chi tiết sản phẩm
    const productLinks = document.querySelectorAll('.product-link');
    productLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault(); // Ngăn chuyển trang mặc định
            const id = link.getAttribute('data-id'); // Lấy id sản phẩm
            fetch(`product.php?id=${id}`) // Gửi AJAX lấy chi tiết sản phẩm
                .then(response => response.text())
                .then(data => {
                    document.getElementById('product-details').innerHTML = data; // Hiển thị chi tiết
                    document.getElementById('show-reviews').setAttribute('data-id', id); // Gán id cho nút xem đánh giá
                })
                .catch(error => console.error('Error:', error));
        });
    });

    // Tính năng 2: Thêm vào giỏ hàng
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id'); 
            fetch('cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${id}` // Gửi id sản phẩm lên server
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật số lượng giỏ hàng trên giao diện
                        document.getElementById('cart-count').textContent = `Giỏ hàng: ${data.cartCount}`;
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    // Tính năng 3: Hiển thị đánh giá
    document.getElementById('show-reviews').addEventListener('click', () => {
        const id = document.getElementById('show-reviews').getAttribute('data-id'); // Lấy id sản phẩm
        fetch(`reviews.php?product_id=${id}`) // Gửi AJAX lấy đánh giá
            .then(response => response.text())
            .then(data => {
                document.getElementById('reviews').innerHTML = data; // Hiển thị đánh giá
            })
            .catch(error => console.error('Error:', error));
    });

    // Tính năng 4: Lấy danh sách thương hiệu
    document.getElementById('category').addEventListener('change', () => {
        const category = document.getElementById('category').value;
        if (category) {
            fetch(`brands.php?category=${encodeURIComponent(category)}`) // Gửi AJAX lấy thương hiệu theo danh mục
                .then(response => response.text())
                .then(data => {
                    document.getElementById('brand').innerHTML = data; // Hiển thị danh sách thương hiệu
                })
                .catch(error => console.error('Error:', error));
        }
    });

    // Tính năng 5: Tìm kiếm thời gian thực
    document.getElementById('search').addEventListener('input', () => {
        const query = document.getElementById('search').value;
        fetch(`search.php?q=${encodeURIComponent(query)}`) // Gửi AJAX tìm kiếm sản phẩm
            .then(response => response.text())
            .then(data => {
                document.getElementById('search-results').innerHTML = data; // Hiển thị kết quả tìm kiếm
            })
            .catch(error => console.error('Error:', error));
    });

    // Tính năng 6: Bình chọn
    document.getElementById('poll-form').addEventListener('submit', (e) => {
        e.preventDefault(); // Ngăn reload trang
        const option = document.querySelector('input[name="option"]:checked'); // Lấy lựa chọn
        if (option) {
            fetch('poll.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `option=${encodeURIComponent(option.value)}` // Gửi lựa chọn lên server
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hiển thị kết quả bình chọn
                        let resultHtml = '<h3>Kết quả bình chọn:</h3>';
                        data.result.forEach(item => {
                            resultHtml += `<p>${item.option}: ${item.percent}%</p>`;
                        });
                        document.getElementById('poll-results').innerHTML = resultHtml;
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
});