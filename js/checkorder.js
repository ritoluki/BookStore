// Sự kiện tra cứu đơn hàng
document.querySelector(".form-tracuu").addEventListener("submit", (e) => {
    e.preventDefault(); // Ngăn chặn hành vi mặc định của form

    // Lấy số điện thoại từ input
    let sdt = document.querySelector(".tracuudon").value;

    // Kiểm tra nếu số điện thoại không trống
    if (sdt === "") {
        toast({ title: "Chú ý", message: "Vui lòng nhập số điện thoại!", type: "warning", duration: 3000 });
        return;
    }

    // Lấy danh sách đơn hàng từ localStorage
    let orders = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];

    // Lọc danh sách đơn hàng theo số điện thoại
    let filteredOrders = orders.filter(order => order.sdtnhan === sdt);

    // Hiển thị kết quả tra cứu
    showOrdersdt(filteredOrders);
});

// Hàm hiển thị đơn hàng
function showOrdersdt(arr) {
    let orderHtml = ``;
    if (arr.length === 0) {
        orderHtml = `<br><h2>Giỏ hàng của bạn vẫn đang chờ bạn!<br>Đừng bỏ lỡ những sản phẩm tuyệt vời từ Shop mình nhé (^.^)</h2>`;
    } else {
        orderHtml = '<br><div class="main-account"><div class="main-account-header"><h3>Thông tin đơn hàng từ SĐT của bạn</h3><p>Xem chi tiết, trạng thái của những đơn hàng.</p></div><div class="section"><div class="table"><table width="100%"><thead><tr><td>Mã đơn</td><td>Tên người nhận</td><td>Ngày đặt</td><td>Tổng tiền</td><td>Trạng thái</td><td>Thao tác</td></tr></thead><tbody>';
        arr.forEach((item) => {
            // Chuyển đổi trạng thái thành số nguyên để đảm bảo so sánh chính xác
            let trangThai = parseInt(item.trangthai);
            let status;
            
            if (trangThai === 0) {
                status = `<span class="status-no-complete">Chưa xử lý</span>`;
            } else if (trangThai === 1) {
                status = `<span class="status-complete">Đã xử lý</span>`;
            } else if (trangThai === 4) {
                status = `<span class="status-cancel">Đã hủy</span>`;
            } else {
                status = `<span class="status-no-complete">Không xác định (${trangThai})</span>`;
            }
            
            console.log(`Đơn hàng ${item.id}: trangthai = ${item.trangthai}, trangThai = ${trangThai}`);
            
            let date = formatDate(item.thoigiandat);
            
            // Check if the current logged-in user's phone matches the order's phone
            let currentUser = JSON.parse(localStorage.getItem('currentuser')) || null;
            let isPhoneMatch = currentUser && currentUser.phone === item.sdtnhan;
            
            // Only show cancel button if status is 0 (not processed) AND phone matches
            let cancelButton = '';
            if (trangThai === 0) {
                if (isPhoneMatch) {
                    cancelButton = `<button class="btn-cancel" onclick="cancelOrder('${item.id}')"><i class="fa-regular fa-times"></i> Hủy đơn</button>`;
                } else {
                    // Add a disabled button with tooltip explaining why it's disabled
                    cancelButton = `<button class="btn-cancel" disabled title="Chỉ chủ sở hữu đơn hàng mới có thể hủy đơn"><i class="fa-regular fa-times"></i> Hủy đơn</button>`;
                }
            }
            
            orderHtml += `
            <tr>
                <td>${item.id}</td>
                <td>${item.tenguoinhan}</td>
                <td>${date}</td>
                <td>${vnd(item.tongtien)}</td>
                <td>${status}</td>
                <td class="control">
                    <button class="btn-detail" onclick="detailOrder('${item.id}')"><i class="fa-regular fa-eye"></i> Chi tiết</button>
                    ${cancelButton}
                </td>
            </tr>`;
        });
        orderHtml += '</tbody></table></div></div></div>';
    }
    document.getElementById("showOrdersdt").innerHTML = orderHtml;
}

// Hàm định dạng tiền tệ (vnd)
function vnd(price) {
    return price.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
}

// Hàm định dạng ngày tháng
function formatDate(dateString) {
    let date = new Date(dateString);
    // Chuẩn hóa múi giờ Việt Nam (+7)
    const options = { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit',
        timeZone: 'Asia/Ho_Chi_Minh' 
    };
    
    return new Intl.DateTimeFormat('vi-VN', options).format(date);
}

// Hàm hiển thị chi tiết đơn hàng
// function detailOrder(id) {
//     let orders = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];
//     let products = localStorage.getItem("products") ? JSON.parse(localStorage.getItem("products")) : [];
//     let order = orders.find((item) => item.id == id);
//     let ctDon = getOrderDetails(id);

//     let spHtml = `<div class="modal-detail-left"><div class="order-item-group">`;

//     ctDon.forEach((item) => {
//         let detaiSP = products.find(product => product.id == item.id);
//         spHtml += `<div class="order-product">
//             <div class="order-product-left">
//                 <img src="${detaiSP.img}" alt="">
//                 <div class="order-product-info">
//                     <h4>${detaiSP.title}</h4>
//                     <p class="order-product-note"><i class="fa-light fa-pen"></i> ${item.note}</p>
//                     <p class="order-product-quantity">SL: ${item.soluong}<p>
//                 </div>
//             </div>
//             <div class="order-product-right">
//                 <div class="order-product-price">
//                     <span class="order-product-current-price">${vnd(item.price)}</span>
//                 </div>                         
//             </div>
//         </div>`;
//     });
//     spHtml += `</div></div>`;
//     spHtml += `<div class="modal-detail-right">
//         <ul class="detail-order-group">
//             <li class="detail-order-item">
//                 <span class="detail-order-item-left"><i class="fa-light fa-calendar-days"></i> Ngày đặt hàng</span>
//                 <span class="detail-order-item-right">${formatDate(order.thoigiandat)}</span>
//             </li>
//             <li class="detail-order-item">
//                 <span class="detail-order-item-left"><i class="fa-light fa-truck"></i> Hình thức giao</span>
//                 <span class="detail-order-item-right">${order.hinhthucgiao}</span>
//             </li>
//             <li class="detail-order-item">
//             <span class="detail-order-item-left"><i class="fa-thin fa-person"></i> Người nhận</span>
//             <span class="detail-order-item-right">${order.tenguoinhan}</span>
//             </li>
//             <li class="detail-order-item">
//             <span class="detail-order-item-left"><i class="fa-light fa-phone"></i> Số điện thoại</span>
//             <span class="detail-order-item-right">${order.sdtnhan}</span>
//             </li>
//             <li class="detail-order-item tb">
//                 <span class="detail-order-item-left"><i class="fa-light fa-clock"></i> Thời gian giao</span>
//                 <p class="detail-order-item-b">${(order.thoigiangiao == "" ? "" : (order.thoigiangiao + " - ")) + formatDate(order.ngaygiaohang)}</p>
//             </li>
//             <li class="detail-order-item tb">
//                 <span class="detail-order-item-t"><i class="fa-light fa-location-dot"></i> Địa chỉ nhận</span>
//                 <p class="detail-order-item-b">${order.diachinhan}</p>
//             </li>
//             <li class="detail-order-item tb">
//                 <span class="detail-order-item-t"><i class="fa-light fa-note-sticky"></i> Ghi chú</span>
//                 <p class="detail-order-item-b">${order.ghichu}</p>
//             </li>
//         </ul>
//     </div>`;
//     document.querySelector(".modal-detail-order").innerHTML = spHtml;

//     let classDetailBtn = order.trangthai == 0 ? "btn-chuaxuly" : "btn-daxuly";
//     let textDetailBtn = order.trangthai == 0 ? "Chưa xử lý" : "Đã xử lý";
//     document.querySelector(
//         ".modal-detail-bottom"
//     ).innerHTML = `<div class="modal-detail-bottom-left">
//         <div class="price-total">
//             <span class="thanhtien">Thành tiền</span>
//             <span class="price">${vnd(order.tongtien)}</span>
//         </div>
//     </div>
//     <div class="modal-detail-bottom-right">
//         <button class="modal-detail-btn ${classDetailBtn}" onclick="changeStatus('${order.id}',this)">${textDetailBtn}</button>
//     </div>`;
// }

// Hàm lấy chi tiết đơn hàng từ localStorage
function getOrderDetails(madon) {
    let orderDetails = localStorage.getItem("orderDetails") ?
        JSON.parse(localStorage.getItem("orderDetails")) : [];
    let ctDon = orderDetails.filter(item => item.madon == madon);
    return ctDon;
}

// Hàm hủy đơn hàng
function cancelOrder(orderId) {
    if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
        // Lấy thông tin người dùng hiện tại
        let currentUser = JSON.parse(localStorage.getItem('currentuser')) || null;
        let userPhone = currentUser ? currentUser.phone : null;
        
        // Chuẩn bị dữ liệu để gửi đến server
        const requestData = {
            orderId: orderId,
            userPhone: userPhone
        };
        
        // Gọi API hủy đơn hàng
        fetch('cancel_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('API response:', data);
            
            if (data.success) {
                // Hiển thị thông báo thành công
                toast({ 
                    title: "Thành công", 
                    message: "Đã hủy đơn hàng thành công!", 
                    type: "success", 
                    duration: 3000 
                });
                
                // Cập nhật lại giao diện
                let sdt = document.querySelector(".tracuudon").value;
                
                // Lấy danh sách đơn hàng từ localStorage
                let orders = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];
                
                // Tìm và cập nhật đơn hàng đã hủy trong localStorage
                let orderIndex = orders.findIndex(order => order.id === orderId);
                if (orderIndex !== -1) {
                    // Sử dụng trạng thái từ phản hồi API thay vì giá trị cứng
                    if (data.order && typeof data.order.trangthai !== 'undefined') {
                        orders[orderIndex].trangthai = data.order.trangthai;
                        console.log(`Đơn hàng ${orderId} đã được cập nhật thành trạng thái ${data.order.trangthai} từ API`);
                    } else {
                        // Sử dụng giá trị mặc định nếu không nhận được từ API
                        orders[orderIndex].trangthai = 4;
                        console.log(`Đơn hàng ${orderId} đã được cập nhật thành trạng thái 4 (mặc định)`);
                    }
                    
                    // Lưu vào localStorage
                    localStorage.setItem("order", JSON.stringify(orders));
                    
                    // Kiểm tra xem lưu có thành công không
                    let updatedOrders = JSON.parse(localStorage.getItem("order"));
                    let updatedOrder = updatedOrders.find(o => o.id === orderId);
                    console.log(`Sau khi lưu, trạng thái đơn hàng ${orderId}: ${updatedOrder.trangthai} (type: ${typeof updatedOrder.trangthai})`);
                }
                
                // Lọc và hiển thị lại danh sách đơn hàng
                let filteredOrders = orders.filter(order => order.sdtnhan === sdt);
                showOrdersdt(filteredOrders);
            } else {
                // Hiển thị thông báo lỗi
                toast({ 
                    title: "Thất bại", 
                    message: data.message || "Không thể hủy đơn hàng!", 
                    type: "error", 
                    duration: 3000 
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toast({ 
                title: "Lỗi", 
                message: "Đã xảy ra lỗi khi hủy đơn hàng: " + error.message, 
                type: "error", 
                duration: 3000 
            });
        });
    }
}

// Hàm đồng bộ trạng thái đơn hàng với server
function syncOrderStatusWithServer() {
    // Lấy danh sách đơn hàng từ localStorage
    let orders = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];
    
    if (orders.length === 0) {
        console.log("Không có đơn hàng nào trong localStorage để đồng bộ");
        return;
    }
    
    console.log("Đang đồng bộ trạng thái đơn hàng với server...");
    
    // Gọi API để lấy trạng thái đơn hàng từ server
    fetch('get_orders.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(serverOrders => {
            if (!Array.isArray(serverOrders)) {
                throw new Error('Invalid server response format');
            }
            
            let hasUpdates = false;
            let fixes = [];
            
            // Duyệt qua các đơn hàng trong localStorage và so sánh với server
            orders.forEach((localOrder, index) => {
                // Đảm bảo trạng thái là số nguyên trong localStorage
                if (typeof localOrder.trangthai === 'string') {
                    orders[index].trangthai = parseInt(localOrder.trangthai) || 0;
                    fixes.push({
                        id: localOrder.id,
                        old: localOrder.trangthai,
                        new: orders[index].trangthai,
                        reason: 'string_to_int'
                    });
                    hasUpdates = true;
                }
                
                // Tìm đơn hàng tương ứng từ server
                const serverOrder = serverOrders.find(o => o.id === localOrder.id);
                
                if (serverOrder) {
                    // Chuyển đổi trạng thái thành số nguyên để so sánh
                    const localStatus = parseInt(orders[index].trangthai);
                    const serverStatus = parseInt(serverOrder.trangthai);
                    
                    // Nếu trạng thái khác nhau, cập nhật localStorage
                    if (localStatus !== serverStatus) {
                        console.log(`Đồng bộ đơn hàng ${localOrder.id}: local=${localStatus}, server=${serverStatus}`);
                        orders[index].trangthai = serverStatus;
                        fixes.push({
                            id: localOrder.id,
                            old: localStatus,
                            new: serverStatus,
                            reason: 'sync_with_server'
                        });
                        hasUpdates = true;
                    }
                }
            });
            
            // Nếu có cập nhật, lưu lại vào localStorage
            if (hasUpdates) {
                localStorage.setItem("order", JSON.stringify(orders));
                console.log("Đã đồng bộ trạng thái đơn hàng với server!", fixes);
                
                // Hiển thị thông báo khi có sự thay đổi
                if (fixes.length > 0) {
                    toast({ 
                        title: "Đồng bộ dữ liệu", 
                        message: `Đã cập nhật trạng thái cho ${fixes.length} đơn hàng`, 
                        type: "info", 
                        duration: 3000 
                    });
                }
                
                // Nếu đang ở trang tra cứu đơn hàng, hiển thị lại danh sách
                let sdt = document.querySelector(".tracuudon");
                if (sdt && sdt.value) {
                    let filteredOrders = orders.filter(order => order.sdtnhan === sdt.value);
                    showOrdersdt(filteredOrders);
                }
            } else {
                console.log("Không có sự khác biệt trạng thái nào được tìm thấy.");
            }
        })
        .catch(error => {
            console.error('Lỗi khi đồng bộ trạng thái đơn hàng:', error);
        });
}

// Thêm sự kiện khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    // Gọi hàm đồng bộ khi trang load
    syncOrderStatusWithServer();
    
    // Thiết lập đồng bộ định kỳ (mỗi 5 phút)
    setInterval(syncOrderStatusWithServer, 5 * 60 * 1000);
});

// Thêm sự kiện đồng bộ khi nút tra cứu được nhấn
document.querySelector(".form-tracuu").addEventListener("submit", function(e) {
    // Đồng bộ trước khi hiển thị kết quả
    syncOrderStatusWithServer();
});

// Script để tìm kiếm và hiển thị đơn hàng theo số điện thoại

document.addEventListener('DOMContentLoaded', function() {
    // Lấy các phần tử DOM
    const searchForm = document.querySelector('.form-tracuu');
    const searchInput = document.querySelector('.tracuudon');
    const searchButton = document.querySelector('.filter-don');
    
    // Xử lý sự kiện submit form tìm kiếm
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        searchOrders();
    });
    
    // Xử lý sự kiện click nút tìm kiếm
    searchButton.addEventListener('click', function(e) {
        e.preventDefault();
        searchOrders();
    });
    
    // Hàm tìm kiếm đơn hàng
    function searchOrders() {
        const phoneNumber = searchInput.value.trim();
        
        if (!phoneNumber) {
            toast({ 
                title: 'Cảnh báo', 
                message: 'Vui lòng nhập số điện thoại để tra cứu đơn hàng', 
                type: 'warning', 
                duration: 3000 
            });
            return;
        }
        
        // Lấy danh sách đơn hàng từ localStorage
        const orders = localStorage.getItem('order') ? JSON.parse(localStorage.getItem('order')) : [];
        
        // Lọc đơn hàng theo số điện thoại
        const filteredOrders = orders.filter(order => order.khachhang === phoneNumber || order.sdtnhan === phoneNumber);
        
        // Hiển thị kết quả
        displayOrders(filteredOrders);
    }
    
    // Hàm hiển thị đơn hàng
    function displayOrders(orders) {
        const ordersContainer = document.getElementById('showOrdersdt');
        
        // Nếu không có đơn hàng nào
        if (orders.length === 0) {
            ordersContainer.innerHTML = `
                <div style="text-align: center; padding: 30px;">
                    <img src="./assets/img/empty-order.jpg" alt="Không có đơn hàng" style="max-width: 200px; margin-bottom: 20px;">
                    <p style="color: #666; font-size: 16px;">Không tìm thấy đơn hàng nào với số điện thoại này</p>
                </div>
            `;
            return;
        }
        
        // Sắp xếp đơn hàng theo thời gian (mới nhất lên đầu)
        orders.sort((a, b) => new Date(b.thoigiandat) - new Date(a.thoigiandat));
        
        // Tạo HTML cho bảng đơn hàng
        let html = `
            <table>
                <thead>
                    <tr>
                        <td>MÃ ĐƠN</td>
                        <td>TÊN NGƯỜI NHẬN</td>
                        <td>NGÀY ĐẶT</td>
                        <td>TỔNG TIỀN</td>
                        <td>TRẠNG THÁI</td>
                        <td>THAO TÁC</td>
                    </tr>
                </thead>
                <tbody>
        `;
        
        // Thêm từng đơn hàng vào bảng
        orders.forEach(order => {
            // Xử lý trạng thái đơn hàng
            let statusHtml = '';
            let statusClass = '';
            
            switch (parseInt(order.trangthai)) {
                case 0:
                    statusHtml = 'Đang xử lý';
                    statusClass = 'status-no-complete';
                    break;
                case 1:
                    statusHtml = 'Đã xử lý';
                    statusClass = 'status-complete';
                    break;
                case 4:
                    statusHtml = 'Đã hủy';
                    statusClass = 'status-cancel';
                    break;
                default:
                    statusHtml = 'Không xác định';
                    statusClass = 'status-no-complete';
            }
            
            // Format ngày đặt hàng
            const orderDate = formatDate(order.thoigiandat);
            
            // Thêm dòng đơn hàng
            html += `
                <tr>
                    <td>${order.id}</td>
                    <td>${order.tenguoinhan}</td>
                    <td>${orderDate}</td>
                    <td>${vnd(order.tongtien)}</td>
                    <td><span class="${statusClass}">${statusHtml}</span></td>
                    <td>
                        <button class="btn-detail" onclick="detailOrder('${order.id}')">
                            <i class="fa-regular fa-eye"></i> Chi tiết
                        </button>
                    </td>
                </tr>
            `;
        });
        
        html += `
                </tbody>
            </table>
        `;
        
        // Hiển thị bảng đơn hàng
        ordersContainer.innerHTML = html;
    }
    
    // Hàm định dạng ngày tháng
    function formatDate(dateString) {
        const date = new Date(dateString);
        return `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getFullYear()}`;
    }
    
    // Hàm định dạng tiền VND
    function vnd(price) {
        return price.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
    }
});
