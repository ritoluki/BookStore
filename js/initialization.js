// Khởi tạo danh sách sản phẩm
function createProduct() {
    if (localStorage.getItem('products') === null) {
        // Sử dụng AJAX để lấy dữ liệu từ server
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "src/controllers/get_products.php", true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Chuyển đổi dữ liệu JSON thành đối tượng JavaScript
                let products = JSON.parse(xhr.responseText);

                // Đảm bảo rằng sản phẩm có cấu trúc chính xác
                products = products.map(product => {
                    return {
                        id: Number(product.id),             // Chuyển đổi thành số nếu cần
                        status: Number(product.status),     // Chuyển đổi thành số nếu cần
                        title: String(product.title),       // Đảm bảo là chuỗi
                        img: String(product.img),           // Đảm bảo là chuỗi
                        category: String(product.category), // Đảm bảo là chuỗi
                        price: Number(product.price),       // Chuyển đổi thành số nếu cần
                        soluong: Number(product.soluong),   // Thêm trường số lượng
                        sold_quantity: Number(product.sold_quantity || 0), // Số lượng đã bán
                        is_bestseller: Boolean(product.is_bestseller), // Sách bán chạy
                        desc: String(product.describes), // Đảm bảo là chuỗi
                        
                        // Thông tin giảm giá
                        discounted_price: product.discounted_price ? Number(product.discounted_price) : null,
                        discount_type: product.discount_type || null,
                        discount_value: product.discount_value ? Number(product.discount_value) : null,
                        min_order_amount: product.min_order_amount ? Number(product.min_order_amount) : 0,
                        is_discounted: Boolean(product.is_discounted)
                    };
                });

                // Lưu dữ liệu vào localStorage
                localStorage.setItem('products', JSON.stringify(products));
            }
        };
        xhr.send();
    }
}

// Hàm cập nhật danh sách sản phẩm từ server
function refreshProducts() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "src/controllers/get_products.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let products = JSON.parse(xhr.responseText);

            // Đảm bảo rằng sản phẩm có cấu trúc chính xác
            products = products.map(product => {
                return {
                    id: Number(product.id),
                    status: Number(product.status),
                    title: String(product.title),
                    img: String(product.img),
                    category: String(product.category),
                    price: Number(product.price),
                    soluong: Number(product.soluong),
                    sold_quantity: Number(product.sold_quantity || 0), // Số lượng đã bán
                    is_bestseller: Boolean(product.is_bestseller), // Sách bán chạy
                    desc: String(product.describes),
                    
                    // Thông tin giảm giá
                    discounted_price: product.discounted_price ? Number(product.discounted_price) : null,
                    discount_type: product.discount_type || null,
                    discount_value: product.discount_value ? Number(product.discount_value) : null,
                    min_order_amount: product.min_order_amount ? Number(product.min_order_amount) : 0,
                    is_discounted: Boolean(product.is_discounted)
                };
            });

            // Cập nhật lại localStorage với dữ liệu mới
            localStorage.setItem('products', JSON.stringify(products));
        }
    };
    xhr.send();
}

// Hàm lấy danh sách sản phẩm từ localStorage
function getProducts() {
    let products = localStorage.getItem('products');
    if (products) {
        products = JSON.parse(products);
        console.log(products);
    }
}

// Tạo tài khoản admin
function createAdminAccount() {
    let accounts = localStorage.getItem("accounts");
    if (!accounts) {
        fetch('src/controllers/getAccounts.php')
            .then(response => response.json())
            .then(data => {
                // Đảm bảo các kiểu dữ liệu được xử lý chính xác trong JavaScript
                data = data.map(account => {
                    return {
                        fullname: account.fullname,
                        phone: account.phone,
                        password: account.password,
                        address: account.address,
                        email: account.email,
                        status: account.status, // Đã là kiểu số nguyên từ PHP
                        join: new Date(account.join_date), // Chuyển chuỗi thành đối tượng Date
                        cart: account.cart || [], // Đảm bảo cart là một mảng
                        userType: account.userType // Đã là kiểu số nguyên từ PHP
                    };
                });

                localStorage.setItem('accounts', JSON.stringify(data));
            });
    }
}

// Hàm cập nhật danh sách tài khoản admin từ server
function refreshAccounts() {
    fetch('src/controllers/getAccounts.php')
        .then(response => response.json())
        .then(data => {
            data = data.map(account => {
                return {
                    fullname: account.fullname,
                    phone: account.phone,
                    password: account.password,
                    address: account.address,
                    email: account.email,
                    status: account.status,
                    join: new Date(account.join_date),
                    cart: account.cart || [],
                    userType: account.userType
                };
            });

            // Cập nhật lại localStorage với dữ liệu mới
            localStorage.setItem('accounts', JSON.stringify(data));
        });
}

// Khởi tạo danh sách chi tiết đơn hàng
function createOrderDetails() {
    if (localStorage.getItem('orderDetails') === null) {
        // Sử dụng AJAX để lấy dữ liệu từ server
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "src/controllers/get_order_details.php", true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                try {
                    // Chuyển đổi dữ liệu JSON thành đối tượng JavaScript
                    let response = JSON.parse(xhr.responseText);
                    let orderDetails = response.orderDetails || [];

                    // Đảm bảo rằng chi tiết đơn hàng có cấu trúc chính xác
                    if (Array.isArray(orderDetails)) {
                        orderDetails = orderDetails.map(detail => {
                            return {
                                madon: String(detail.madon || ''),
                                id: Number(detail.product_id || 0),
                                note: String(detail.note || ''),
                                price: Number(detail.product_price || 0),
                                soluong: Number(detail.soluong || 0)
                            };
                        });

                        // Lưu dữ liệu vào localStorage
                        localStorage.setItem('orderDetails', JSON.stringify(orderDetails));
                    } else {
                        // Nếu không phải mảng, khởi tạo mảng rỗng
                        localStorage.setItem('orderDetails', JSON.stringify([]));
                    }
                } catch (e) {
                    console.error("Error parsing JSON response:", e);
                    localStorage.setItem('orderDetails', JSON.stringify([]));
                }
            }
        };
        xhr.send();
    }
}

// Khởi tạo danh sách đơn hàng
function createOrders() {
    if (localStorage.getItem('order') === null) {
        // Sử dụng AJAX để lấy dữ liệu từ server
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "src/controllers/get_orders.php", true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Chuyển đổi dữ liệu JSON thành đối tượng JavaScript
                let orders = JSON.parse(xhr.responseText);

                // Đảm bảo rằng đơn hàng có cấu trúc chính xác
                orders = orders.map(order => {
                    return {
                        id: String(order.id),                       // Chuyển đổi thành số nếu cần
                        khachhang: String(order.khachhang),         // Đảm bảo là chuỗi
                        hinhthucgiao: String(order.hinhthucgiao),   // Đảm bảo là chuỗi
                        ngaygiaohang: String(order.ngaygiaohang),   // Đảm bảo là chuỗi
                        thoigiangiao: String(order.thoigiangiao),   // Đảm bảo là chuỗi
                        ghichu: String(order.ghichu),               // Đảm bảo là chuỗi
                        tenguoinhan: String(order.tenguoinhan),     // Đảm bảo là chuỗi
                        sdtnhan: String(order.sdtnhan),             // Đảm bảo là chuỗi
                        diachinhan: String(order.diachinhan),       // Đảm bảo là chuỗi
                        thoigiandat: String(order.thoigiandat),   // Chuyển chuỗi thành đối tượng Date
                        tongtien: Number(order.tongtien),           // Chuyển đổi thành số nếu cần
                        trangthai: Number(order.trangthai)          // Chuyển đổi thành số nếu cần
                    };
                });

                // Lưu dữ liệu vào localStorage
                localStorage.setItem('order', JSON.stringify(orders));
            }
        };
        xhr.send();
    }
}

function cancelOrder(orderId, btn) {
    let currentUser = JSON.parse(localStorage.getItem('currentuser'));
    if (!currentUser) {
        toast({ title: 'Lỗi', message: 'Bạn cần đăng nhập để hủy đơn hàng!', type: 'error', duration: 2000 });
        return;
    }
    if (!confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')) return;

    let bodyData = { orderId: orderId };
    if (currentUser.userType == 1) {
        // Admin
        bodyData.isAdmin = true;
    } else {
        // Khách
        bodyData.userPhone = currentUser.phone;
    }

    fetch('src/controllers/cancel_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(bodyData)
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                toast({ title: 'Thành công', message: data.message, type: 'success', duration: 2000 });
                // Reload lại danh sách đơn hàng
                renderOrderProduct && renderOrderProduct();
                // Đóng modal nếu có
                document.querySelector('.modal.detail-order')?.classList.remove('open');
            } else {
                toast({ title: 'Lỗi', message: data.message, type: 'error', duration: 2000 });
            }
        })
        .catch(err => {
            toast({ title: 'Lỗi', message: 'Có lỗi khi kết nối server!', type: 'error', duration: 2000 });
        });
}

// Gọi các hàm cập nhật khi tải lại trang
window.onload = function () {
    createProduct();
    createAdminAccount();
    createOrders();
    createOrderDetails();
    refreshProducts();  // Cập nhật sản phẩm sau khi tải lại trang
    refreshAccounts();  // Cập nhật tài khoản sau khi tải lại trang
    
    // Cập nhật dữ liệu sản phẩm với thông tin giảm giá khi focus lại trang
    // (người dùng có thể đã thay đổi khuyến mãi ở tab khác)
    window.addEventListener('focus', async () => {
        if (typeof window.updateProductsWithDiscounts === 'function') {
            await window.updateProductsWithDiscounts();
        }
    });
    
    // Cập nhật dữ liệu định kỳ nhưng ít thường xuyên hơn (mỗi 5 phút)
    setInterval(async () => {
        if (typeof window.updateProductsWithDiscounts === 'function') {
            await window.updateProductsWithDiscounts();
        }
    }, 300000); // 5 phút
};
