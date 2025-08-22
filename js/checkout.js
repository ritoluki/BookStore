const PHIVANCHUYEN = 30000;
let priceFinal = document.getElementById("checkout-cart-price-final");
// Trang thanh toan
function thanhtoanpage(option,product) {
    // Xu ly ngay nhan hang
    let today = new Date();
    let ngaymai = new Date();
    let ngaykia = new Date();
    ngaymai.setDate(today.getDate() + 1);
    ngaykia.setDate(today.getDate() + 2);
    let dateorderhtml = `<a href="javascript:;" class="pick-date active" data-date="${today}">
        <span class="text">Hôm nay</span>
        <span class="date">${today.getDate()}/${today.getMonth() + 1}</span>
        </a>
        <a href="javascript:;" class="pick-date" data-date="${ngaymai}">
            <span class="text">Ngày mai</span>
            <span class="date">${ngaymai.getDate()}/${ngaymai.getMonth() + 1}</span>
        </a>

        <a href="javascript:;" class="pick-date" data-date="${ngaykia}">
            <span class="text">Ngày kia</span>
            <span class="date">${ngaykia.getDate()}/${ngaykia.getMonth() + 1}</span>
    </a>`
    document.querySelector('.date-order').innerHTML = dateorderhtml;
    let pickdate = document.getElementsByClassName('pick-date')
    for(let i = 0; i < pickdate.length; i++) {
        pickdate[i].onclick = function () {
            document.querySelector(".pick-date.active").classList.remove("active");
            this.classList.add('active');
        }
    }

    let totalBillOrder = document.querySelector('.total-bill-order');
    let totalBillOrderHtml;
    // Xu ly don hang
    switch (option) {
        case 1: // Truong hop thanh toan san pham trong gio
            // Hien thi don hang
            showProductCart();
            // Tinh tien
            totalBillOrderHtml = `<div class="priceFlx">
            <div class="text">
                Tiền hàng 
                <span class="count">${getAmountCart()} sách</span>
            </div>
            <div class="price-detail">
                <span id="checkout-cart-total">${vnd(getCartTotal())}</span>
            </div>
        </div>
        <div class="priceFlx chk-ship">
            <div class="text">Phí vận chuyển</div>
            <div class="price-detail chk-free-ship">
                <span>${vnd(PHIVANCHUYEN)}</span>
            </div>
        </div>`;
            // Tong tien
            priceFinal.innerText = vnd(getCartTotal() + PHIVANCHUYEN);
            break;
        case 2: // Truong hop mua ngay
            // Hien thi san pham
            showProductBuyNow(product);
            // Tinh tien
            // Tính giá hỗn hợp nếu có thông tin chi tiết
            let totalProductPrice;
            if (product.discount_info) {
                const discountQty = product.discount_info.applicable_quantity;
                const regularQty = product.discount_info.remaining_quantity;
                const discountedPrice = product.discount_info.discounted_price;
                const originalPrice = product.discount_info.original_price;
                
                totalProductPrice = (discountQty * discountedPrice) + (regularQty * originalPrice);
            } else {
                // Fallback: sử dụng giá sau giảm nếu có
                const finalPrice = (product.is_discounted && product.discounted_price) ? product.discounted_price : product.price;
                totalProductPrice = product.soluong * finalPrice;
            }
            
            totalBillOrderHtml = `<div class="priceFlx">
                <div class="text">
                    Tiền hàng 
                    <span class="count">${product.soluong} sách</span>
                </div>
                <div class="price-detail">
                    <span id="checkout-cart-total">${vnd(totalProductPrice)}</span>
                </div>
            </div>
            <div class="priceFlx chk-ship">
                <div class="text">Phí vận chuyển</div>
                <div class="price-detail chk-free-ship">
                    <span>${vnd(PHIVANCHUYEN)}</span>
                </div>
            </div>`
            // Tong tien
            priceFinal.innerText = vnd(totalProductPrice + PHIVANCHUYEN);
            window.productBuyNow = product; // Lưu sản phẩm mua ngay vào biến toàn cục
            break;
    }

    // Tinh tien
    totalBillOrder.innerHTML = totalBillOrderHtml;

    // Xu ly hinh thuc giao hang
    let giaotannoi = document.querySelector('#giaotannoi');
    let tudenlay = document.querySelector('#tudenlay');
    let tudenlayGroup = document.querySelector('#tudenlay-group');
    let chkShip = document.querySelectorAll(".chk-ship");
    
    tudenlay.addEventListener('click', () => {
        giaotannoi.classList.remove("active");
        tudenlay.classList.add("active");
        chkShip.forEach(item => {
            item.style.display = "none";
        });
        tudenlayGroup.style.display = "block";
        switch (option) {
            case 1:
                priceFinal.innerText = vnd(getCartTotal());
                break;
            case 2:
                priceFinal.innerText = vnd((product.soluong * product.price));
                break;
        }
    })

    giaotannoi.addEventListener('click', () => {
        tudenlay.classList.remove("active");
        giaotannoi.classList.add("active");
        tudenlayGroup.style.display = "none";
        chkShip.forEach(item => {
            item.style.display = "flex";
        });
        switch (option) {
            case 1:
                priceFinal.innerText = vnd(getCartTotal() + PHIVANCHUYEN);
                break;
            case 2:
                priceFinal.innerText = vnd((product.soluong * product.price) + PHIVANCHUYEN);
                break;
        }
    })

    // Su kien khu nhan nut dat hang
    document.querySelector(".complete-checkout-btn").onclick = () => {
        switch (option) {
            case 1:
                xulyDathang();
                break;
            case 2:
                xulyDathang(product);
                break;
        }
    }
}

// Hien thi hang trong gio
function showProductCart() {
    let currentuser = JSON.parse(localStorage.getItem('currentuser'));
    let listOrder = document.getElementById("list-order-checkout");
    let listOrderHtml = '';
    currentuser.cart.forEach(item => {
        let product = getProduct(item);
        listOrderHtml += `<div class="book-total">
        <div class="count">${product.soluong}x</div>
        <div class="info-book">
            <div class="name-book">${product.title}</div>
        </div>
    </div>`
    })
    listOrder.innerHTML = listOrderHtml;
}

// Hien thi hang mua ngay
function showProductBuyNow(product) {
    let listOrder = document.getElementById("list-order-checkout");
    let listOrderHtml = `<div class="book-total">
        <div class="count">${product.soluong}x</div>
        <div class="info-book">
            <div class="name-book">${product.title}</div>
        </div>
    </div>`;
    listOrder.innerHTML = listOrderHtml;
}

// Hàm tự động điền thông tin người nhận nếu đã đăng nhập
function autofillReceiverInfo() {
    let currentUser = localStorage.getItem('currentuser') ? JSON.parse(localStorage.getItem('currentuser')) : null;
    if (currentUser) {
        const tenInput = document.getElementById('tennguoinhan');
        const sdtInput = document.getElementById('sdtnhan');
        const diachiInput = document.getElementById('diachinhan');
        if (tenInput) tenInput.value = currentUser.fullname || '';
        if (sdtInput) sdtInput.value = currentUser.phone || '';
        if (diachiInput) diachiInput.value = currentUser.address || '';
    }
}

//Open Page Checkout
let nutthanhtoan = document.querySelector('.thanh-toan')
let checkoutpage = document.querySelector('.checkout-page');
nutthanhtoan.addEventListener('click', () => {
    checkoutpage.classList.add('active');
    thanhtoanpage(1);
    autofillReceiverInfo(); // Gọi trực tiếp sau khi render form
    closeCart();
    body.style.overflow = "hidden"
})

// Đặt hàng ngay
function dathangngay() {
    let productInfo = document.getElementById("product-detail-content");
    let datHangNgayBtn = productInfo.querySelector(".button-dathangngay");
    datHangNgayBtn.onclick = async () => {
        let productId = datHangNgayBtn.getAttribute("data-product");
        let soluong = parseInt(productInfo.querySelector(".buttons_added .input-qty").value);
        let products = JSON.parse(localStorage.getItem('products'));
        let infoProduct = products.find(item => item.id == productId);
        
        if (soluong > infoProduct.soluong) {
            toast({ title: 'Lỗi', message: 'Số lượng vượt quá số lượng còn lại!', type: 'error', duration: 2000 });
            productInfo.querySelector(".buttons_added .input-qty").value = infoProduct.soluong;
            return;
        }
        
        // Kiểm tra giới hạn giảm giá nếu sản phẩm có giảm giá
        let discountCheck = null;
        if (infoProduct.is_discounted) {
            try {
                const response = await fetch('/Bookstore_DATN/src/controllers/check_discount_availability.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: soluong
                    })
                });
                
                discountCheck = await response.json();
                
                if (discountCheck.success && discountCheck.has_discount) {
                    if (!discountCheck.can_apply_full_discount) {
                        const maxDiscountQty = discountCheck.applicable_quantity;
                        const remainingQty = discountCheck.remaining_quantity;
                        
                        if (maxDiscountQty === 0) {
                            toast({ 
                                title: 'Thông báo', 
                                message: 'Chương trình giảm giá đã hết lượt sử dụng! Sản phẩm sẽ được bán với giá gốc.', 
                                type: 'warning', 
                                duration: 4000 
                            });
                        } else {
                            const confirmMsg = `Chương trình giảm giá chỉ còn ${maxDiscountQty} lượt sử dụng.\n` +
                                             `${maxDiscountQty} sản phẩm sẽ được giảm giá, ${remainingQty} sản phẩm còn lại sẽ có giá gốc.\n` +
                                             `Bạn có muốn tiếp tục?`;
                            
                            if (!confirm(confirmMsg)) {
                                return;
                            }
                        }
                    }
                }
            } catch (error) {
                console.error('Lỗi kiểm tra giảm giá:', error);
                // Vẫn cho phép đặt hàng nếu API lỗi
            }
        }
        
        let notevalue = productInfo.querySelector("#popup-detail-note").value;
        let ghichu = notevalue == "" ? "Không có ghi chú" : notevalue;
        let originalProduct = products.find(item => item.id == productId);
        
        // Tạo bản copy đầy đủ thông tin sản phẩm bao gồm thông tin giảm giá
        let a = {
            ...originalProduct,
            soluong: parseInt(soluong),
            note: ghichu
        };
        
        // Thêm thông tin giảm giá chi tiết nếu có
        if (infoProduct.is_discounted && discountCheck && discountCheck.success && discountCheck.has_discount) {
            a.discount_info = {
                applicable_quantity: discountCheck.applicable_quantity,
                remaining_quantity: discountCheck.remaining_quantity,
                discounted_price: discountCheck.discount_info?.discounted_price || infoProduct.discounted_price,
                original_price: discountCheck.discount_info?.original_price || infoProduct.price
            };
        }
        checkoutpage.classList.add('active');
        thanhtoanpage(2,a);
        autofillReceiverInfo(); // Tự động điền thông tin người nhận khi đặt hàng ngay
        closeCart();
        let modal = document.querySelector('.modal.product-detail');
        modal.classList.remove('open');
        body.style.overflow = "hidden"
    }
}

// Close Page Checkout
function closecheckout() {
    checkoutpage.classList.remove('active');
    body.style.overflow = "auto"
}

// Thong tin cac don hang da mua - Xu ly khi nhan nut dat hang
async function xulyDathang(product, paymentMethod = 'cod', returnInfo = false) {
    let diachinhan = "";
    let hinhthucgiao = "";
    let thoigiangiao = "";
    let giaotannoi = document.querySelector("#giaotannoi");
    let tudenlay = document.querySelector("#tudenlay");
    let giaongay = document.querySelector("#giaongay");
    let giaovaogio = document.querySelector("#deliverytime");
    let currentUser = JSON.parse(localStorage.getItem('currentuser'));
    
    // Hình thức giao & Địa chỉ nhận hàng
    if(giaotannoi.classList.contains("active")) {
        diachinhan = document.querySelector("#diachinhan").value;
        hinhthucgiao = giaotannoi.innerText;
    }
    if(tudenlay.classList.contains("active")){
        let chinhanh1 = document.querySelector("#chinhanh-1");
        let chinhanh2 = document.querySelector("#chinhanh-2");
        if(chinhanh1.checked) {
            diachinhan = "Hoài Đức, Hà Nội";
        }
        if(chinhanh2.checked) {
            diachinhan = "Cầu Giấy, Hà Nội";
        }
        hinhthucgiao = tudenlay.innerText;
    }

    // Thời gian nhận hàng
    if(giaongay.checked) {
        thoigiangiao = "Giao ngay khi xong";
    }

    if(giaovaogio.checked) {
        thoigiangiao = document.querySelector(".choise-time").value;
    }

    let orderDetails = localStorage.getItem("orderDetails") ? JSON.parse(localStorage.getItem("orderDetails")) : [];
    let order = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];
    let madon = createId(order);
    let tongtien = 0;
    let newOrderDetails = [];
    
    // Handle product(s)
    if(product == undefined) {
        if (currentUser && currentUser.cart) {
            currentUser.cart.forEach(item => {
                item.madon = madon;
                item.price = getpriceProduct(item.id);
                tongtien += item.price * item.soluong;
                newOrderDetails.push(item);
            });
        }
    } else {
        product.madon = madon;
        product.price = getpriceProduct(product.id);
        tongtien += product.price * product.soluong;
        newOrderDetails.push(product);
    }
    // CỘNG PHÍ VẬN CHUYỂN nếu chọn giao tận nơi
    if (giaotannoi.classList.contains("active")) {
        tongtien += PHIVANCHUYEN;
    }
    
    let tennguoinhan = document.querySelector("#tennguoinhan").value;
    let sdtnhan = document.querySelector("#sdtnhan").value;

    // Kiểm tra nếu chưa đăng nhập và số điện thoại đã tồn tại trong user
    let accounts = localStorage.getItem('accounts') ? JSON.parse(localStorage.getItem('accounts')) : [];
    let isRegistered = accounts.some(acc => acc.phone == sdtnhan);
    if (!currentUser && isRegistered) {
        toast({ title: 'Chú ý', message: 'Số điện thoại này đã đăng ký tài khoản. Vui lòng đăng nhập để đặt hàng!', type: 'warning', duration: 4000 });
        return;
    }

    if(tennguoinhan == "" || sdtnhan == "" || diachinhan == "") {
        toast({ title: 'Chú ý', message: 'Vui lòng nhập đầy đủ thông tin !', type: 'warning', duration: 4000 });
    } else {
        // Tạo đối tượng Date với time zone cho Vietnam (GMT+7)
        const now = new Date();
        const vnTime = new Date(now.getTime() + (7 * 60 * 60 * 1000));
        
        let donhang = {
            id: madon,
            khachhang: currentUser ? currentUser.phone : sdtnhan,
            hinhthucgiao: hinhthucgiao,
            ngaygiaohang: document.querySelector(".pick-date.active").getAttribute("data-date"),
            thoigiangiao: thoigiangiao,
            ghichu: document.querySelector(".note-order").value,
            tenguoinhan: tennguoinhan,
            sdtnhan: sdtnhan,
            diachinhan: diachinhan,
            thoigiandat: vnTime.toISOString(),
            tongtien: tongtien,
            trangthai: 0,
            payment_method: paymentMethod
        }
    
        order.unshift(donhang);

        // Clear cart if user is logged in
        if(currentUser && currentUser.cart) {
            currentUser.cart.length = 0;
            localStorage.setItem("currentuser", JSON.stringify(currentUser));
        }
    
        localStorage.setItem("order", JSON.stringify(order));
        let allOrderDetails = localStorage.getItem("orderDetails") ? JSON.parse(localStorage.getItem("orderDetails")) : [];
        allOrderDetails = newOrderDetails.concat(allOrderDetails);
        localStorage.setItem("orderDetails", JSON.stringify(allOrderDetails));

        // Trừ số lượng tồn kho
        let products = JSON.parse(localStorage.getItem('products'));
        if(product == undefined) {
            if (currentUser && currentUser.cart) {
                currentUser.cart.forEach(async item => {
                    let p = products.find(sp => sp.id == item.id);
                    if (p) {
                        p.soluong = Math.max(0, Number(p.soluong) - Number(item.soluong));
                        // Gọi API cập nhật số lượng về database
                        await fetch('src/controllers/update_product_quantity.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: p.id, soluong: Number(p.soluong) })
                        });
                    }
                });
            }
        } else {
            let p = products.find(sp => sp.id == product.id);
            if (p) {
                p.soluong = Math.max(0, Number(p.soluong) - Number(product.soluong));
                // Gọi API cập nhật số lượng về database
                await fetch('src/controllers/update_product_quantity.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: p.id, soluong: Number(p.soluong) })
                });
            }
        }
        localStorage.setItem('products', JSON.stringify(products));

        // Cập nhật số lượng đã bán cho sản phẩm
        if(product == undefined) {
            if (currentUser && currentUser.cart) {
                currentUser.cart.forEach(item => {
                    updateProductSales(item.id, item.soluong);
                });
            }
        } else {
            updateProductSales(product.id, product.soluong);
        }

        toast({ title: 'Thành công', message: 'Đặt hàng thành công !', type: 'success', duration: 1000 });
        
        // Gửi dữ liệu đơn hàng và chi tiết đơn hàng đến server để lưu vào database
        let formData = new FormData();
        formData.append('order', JSON.stringify(donhang));
        formData.append('orderDetails', JSON.stringify(newOrderDetails));
        const response = await fetch('src/controllers/add_order.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        // Nếu đặt hàng thành công, cập nhật số lượng sử dụng giảm giá
        if (result.success && result.orderId) {
            try {
                await fetch('/Bookstore_DATN/src/controllers/update_discount_usage.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: result.orderId })
                });
            } catch (error) {
                console.error('Lỗi cập nhật discount usage:', error);
            }
        }

        // Redirect đến trang thành công nếu có URL
        if (result.redirect_url) {
            setTimeout(() => {
                window.location.href = result.redirect_url;
            }, 1500);
        } else {
            // Fallback về trang chủ nếu không có redirect_url
            setTimeout(() => {
                window.location.href = "/Bookstore_DATN/";
            }, 2000);
        }  
    }

    // Sau khi lưu đơn hàng và orderDetails:
    if (returnInfo) {
        return { orderId: madon, amount: tongtien };
    }
}


function getpriceProduct(id) {
    let products = JSON.parse(localStorage.getItem('products'));
    let sp = products.find(item => {
        return item.id == id;
    })
    // Sử dụng giá sau giảm nếu có, không thì dùng giá gốc
    return (sp.is_discounted && sp.discounted_price) ? sp.discounted_price : sp.price;
}

// Thêm sự kiện cho nút VNPay
const vnpayBtn = document.getElementById('btnVnpay');
if (vnpayBtn) {
    vnpayBtn.addEventListener('click', async function() {
        let product = window.productBuyNow || undefined;
        let result = await xulyDathang(product, 'online', true);
        if (!result || !result.orderId || !result.amount || result.amount <= 0) {
            toast({ title: 'Lỗi', message: 'Đơn hàng không hợp lệ hoặc không có sản phẩm!', type: 'error', duration: 3000 });
            return;
        }
        let amount = result.amount;
        let orderId = result.orderId;
        let orderInfo = 'Thanh toán đơn hàng ' + orderId;

        // Xóa biến productBuyNow sau khi đặt hàng
        window.productBuyNow = undefined;

        // Tạo form ẩn để submit sang PHP (POST)
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '/Bookstore_DATN/vnpay_php/vnpay_pay.php';

        let inputAmount = document.createElement('input');
        inputAmount.type = 'hidden';
        inputAmount.name = 'amount';
        inputAmount.value = amount;

        let inputOrderId = document.createElement('input');
        inputOrderId.type = 'hidden';
        inputOrderId.name = 'order_id';
        inputOrderId.value = orderId;

        let inputOrderInfo = document.createElement('input');
        inputOrderInfo.type = 'hidden';
        inputOrderInfo.name = 'order_desc';
        inputOrderInfo.value = orderInfo;

        form.appendChild(inputAmount);
        form.appendChild(inputOrderId);
        form.appendChild(inputOrderInfo);

        document.body.appendChild(form);
        form.submit();
    });
}