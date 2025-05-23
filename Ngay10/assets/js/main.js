document.addEventListener('DOMContentLoaded', () => {
    // Tính năng 1: Lấy chi tiết sản phẩm
    const productLinks = document.querySelectorAll('.product-link');
    productLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const id = link.getAttribute('data-id');
            fetch(`product.php?id=${id}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('product-details').innerHTML = data;
                    document.getElementById('show-reviews').setAttribute('data-id', id);
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
                body: `product_id=${id}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('cart-count').textContent = `Giỏ hàng: ${data.cartCount}`;
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    // Tính năng 3: Hiển thị đánh giá
    document.getElementById('show-reviews').addEventListener('click', () => {
        const id = document.getElementById('show-reviews').getAttribute('data-id');
        fetch(`reviews.php?product_id=${id}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('reviews').innerHTML = data;
            })
            .catch(error => console.error('Error:', error));
    });

    // Tính năng 4: Lấy danh sách thương hiệu
    document.getElementById('category').addEventListener('change', () => {
        const category = document.getElementById('category').value;
        if (category) {
            fetch(`brands.php?category=${encodeURIComponent(category)}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('brand').innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }
    });

    // Tính năng 5: Tìm kiếm thời gian thực
    document.getElementById('search').addEventListener('input', () => {
        const query = document.getElementById('search').value;
        fetch(`search.php?q=${encodeURIComponent(query)}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('search-results').innerHTML = data;
            })
            .catch(error => console.error('Error:', error));
    });

    // Tính năng 6: Bình chọn
    document.getElementById('poll-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const option = document.querySelector('input[name="option"]:checked');
        if (option) {
            fetch('poll.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `option=${encodeURIComponent(option.value)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
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