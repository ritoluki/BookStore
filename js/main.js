// Doi sang dinh dang tien VND
function vnd(price) {
    return price.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
}

// Close popup 
const body = document.querySelector("body");
let modalContainer = document.querySelectorAll('.modal');
let modalBox = document.querySelectorAll('.mdl-cnt');
let formLogSign = document.querySelector('.forms');
let currentPage = 1;
let totalPage = 0;
let perPage = 12;

// Click vùng ngoài sẽ tắt Popup
modalContainer.forEach(item => {
    item.addEventListener('click', closeModal);
});

modalBox.forEach(item => {
    item.addEventListener('click', function (event) {
        event.stopPropagation();
    })
});

function closeModal() {
    modalContainer.forEach(item => {
        item.classList.remove('open');
    });
    body.style.overflow = "auto";
}

function increasingNumber(e) {
    let qty = e.parentNode.querySelector('.input-qty');
    if (parseInt(qty.value) < qty.max) {
        qty.value = parseInt(qty.value) + 1;
    } else {
        qty.value = qty.max;
    }
}

function decreasingNumber(e) {
    let qty = e.parentNode.querySelector('.input-qty');
    if (qty.value > qty.min) {
        qty.value = parseInt(qty.value) - 1;
    } else {
        qty.value = qty.min;
    }
}


//Xem chi tiet san pham
function detailProduct(index) {
    let modal = document.querySelector('.modal.product-detail');
    let products = JSON.parse(localStorage.getItem('products'));
    event.preventDefault();
    // Sửa: dùng so sánh == và kiểm tra null
    let infoProduct = products.find(sp => sp.id == index);
    if (!infoProduct) {
        alert('Không tìm thấy thông tin sản phẩm!');
        return;
    }
    let modalHtml = `<div align="center" class="modal-header">
    <img class="product-image" src="${infoProduct.img || './assets/img/blank-image.png'}" alt="" >
    </div>
    <div class="modal-body">
        <h2 class="product-title">${infoProduct.title}</h2>
        <div class="product-control">
            <div class="priceBox">
                <span class="current-price">${vnd(infoProduct.price)}</span>
            </div>
            ${Number(infoProduct.soluong) == 0 ? `<div class="product-stock out-of-stock">Tạm hết hàng</div>` : `<div class="product-stock">Số lượng: <b>${Number(infoProduct.soluong)}</b></div>`}
            <div class="buttons_added" style="${Number(infoProduct.soluong) == 0 ? 'display:none;' : ''}">
                <input class="minus is-form" type="button" value="-" onclick="decreasingNumber(this)">
                <input class="input-qty" max="${infoProduct.soluong}" min="1" name="" type="number" value="1">
                <input class="plus is-form" type="button" value="+" onclick="increasingNumber(this)">
            </div>
        </div>
        <p class="product-description">${infoProduct.describes || ''}</p>
        <div class="book-detail-tabs">
            <button id="tab-buy" class="tab-btn active" onclick="switchBookTab('buy')">Mua sách</button>
            <button id="tab-review" class="tab-btn" onclick="switchBookTab('review')">Đánh giá</button>
        </div>
        <div class="tab-buy-content">
            <div class="notebox">
                <p class="notebox-title">Ghi chú</p>
                <textarea class="text-note" id="popup-detail-note" placeholder="Nhập thông tin cần lưu ý..."></textarea>
            </div>
            <div class="modal-footer">
                <div class="price-total">
                    <span class="thanhtien">Thành tiền</span>
                    <span class="price">${vnd(infoProduct.price)}</span>
                </div>
                <div class="modal-footer-control">
                    <button class="button-dathangngay${Number(infoProduct.soluong) == 0 ? ' btn-disabled' : ''}" data-product="${infoProduct.id}" ${Number(infoProduct.soluong) == 0 ? 'disabled' : ''}>
                        <i class="fa-light"></i> Đặt hàng ngay
                    </button>
                    <button class="button-dat${Number(infoProduct.soluong) == 0 ? ' btn-disabled' : ''}" id="add-cart" onclick="animationCart()" ${Number(infoProduct.soluong) == 0 ? 'disabled' : ''}>
                        <i class="fa-light fa-basket-shopping"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="tab-review-content tab-hidden">
            <div class="book-review-section">
                <div class="book-rating-container">
                    <div class="book-rating-options">
                        <div class="book-rating-option selected" data-rating="excellent">
                            <div class="book-emoji">😍</div>
                            <div class="book-rating-label">Tuyệt vời</div>
                        </div>
                        <div class="book-rating-option" data-rating="good">
                            <div class="book-emoji">😊</div>
                            <div class="book-rating-label">Sách hay</div>
                        </div>
                        <div class="book-rating-option" data-rating="ok">
                            <div class="book-emoji">😐</div>
                            <div class="book-rating-label">Khá ổn</div>
                        </div>
                        <div class="book-rating-option" data-rating="bad">
                            <div class="book-emoji">😞</div>
                            <div class="book-rating-label">Chưa hay</div>
                        </div>
                        <div class="book-rating-option" data-rating="terrible">
                            <div class="book-emoji">😱</div>
                            <div class="book-rating-label">Dở tệ</div>
                        </div>
                    </div>
                    <div class="book-comment-section">
                        <textarea 
                            class="book-comment-input" 
                            placeholder="Viết nhận xét về sách (tùy chọn)"
                            rows="3"
                        ></textarea>
                    </div>
                </div>
                <div class="book-button-group">
                    <button class="book-btn book-btn-primary" onclick="submitBookRating()">Gửi đánh giá</button>
                </div>
            </div>
        </div>`;
    document.querySelector('#product-detail-content').innerHTML = modalHtml;
    modal.classList.add('open');
    body.style.overflow = "hidden";
    
    // Gắn sự kiện chọn emoji - QUAN TRỌNG: phải sau khi render HTML
    setTimeout(() => {
        document.querySelectorAll('.book-rating-option').forEach(opt => {
            opt.addEventListener('click', function() {
                document.querySelectorAll('.book-rating-option').forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
    }, 100);
    
    //Cap nhat gia tien khi tang so luong san pham
    let tgbtn = document.querySelectorAll('.is-form');
    let qty = document.querySelector('.product-control .input-qty');
    let priceText = document.querySelector('.price');
    tgbtn.forEach(element => {
        element.addEventListener('click', () => {
            let price = infoProduct.price * parseInt(qty.value);
            priceText.innerHTML = vnd(price);
        });
    });
    // Chặn nhập tay vượt quá tồn kho
    qty.addEventListener('input', function() {
        let max = parseInt(qty.getAttribute('max'));
        let min = parseInt(qty.getAttribute('min'));
        let val = parseInt(qty.value);
        if (isNaN(val) || val < min) qty.value = min;
        if (val > max) {
            qty.value = max;
            toast({ title: 'Lỗi', message: 'Số lượng vượt quá số lượng còn lại!', type: 'error', duration: 2000 });
        }
        priceText.innerHTML = vnd(infoProduct.price * parseInt(qty.value));
    });
    // Them san pham vao gio hang
    let productbtn = document.querySelector('.button-dat');
    productbtn.addEventListener('click', (e) => {
        if (localStorage.getItem('currentuser')) {
            addCart(infoProduct.id);
        } else {
            toast({ title: 'Warning', message: 'Chưa đăng nhập tài khoản !', type: 'warning', duration: 3000 });
        }

    })
    // Mua ngay san pham
    dathangngay();
    renderBookReviews(index);
}

// Xóa hàm renderBookReviews trùng lặp, chỉ giữ phiên bản localStorage
function renderBookReviews(bookId) {
    window.currentBookId = bookId;
    
    // Lấy đánh giá từ localStorage trước
    let localReviews = JSON.parse(localStorage.getItem('bookReviews') || '[]');
    let bookReviews = localReviews.filter(review => review.product_id == bookId);
    
    // Hiển thị dữ liệu từ localStorage ngay lập tức
    displayBookReviewsFromLocal(bookReviews);
    
    // Đồng thời lấy dữ liệu mới từ server để cập nhật
    syncReviewsFromServer(bookId);
}

// Hàm hiển thị đánh giá từ localStorage
function displayBookReviewsFromLocal(reviews) {
    let reviewsHtml = '';
    
    if (reviews.length === 0) {
        reviewsHtml = `<div class="no-reviews">
            <i class="fa-light fa-comment-slash"></i>
            <p>Chưa có đánh giá nào cho sách này</p>
        </div>`;
    } else {
        // Tính điểm trung bình từ localStorage
        let totalRating = reviews.reduce((sum, review) => sum + review.rating, 0);
        let avgRating = (totalRating / reviews.length).toFixed(1);
        
        reviewsHtml = `
            <div class="reviews-summary">
                <div class="avg-rating">
                    <span class="rating-number">${avgRating}</span>
                    <div class="stars">${renderStars(avgRating)}</div>
                    <span class="review-count">(${reviews.length} đánh giá)</span>
                </div>
            </div>
            <div class="reviews-list">`;
        
        reviews.forEach(review => {
            const reviewDate = new Date(review.created_at).toLocaleDateString('vi-VN');
            reviewsHtml += `
                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <span class="reviewer-name">${review.user_name}</span>
                            <div class="review-rating">${renderStars(review.rating)}</div>
                        </div>
                        <span class="review-date">${reviewDate}</span>
                    </div>
                    ${review.content ? `<div class="review-comment">${review.content}</div>` : ''}
                    ${review.image ? `<div class="review-image"><img src="${review.image}" alt="Review image"></div>` : ''}
                </div>`;
        });
        reviewsHtml += `</div>`;
    }
    
    // Cập nhật DOM
    const reviewContent = document.querySelector('.tab-review-content .book-review-section');
    if (reviewContent) {
        let existingReviews = reviewContent.querySelector('.existing-reviews');
        if (existingReviews) {
            existingReviews.remove();
        }
        reviewContent.innerHTML += `<div class="existing-reviews">${reviewsHtml}</div>`;
    }
}

// Hàm đồng bộ đánh giá từ server (chạy ngầm)
function syncReviewsFromServer(bookId) {
    fetch(`get_book_reviews.php?product_id=${bookId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Cập nhật localStorage với dữ liệu mới từ server
                let allReviews = JSON.parse(localStorage.getItem('bookReviews') || '[]');
                
                // Xóa các review cũ của sản phẩm này
                allReviews = allReviews.filter(review => review.product_id != bookId);
                
                // Thêm các review mới từ server
                data.reviews.forEach(serverReview => {
                    allReviews.push({
                        id: serverReview.id,
                        user_id: serverReview.user_id,
                        user_name: serverReview.user_name,
                        product_id: serverReview.product_id,
                        rating: serverReview.rating,
                        content: serverReview.content,
                        image: serverReview.image,
                        created_at: serverReview.created_at
                    });
                });
                
                // Lưu lại vào localStorage
                localStorage.setItem('bookReviews', JSON.stringify(allReviews));
                
                // Render lại nếu có sự khác biệt
                let currentBookReviews = allReviews.filter(review => review.product_id == bookId);
                displayBookReviewsFromLocal(currentBookReviews);
            }
        })
        .catch(err => {
            console.error('Lỗi đồng bộ đánh giá:', err);
        });
}

// Hàm tạo stars rating display
function renderStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fa-solid fa-star"></i>';
        } else if (i - 0.5 <= rating) {
            stars += '<i class="fa-solid fa-star-half-stroke"></i>';
        } else {
            stars += '<i class="fa-regular fa-star"></i>';
        }
    }
    return stars;
}

// Cập nhật hàm submitBookRating để lưu cả localStorage và database
function submitBookRating() {
    const selected = document.querySelector('.book-rating-option.selected');
    const ratingMap = { excellent: 5, good: 4, ok: 3, bad: 2, terrible: 1 };
    const ratingKey = selected ? selected.dataset.rating : null;
    const rating = ratingMap[ratingKey];
    const content = document.querySelector('.book-comment-input').value.trim();
    const currentUser = JSON.parse(localStorage.getItem('currentuser'));
    
    if (!currentUser) {
        toast({ title: 'Lỗi', message: 'Vui lòng đăng nhập để đánh giá!', type: 'error', duration: 2000 });
        return;
    }
    
    if (!rating) {
        toast({ title: 'Lỗi', message: 'Vui lòng chọn mức độ đánh giá!', type: 'error', duration: 2000 });
        return;
    }

    // Kiểm tra trùng lặp trong localStorage
    let reviews = JSON.parse(localStorage.getItem('bookReviews') || '[]');
    const existingReview = reviews.find(review => 
        review.product_id == window.currentBookId && review.user_name === currentUser.fullname
    );
    
    if (existingReview) {
        toast({ title: 'Lỗi', message: 'Bạn đã đánh giá sách này rồi!', type: 'error', duration: 2000 });
        return;
    }

    // Tạo object review cho localStorage
    const review = {
        id: Date.now(),
        user_id: currentUser.id || currentUser.phone,
        user_name: currentUser.fullname,
        product_id: window.currentBookId,
        rating: rating,
        content: content,
        image: null,
        created_at: new Date().toISOString()
    };

    // Lưu vào localStorage ngay lập tức
    reviews.push(review);
    localStorage.setItem('bookReviews', JSON.stringify(reviews));
    
    // Hiển thị ngay lập tức
    toast({ title: 'Thành công', message: 'Cảm ơn bạn đã đánh giá!', type: 'success', duration: 2000 });
    
    // Reset form
    document.querySelector('.book-comment-input').value = '';
    document.querySelectorAll('.book-rating-option').forEach(opt => opt.classList.remove('selected'));
    document.querySelector('.book-rating-option[data-rating="excellent"]').classList.add('selected');
    
    // Render lại reviews từ localStorage
    let bookReviews = reviews.filter(r => r.product_id == window.currentBookId);
    displayBookReviewsFromLocal(bookReviews);

    // Gửi lên server với debug chi tiết
    getUserRealId(currentUser.phone).then(realUserId => {
        const reviewData = {
            user_id: realUserId,
            product_id: window.currentBookId,
            rating: rating,
            content: content
        };

        fetch('add_book_review.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(reviewData)
        })
        .then(res => {
            return res.text(); // Đổi thành text để debug
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                
                if (data.success) {
                    syncReviewsFromServer(window.currentBookId);
                } else {
                    let updatedReviews = JSON.parse(localStorage.getItem('bookReviews') || '[]');
                    updatedReviews = updatedReviews.filter(r => r.id !== review.id);
                    localStorage.setItem('bookReviews', JSON.stringify(updatedReviews));
                    displayBookReviewsFromLocal(updatedReviews.filter(r => r.product_id == window.currentBookId));
                    
                    toast({ title: 'Lỗi', message: 'Không thể lưu đánh giá: ' + data.message, type: 'error', duration: 3000 });
                }
            } catch (e) {
                console.error('add_book_review JSON parse error:', e);
                console.error('Response text:', text);
                toast({ title: 'Lỗi', message: 'Có lỗi khi xử lý phản hồi từ server!', type: 'error', duration: 3000 });
            }
        })
        .catch(err => {
            toast({ title: 'Lỗi', message: 'Không thể kết nối tới server!', type: 'error', duration: 3000 });
        });
    });
}

// Thêm hàm lấy user ID thực từ database với debug tốt hơn
function getUserRealId(phone) {
    return fetch('get_user_id.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ phone: phone })
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.text(); // Đổi thành text để debug
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            
            if (data.success) {
                return data.user_id;
            } else {
                return null;
            }
        } catch (e) {
            console.error('getUserRealId JSON parse error:', e);
            console.error('Response text:', text);
            return null;
        }
    })
    .catch(err => {
        console.error('Error getting user ID:', err);
        return null;
    });
}

function animationCart() {
    document.querySelector(".count-product-cart").style.animation = "slidein ease 1s"
    setTimeout(()=>{
        document.querySelector(".count-product-cart").style.animation = "none"
    },1000)
}

// Them SP vao gio hang
function addCart(index) {
    let currentuser = localStorage.getItem('currentuser') ? JSON.parse(localStorage.getItem('currentuser')) : [];
    let soluong = document.querySelector('.input-qty').value;
    let popupDetailNote = document.querySelector('#popup-detail-note').value;
    let note = popupDetailNote == "" ? "Không có ghi chú" : popupDetailNote;
    let productcart = {
        id: index,
        soluong: parseInt(soluong),
        note: note
    }
    let vitri = currentuser.cart.findIndex(item => item.id == productcart.id);
    let products = JSON.parse(localStorage.getItem('products'));
    let infoProduct = products.find(sp => sp.id == index);
    let currentQtyInCart = vitri !== -1 ? parseInt(currentuser.cart[vitri].soluong) : 0;
    if (parseInt(productcart.soluong) + currentQtyInCart > infoProduct.soluong) {
        toast({ title: 'Lỗi', message: 'Số lượng đặt vượt quá số lượng còn lại!', type: 'error', duration: 3000 });
        return;
    }
    if (vitri == -1) {
        currentuser.cart.push(productcart);
    } else {
        currentuser.cart[vitri].soluong = parseInt(currentuser.cart[vitri].soluong) + parseInt(productcart.soluong);
    }
    localStorage.setItem('currentuser', JSON.stringify(currentuser));
    updateAmount();
    closeModal();
}

//Show gio hang
function showCart() {
    if (localStorage.getItem('currentuser') != null) {
        let currentuser = JSON.parse(localStorage.getItem('currentuser'));
        if (currentuser.cart.length != 0) {
            document.querySelector('.gio-hang-trong').style.display = 'none';
            document.querySelector('button.thanh-toan').classList.remove('disabled');
            let productcarthtml = '';
            currentuser.cart.forEach(item => {
                let product = getProduct(item);
                productcarthtml += `<li class="cart-item" data-id="${product.id}">
                <div class="cart-item-info">
                    <p class="cart-item-title">
                        ${product.title}
                    </p>
                    <span class="cart-item-price price" data-price="${product.price}">
                    ${vnd(parseInt(product.price))}
                    </span>
                </div>
                <p class="product-note"><i class="fa-light fa-pencil"></i><span>${product.note}</span></p>
                <div class="cart-item-control">
                    <button class="cart-item-delete" onclick="deleteCartItem(${product.id},this)">Xóa</button>
                    <div class="buttons_added">
                        <input class="minus is-form" type="button" value="-" onclick="decreasingNumber(this)">
                        <input class="input-qty" max="${product.soluong}" min="1" name="" type="number" value="${product.soluong}">
                        <input class="plus is-form" type="button" value="+" onclick="increasingNumber(this)">
                    </div>
                </div>
            </li>`
            });
            document.querySelector('.cart-list').innerHTML = productcarthtml;
            updateCartTotal();
            saveAmountCart();
        } else {
            document.querySelector('.gio-hang-trong').style.display = 'flex'
        }
    }
    let modalCart = document.querySelector('.modal-cart');
    let containerCart = document.querySelector('.cart-container');
    let themsach = document.querySelector('.them-sach');
    modalCart.onclick = function () {
        closeCart();
    }
    themsach.onclick = function () {
        closeCart();
    }
    containerCart.addEventListener('click', (e) => {
        e.stopPropagation();
    })
}

// Delete cart item
function deleteCartItem(id, el) {
    let cartParent = el.parentNode.parentNode;
    cartParent.remove();
    let currentUser = JSON.parse(localStorage.getItem('currentuser'));
    let vitri = currentUser.cart.findIndex(item => item.id = id)
    currentUser.cart.splice(vitri, 1);

    // Nếu trống thì hiển thị giỏ hàng trống
    if (currentUser.cart.length == 0) {
        document.querySelector('.gio-hang-trong').style.display = 'flex';
        document.querySelector('button.thanh-toan').classList.add('disabled');
    }
    localStorage.setItem('currentuser', JSON.stringify(currentUser));
    updateCartTotal();
}

//Update cart total
function updateCartTotal() {
    const priceEl = document.querySelector('.text-price');
    if (priceEl) priceEl.innerText = vnd(getCartTotal());
}

// Lay tong tien don hang
function getCartTotal() {
    let currentUser = JSON.parse(localStorage.getItem('currentuser'));
    let tongtien = 0;
    if (currentUser != null) {
        currentUser.cart.forEach(item => {
            let product = getProduct(item);
            tongtien += (parseInt(product.soluong) * parseInt(product.price));
        });
    }
    return tongtien;
}

// Get Product 
function getProduct(item) {
    let products = JSON.parse(localStorage.getItem('products'));
    let infoProductCart = products.find(sp => item.id == sp.id)
    let product = {
        ...infoProductCart,
        ...item
    }
    return product;
}

window.onload = updateAmount();
window.onload = updateCartTotal();

// Lay so luong hang

function getAmountCart() {
    let currentuser = JSON.parse(localStorage.getItem('currentuser'))
    let amount = 0;
    currentuser.cart.forEach(element => {
        amount += parseInt(element.soluong);
    });
    return amount;
}

//Update Amount Cart 
function updateAmount() {
    if (localStorage.getItem('currentuser') != null) {
        let amount = getAmountCart();
        document.querySelector('.count-product-cart').innerText = amount;
    }
}

// Save Cart Info
function saveAmountCart() {
    let cartAmountbtn = document.querySelectorAll(".cart-item-control .is-form");
    let listProduct = document.querySelectorAll('.cart-item');
    let currentUser = JSON.parse(localStorage.getItem('currentuser'));
    cartAmountbtn.forEach((btn, index) => {
        btn.addEventListener('click', () => {
            let id = listProduct[parseInt(index / 2)].getAttribute("data-id");
            let productId = currentUser.cart.find(item => {
                return item.id == id;
            });
            let products = JSON.parse(localStorage.getItem('products'));
            let infoProduct = products.find(sp => sp.id == id);
            let newQty = parseInt(listProduct[parseInt(index / 2)].querySelector(".input-qty").value);
            if (newQty > infoProduct.soluong) {
                toast({ title: 'Lỗi', message: 'Số lượng vượt quá số lượng còn lại!', type: 'error', duration: 3000 });
                listProduct[parseInt(index / 2)].querySelector(".input-qty").value = infoProduct.soluong;
                return;
            }
            productId.soluong = newQty;
            localStorage.setItem('currentuser', JSON.stringify(currentUser));
            updateCartTotal();
        })
    });
}

// Open & Close Cart
function openCart() {
    showCart();
    document.querySelector('.modal-cart').classList.add('open');
    body.style.overflow = "hidden";
}

function closeCart() {
    document.querySelector('.modal-cart').classList.remove('open');
    body.style.overflow = "auto";
    updateAmount();
}

// Open Search Advanced
document.querySelector(".filter-btn").addEventListener("click",(e) => {
    e.preventDefault();
    document.querySelector(".advanced-search").classList.toggle("open");
    document.getElementById("home-service").scrollIntoView();
})

document.querySelector(".form-search-input").addEventListener("click",(e) => {
    e.preventDefault();
    document.getElementById("home-service").scrollIntoView();
})

function closeSearchAdvanced() {
    document.querySelector(".advanced-search").classList.toggle("open");
}

//Open Search Mobile 
function openSearchMb() {
    document.querySelector(".header-middle-left").style.display = "none";
    document.querySelector(".header-middle-center").style.display = "block";
    document.querySelector(".header-middle-right-item.close").style.display = "block";
    let liItem = document.querySelectorAll(".header-middle-right-item.open");
    for(let i = 0; i < liItem.length; i++) {
        liItem[i].style.setProperty("display", "none", "important")
    }
}

//Close Search Mobile 
function closeSearchMb() {
    document.querySelector(".header-middle-left").style.display = "block";
    document.querySelector(".header-middle-center").style.display = "none";
    document.querySelector(".header-middle-right-item.close").style.display = "none";
    let liItem = document.querySelectorAll(".header-middle-right-item.open");
    for(let i = 0; i < liItem.length; i++) {
        liItem[i].style.setProperty("display", "block", "important")
    }
}

//Signup && Login Form

// Chuyen doi qua lai SignUp & Login 
let signup = document.querySelector('.signup-link');
let login = document.querySelector('.login-link');
let container = document.querySelector('.signup-login .modal-container');
login.addEventListener('click', () => {
    container.classList.add('active');
})

signup.addEventListener('click', () => {
    container.classList.remove('active');
})

let signupbtn = document.getElementById('signup');
let loginbtn = document.getElementById('login');
let formsg = document.querySelector('.modal.signup-login')
signupbtn.addEventListener('click', () => {
    formsg.classList.add('open');
    container.classList.remove('active');
    body.style.overflow = "hidden";
})

loginbtn.addEventListener('click', () => {
    document.querySelector('.form-message-check-login').innerHTML = '';
    formsg.classList.add('open');
    container.classList.add('active');
    body.style.overflow = "hidden";
})

// Dang nhap & Dang ky

// Chức năng đăng ký
let signupButton = document.getElementById('signup-button');
let loginButton = document.getElementById('login-button');
signupButton.addEventListener('click', () => {
    event.preventDefault();
    let fullNameUser = document.getElementById('fullname').value;
    let emailUser = document.getElementById('email').value;
    let phoneUser = document.getElementById('phone').value;
    let passwordUser = document.getElementById('password').value;
    let passwordConfirmation = document.getElementById('password_confirmation').value;
    let checkSignup = document.getElementById('checkbox-signup').checked;
    
    // Check validate
    let isValid = true;

    if (fullNameUser.length == 0) {
        document.querySelector('.form-message-name').innerHTML = 'Vui lòng nhập họ và tên';
        document.getElementById('fullname').focus();
        isValid = false;
    } else if (fullNameUser.length < 3) {
        document.getElementById('fullname').value = '';
        document.querySelector('.form-message-name').innerHTML = 'Vui lòng nhập họ và tên lớn hơn 3 kí tự';
        isValid = false;
    } else {
        document.querySelector('.form-message-name').innerHTML = '';
    }

    if (emailUser.length == 0) {
        document.querySelector('.form-message-email').innerHTML = 'Vui lòng nhập email';
        isValid = false;
    } else if (!validateEmail(emailUser)) {
        document.querySelector('.form-message-email').innerHTML = 'Email không hợp lệ';
        document.getElementById('email').value = '';
        isValid = false;
    } else {
        document.querySelector('.form-message-email').innerHTML = '';
    }

    if (phoneUser.length == 0) {
        document.querySelector('.form-message-phone').innerHTML = 'Vui lòng nhập vào số điện thoại';
        isValid = false;
    } else if (phoneUser.length != 10) {
        document.querySelector('.form-message-phone').innerHTML = 'Vui lòng nhập vào số điện thoại 10 số';
        document.getElementById('phone').value = '';
        isValid = false;
    } else {
        document.querySelector('.form-message-phone').innerHTML = '';
    }

    if (passwordUser.length == 0) {
        document.querySelector('.form-message-password').innerHTML = 'Vui lòng nhập mật khẩu';
        isValid = false;
    } else if (passwordUser.length < 6) {
        document.querySelector('.form-message-password').innerHTML = 'Vui lòng nhập mật khẩu lớn hơn 6 kí tự';
        document.getElementById('password').value = '';
        isValid = false;
    } else {
        document.querySelector('.form-message-password').innerHTML = '';
    }

    if (passwordConfirmation.length == 0) {
        document.querySelector('.form-message-password-confi').innerHTML = 'Vui lòng nhập lại mật khẩu';
        isValid = false;
    } else if (passwordConfirmation !== passwordUser) {
        document.querySelector('.form-message-password-confi').innerHTML = 'Mật khẩu không khớp';
        document.getElementById('password_confirmation').value = '';
        isValid = false;
    } else {
        document.querySelector('.form-message-password-confi').innerHTML = '';
    }

    if (checkSignup != true) {
        document.querySelector('.form-message-checkbox').innerHTML = 'Vui lòng check đăng ký';
        isValid = false;
    } else {
        document.querySelector('.form-message-checkbox').innerHTML = '';
    }

    if (isValid) {
        if (passwordConfirmation == passwordUser) {
            let user = {
                fullname: fullNameUser,
                email: emailUser,
                phone: phoneUser,
                password: passwordUser,
                address: '',
                status: 1,
                join: new Date(),
                cart: [],
                userType: 0
            }
            let accounts = localStorage.getItem('accounts') ? JSON.parse(localStorage.getItem('accounts')) : [];
            let checkloop = accounts.some(account => {
                return account.phone == user.phone || account.email == user.email;
            })
            if (!checkloop) {
                accounts.push(user);
                localStorage.setItem('accounts', JSON.stringify(accounts));
                localStorage.setItem('currentuser', JSON.stringify(user));
                // Đồng bộ đơn guest vào tài khoản
                let orders = localStorage.getItem('order') ? JSON.parse(localStorage.getItem('order')) : [];
                let updated = false;
                for (let i = 0; i < orders.length; i++) {
                    if (orders[i].khachhang == user.phone) {
                        orders[i].khachhang = user.phone; // Nếu sau này dùng id user thì đổi thành user.id
                        updated = true;
                    }
                }
                if (updated) {
                    localStorage.setItem('order', JSON.stringify(orders));
                }
                fetch('register_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(user)
                })
                toast({ title: 'Thành công', message: 'Tạo thành công tài khoản !', type: 'success', duration: 2000 });
                closeModal();
                kiemtradangnhap();
                updateAmount();
                setTimeout((e) => {
                    window.location = "http://localhost/bookstore_datn/";
                }, 2000); 
            } else {
                toast({ title: 'Thất bại', message: 'Email hoặc số điện thoại đã tồn tại !', type: 'error', duration: 3000 });
            }
        } else {
            toast({ title: 'Thất bại', message: 'Sai mật khẩu !', type: 'error', duration: 3000 });
        }
    }
});

// Dang nhap
loginButton.addEventListener('click', () => {
    event.preventDefault();
    let phonelog = document.getElementById('phone-login').value;
    let passlog = document.getElementById('password-login').value;
    let accounts = JSON.parse(localStorage.getItem('accounts'));

    if (phonelog.length == 0) {
        document.querySelector('.form-message.phonelog').innerHTML = 'Vui lòng nhập vào số điện thoại';
    } else if (phonelog.length != 10) {
        document.querySelector('.form-message.phonelog').innerHTML = 'Vui lòng nhập vào số điện thoại 10 số';
        document.getElementById('phone-login').value = '';
    } else {
        document.querySelector('.form-message.phonelog').innerHTML = '';
    }

    if (passlog.length == 0) {
        document.querySelector('.form-message-check-login').innerHTML = 'Vui lòng nhập mật khẩu';
    } else if (passlog.length < 6) {
        document.querySelector('.form-message-check-login').innerHTML = 'Vui lòng nhập mật khẩu lớn hơn 6 kí tự';
        document.getElementById('passwordlogin').value = '';
    } else {
        document.querySelector('.form-message-check-login').innerHTML = '';
    }

    if (phonelog && passlog) {
        let user = accounts.find(item => item.phone == phonelog);
        if (!user) {
            toast({ title: 'Error', message: 'Tài khoản của bạn không tồn tại', type: 'error', duration: 3000 });
        } else if (user.password == passlog) {
            if(user.status == 0) {
                toast({ title: 'Warning', message: 'Tài khoản của bạn đã bị khóa', type: 'warning', duration: 3000 });
            } else {
                localStorage.setItem('currentuser', JSON.stringify(user));
                toast({ title: 'Success', message: 'Đăng nhập thành công', type: 'success', duration: 2000 });
                closeModal();
                kiemtradangnhap();
                checkAdmin();
                updateAmount();
                setTimeout((e) => {
                    window.location = "http://localhost/bookstore_datn/";
                }, 2000);  
            }
        } else {
            toast({ title: 'Warning', message: 'Sai mật khẩu', type: 'warning', duration: 3000 });
        }
    }
})

// Kiểm tra xem có tài khoản đăng nhập không ?
function kiemtradangnhap() {
    let currentUser = localStorage.getItem('currentuser');
    if (currentUser != null) {
        let user = JSON.parse(currentUser);
        document.querySelector('.auth-container').innerHTML = `<span class="text-dndk">Tài khoản</span>
            <span class="text-tk">${user.fullname} <i class="fa-sharp fa-solid fa-caret-down"></span>`
        document.querySelector('.header-middle-right-menu').innerHTML = `<li><a href="javascript:;" onclick="myAccount()"><i class="fa-light fa-circle-user"></i> Tài khoản của tôi</a></li>
            <li class="border"><a id="logout" href="javascript:;"><i class="fa-light fa-right-from-bracket"></i class="updateCart1"> Thoát tài khoản</a></li>`
        document.querySelector('#logout').addEventListener('click',logOut)
    }
}

function logOut() {
    let accounts = JSON.parse(localStorage.getItem('accounts'));
    let user = JSON.parse(localStorage.getItem('currentuser'));
    let vitri = accounts.findIndex(item => item.phone == user.phone);
    
    // Cập nhật giỏ hàng trong accounts localStorage
    accounts[vitri].cart.length = 0;
    for (let i = 0; i < user.cart.length; i++) {
        accounts[vitri].cart[i] = user.cart[i];
    }
    

    // Gửi giỏ hàng cập nhật lên server
    fetch('updateCart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            phone: user.phone,
            cart: user.cart,
        })
    });
    
    localStorage.setItem('accounts', JSON.stringify(accounts));
    // Xóa currentuser khỏi localStorage và chuyển hướng
    localStorage.removeItem('currentuser');
    window.location = "./index.php";
}


function checkAdmin() {
    let user = JSON.parse(localStorage.getItem('currentuser'));
    if(user && user.userType == 1) {
        let node = document.createElement(`li`);
        node.innerHTML = `<a href="./admin.php"><i class="fa-light fa-gear"></i> Quản lý cửa hàng</a>`
        document.querySelector('.header-middle-right-menu').prepend(node);
    } 
}

window.onload = kiemtradangnhap();
window.onload = checkAdmin();

// Chuyển đổi trang chủ và trang thông tin tài khoản
function myAccount() {
    document.getElementById('gioithieu').style.display = 'none';
    document.getElementById('tracuu').style.display = 'none';
    window.scrollTo({ top: 0, behavior: 'smooth' });
    document.getElementById('trangchu').classList.add('hide');
    document.getElementById('order-history').classList.remove('open');
    document.getElementById('account-user').classList.add('open');
    userInfo();
}

// Chuyển đổi trang chủ và trang xem lịch sử đặt hàng 
function orderHistory() {
    document.getElementById('gioithieu').style.display = 'none';
    document.getElementById('tracuu').style.display = 'none';
    window.scrollTo({ top: 0, behavior: 'smooth' });
    document.getElementById('account-user').classList.remove('open');
    document.getElementById('trangchu').classList.add('hide');
    document.getElementById('order-history').classList.add('open');
    renderOrderProduct();
}

function emailIsValid(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
}

function userInfo() {
    let user = JSON.parse(localStorage.getItem('currentuser'));
    document.getElementById('infoname').value = user.fullname;
    document.getElementById('infophone').value = user.phone;
    document.getElementById('infoemail').value = user.email;
    document.getElementById('infoaddress').value = user.address;
    if (user.email == undefined) {
        infoemail.value = '';
    }
    if (user.address == undefined) {
        infoaddress.value = '';
    }
}

// Thay doi thong tin
function changeInformation() {
    let accounts = JSON.parse(localStorage.getItem('accounts'));
    let user = JSON.parse(localStorage.getItem('currentuser'));
    let infoname = document.getElementById('infoname');
    let infoemail = document.getElementById('infoemail');
    let infoaddress = document.getElementById('infoaddress');

    user.fullname = infoname.value;
    if (infoemail.value.length > 0) {
        if (!emailIsValid(infoemail.value)) {
            document.querySelector('.inforemail-error').innerHTML = 'Vui lòng nhập lại email!';
            infoemail.value = '';
        } else {
            user.email = infoemail.value;
            document.querySelector('.inforemail-error').innerHTML = ''; // Xóa lỗi nếu email hợp lệ
        }
    }

    if (infoaddress.value.length > 0) {
        user.address = infoaddress.value;
    }

    let vitri = accounts.findIndex(item => item.phone == user.phone);
    accounts[vitri].fullname = user.fullname;
    accounts[vitri].email = user.email;
    accounts[vitri].address = user.address;

    // Lưu thông tin vào localStorage
    localStorage.setItem('currentuser', JSON.stringify(user));
    localStorage.setItem('accounts', JSON.stringify(accounts));
    
    
    // Gửi yêu cầu AJAX tới PHP để cập nhật thông tin trong cơ sở dữ liệu
    fetch('update_user_info.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ 
            phone: user.phone, 
            fullname: user.fullname, 
            email: user.email, 
            address: user.address 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toast({ title: 'Success', message: 'Cập nhật thông tin thành công!', type: 'success', duration: 3000 });
        } else {
            toast({ title: 'Thất bại', message: 'Đã xảy ra lỗi khi cập nhật thông tin!', type: 'error', duration: 3000 });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toast({ title: 'Thất bại', message: 'Đã xảy ra lỗi, vui lòng thử lại sau!', type: 'error', duration: 3000 });
    });

    kiemtradangnhap();
}


function changePassword() {
    let currentUser = JSON.parse(localStorage.getItem("currentuser"));
    let passwordCur = document.getElementById('password-cur-info');
    let passwordAfter = document.getElementById('password-after-info');
    let passwordConfirm = document.getElementById('password-comfirm-info');
    let check = true;

    // Kiểm tra các trường thông tin
    if (passwordCur.value.length == 0) {
        document.querySelector('.password-cur-info-error').innerHTML = 'Vui lòng nhập mật khẩu hiện tại';
        check = false;
    } else {
        document.querySelector('.password-cur-info-error').innerHTML = '';
    }

    if (passwordAfter.value.length == 0) {
        document.querySelector('.password-after-info-error').innerHTML = 'Vui lòng nhập mật khẩu mới';
        check = false;
    } else {
        document.querySelector('.password-after-info-error').innerHTML = '';
    }

    if (passwordConfirm.value.length == 0) {
        document.querySelector('.password-after-comfirm-error').innerHTML = 'Vui lòng nhập mật khẩu xác nhận';
        check = false;
    } else {
        document.querySelector('.password-after-comfirm-error').innerHTML = '';
    }

    // Thực hiện thay đổi mật khẩu nếu tất cả kiểm tra đều đúng
    if (check == true) {
        if (passwordCur.value == currentUser.password) {
            if (passwordAfter.value.length >= 6) {
                if (passwordConfirm.value == passwordAfter.value) {
                    currentUser.password = passwordAfter.value;
                    localStorage.setItem('currentuser', JSON.stringify(currentUser));

                    let accounts = JSON.parse(localStorage.getItem('accounts'));
                    let accountChange = accounts.find(acc => acc.phone === currentUser.phone);
                    accountChange.password = currentUser.password;
                    localStorage.setItem('accounts', JSON.stringify(accounts));

                    // Gửi yêu cầu AJAX tới PHP để cập nhật mật khẩu trong database
                    fetch('update_password.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ phone: currentUser.phone, password: currentUser.password })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toast({ title: 'Success', message: 'Đổi mật khẩu thành công!', type: 'success', duration: 3000 });
                        } else {
                            toast({ title: 'Thất bại', message: 'Đã xảy ra lỗi khi đổi mật khẩu!', type: 'error', duration: 3000 });
                        }
                    })
                    .catch(error => {
                        toast({ title: 'Thất bại', message: 'Đã xảy ra lỗi, vui lòng thử lại sau!', type: 'error', duration: 3000 });
                    });
                } else {
                    document.querySelector('.password-after-comfirm-error').innerHTML = 'Mật khẩu bạn nhập không trùng khớp';
                }
            } else {
                document.querySelector('.password-after-info-error').innerHTML = 'Vui lòng nhập mật khẩu mới có số kí tự lớn hơn hoặc bằng 6';
            }
        } else {
            document.querySelector('.password-cur-info-error').innerHTML = 'Bạn đã nhập sai mật khẩu hiện tại';
        }
    }
}


function getProductInfo(id) {
    let products = JSON.parse(localStorage.getItem('products'));
    return products.find(item => {
        return item.id == id;
    })
}

// Quan ly don hang
function renderOrderProduct() {
    let currentUser = JSON.parse(localStorage.getItem('currentuser'));
    let order = localStorage.getItem('order') ? JSON.parse(localStorage.getItem('order')) : [];
    let orderHtml = "";
    let arrDonHang = [];
    for (let i = 0; i < order.length; i++) {
        if (order[i].khachhang == currentUser.phone) {
            arrDonHang.push(order[i]);
        }
    }
    if (arrDonHang.length == 0) {
        orderHtml = `<div class="empty-order-section"><img src="./assets/img/empty-order.jpg" alt="" class="empty-order-img"><p>Chưa có đơn hàng nào</p></div>`;
    } else {
        arrDonHang.forEach(item => {
            let productHtml = `<div class="order-history-group">`;
            let chiTietDon = getOrderDetails(item.id);
            chiTietDon.forEach(sp => {
                let infosp = getProductInfo(sp.id);
                productHtml += `<div class="order-history">
                    <div class="order-history-left">
                        <img src="${infosp.img}" alt="">
                        <div class="order-history-info">
                            <h4>${infosp.title}!</h4>
                            <p class="order-history-note"><i class="fa-light fa-pen"></i> ${sp.note}</p>
                            <p class="order-history-quantity">x${sp.soluong}</p>
                        </div>
                    </div>
                    <div class="order-history-right">
                        <div class="order-history-price">
                            <span class="order-history-current-price">${vnd(sp.price)}</span>
                        </div>                         
                    </div>
                </div>`;
            });
            let textCompl, classCompl;
            if (item.trangthai == 1) {
                textCompl = "Đã xác nhận";
                classCompl = "confirmed";
            } else if (item.trangthai == 2) {
                textCompl = "Đang giao hàng";
                classCompl = "shipping";
            } else if (item.trangthai == 3) {
                textCompl = "Hoàn thành";
                classCompl = "completed";
            } else if (item.trangthai == 4) {
                textCompl = "Đã hủy";
                classCompl = "cancel";
            } else {
                textCompl = "Đang xử lý";
                classCompl = "no-complete";
            }
            productHtml += `<div class="order-history-control">
                <div class="order-history-status">
                    <span class="order-history-status-sp ${classCompl}">${textCompl}</span>
                    <button id="order-history-detail" onclick="detailOrder('${item.id}')"><i class="fa-regular fa-eye"></i> Xem chi tiết</button>
                    ${item.trangthai == 2 ? `<button class="btn-danhanhang" onclick="confirmReceived('${item.id}')">Đã nhận được hàng</button>` : ''}
                </div>
                <div class="order-history-total">
                    <span class="order-history-total-desc">Tổng tiền: </span>
                    <span class="order-history-toltal-price">${vnd(item.tongtien)}</span>
                </div>
            </div>`
            productHtml += `</div>`;
            orderHtml += productHtml;
        });
    }
    const orderSection = document.querySelector(".order-history-section");
    if (orderSection) orderSection.innerHTML = orderHtml;
}

// Get Order Details
function getOrderDetails(madon) {
    let orderDetails = localStorage.getItem("orderDetails") ? JSON.parse(localStorage.getItem("orderDetails")) : [];
    let ctDon = [];
    orderDetails.forEach(item => {
        if(item.madon == madon) {
            ctDon.push(item);
        }
    });
    return ctDon;
}

// Format Date
function formatDate(date) {
    let fm = new Date(date);
    
    // Chuẩn hóa múi giờ Việt Nam (+7)
    const options = { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit',
        timeZone: 'Asia/Ho_Chi_Minh' 
    };
    
    return new Intl.DateTimeFormat('vi-VN', options).format(fm);
}

// Xem chi tiet don hang
// function detailOrder(id) {
//     let order = JSON.parse(localStorage.getItem("order"));
//     let detail = order.find(item => {
//         return item.id == id;
//     })
//     document.querySelector(".modal.detail-order").classList.add("open");
//     let detailOrderHtml = `<ul class="detail-order-group">
//         <li class="detail-order-item">
//             <span class="detail-order-item-left"><i class="fa-light fa-calendar-days"></i> Ngày đặt hàng</span>
//             <span class="detail-order-item-right">${formatDate(detail.thoigiandat)}</span>
//         </li>
//         <li class="detail-order-item">
//             <span class="detail-order-item-left"><i class="fa-light fa-truck"></i> Hình thức giao</span>
//             <span class="detail-order-item-right">${detail.hinhthucgiao}</span>
//         </li>
//         <li class="detail-order-item">
//             <span class="detail-order-item-left"><i class="fa-light fa-clock"></i> Ngày nhận hàng</span>
//             <span class="detail-order-item-right">${(detail.thoigiangiao == "" ? "" : (detail.thoigiangiao + " - ")) + formatDate(detail.ngaygiaohang)}</span>
//         </li>
//         <li class="detail-order-item">
//             <span class="detail-order-item-left"><i class="fa-light fa-location-dot"></i> Địa điểm nhận</span>
//             <span class="detail-order-item-right">${detail.diachinhan}</span>
//         </li>
//         <li class="detail-order-item">
//             <span class="detail-order-item-left"><i class="fa-thin fa-person"></i> Người nhận</span>
//             <span class="detail-order-item-right">${detail.tenguoinhan}</span>
//         </li>
//         <li class="detail-order-item">
//             <span class="detail-order-item-left"><i class="fa-light fa-phone"></i> Số điện thoại nhận</span>
//             <span class="detail-order-item-right">${detail.sdtnhan}</span>
//         </li>
//     </ul>`
//     document.querySelector(".detail-order-content").innerHTML = detailOrderHtml;
// }

// Create id order 
function createId(arr) {
    let id = arr.length + 1;
    let check = arr.find(item => item.id == "DH" + id)
    while (check != null) {
        id++;
        check = arr.find(item => item.id == "DH" + id)
    }
    return "DH" + id;
}

// Back to top
window.onscroll = () => {
    let backtopTop = document.querySelector(".back-to-top")
    if (document.documentElement.scrollTop > 100) {
        backtopTop.classList.add("active");
    } else {
        backtopTop.classList.remove("active");
    }
}

// Auto hide header on scroll - Đã sửa để header luôn hiển thị khi cuộn
const headerNav = document.querySelector(".header-bottom");
let lastScrollY = window.scrollY;

window.addEventListener("scroll", () => {
    // Luôn giữ header visible (xóa phần ẩn đi khi cuộn xuống)
    headerNav.classList.remove("hide");
    lastScrollY = window.scrollY;
})

// Page
function renderProducts(showProduct) {
    let productHtml = '';
    if(showProduct.length == 0) {
        document.getElementById("home-title").style.display = "none";
        productHtml = `<div class="no-result"><div class="no-result-h">Tìm kiếm không có kết quả</div><div class="no-result-p">Xin lỗi, chúng tôi không thể tìm được kết quả hợp với tìm kiếm của bạn</div><div class="no-result-i"><i class="fa-light fa-face-sad-cry"></i></div></div>`;
    } else {
        document.getElementById("home-title").style.display = "block";
        showProduct.forEach((product) => {
            productHtml += `<div class="col-product">
            <article class="card-product" >
                <div align="center" class="card-header">
                    <a href="#" class="card-image-link" onclick="detailProduct(${product.id})">
                    <img  class="card-image" src="${product.img}" alt="${product.title}">
                    </a>
                </div>
                <div class="book-info">
                    <div class="card-content">
                        <div class="card-title">
                            <a href="#" class="card-title-link" onclick="detailProduct(${product.id})">${product.title}</a>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="product-price">
                            <span class="current-price">${vnd(product.price)}</span>
                        </div>
                    <div class="product-buy">
                        <button onclick="detailProduct(${product.id})" class="card-button order-item"><i class="fa-regular fa-cart-shopping-fast"></i> Đặt sách</button>
                    </div> 
                </div>
                </div>
            </article>
        </div>`;
        });
    }
    document.getElementById('home-products').innerHTML = productHtml;
}

// Find Product
var productAll = JSON.parse(localStorage.getItem('products')).filter(item => item.status == 1);
function searchProducts(mode) {
    let valeSearchInput = document.querySelector('.form-search-input').value;
    let valueCategory = document.getElementById("advanced-search-category-select").value;
    let minPrice = document.getElementById("min-price").value;
    let maxPrice = document.getElementById("max-price").value;
    if(parseInt(minPrice) > parseInt(maxPrice) && minPrice != "" && maxPrice != "") {
        alert("Giá đã nhập sai !");
    }

    let result = valueCategory == "Tất cả" ? productAll : productAll.filter((item) => {
        return item.category == valueCategory;
    });

    result = valeSearchInput == "" ? result : result.filter(item => {
        return item.title.toString().toUpperCase().includes(valeSearchInput.toString().toUpperCase());
    })

    if(minPrice == "" && maxPrice != "") {
        result = result.filter((item) => item.price <= maxPrice);
    } else if (minPrice != "" && maxPrice == "") {
        result = result.filter((item) => item.price >= minPrice);
    } else if(minPrice != "" && maxPrice != "") {
        result = result.filter((item) => item.price <= maxPrice && item.price >= minPrice);
    }

    document.getElementById("home-service").scrollIntoView();
    switch (mode){
        case 0:
            // Reset to original filtered productAll, not the entire products list
            result = JSON.parse(localStorage.getItem('products')).filter(item => item.status == 1);
            document.querySelector('.form-search-input').value = "";
            document.getElementById("advanced-search-category-select").value = "Tất cả";
            document.getElementById("min-price").value = "";
            document.getElementById("max-price").value = "";
            break;
        case 1:
            result.sort((a,b) => a.price - b.price)
            break;
        case 2:
            result.sort((a,b) => b.price - a.price)
            break;
    }
    showHomeProduct(result)
}

// Phân trang 
let perProducts = [];

function displayList(productAll, perPage, currentPage) {
    let start = (currentPage - 1) * perPage;
    let end = (currentPage - 1) * perPage + perPage;
    let productShow = productAll.slice(start, end);
    renderProducts(productShow);
}

function showHomeProduct(products) {
    let productAll = products.filter(item => item.status == 1)
    displayList(productAll, perPage, currentPage);
    setupPagination(productAll, perPage, currentPage);
}

document.addEventListener('DOMContentLoaded', function() {
    kiemtradangnhap();
    checkAdmin();
    updateAmount();
    updateCartTotal();
    showHomeProduct(JSON.parse(localStorage.getItem('products')));
    // Gán lại các sự kiện cho nút đăng nhập/đăng ký nếu cần
    let signup = document.querySelector('.signup-link');
    let login = document.querySelector('.login-link');
    let container = document.querySelector('.signup-login .modal-container');
    if (login) {
        login.addEventListener('click', () => {
            container.classList.add('active');
        });
    }
    if (signup) {
        signup.addEventListener('click', () => {
            container.classList.remove('active');
        });
    }
    let signupbtn = document.getElementById('signup');
    let loginbtn = document.getElementById('login');
    let formsg = document.querySelector('.modal.signup-login');
    if (signupbtn) {
        signupbtn.addEventListener('click', () => {
            formsg.classList.add('open');
            container.classList.remove('active');
            body.style.overflow = "hidden";
        });
    }
    if (loginbtn) {
        loginbtn.addEventListener('click', () => {
            document.querySelector('.form-message-check-login').innerHTML = '';
            formsg.classList.add('open');
            container.classList.add('active');
            body.style.overflow = "hidden";
        });
    }
    // Khởi tạo đánh giá
    initBookReviews();
});

// Hàm khởi tạo đánh giá khi tải trang
function initBookReviews() {
    // Lấy đánh giá từ server và lưu vào localStorage
    fetch('get_all_book_reviews.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                localStorage.setItem('bookReviews', JSON.stringify(data.reviews));
            }
        })
        .catch(err => {
            console.log('Sử dụng dữ liệu đánh giá offline');
        });
}

function setupPagination(productAll, perPage, currentPage) {
    document.querySelector('.page-nav-list').innerHTML = '';
    let page_count = Math.ceil(productAll.length / perPage);
    for (let i = 1; i <= page_count; i++) {
        let li = paginationChange(i, productAll, currentPage);
        document.querySelector('.page-nav-list').appendChild(li);
    }
}

function paginationChange(page, productAll, currentPage) {
    let node = document.createElement(`li`);
    node.classList.add('page-nav-item');
    node.innerHTML = `<a href="javascript:;">${page}</a>`;
    if (currentPage == page) node.classList.add('active');
    node.addEventListener('click', function () {
        currentPage = page;
        displayList(productAll, perPage, currentPage);
        let t = document.querySelectorAll('.page-nav-item.active');
        for (let i = 0; i < t.length; i++) {
            t[i].classList.remove('active');
        }
        node.classList.add('active');
        document.getElementById("home-service").scrollIntoView();
    })
    return node;
}

// Thêm hàm để xóa class active trên tất cả menu items
function clearActiveMenuItems() {
    document.querySelectorAll('.menu-list-item').forEach(item => {
        item.classList.remove('active');
    });
}

// Hiển thị chuyên mục
function showCategory(category) {
    document.getElementById('trangchu').classList.remove('hide');
    document.getElementById('gioithieu').style.display = 'none';
    document.getElementById('tracuu').style.display = 'none';
    document.getElementById('account-user').classList.remove('open');
    document.getElementById('order-history').classList.remove('open');
    
    // Xóa active class khỏi tất cả menu items và đặt active cho menu item hiện tại
    clearActiveMenuItems();
    document.querySelectorAll('.menu-list-item').forEach(item => {
        if (item.textContent.trim() === category) {
            item.classList.add('active');
        }
    });
    
    let searchCategory = category;
    // Lọc sản phẩm theo danh mục - Sử dụng trực tiếp từ localStorage thay vì biến productAll
    let products = JSON.parse(localStorage.getItem('products'));
    let productSearch = products.filter(value => {
        return value.category === searchCategory && value.status == 1; 
    });
    
    currentPage = 1; // Reset về trang đầu tiên khi chuyển category
    displayList(productSearch, perPage, currentPage);
    setupPagination(productSearch, perPage, currentPage);
    document.getElementById("home-title").scrollIntoView();
}

function showGioiThieu() {
    document.getElementById('trangchu').classList.add('hide');
    document.getElementById('home-products').classList.add('hide');
    document.getElementById('home-title').classList.add('hide');
    document.getElementById('gioithieu').style.display = 'block';
    document.getElementById('tracuu').style.display = 'none';
    document.getElementById('account-user').classList.remove('open');
    document.getElementById('order-history').classList.remove('open');

    // Xóa active class khỏi tất cả menu items và đặt active cho menu Giới thiệu
    clearActiveMenuItems();
    document.querySelectorAll('.menu-list-item').forEach(item => {
        if (item.textContent.trim() === 'Giới thiệu') {
            item.classList.add('active');
        }
    });

    // document.body.style.overflow = 'hidden';
}

function showTraCuu() {
    document.getElementById('trangchu').classList.add('hide');
    document.getElementById('home-products').classList.add('hide');
    document.getElementById('home-title').classList.add('hide');
    document.getElementById('gioithieu').style.display = 'none';

    document.getElementById('tracuu').style.display = 'block';
    document.getElementById('account-user').classList.remove('open');
    document.getElementById('order-history').classList.remove('open');

    // Xóa active class khỏi tất cả menu items và đặt active cho menu Tra cứu đơn hàng
    clearActiveMenuItems();
    document.querySelectorAll('.menu-list-item').forEach (item => {
        if (item.textContent.trim() === 'Tra cứu đơn hàng') {
            item.classList.add('active');
        }
    });
}

// Hàm hiển thị đơn hàng
function showOrder(arr) {
    let orderHtml = "";
    if(arr.length == 0) {
        orderHtml = `<td colspan="8">Không có dữ liệu</td>`;
    } else {
        arr.forEach((item) => {
            let trangThai = parseInt(item.trangthai);
            let status;
            if (trangThai === 0) {
                status = `<span class="status-no-complete">Chưa xử lý</span>`;
            } else if (trangThai === 1) {
                status = `<span class="status-complete">Đã xác nhận</span>`;
            } else if (trangThai === 2) {
                status = `<span class="status-shipping">Đang giao hàng</span>`;
            } else if (trangThai === 3) {
                status = `<span class="status-complete">Hoàn thành</span>`;
            } else if (trangThai === 4) {
                status = `<span class="status-cancel">Đã hủy</span>`;
            } else {
                status = `<span class="status-no-complete">Không xác định (${trangThai})</span>`;
            }
            let paymentStatus = (parseInt(item.payment_status) === 1) ? `<span class="status-complete">Đã thanh toán</span>` : `<span class="status-no-complete">Chưa thanh toán</span>`;
            let paymentMethod = item.payment_method ? (item.payment_method.toLowerCase() === 'online' ? 'Online' : 'COD') : 'COD';
            let date = formatDate(item.thoigiandat);
            orderHtml += `
            <tr>
            <td>${item.id}</td>
            <td>${item.tenguoinhan}</td>
            <td>${date}</td>
            <td>${vnd(item.tongtien)}</td>                               
            <td>${status}</td>
            <td>${paymentStatus}</td>
            <td>${paymentMethod}</td>
            <td class="control">
            <button class="btn-detail" id="" onclick="detailOrder('${item.id}')"><i class="fa-regular fa-eye"></i> Chi tiết</button>
            ${item.trangthai == 2 ? `<button class="btn-danhanhang" onclick="confirmReceived('${item.id}')">Đã nhận được hàng</button>` : ''}
            </td>
            </tr>      
            `;
        });
    }
    document.getElementById("showOrder").innerHTML = orderHtml;
}

// Hiển thị đơn hàng khi trang tải
//window.onload = () => showOrder(orders);

// Show Order Detail
function detailOrder(id) {
    document.querySelector(".modal.detail-order").classList.add("open");
    let orders = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];
    let products = localStorage.getItem("products") ? JSON.parse(localStorage.getItem("products")) : [];
    let order = orders.find((item) => item.id == id);
    let ctDon = getOrderDetails(id);
    let spHtml = `<div class="modal-detail-left"><div class="order-item-group">`;
    ctDon.forEach((item) => {
        let detaiSP = products.find(product => product.id == item.id);
        if (detaiSP) {
            spHtml += `<div class="order-product">
                <div class="order-product-left">
                    <img src="${detaiSP.img}" alt="">
                    <div class="order-product-info">
                        <h4>${detaiSP.title}</h4>
                        <p class="order-product-note"><i class="fa-light fa-pen"></i> ${item.note || ''}</p>
                        <p class="order-product-quantity">SL: ${item.soluong}<p>
                    </div>
                </div>
                <div class="order-product-right">
                    <div class="order-product-price">
                        <span class="order-product-current-price">${vnd(item.price)}</span>
                    </div>                         
                </div>
            </div>`;
        }
    });
    spHtml += `</div></div>`;
    spHtml += `<div class="modal-detail-right">
        <ul class="detail-order-group">
            <li class="detail-order-item">
                <span class="detail-order-item-left"><i class="fa-light fa-calendar-days"></i> Ngày đặt hàng</span>
                <span class="detail-order-item-right">${formatDate(order.thoigiandat)}</span>
            </li>
            <li class="detail-order-item">
                <span class="detail-order-item-left"><i class="fa-light fa-truck"></i> Hình thức giao</span>
                <span class="detail-order-item-right">${order.hinhthucgiao}</span>
            </li>
            <li class="detail-order-item">
            <span class="detail-order-item-left"><i class="fa-thin fa-person"></i> Người nhận</span>
            <span class="detail-order-item-right">${order.tenguoinhan}</span>
            </li>
            <li class="detail-order-item">
            <span class="detail-order-item-left"><i class="fa-light fa-phone"></i> Số điện thoại</span>
            <span class="detail-order-item-right">${order.sdtnhan}</span>
            </li>
            <li class="detail-order-item tb">
                <span class="detail-order-item-left"><i class="fa-light fa-clock"></i> Thời gian giao</span>
                <p class="detail-order-item-b">${(order.thoigiangiao == "" ? "" : (order.thoigiangiao + " - ")) + formatDate(order.ngaygiaohang)}</p>
            </li>
            <li class="detail-order-item tb">
                <span class="detail-order-item-t"><i class="fa-light fa-location-dot"></i> Địa chỉ nhận</span>
                <p class="detail-order-item-b">${order.diachinhan}</p>
            </li>
            <li class="detail-order-item tb">
                <span class="detail-order-item-t"><i class="fa-light fa-note-sticky"></i> Ghi chú</span>
                <p class="detail-order-item-b">${order.ghichu || ''}</p>
            </li>
        </ul>
    </div>`;
    document.querySelector(".modal-detail-order").innerHTML = spHtml;

    let trangThai = parseInt(order.trangthai);
    let classDetailBtn, textDetailBtn, actionDetailBtn;
    let extraBtns = '';
    // Hiện nút Thanh toán ngay nếu đơn online, chưa thanh toán, trạng thái là Chưa xử lý hoặc Đã xác nhận
    if (
        (trangThai === 0 || trangThai === 1) &&
        order.payment_method &&
        order.payment_method.toLowerCase() === 'online' &&
        (!order.payment_status || parseInt(order.payment_status) !== 1)
    ) {
        extraBtns = `<button class="modal-detail-btn btn-payagain" onclick="payAgain('${order.id}')">Thanh toán ngay</button>`;
    }
    if (trangThai === 0) {
        classDetailBtn = "btn-cancel-order";
        textDetailBtn = "Hủy đơn";
        actionDetailBtn = `onclick=\"cancelOrder('${order.id}', this)\"`;
    } else if (trangThai === 1) {
        classDetailBtn = "btn-daxuly";
        textDetailBtn = "Đã xác nhận";
        actionDetailBtn = '';
    } else if (trangThai === 2) {
        classDetailBtn = "btn-shipping";
        textDetailBtn = "Đang giao hàng";
        actionDetailBtn = '';
        extraBtns = `<button class=\"btn-danhanhang\" onclick=\"confirmReceived('${order.id}')\">Đã nhận được hàng</button>`;
    } else if (trangThai === 3) {
        classDetailBtn = "btn-complete";
        textDetailBtn = "Hoàn thành";
        actionDetailBtn = '';
        extraBtns = '';
    } else if (trangThai === 4) {
        classDetailBtn = "btn-dahuy";
        textDetailBtn = "Đã hủy";
        actionDetailBtn = '';
        extraBtns = '';
    }
    document.querySelector(
        ".modal-detail-bottom"
    ).innerHTML = `<div class=\"modal-detail-bottom-left\">
        <div class=\"price-total\">
            <span class=\"thanhtien\">Thành tiền</span>
            <span class=\"price\">${vnd(order.tongtien)}</span>
        </div>
    </div>
    <div class=\"modal-detail-bottom-right\">
        ${extraBtns}
        <button class=\"modal-detail-btn ${classDetailBtn}\" ${actionDetailBtn}>${textDetailBtn}</button>
    </div>`;
}

// Validation email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Toggle password visibility
document.addEventListener('DOMContentLoaded', function() {
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});

// Form validation
document.querySelector('.signup-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email');
    const emailError = document.querySelector('.form-message-email');
    
    if (!email.value) {
        emailError.textContent = 'Vui lòng nhập email';
        email.classList.add('invalid');
        return;
    }
    
    if (!validateEmail(email.value)) {
        emailError.textContent = 'Email không hợp lệ';
        email.classList.add('invalid');
        return;
    }
    
    emailError.textContent = '';
    email.classList.remove('invalid');
    
    // Continue with form submission
    this.submit();
});

// Clear error when user starts typing
document.getElementById('email').addEventListener('input', function() {
    const emailError = document.querySelector('.form-message-email');
    emailError.textContent = '';
    this.classList.remove('invalid');
});

// Thiết lập Trang chủ là active khi trang được tải
window.addEventListener('DOMContentLoaded', function() {
    // Kích hoạt menu Trang chủ mặc định
    document.querySelectorAll('.menu-list-item').forEach(item => {
        if (item.textContent.trim() === 'Trang chủ') {
            item.classList.add('active');
        }
    });
});

// Hiển thị trang chủ
function showTrangChu() {
    document.getElementById('trangchu').classList.remove('hide');
    document.getElementById('gioithieu').style.display = 'none';
    document.getElementById('tracuu').style.display = 'none';
    document.getElementById('account-user').classList.remove('open');
    document.getElementById('order-history').classList.remove('open');
    document.getElementById('home-products').classList.remove('hide');
    document.getElementById('home-title').classList.remove('hide');
    
    // Xóa active class khỏi tất cả menu items và đặt active cho menu Trang chủ
    clearActiveMenuItems();
    document.querySelectorAll('.menu-list-item').forEach(item => {
        if (item.textContent.trim() === 'Trang chủ') {
            item.classList.add('active');
        }
    });
    
    // Hiển thị tất cả sản phẩm - Lấy dữ liệu mới từ localStorage
    let allProducts = JSON.parse(localStorage.getItem('products')).filter(item => item.status == 1);
    currentPage = 1;
    displayList(allProducts, perPage, currentPage);
    setupPagination(allProducts, perPage, currentPage);
}

// Thêm hàm hủy đơn hàng chuẩn phân quyền
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

    fetch('cancel_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(bodyData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            toast({ title: 'Thành công', message: data.message, type: 'success', duration: 2000 });
            fetch('get_orders.php')
                .then(res => res.json())
                .then(orders => {
                    localStorage.setItem('order', JSON.stringify(orders));
                    renderOrderProduct && renderOrderProduct();
                });
            refreshProducts(); // Đồng bộ lại sản phẩm từ server
            document.querySelector('.modal.detail-order')?.classList.remove('open');
        } else {
            toast({ title: 'Lỗi', message: data.message, type: 'error', duration: 2000 });
        }
    })
    .catch(err => {
        toast({ title: 'Lỗi', message: 'Có lỗi khi kết nối server!', type: 'error', duration: 2000 });
    });
}

function switchBookTab(tab) {
    // Nút tab
    document.getElementById('tab-buy').classList.remove('active');
    document.getElementById('tab-review').classList.remove('active');
    // Nội dung
    const buyContent = document.querySelector('.tab-buy-content');
    const reviewContent = document.querySelector('.tab-review-content');

    if(tab === 'buy') {
        document.getElementById('tab-buy').classList.add('active');
        buyContent.classList.remove('tab-hidden');
        reviewContent.classList.add('tab-hidden');
    } else {
        document.getElementById('tab-review').classList.add('active');
        reviewContent.classList.remove('tab-hidden');
        buyContent.classList.add('tab-hidden');
    }
}

// Hàm Thanh toán lại cho đơn online chưa thanh toán
function payAgain(orderId) {
    let orders = JSON.parse(localStorage.getItem('order') || '[]');
    let order = orders.find(o => o.id === orderId);
    if (order) {
        // Tạo lại form gửi sang VNPay với thông tin đơn hàng cũ
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '/Bookstore_DATN/vnpay_php/vnpay_pay.php';

        let inputAmount = document.createElement('input');
        inputAmount.type = 'hidden';
        inputAmount.name = 'amount';
        inputAmount.value = order.tongtien;

        let inputOrderId = document.createElement('input');
        inputOrderId.type = 'hidden';
        inputOrderId.name = 'order_id';
        inputOrderId.value = order.id;

        let inputOrderInfo = document.createElement('input');
        inputOrderInfo.type = 'hidden';
        inputOrderInfo.name = 'order_desc';
        inputOrderInfo.value = 'Thanh toán đơn hàng ' + order.id;

        form.appendChild(inputAmount);
        form.appendChild(inputOrderId);
        form.appendChild(inputOrderInfo);

        document.body.appendChild(form);
        form.submit();
    }
}

// --- confirmReceived ---
function confirmReceived(orderId) {
    if (!confirm('Bạn xác nhận đã nhận được hàng?')) return;
    fetch('update_order_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ orderId: orderId, status: 3 })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            toast({ title: 'Thành công', message: 'Cảm ơn bạn đã xác nhận!', type: 'success', duration: 2000 });
            fetch('get_orders.php')
                .then(res => res.json())
                .then(orders => {
                    localStorage.setItem('order', JSON.stringify(orders));
                    renderOrderProduct && renderOrderProduct();
                });
            document.querySelector('.modal.detail-order')?.classList.remove('open');
        } else {
            toast({ title: 'Lỗi', message: data.message, type: 'error', duration: 3000 });
        }
    })
    .catch(err => {
        toast({ title: 'Lỗi', message: 'Không thể kết nối tới server!', type: 'error', duration: 3000 });
    });
}