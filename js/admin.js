function checkLogin() {
    let currentUser = JSON.parse(localStorage.getItem("currentuser"));
    if(currentUser == null || currentUser.userType == 0) {
        document.querySelector("body").innerHTML = `<div class="access-denied-section">
            <img class="access-denied-img" src="./assets/img/access-denied.webp" alt="">
        </div>`
    } else {
        document.getElementById("name-acc").innerHTML = currentUser.fullname;
    }
}
window.onload = checkLogin();

//do sidebar open and close
const menuIconButton = document.querySelector(".menu-icon-btn");
const sidebar = document.querySelector(".sidebar");
menuIconButton.addEventListener("click", () => {
    sidebar.classList.toggle("open");
});

// tab for section
const sidebars = document.querySelectorAll(".sidebar-list-item.tab-content");
const sections = document.querySelectorAll(".section");

for(let i = 0; i < sidebars.length; i++) {
    sidebars[i].onclick = function () {
        document.querySelector(".sidebar-list-item.active").classList.remove("active");
        document.querySelector(".section.active").classList.remove("active");
        sidebars[i].classList.add("active");
        sections[i].classList.add("active");
    };
}

const closeBtn = document.querySelectorAll('.section');
for(let i=0;i<closeBtn.length;i++){
    closeBtn[i].addEventListener('click',(e) => {
        sidebar.classList.add("open");
    })
}

// Get amount product
function getAmoumtProduct() {
    let products = localStorage.getItem("products") ? JSON.parse(localStorage.getItem("products")) : [];
    return products.length;
}

// Get amount user
function getAmoumtUser() {
    let accounts = localStorage.getItem("accounts") ? JSON.parse(localStorage.getItem("accounts")) : [];
    return accounts.filter(item => item.userType == 0).length;
}

// Get amount user
function getMoney() {
    // First, try to get the latest order data from the server
    return new Promise((resolve, reject) => {
        fetch('src/controllers/get_orders.php')
            .then(response => response.json())
            .then(serverOrders => {
                if (Array.isArray(serverOrders)) {
                    // Update localStorage with the latest data
                    localStorage.setItem("order", JSON.stringify(serverOrders));
                    
                    // Also fetch order details
                    return fetch('src/controllers/get_all_order_details.php');
                }
                throw new Error('Invalid server order data');
            })
            .then(response => response.json())
            .then(orderDetailsData => {
                if (Array.isArray(orderDetailsData)) {
                    // Update localStorage with the latest order details
                    localStorage.setItem("orderDetails", JSON.stringify(orderDetailsData));
                    
                    // Now calculate the revenue with the updated data
                    let tongtien = 0;
                    let orders = JSON.parse(localStorage.getItem("order")) || [];
                    let orderDetails = JSON.parse(localStorage.getItem("orderDetails")) || [];
                    
                    // Tính tổng doanh thu từ tất cả đơn hàng, loại trừ đơn hàng đã bị hủy (trangthai = 4)
                    // và chỉ tính doanh thu của đơn hàng đã thanh toán (payment_status = 1)
                    orderDetails.forEach(detail => {
                        // Tìm đơn hàng tương ứng với chi tiết này
                        const order = orders.find(o => o.id === detail.madon);
                        
                        // Chỉ tính doanh thu nếu đơn hàng tồn tại, không bị hủy (trangthai khác 4) và đã thanh toán (payment_status = 1)
                        if (order && parseInt(order.trangthai) !== 4 && parseInt(order.payment_status) === 1) {
                            tongtien += detail.price * detail.soluong;
                        }
                    });
                    
                    resolve(tongtien);
                } else {
                    // If we can't get order details, still try to calculate with whatever is in localStorage
                    let tongtien = calculateRevenueFromLocalStorage();
                    resolve(tongtien);
                }
            })
            .catch(error => {
                console.error("Error fetching order data:", error);
                // Fallback to calculate from localStorage
                let tongtien = calculateRevenueFromLocalStorage();
                resolve(tongtien);
            });
    });
}

// Helper function to calculate revenue from localStorage as fallback
function calculateRevenueFromLocalStorage() {
    let tongtien = 0;
    let orders = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];
    let orderDetails = localStorage.getItem("orderDetails") ? JSON.parse(localStorage.getItem("orderDetails")) : [];

    // Tính tổng doanh thu từ tất cả đơn hàng, loại trừ đơn hàng đã bị hủy (trangthai = 4)
    // và chỉ tính doanh thu của đơn hàng đã thanh toán (payment_status = 1)
    orderDetails.forEach(detail => {
        // Tìm đơn hàng tương ứng với chi tiết này
        const order = orders.find(o => o.id === detail.madon);
        
        // Chỉ tính doanh thu nếu đơn hàng tồn tại, không bị hủy (trangthai khác 4) và đã thanh toán (payment_status = 1)
        if (order && parseInt(order.trangthai) !== 4 && parseInt(order.payment_status) === 1) {
            tongtien += detail.price * detail.soluong;
        }
    });

    return tongtien;
}

// Update the display of user count, product count, and revenue
async function updateStatisticsDisplay() {
    document.getElementById("amount-user").innerHTML = getAmoumtUser();
    document.getElementById("amount-product").innerHTML = getAmoumtProduct();
    
    // Get revenue asynchronously
    const revenue = await getMoney();
    document.getElementById("doanh-thu").innerHTML = vnd(revenue);
}

// Call this function when the page loads
document.addEventListener('DOMContentLoaded', function() {
    updateStatisticsDisplay();
    // Load orders when page loads
    loadOrdersFromDatabase();
});

// Doi sang dinh dang tien VND
function vnd(price) {
    return price.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
}

// Phân trang 
let perPage = 12;
let currentPage = 1;
let totalPage = 0;
let perProducts = [];

function displayList(productAll, perPage, currentPage) {
    let start = (currentPage - 1) * perPage;
    let end = (currentPage - 1) * perPage + perPage;
    let productShow = productAll.slice(start, end);
    showProductArr(productShow);
}

function setupPagination(productAll, perPage) {
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
    node.innerHTML = `<a href="#">${page}</a>`;
    if (currentPage == page) node.classList.add('active');
    node.addEventListener('click', function () {
        currentPage = page;
        displayList(productAll, perPage, currentPage);
        let t = document.querySelectorAll('.page-nav-item.active');
        for (let i = 0; i < t.length; i++) {
            t[i].classList.remove('active');
        }
        node.classList.add('active');
    })
    return node;
}

// Hiển thị danh sách sản phẩm 
function showProductArr(arr) {
    let productHtml = "";
    if(arr.length == 0) {
        productHtml = `<div class="no-result"><div class="no-result-i"><i class="fa-light fa-face-sad-cry"></i></div><div class="no-result-h">Không có sản phẩm để hiển thị</div></div>`;
    } else {
        arr.forEach(product => {
            let btnCtl = product.status == 1 ? 
            `<button class="btn-delete" onclick="deleteProduct(${product.id})"><i class="fa-regular fa-trash"></i></button>` :
            `<button class="btn-delete" onclick="changeStatusProduct(${product.id})"><i class="fa-regular fa-eye"></i></button>`;
            
            // Tìm đường dẫn hình ảnh đầy đủ với thư mục con
            let imgPath = findProductImagePath(product.img);
            
            productHtml += `
            <div class="list">
                    <div class="list-left">
                    <img src="${imgPath}" alt="">
                    <div class="list-info">
                        <h4>${product.title}</h4>
                        <p class="list-note">${product.describes || ''}</p>
                        <span class="list-category">${product.category}</span>
                    </div>
                </div>
                <div class="list-right">
                    <div class="list-price">
                    <span class="list-current-price">${vnd(product.price)}</span>                   
                    </div>
                    <div class="list-control">
                    <div class="list-tool">
                        <button class="btn-edit" onclick="editProduct(${product.id})"><i class="fa-light fa-pen-to-square"></i></button>
                        ${btnCtl}
                    </div>                       
                </div>
                </div> 
            </div>`;
        });
    }
    document.getElementById("show-product").innerHTML = productHtml;
}   

// Hàm tìm đường dẫn đầy đủ của hình ảnh
function findProductImagePath(imgPath) {
    // If it's already a full URL (from upload_image.php) or an absolute path, return as is
    if (imgPath.startsWith('./assets/img/products/') || imgPath.startsWith('http')) {
        return imgPath;
    // If it's a data URL (from FileReader), return as is
    } else if (imgPath.startsWith('data:image/')) {
        return imgPath;
    // Handle relative paths
    } else {
        // Extract filename if it contains a path
        const fileName = imgPath.split('/').pop();
        return `./assets/img/products/${fileName}`;
    }
}

function showProduct() {
    let selectOp = document.getElementById('the-loai').value;
    let valeSearchInput = document.getElementById('form-search-product').value;
    let products = localStorage.getItem("products") ? JSON.parse(localStorage.getItem("products")) : [];
    if(selectOp == "Tất cả") {
        result = products.filter((item) => item.status == 1);
    } else if(selectOp == "Đã xóa") {
        result = products.filter((item) => item.status == 0);
    } else {
        result = products.filter((item) => item.category == selectOp);
    }
    result = valeSearchInput == "" ? result : result.filter(item => {
        return item.title.toString().toUpperCase().includes(valeSearchInput.toString().toUpperCase());
    })
    displayList(result, perPage, currentPage);
    setupPagination(result, perPage, currentPage);
}

function cancelSearchProduct() {
    let products = localStorage.getItem("products") ? JSON.parse(localStorage.getItem("products")).filter(item => item.status == 1) : [];
    document.getElementById('the-loai').value = "Tất cả";
    document.getElementById('form-search-product').value = "";
    displayList(products, perPage, currentPage);
    setupPagination(products, perPage, currentPage);
}

function createId(arr) {
    let id = arr.length;
    let check = arr.find((item) => item.id == id);
    while (check != null) {
        id++;
        check = arr.find((item) => item.id == id);
    }
    return id;
}

// Xóa sản phẩm 
function deleteProduct(id) {
    let products = JSON.parse(localStorage.getItem("products"));
    let index = products.findIndex(item => {
        return item.id == id;
    })
    if (confirm("Bạn có chắc muốn xóa?") == true) {
        products[index].status = 0;
        localStorage.setItem("products", JSON.stringify(products));
        // Gửi yêu cầu AJAX tới PHP để cập nhật database
        fetch('src/controllers/modify_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id, action: 'delete' })
        }); 
        showProduct();
    }
}

// Khôi phục sản phẩm
function changeStatusProduct(id) {
    let products = JSON.parse(localStorage.getItem("products"));
    let index = products.findIndex(item => {
        return item.id == id;
    })
    if (confirm("Bạn có chắc chắn muốn hủy xóa?") == true) {
        products[index].status = 1;
        localStorage.setItem("products", JSON.stringify(products));
        // Gửi yêu cầu AJAX tới PHP để cập nhật database
        fetch('src/controllers/modify_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id, action: 'restore' })
        }); 
        showProduct();
    }
}

var indexCur;
function editProduct(id) {
    let products = localStorage.getItem("products") ? JSON.parse(localStorage.getItem("products")) : [];
    let index = products.findIndex(item => {
        return item.id == id
    })
    indexCur = index;
    document.querySelectorAll(".add-product-e").forEach(item => {
        item.style.display = "none";
    })
    document.querySelectorAll(".edit-product-e").forEach(item => {
        item.style.display = "block";
    })
    document.querySelector(".add-product").classList.add("open");
    //
    document.querySelector(".upload-image-preview").src = findProductImagePath(products[index].img);
    document.getElementById("ten-sach").value = products[index].title;
    document.getElementById("gia-moi").value = products[index].price;
    document.getElementById("mo-ta").value = products[index].describes || '';
    document.getElementById("chon-sach").value = products[index].category;
    document.getElementById("so-luong").value = products[index].soluong;
}

let btnUpdateProductIn = document.getElementById("update-product-button");
btnUpdateProductIn.addEventListener("click", async (e) => {
    e.preventDefault();
    let products = JSON.parse(localStorage.getItem("products"));
    if (!products || !Array.isArray(products)) {
        toast({ title: "Lỗi", message: "Danh sách sản phẩm không tìm thấy hoặc không hợp lệ!", type: "error", duration: 3000 });
        return;
    }
    // Get the image path from the hidden field (if it exists) or fallback to preview src
    let imgProductCur;
    const hiddenImagePath = document.getElementById('hidden-image-path');
    if (hiddenImagePath && hiddenImagePath.value) {
        imgProductCur = hiddenImagePath.value;
    } else {
        imgProductCur = document.querySelector(".upload-image-preview").src;
    }
    let titleProductCur = document.getElementById("ten-sach").value;
    let curProductCur = document.getElementById("gia-moi").value;
    let soluongCur = document.getElementById("so-luong").value;
    let descProductCur = document.getElementById("mo-ta").value || '';
    let categoryText = document.getElementById("chon-sach").value;
    if (indexCur === undefined || indexCur < 0 || indexCur >= products.length) {
        toast({ title: "Lỗi", message: "Chỉ số sản phẩm không hợp lệ!", type: "error", duration: 3000 });
        return;
    }
    let idProduct = products[indexCur].id;
    let imgProduct = products[indexCur].img;
    let titleProduct = products[indexCur].title;
    let curProduct = products[indexCur].price;
    let soluongProduct = products[indexCur].soluong;
    let descProduct = products[indexCur].describes || '';
    let categoryProduct = products[indexCur].category;
    if (imgProductCur !== imgProduct || titleProductCur !== titleProduct || curProductCur !== curProduct || soluongCur !== soluongProduct || descProductCur !== descProduct || categoryText !== categoryProduct) {
        let productadd = {
            id: idProduct,
            title: titleProductCur,
            img: imgProductCur,
            category: categoryText,
            price: parseInt(curProductCur),
            soluong: parseInt(soluongCur),
            describes: descProductCur,
            status: 1,
        }; 
        products.splice(indexCur, 1, productadd); // Thay thế sản phẩm tại vị trí indexCur
        localStorage.setItem("products", JSON.stringify(products));
        try {
            let formData = new FormData();
            formData.append("id", idProduct);
            formData.append("title", titleProductCur);
            formData.append("img", imgProductCur);
            formData.append("category", categoryText);
            formData.append("price", parseInt(curProductCur));
            formData.append("soluong", parseInt(soluongCur));
            formData.append("describes", descProductCur);
            formData.append("status", 1);
            // Gửi yêu cầu cập nhật sản phẩm đến máy chủ
            const response = await fetch("src/controllers/update-product.php", {
                method: "POST",
                body: formData
            });
            toast({ title: "Thành công", message: "Sửa sản phẩm thành công!", type: "success", duration: 3000 });
            setDefaultValue();
            document.querySelector(".add-product").classList.remove("open");
        } catch (error) {
            toast({ title: "Lỗi", message: "Có lỗi xảy ra khi cập nhật sản phẩm!", type: "error", duration: 3000 });
        }
    } else {
        toast({ title: "Cảnh báo", message: "Sản phẩm của bạn không thay đổi!", type: "warning", duration: 3000 });
    }
});

let btnAddProductIn = document.getElementById("add-product-button");
btnAddProductIn.addEventListener("click", async (e) => {
    e.preventDefault();
    // Get the image path from the hidden field (if it exists) or fallback to preview src
    let imgProduct;
    const hiddenImagePath = document.getElementById('hidden-image-path');
    if (hiddenImagePath && hiddenImagePath.value) {
        imgProduct = hiddenImagePath.value;
    } else {
        // Check if image preview shows a real image or default blank image
        const previewSrc = document.querySelector(".upload-image-preview").src;
        if (previewSrc.includes('blank-image.png')) {
            toast({ title: "Chú ý", message: "Vui lòng tải lên hình ảnh sản phẩm!", type: "warning", duration: 3000 });
            return;
        } else {
            imgProduct = previewSrc;
        }
    }
    let tensach = document.getElementById("ten-sach").value;
    let giaMoi = document.getElementById("gia-moi").value;
    let soluong = document.getElementById("so-luong").value;
    let moTa = document.getElementById("mo-ta").value || '';
    let categoryText = document.getElementById("chon-sach").value;
    if (tensach == "" || giaMoi == "" || soluong == "" || moTa == "") {
        toast({ title: "Chú ý", message: "Vui lòng nhập đầy đủ thông tin sách!", type: "warning", duration: 3000 });
    } else if (isNaN(giaMoi) || isNaN(soluong)) {
        toast({ title: "Chú ý", message: "Giá và số lượng phải là số!", type: "warning", duration: 3000 });
    } else {
        let products = localStorage.getItem("products") ? JSON.parse(localStorage.getItem("products")) : [];
        let product = {
            id: createId(products),
            title: tensach,
            img: imgProduct,
            category: categoryText,
            price: parseInt(giaMoi),
            soluong: parseInt(soluong),
            describes: moTa,
            status: 1,
        }; 
        products.unshift(product);
        localStorage.setItem("products", JSON.stringify(products));
        showProduct();
        document.querySelector(".add-product").classList.remove("open");
        // Gửi dữ liệu sản phẩm mới đến server để thêm vào database
        try {
            let formData = new FormData();
            formData.append('title', tensach);
            formData.append('img', imgProduct);
            formData.append('category', categoryText);
            formData.append('price', parseInt(giaMoi));
            formData.append('soluong', parseInt(soluong));
            formData.append('describes', moTa);
            formData.append('status', 1);
            const response = await fetch('src/controllers/add_product.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.success) {
                toast({ title: "Thành công", message: "Thêm sản phẩm thành công!", type: "success", duration: 3000 });
            } else {
                toast({ title: "Lỗi", message: "Có lỗi xảy ra khi thêm sản phẩm vào cơ sở dữ liệu!", type: "error", duration: 3000 });
            }
        } catch (error) {
            console.error("Error adding product to database:", error);
            toast({ title: "Lỗi", message: "Không thể kết nối đến server!", type: "error", duration: 3000 });
        }
    }
});
document.querySelector(".modal-close.product-form").addEventListener("click",() => {
    setDefaultValue();
})
function setDefaultValue() {
    document.querySelector(".upload-image-preview").src = "./assets/img/blank-image.png";
    document.getElementById("ten-sach").value = "";
    document.getElementById("gia-moi").value = "";
    document.getElementById("mo-ta").value = "";
    document.getElementById("chon-sach").value = "Sách khác";
    document.getElementById("so-luong").value = "";
    // Reset the hidden image path field if it exists
    const hiddenImagePath = document.getElementById('hidden-image-path');
    if (hiddenImagePath) {
        hiddenImagePath.value = "";
    }
}

// Open Popup Modal
let btnAddProduct = document.getElementById("btn-add-product");
btnAddProduct.addEventListener("click", () => {
    document.querySelectorAll(".add-product-e").forEach(item => {
        item.style.display = "block";
    })
    document.querySelectorAll(".edit-product-e").forEach(item => {
        item.style.display = "none";
    })
    document.querySelector(".add-product").classList.add("open");
});

// Close Popup Modal
let closePopup = document.querySelectorAll(".modal-close");
let modalPopup = document.querySelectorAll(".modal");
for (let i = 0; i < closePopup.length; i++) {
    closePopup[i].onclick = () => {
        modalPopup[i].classList.remove("open");
    };
}

// On change Image
function uploadImage(el) {
    // Check if files were selected
    if (!el.files || !el.files[0]) {
        toast({ title: 'Lỗi', message: 'Không tìm thấy tệp hình ảnh!', type: 'error', duration: 3000 });
        return;
    }
    const file = el.files[0];
    
    // Validate file type (only images)
    if (!file.type.match('image.*')) {
        toast({ title: 'Lỗi', message: 'Vui lòng chọn một tệp hình ảnh!', type: 'error', duration: 3000 });
        return;
    }
    // Show preview immediately using FileReader
    const reader = new FileReader();
    reader.onload = function(e) {
        document.querySelector(".upload-image-preview").setAttribute("src", e.target.result);
    }
    reader.readAsDataURL(file);
    // Upload the file to server
    const formData = new FormData();
    formData.append('product_image', file);
    // LẤY CATEGORY ĐANG CHỌN
    var category = document.getElementById('chon-sach').value;
    formData.append('category', category);
    fetch('src/controllers/upload_image.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Store server path in a hidden field for later use when submitting the form
            // Create hidden input if it doesn't exist
            let hiddenImagePath = document.getElementById('hidden-image-path');
            if (!hiddenImagePath) {
                hiddenImagePath = document.createElement('input');
                hiddenImagePath.type = 'hidden';
                hiddenImagePath.id = 'hidden-image-path';
                document.querySelector('.add-product form').appendChild(hiddenImagePath);
            }
            hiddenImagePath.value = data.file_path;
            toast({ title: 'Thành công', message: 'Tải lên hình ảnh thành công!', type: 'success', duration: 3000 });
        } else {
            toast({ title: 'Lỗi', message: data.message || 'Lỗi tải lên hình ảnh!', type: 'error', duration: 3000 });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toast({ title: 'Lỗi', message: 'Lỗi kết nối máy chủ!', type: 'error', duration: 3000 });
    });
}

// Đổi trạng thái đơn hàng
async function changeStatus(id, el) {
    let status = el.classList.contains('btn-chuaxuly') ? 1 : 0;
    try {
        const response = await fetch('src/controllers/update_order_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                orderId: id,
                status: status
            })
        });
        const data = await response.json();
        
        if (data.success) {
            if (status === 1) {
                el.classList.remove('btn-chuaxuly');
                el.classList.add('btn-daxuly');
                el.innerText = 'Đã xác nhận';
            } else {
                el.classList.remove('btn-daxuly');
                el.classList.add('btn-chuaxuly');
                el.innerText = 'Chưa xử lý';
            }
            // Update in local display
            loadOrdersFromDatabase();
            toast({
                title: "Thành công",
                message: "Cập nhật trạng thái đơn hàng thành công!",
                type: "success",
                duration: 3000
            });
        } else {
            throw new Error(data.message || 'Failed to update order status');
        }
    } catch (error) {
        console.error('Error:', error);
        toast({
            title: "Thất bại",
            message: "Có lỗi xảy ra khi cập nhật trạng thái đơn hàng",
            type: "error",
            duration: 3000
        });
    }
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

// Load orders from database
async function loadOrdersFromDatabase() {
    try {
        const response = await fetch('src/controllers/get_orders.php');
        const data = await response.json();
        if (Array.isArray(data)) {
            showOrder(data);
        } else {
            throw new Error('Invalid data format');
        }
    } catch (error) {
        console.error("Error loading orders:", error);
        toast({
            title: "Lỗi",
            message: "Không thể tải danh sách đơn hàng",
            type: "error",
            duration: 3000
        });
    }
}

// Hàm lọc đơn hàng theo trạng thái, thanh toán, tìm kiếm, ngày đặt
function findOrder() {
    let orders = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];
    let status = document.getElementById('tinh-trang').value;
    let payment = document.getElementById('thanh-toan').value;
    let search = document.getElementById('form-search-order').value.trim().toLowerCase();
    let timeStart = document.getElementById('time-start').value;
    let timeEnd = document.getElementById('time-end').value;

    // Lọc trạng thái đơn hàng
    if (status !== "2") {
        orders = orders.filter(o => String(o.trangthai) === status);
    }
    // Lọc trạng thái thanh toán
    if (payment !== "2") {
        orders = orders.filter(o => String(o.payment_status) === payment);
    }
    // Lọc tìm kiếm
    if (search) {
        orders = orders.filter(o =>
            (o.id && o.id.toLowerCase().includes(search)) ||
            (o.khachhang && o.khachhang.toString().toLowerCase().includes(search))
        );
    }
    // Lọc theo ngày
    if (timeStart) {
        orders = orders.filter(o => new Date(o.thoigiandat) >= new Date(timeStart));
    }
    if (timeEnd) {
        orders = orders.filter(o => new Date(o.thoigiandat) <= new Date(timeEnd));
    }
    showOrder(orders);
}

// Sửa showOrder: Nút chi tiết chỉ icon, thêm nút xóa
function showOrder(arr) {
    let orderHtml = "";
    if(arr.length == 0) {
        orderHtml = `<td colspan="8">Không có dữ liệu</td>`;
    } else {
        arr.forEach((item) => {
            // Đảm bảo trạng thái là số nguyên
            let trangThai = parseInt(item.trangthai);
            let status = "";
            let paymentStatus = "";
            let actionButtons = "";
            // Xác định status dựa trên trạng thái
            if (trangThai === 0) {
                status = `<span class="status-no-complete">Chưa xử lý</span>`;
            } else if (trangThai === 1) {
                status = `<span class="confirmed">Đã xác nhận</span>`;
            } else if (trangThai === 2) {
                status = `<span class="status-shipping">Đang giao hàng</span>`;
            } else if (trangThai === 3) {
                status = `<span class="completed">Hoàn thành</span>`;
            } else if (trangThai === 4) {
                status = `<span class="status-cancel">Đã hủy</span>`;
            }
            // Xác định trạng thái thanh toán
            const paymentStatusValue = item.payment_status !== undefined ? parseInt(item.payment_status) : 0;
            if (paymentStatusValue === 1) {
                paymentStatus = `<span class="status-complete">Đã thanh toán</span>`;
            } else {
                paymentStatus = `<span class="status-no-complete">Chưa thanh toán</span>`;
            }
            let date = formatDate(item.thoigiandat);
            actionButtons = `
                <button class="btn-detail" title="Chi tiết" onclick="detailOrder('${item.id}')"><i class="fa-regular fa-eye"></i></button>
                <button class="btn-delete" title="Xóa đơn hàng" onclick="deleteOrderAdmin('${item.id}')"><i class="fa-regular fa-trash"></i></button>
            `;
            let paymentMethod = item.payment_method ? (item.payment_method.toLowerCase() === 'online' ? 'Online' : 'COD') : 'COD';
            orderHtml += `
            <tr>
                <td>${item.id}</td>
                <td>${item.khachhang}</td>
                <td>${date}</td>
                <td>${vnd(item.tongtien)}</td>                               
                <td>${status}</td>
                <td>${paymentStatus}</td>
                <td>${paymentMethod}</td>
                <td class="control">${actionButtons}</td>
            </tr>`;
        });
    }
    document.getElementById("showOrder").innerHTML = orderHtml;
}

// Hàm xóa đơn hàng từ admin
function deleteOrderAdmin(orderId) {
    if (!confirm("Bạn có chắc muốn xóa đơn hàng này?")) return;
    fetch('delete_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ orderId: orderId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            toast({ title: 'Thành công', message: 'Đã xóa đơn hàng', type: 'success', duration: 3000 });
            // Đồng bộ lại đơn hàng từ server
            fetch('src/controllers/get_orders.php')
                .then(res => res.json())
                .then(orders => {
                    localStorage.setItem('order', JSON.stringify(orders));
                    findOrder();
                });
        } else {
            toast({ title: 'Lỗi', message: data.message, type: 'error', duration: 3000 });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toast({ title: 'Lỗi', message: 'Có lỗi xảy ra khi xóa đơn hàng', type: 'error', duration: 3000 });
    });
}

// Get Order Details
function getOrderDetails(madon) {
    // Lấy chi tiết đơn hàng từ database thay vì localStorage
    return fetch(`get_order_details.php?order_id=${madon}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            return data.orderDetails;
        } else {
            console.error('Error fetching order details:', data.message);
            return [];
        }
    })
    .catch(error => {
        console.error('Error:', error);
        return [];
    });
}

// Show Order Detail
async function detailOrder(id) {
    document.querySelector(".modal.detail-order").classList.add("open");
    try {
        // Lấy thông tin đơn hàng
        const orderResponse = await fetch(`get_order.php?order_id=${id}`);
        const orderData = await orderResponse.json();
        if (!orderData.success) {
            throw new Error('Failed to get order');
        }
        const order = orderData.order;

        // Lấy chi tiết đơn hàng
        const orderDetails = await getOrderDetails(id);

        // Lấy thông tin sản phẩm
        const productsResponse = await fetch('src/controllers/get_products.php');
        const products = await productsResponse.json();

        let spHtml = `<div class="modal-detail-left"><div class="order-item-group">`;
        for (const item of orderDetails) {
            const product = products.find(p => p.id == (item.product_id || item.id));
            if (product) {
                let imgPath = findProductImagePath(product.img);
                spHtml += `<div class="order-product">
                    <div class="order-product-left">
                        <img src="${imgPath}" alt="">
                        <div class="order-product-info">
                            <h4>${product.title}</h4>
                            <p class="order-product-note">${item.note || ''}</p>
                            <p class="order-product-quantity">SL: ${item.quantity || item.soluong || 0}</p>
                        </div>
                    </div>
                    <div class="order-product-right">
                        <div class="order-product-price">
                            <span class="order-product-current-price">${vnd(item.price)}</span>
                        </div>                         
                    </div>
                </div>`;
            }
        }
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

        // Nút trạng thái đơn hàng
        let statusButton = '';
        if (order.trangthai == 0) {
            statusButton = `<button class="modal-detail-btn btn-chuaxuly" onclick="changeStatus('${order.id}', this)">Chưa xử lý</button>`;
        } else if (order.trangthai == 1) {
            statusButton = `<button class="modal-detail-btn btn-shipping" onclick="changeStatusShipping('${order.id}', this)">Chuyển sang đang giao hàng</button>`;
        } else if (order.trangthai == 2) {
            statusButton = `<button class="modal-detail-btn btn-complete" onclick="changeStatusComplete('${order.id}', this)">Hoàn thành đơn</button>`;
        } else if (order.trangthai == 3) {
            statusButton = `<button class="modal-detail-btn btn-success" disabled>Hoàn thành</button>`;
        } else if (order.trangthai == 4) {
            statusButton = `<button class="modal-detail-btn btn-danger" disabled>Đã hủy</button>`;
        }
        // Trạng thái thanh toán
        const paymentStatus = order.payment_status !== undefined ? parseInt(order.payment_status) : 0;
        let paymentStatusButton = '';
        if (paymentStatus === 1) {
            paymentStatusButton = `<button class="modal-detail-btn btn-dathanhtoan payment-status-btn" onclick="togglePaymentStatus('${order.id}', 1)">
                <i class="fa-regular "></i> Đã thanh toán
            </button>`;
        } else {
            paymentStatusButton = `<button class="modal-detail-btn btn-chuathanhtoan payment-status-btn" onclick="togglePaymentStatus('${order.id}', 0)">
                <i class="fa-regular"></i> Chưa thanh toán
            </button>`;
        }
        document.querySelector(".modal-detail-bottom").innerHTML = `
            <div class="modal-detail-bottom-left">
                <div class="price-total">
                    <span class="thanhtien">Thành tiền</span>
                    <span class="price">${vnd(order.tongtien)}</span>
                </div>
            </div>
            <div class="modal-detail-bottom-right">
                ${statusButton}
                ${paymentStatusButton}
            </div>`;
    } catch (error) {
        console.error('Error:', error);
        toast({
            title: "Lỗi",
            message: "Không thể tải chi tiết đơn hàng: " + error.message,
            type: "error",
            duration: 3000
        });
    }
}

// Cancel order
async function cancelOrder(orderId) {
    if (!confirm("Bạn có chắc muốn hủy đơn hàng này?")) return;

    try {
        // Lấy thông tin đơn hàng và chi tiết đơn hàng trước khi hủy
        let orders = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];
        let orderDetails = localStorage.getItem("orderDetails") ? JSON.parse(localStorage.getItem("orderDetails")) : [];
        let products = JSON.parse(localStorage.getItem('products'));
        
        // Tìm chi tiết đơn hàng cần hủy - sử dụng logic giống như ở phía người dùng
        let details = orderDetails.filter(item => item.madon == orderId);
        
        // Hoàn trả số lượng sách về kho
        details.forEach(async detail => {
            // Sử dụng detail.id thay vì detail.product_id để khớp với localStorage
            let p = products.find(sp => sp.id == detail.id);
            if (p) {
                // Sử dụng detail.soluong để khớp với cấu trúc trong localStorage
                p.soluong += parseInt(detail.soluong);
                console.log(`Hoàn trả ${detail.soluong} sách cho sản phẩm ${p.title}. Số lượng mới: ${p.soluong}`);
                
                // Gọi API cập nhật số lượng về database
                await fetch('src/controllers/update_product_quantity.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: p.id, soluong: p.soluong })
                });
            }
        });
        
        // Cập nhật lại products trong localStorage
        localStorage.setItem('products', JSON.stringify(products));

        const requestData = { 
            orderId: orderId, 
            status: 4 // Status for canceled orders
        };
        
        const response = await fetch('src/controllers/update_order_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData)
        });
        
        if (!response.ok) {
            throw new Error(`Server responded with status ${response.status}`);
        }
        
        const data = await response.json();

        if (data.success) {
            toast({
                title: "Thành công",
                message: "Đã hủy đơn hàng và hoàn trả số lượng sách thành công!",
                type: "success",
                duration: 3000
            });
            // Đóng modal chi tiết
            document.querySelector(".modal.detail-order").classList.remove("open");
            // Tải lại danh sách đơn hàng
            loadOrdersFromDatabase();
            // Refresh product list to show updated quantities
            showProduct();
        } else {
            throw new Error(data.message || "Failed to update order status");
        }
    } catch (error) {
        console.error('Error:', error);
        toast({
            title: "Thất bại",
            message: "Có lỗi xảy ra khi hủy đơn hàng: " + error.message,
            type: "error",
            duration: 3000
        });
    }
}

// Add the togglePaymentStatus function
async function togglePaymentStatus(orderId, currentStatus) {
    const newStatus = currentStatus === 1 ? 0 : 1;
    const statusText = newStatus === 1 ? "Đã thanh toán" : "Chưa thanh toán";
    
    if (confirm(`Bạn có chắc chắn muốn thay đổi trạng thái thanh toán thành "${statusText}"?`)) {
        try {
            const response = await fetch('src/controllers/update_payment_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    orderId: orderId,
                    paymentStatus: newStatus
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                toast({
                    title: "Thành công",
                    message: `Đã cập nhật trạng thái thanh toán thành ${statusText}`,
                    type: "success",
                    duration: 3000
                });
                
                // Update the button appearance immediately
                const btn = document.querySelector(".payment-status-btn");
                if (btn) {
                    if (newStatus === 1) {
                        btn.classList.remove("btn-chuathanhtoan");
                        btn.classList.add("btn-dathanhtoan");
                        btn.innerHTML = '<i class="fa-regular"></i> Đã thanh toán';
                    } else {
                        btn.classList.remove("btn-dathanhtoan");
                        btn.classList.add("btn-chuathanhtoan");
                        btn.innerHTML = '<i class="fa-regular"></i> Chưa thanh toán';
                    }
                    btn.setAttribute("onclick", `togglePaymentStatus('${orderId}', ${newStatus})`);
                }
                
                // Refresh the order list to update payment status there too
                loadOrdersFromDatabase();
            } else {
                throw new Error(result.message || "Failed to update payment status");
            }
        } catch (error) {
            console.error('Error:', error);
            toast({
                title: "Thất bại",
                message: "Có lỗi xảy ra khi cập nhật trạng thái thanh toán: " + error.message,
                type: "error",
                duration: 3000
            });
        }
    }
}

// Filter thống kê
async function thongKe(mode) {
    // Trước khi thống kê, tải dữ liệu mới nhất từ server để đảm bảo thông tin được cập nhật
    try {
        const response = await fetch('src/controllers/get_orders.php');
        const data = await response.json();
        if (Array.isArray(data)) {
            // Cập nhật dữ liệu order trong localStorage
            localStorage.setItem("order", JSON.stringify(data));
        }
        
        // Tải chi tiết đơn hàng
        const detailsResponse = await fetch('src/controllers/get_all_order_details.php');
        const detailsData = await detailsResponse.json();
        if (Array.isArray(detailsData)) {
            localStorage.setItem("orderDetails", JSON.stringify(detailsData));
        }
    } catch (error) {
        console.error("Lỗi khi tải dữ liệu đơn hàng:", error);
    }

    // Tiếp tục với thống kê
    let categoryTk = document.getElementById("the-loai-tk").value;
    let ct = document.getElementById("form-search-tk").value;
    let timeStart = document.getElementById("time-start-tk").value;
    let timeEnd = document.getElementById("time-end-tk").value;

    if (timeEnd < timeStart && timeEnd != "" && timeStart != "") {
        alert("Lựa chọn thời gian sai !");
        return;
    }

    let arrDetail = await createObj();
    let result = arrDetail;

    // Lọc theo thể loại
    if (categoryTk != "Tất cả") {
        result = result.filter((item) => {
            return item.category == categoryTk;
        });
    }

    // Lọc theo từ khóa tìm kiếm
    result = ct == "" ? result : result.filter((item) => {
        return item.title.toLowerCase().includes(ct.toLowerCase());
    });

    // Lọc theo thời gian
    if (timeStart != "" && timeEnd == "") {
        result = result.filter((item) => {
            return new Date(item.time) >= new Date(timeStart).setHours(0, 0, 0);
        });
    } else if (timeStart == "" && timeEnd != "") {
        result = result.filter((item) => {
            return new Date(item.time) <= new Date(timeEnd).setHours(23, 59, 59);
        });
    } else if (timeStart != "" && timeEnd != "") {
        result = result.filter((item) => {
            return (new Date(item.time) >= new Date(timeStart).setHours(0, 0, 0) && 
                    new Date(item.time) <= new Date(timeEnd).setHours(23, 59, 59));
        });
    }
    
    await showThongKe(result, mode);
}

// Show số lượng sp, số lượng đơn bán, doanh thu
function showOverview(arr){
    document.getElementById("quantity-product").innerText = arr.length;
    document.getElementById("quantity-order").innerText = arr.reduce((sum, cur) => (sum + parseInt(cur.quantity)),0);
    document.getElementById("quantity-sale").innerText = vnd(arr.reduce((sum, cur) => (sum + parseInt(cur.doanhthu)),0));
}

// Hàm fetch dữ liệu thống kê, chỉ gọi khi load trang hoặc cần làm mới
async function fetchStatisticsData() {
    try {
        const [ordersResponse, detailsResponse, productsResponse] = await Promise.all([
            fetch('src/controllers/get_orders.php'),
            fetch('src/controllers/get_all_order_details.php'),
            fetch('src/controllers/get_products.php')
        ]);
        const [orders, orderDetails, products] = await Promise.all([
            ordersResponse.json(),
            detailsResponse.json(),
            productsResponse.json()
        ]);
        // Ghi đè dữ liệu, không push thêm
        localStorage.setItem("order", JSON.stringify(orders));
        localStorage.setItem("orderDetails", JSON.stringify(orderDetails));
        localStorage.setItem("products", JSON.stringify(products));
    } catch (error) {
        console.error("Lỗi khi tải dữ liệu thống kê:", error);
    }
}

// createObj chỉ lấy dữ liệu từ localStorage, không fetch lại
async function createObj() {
    let orders = JSON.parse(localStorage.getItem("order") || "[]");
    let products = JSON.parse(localStorage.getItem("products") || "[]");
    let orderDetails = JSON.parse(localStorage.getItem("orderDetails") || "[]");
    let result = [];
    // Lọc ra các đơn hàng đã thanh toán và chưa bị hủy
    const validOrders = orders.filter(order => parseInt(order.trangthai) !== 4 && parseInt(order.payment_status) === 1);
    const validOrderIds = validOrders.map(order => order.id);
    orderDetails.forEach(item => {
        if (validOrderIds.includes(item.madon)) {
            let prod = products.find(product => product.id == item.id);
            if (prod) {
                let obj = {
                    id: item.id,
                    madon: item.madon,
                    price: item.price,
                    quantity: item.soluong,
                    category: prod.category,
                    title: prod.title,
                    img: prod.img,
                    time: validOrders.find(o => o.id === item.madon).thoigiandat
                };
                result.push(obj);
            }
        }
    });
    return result;
}

// Reset bảng thống kê trước khi render
async function showThongKe(arr, mode) {
    let orderHtml = "";
    let mergeObj = mergeObjThongKe(arr);
    showOverview(mergeObj);

    // Reset bảng trước khi render
    document.getElementById("showTk").innerHTML = "";

    switch (mode){
        case 0:
            // Khi reset, vẫn phải đảm bảo lọc ra các đơn hàng đã hủy
            mergeObj = mergeObjThongKe(await createObj());
            showOverview(mergeObj);
            document.getElementById("the-loai-tk").value = "Tất cả";
            document.getElementById("form-search-tk").value = "";
            document.getElementById("time-start-tk").value = "";
            document.getElementById("time-end-tk").value = "";
            break;
        case 1:
            mergeObj.sort((a,b) => parseInt(a.quantity) - parseInt(b.quantity));
            break;
        case 2:
            mergeObj.sort((a,b) => parseInt(b.quantity) - parseInt(a.quantity));
            break;
    }

    // Hiển thị thông tin tổng quan về đơn hàng và doanh thu
    const totalProductsCountElement = document.getElementById("total-products-count");
    const totalRevenueElement = document.getElementById("total-revenue");

    if (totalProductsCountElement) {
        totalProductsCountElement.textContent = mergeObj.length;
    }

    if (totalRevenueElement) {
        totalRevenueElement.textContent = vnd(mergeObj.reduce((sum, cur) => (sum + parseInt(cur.doanhthu)), 0));
    }

    for(let i = 0; i < mergeObj.length; i++) {
        // Sử dụng findProductImagePath để tìm đường dẫn đầy đủ
        let imgPath = findProductImagePath(mergeObj[i].img);
        orderHtml += `
        <tr>
        <td>${i + 1}</td>
        <td><div class="prod-img-title"><img class="prd-img-tbl" src="${imgPath}" alt=""><p>${mergeObj[i].title}</p></div></td>
        <td>${mergeObj[i].quantity}</td>
        <td>${vnd(mergeObj[i].doanhthu)}</td>
        <td><button class="btn-detail product-order-detail" data-id="${mergeObj[i].id}"><i class="fa-regular fa-eye"></i> Chi tiết</button></td>
        </tr>      
        `;
    }
    document.getElementById("showTk").innerHTML = orderHtml;
    
    document.querySelectorAll(".product-order-detail").forEach(item => {
        let idProduct = item.getAttribute("data-id");           
        item.addEventListener("click", () => {           
            detailOrderProduct(arr, idProduct);
        });
    });
}

// Khởi tạo thống kê chỉ fetch dữ liệu 1 lần khi load trang
(async function initializeStatistics() {
    await fetchStatisticsData(); // Chỉ fetch 1 lần khi load trang
    try {
        const objData = await createObj();
        await showThongKe(objData);
    } catch (error) {
        console.error("Lỗi khi hiển thị thống kê:", error);
    }
})();

// Debug function to check if order display works
function debugOrders() {
    // Xóa các lệnh console.log debug để không log ra console nữa
    /*
    console.log("Checking if showOrder element exists:", document.getElementById("showOrder"));
    fetch('src/controllers/get_orders.php')
        .then(response => response.json())
        .then(data => {
            console.log("Orders data from server:", data);
            if (Array.isArray(data) && data.length > 0) {
                showOrder(data);
                console.log("Orders should be displayed now");
            } else {
                console.log("No orders found or invalid data format");
            }
        })
        .catch(error => {
            console.error("Error fetching orders for debug:", error);
        });
    */
}

// Call debug function after a short delay to ensure DOM is ready
setTimeout(() => {
    debugOrders();
}, 1000);

// Add missing product management functions
function editAccount(phone) {
    fetch('getAccounts.php')
        .then(response => response.json())
        .then(accounts => {
            let user = accounts.find(account => account.phone == phone);
            if (user) {
                document.querySelector(".signup").classList.add("open");
                document.querySelectorAll(".add-account-e").forEach(item => {
                    item.style.display = "none"
                })
                document.querySelectorAll(".edit-account-e").forEach(item => {
                    item.style.display = "block"
                })
                document.getElementById('fullname').value = user.fullname;
                document.getElementById('phone').value = user.phone;
                document.getElementById('password').value = user.password;
                document.getElementById('btn-update-account').setAttribute('data-phone', user.phone);
                // Set trạng thái cho switch
                document.getElementById('user-status').checked = (user.status == 1);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toast({ title: 'Lỗi', message: 'Không thể tải thông tin tài khoản', type: 'error', duration: 3000 });
        });
}

function deleteAccount(phone) {
    if (confirm("Bạn có chắc muốn xóa tài khoản này?")) {
        fetch('delete_account.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ phone: phone })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Đồng bộ lại localStorage từ server
                fetch('getAccounts.php')
                    .then(res => res.json())
                    .then(accounts => {
                        localStorage.setItem('accounts', JSON.stringify(accounts));
                        showUser();
                    });
                toast({ title: 'Thành công', message: 'Đã xóa tài khoản', type: 'success', duration: 3000 });
            } else {
                toast({ title: 'Lỗi', message: data.message, type: 'error', duration: 3000 });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toast({ title: 'Lỗi', message: 'Có lỗi xảy ra', type: 'error', duration: 3000 });
        });
    }
}

// Hàm đồng bộ accounts từ server và hiển thị user
function syncAccountsAndShowUser() {
    fetch('getAccounts.php')
        .then(res => res.json())
        .then(accounts => {
            localStorage.setItem('accounts', JSON.stringify(accounts));
            showUser();
        });
}
// Gọi hàm này khi load trang
window.addEventListener('DOMContentLoaded', syncAccountsAndShowUser);

// Merge các sản phẩm thống kê theo id, cộng dồn số lượng và doanh thu
function mergeObjThongKe(arr) {
    let result = [];
    arr.forEach(item => {
        let check = result.find(i => i.id == item.id);
        if (check) {
            check.quantity = parseInt(check.quantity) + parseInt(item.quantity);
            check.doanhthu += parseInt(item.price) * parseInt(item.quantity);
        } else {
            const newItem = {...item};
            newItem.doanhthu = newItem.price * newItem.quantity;
            result.push(newItem);
        }
    });
    return result;
}

// Hàm xem chi tiết thống kê sản phẩm theo id
function detailOrderProduct(arr, id) {
    let orderHtml = "";
    let orders = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];
    // Loại bỏ duplicate theo id + madon
    let seen = new Set();
    let uniqueArr = arr.filter(item => {
        let key = item.id + '-' + item.madon;
        if (seen.has(key)) return false;
        seen.add(key);
        return true;
    });
    // Chỉ hiển thị các đơn hàng không bị hủy
    uniqueArr.forEach(item => {
        if (item.id == id) {
            // Tìm đơn hàng để kiểm tra trạng thái
            const order = orders.find(o => o.id === item.madon);
            // Chỉ hiển thị nếu đơn hàng tồn tại và không phải trạng thái hủy (4)
            if (order && parseInt(order.trangthai) !== 4) {
                orderHtml += `<tr>
                <td>${item.madon}</td>
                <td>${item.quantity || item.soluong || 0}</td>
                <td>${vnd(item.price)}</td>
                <td>${formatDate(item.time)}</td>
                </tr>`;
            }
        }
    });
    if (orderHtml === "") {
        orderHtml = `<tr><td colspan="4">Không có dữ liệu hoặc chỉ có đơn hàng đã hủy</td></tr>`;
    }
    document.getElementById("show-product-order-detail").innerHTML = orderHtml;
    document.querySelector(".modal.detail-order-product").classList.add("open");
}

// Hiển thị danh sách khách hàng
function showUser() {
    // Lấy dữ liệu
    let accounts = localStorage.getItem("accounts") ? JSON.parse(localStorage.getItem("accounts")) : [];
    // Lọc chỉ lấy userType = 0 (khách hàng)
    accounts = accounts.filter(acc => acc.userType == 0);

    // Lọc theo trạng thái
    let status = document.getElementById('tinh-trang-user').value;
    if (status !== "2") {
        accounts = accounts.filter(acc => String(acc.status) === status);
    }

    // Lọc theo tìm kiếm
    let search = document.getElementById('form-search-user').value.trim().toLowerCase();
    if (search) {
        accounts = accounts.filter(acc => acc.fullname.toLowerCase().includes(search) || acc.phone.includes(search));
    }

    // Lọc theo ngày tham gia
    let timeStart = document.getElementById('time-start-user').value;
    let timeEnd = document.getElementById('time-end-user').value;
    if (timeStart) {
        accounts = accounts.filter(acc => acc.join && new Date(acc.join) >= new Date(timeStart));
    }
    if (timeEnd) {
        accounts = accounts.filter(acc => acc.join && new Date(acc.join) <= new Date(timeEnd));
    }

    // Render ra bảng
    let html = "";
    accounts.forEach((acc, idx) => {
        html += `<tr>
            <td>${idx + 1}</td>
            <td>${acc.fullname}</td>
            <td>${acc.phone}</td>
            <td>${acc.join_date ? new Date(acc.join_date).toLocaleDateString('vi-VN') : ''}</td>
            <td>${acc.status == 1 ? 'Hoạt động' : 'Bị khóa'}</td>
            <td>
                <button class="btn-edit" onclick="editAccount('${acc.phone}')"><i class='fa-light fa-pen-to-square'></i></button>
                <button class="btn-delete" onclick="deleteAccount('${acc.phone}')"><i class='fa-regular fa-trash'></i></button>
            </td>
        </tr>`
    });
    document.getElementById("show-user").innerHTML = html;
}

// Hàm đồng bộ products từ server và hiển thị sản phẩm
function syncProductsAndShowProduct() {
    fetch('src/controllers/get_products.php')
        .then(res => res.json())
        .then(products => {
            localStorage.setItem('products', JSON.stringify(products));
            showProduct();
        });
}
// Gọi hàm này khi load trang
window.addEventListener('DOMContentLoaded', syncProductsAndShowProduct);

// Reset bộ lọc và tìm kiếm ở tab Khách hàng
function cancelSearchUser() {
    document.getElementById('tinh-trang-user').value = "2";
    document.getElementById('form-search-user').value = "";
    document.getElementById('time-start-user').value = "";
    document.getElementById('time-end-user').value = "";
    showUser();
}

// Reset bộ lọc và tìm kiếm ở tab Đơn hàng
function cancelSearchOrder() {
    document.getElementById('tinh-trang').value = "2";
    document.getElementById('thanh-toan').value = "2";
    document.getElementById('form-search-order').value = "";
    document.getElementById('time-start').value = "";
    document.getElementById('time-end').value = "";
    findOrder();
}

// Thêm hàm openCreateAccount cho admin
function openCreateAccount() {
    // Mở modal tạo tài khoản
    document.querySelector('.signup').classList.add('open');
    // Hiện nút thêm, ẩn nút sửa
    document.querySelectorAll('.add-account-e').forEach(item => item.style.display = 'block');
    document.querySelectorAll('.edit-account-e').forEach(item => item.style.display = 'none');
    // Reset các trường nhập
    document.getElementById('fullname').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('password').value = '';
    document.getElementById('user-status').checked = true;
}

// SỰ KIỆN CHO NÚT ĐĂNG KÝ (THÊM KHÁCH HÀNG MỚI)
document.querySelectorAll('.add-account-e').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const fullname = document.getElementById('fullname').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const password = document.getElementById('password').value.trim();
        const status = document.getElementById('user-status').checked ? 1 : 0;
        const address = '';
        const email = '';
        const join = new Date().toISOString().slice(0, 19).replace('T', ' ');
        const userType = 0;
        if (!fullname || !phone || !password) {
            toast({ title: 'Lỗi', message: 'Vui lòng nhập đầy đủ thông tin!', type: 'error', duration: 3000 });
            return;
        }
        fetch('src/controllers/add_account.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ fullname, phone, password, address, email, status, join, userType })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                toast({ title: 'Thành công', message: data.message, type: 'success', duration: 2000 });
                document.querySelector('.signup').classList.remove('open');
                syncAccountsAndShowUser();
            } else {
                toast({ title: 'Lỗi', message: data.message, type: 'error', duration: 3000 });
            }
        })
        .catch(err => {
            toast({ title: 'Lỗi', message: 'Không thể kết nối server!', type: 'error', duration: 3000 });
        });
    });
});

// SỰ KIỆN CHO NÚT LƯU THÔNG TIN (CHỈNH SỬA KHÁCH HÀNG)
document.querySelectorAll('.edit-account-e').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const fullname = document.getElementById('fullname').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const password = document.getElementById('password').value.trim();
        const status = document.getElementById('user-status').checked ? 1 : 0;
        if (!fullname || !phone || !password) {
            toast({ title: 'Lỗi', message: 'Vui lòng nhập đầy đủ thông tin!', type: 'error', duration: 3000 });
            return;
        }
        fetch('src/controllers/update_account.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ fullname, phone, password, status })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                toast({ title: 'Thành công', message: data.message, type: 'success', duration: 2000 });
                document.querySelector('.signup').classList.remove('open');
                syncAccountsAndShowUser();
            } else {
                toast({ title: 'Lỗi', message: data.message, type: 'error', duration: 3000 });
            }
        })
        .catch(err => {
            toast({ title: 'Lỗi', message: 'Không thể kết nối server!', type: 'error', duration: 3000 });
        });
    });
});

// Thêm các hàm chuyển trạng thái mới
async function changeStatusShipping(orderId, el) {
    // Chuyển sang trạng thái 2: Đang giao hàng
    await changeOrderStatus(orderId, 2, el, 'Đang giao hàng');
}
async function changeStatusComplete(orderId, el) {
    // Chuyển sang trạng thái 3: Hoàn thành
    await changeOrderStatus(orderId, 3, el, 'Hoàn thành');
}
async function changeOrderStatus(orderId, status, el, text) {
    try {
        // Lấy thông tin đơn hàng trước khi chuyển trạng thái
        const orderRes = await fetch(`get_order.php?order_id=${orderId}`);
        const orderData = await orderRes.json();
        if (!orderData.success) throw new Error('Không lấy được thông tin đơn hàng');
        const order = orderData.order;
        // Nếu chuyển sang Đang giao hàng (2) hoặc Hoàn thành (3) mà đơn online chưa thanh toán thì chặn
        if ((status === 2 || status === 3) && (order.payment_method == 'online' || order.payment_method == 1) && order.payment_status != 1) {
            // Gửi mail nhắc nhở thanh toán
            await fetch('send_payment_reminder.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ orderId: orderId })
            });
            toast({ title: 'Cảnh báo', message: 'Khách hàng chưa thanh toán online. Đã gửi mail nhắc nhở!', type: 'warning', duration: 4000 });
            return;
        }
        const response = await fetch('src/controllers/update_order_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ orderId: orderId, status: status })
        });
        const data = await response.json();
        if (data.success) {
            toast({ title: 'Thành công', message: `Đã chuyển trạng thái đơn hàng sang ${text}`, type: 'success', duration: 2000 });
            loadOrdersFromDatabase();
            document.querySelector('.modal.detail-order')?.classList.remove('open');
        } else {
            toast({ title: 'Lỗi', message: data.message, type: 'error', duration: 3000 });
        }
    } catch (error) {
        toast({ title: 'Lỗi', message: 'Có lỗi khi cập nhật trạng thái!', type: 'error', duration: 3000 });
    }
}