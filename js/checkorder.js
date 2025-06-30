// Sự kiện tra cứu đơn hàng
const tracuuForm = document.querySelector(".form-tracuu");
if (tracuuForm) {
    tracuuForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        let sdtInput = document.querySelector(".tracuudon");
        let sdt = sdtInput.value.trim();
        let currentUser = JSON.parse(localStorage.getItem('currentuser')) || null;
        // Nếu chưa nhập SĐT
        if (!sdt) {
            toast({ title: "Cảnh báo", message: "Vui lòng nhập số điện thoại để tra cứu đơn hàng!", type: "warning", duration: 3000 });
            return;
        }
        // Guest: luôn yêu cầu đăng nhập, không cho tra cứu
        if (!currentUser) {
            toast({ title: "Thông báo", message: "Vui lòng đăng nhập để tra cứu đơn hàng!", type: "warning", duration: 3000 });
            return;
        }
        // Admin: tra cứu bất kỳ sdt nào
        if (currentUser.userType == 1) {
            // Lấy mới nhất từ server
            let orders = [];
            try {
                const res = await fetch('get_orders.php');
                orders = await res.json();
            } catch (e) {
                orders = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];
            }
            let filteredOrders = orders.filter(order => order.sdtnhan === sdt);
            showOrdersdt(filteredOrders, sdt);
            return;
        }
        // User thường: chỉ tra cứu đúng sdt của mình
        if (sdt !== currentUser.phone) {
            toast({ title: "Cảnh báo", message: "Bạn chỉ có thể tra cứu đơn hàng của chính mình!", type: "warning", duration: 3000 });
            return;
        }
        // Đúng sdt, lấy mới nhất từ server
        let orders = [];
        try {
            const res = await fetch('get_orders.php');
            orders = await res.json();
        } catch (e) {
            orders = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];
        }
        let filteredOrders = orders.filter(order => order.sdtnhan === sdt);
        showOrdersdt(filteredOrders, sdt);
    });
}

// Hàm hiển thị đơn hàng
function showOrdersdt(arr, sdt) {
    let orderHtml = ``;
    if (arr.length === 0) {
        orderHtml = `<br><h2>Giỏ hàng của bạn vẫn đang chờ bạn!<br>Đừng bỏ lỡ những sản phẩm tuyệt vời từ Shop mình nhé (^.^)</h2>`;
    } else {
        // Kiểm tra có đơn nào là của đúng SĐT tra cứu không
        let hasOwnerOrder = arr.some(item => item.sdtnhan && sdt && item.sdtnhan.trim() === sdt.trim());
        orderHtml = '<br><div class="main-account"><div class="main-account-header"><h3>Thông tin đơn hàng từ SĐT của bạn</h3><p>Xem chi tiết, trạng thái của những đơn hàng.</p></div><div class="section"><div class="table"><table width="100%"><thead><tr><td>Mã đơn</td><td>Tên người nhận</td><td>Ngày đặt</td><td>Tổng tiền</td><td>Trạng thái</td><td>Thanh toán</td><td>Hình thức</td>';
        if (hasOwnerOrder) {
            orderHtml += '<td>Thao tác</td>';
        }
        orderHtml += '</tr></thead><tbody>';
        arr.forEach((item) => {
            let trangThai = parseInt(item.trangthai);
            let status;
            if (trangThai === 0) {
                status = `<span class="status-no-complete">Chưa xử lý</span>`;
            } else if (trangThai === 1) {
                status = `<span class=\"confirmed\">Đã xác nhận</span>`;
            } else if (trangThai === 2) {
                status = `<span class=\"status-shipping\">Đang giao hàng</span>`;
            } else if (trangThai === 3) {
                status = `<span class=\"completed\">Hoàn thành</span>`;
            } else if (trangThai === 4) {
                status = `<span class=\"status-cancel\">Đã hủy</span>`;
            } else {
                status = `<span class=\"status-no-complete\">Không xác định (${trangThai})</span>`;
            }
            let date = formatDate(item.thoigiandat);
            let isOwner = item.sdtnhan && sdt && item.sdtnhan.trim() === sdt.trim();
            // Nút chi tiết chỉ hiện nếu đúng chủ đơn hàng
            let detailBtn = isOwner
                ? `<button class="btn-detail" onclick="detailOrder('${item.id}')"><i class="fa-regular fa-eye"></i> Chi tiết</button>`
                : '';
            // Trạng thái thanh toán
            let paymentStatus = (parseInt(item.payment_status) === 1)
                ? '<span class="pay-status pay-success">Đã thanh toán</span>'
                : '<span class="pay-status pay-pending">Chưa thanh toán</span>';
            let paymentMethod = item.payment_method
                ? (item.payment_method.toLowerCase() === 'online'
                    ? '<span class="pay-method pay-online">Online</span>'
                    : '<span class="pay-method pay-cod">COD</span>')
                : '<span class="pay-method pay-cod">COD</span>';
            orderHtml += `
            <tr>
                <td>${item.id}</td>
                <td>${item.tenguoinhan}</td>
                <td>${date}</td>
                <td>${vnd(item.tongtien)}</td>
                <td>${status}</td>
                <td>${paymentStatus}</td>
                <td>${paymentMethod}</td>`;
            if (hasOwnerOrder) {
                orderHtml += `
                <td class="control">
                    ${detailBtn}
                </td>`;
            }
            orderHtml += `
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
    let currentUser = JSON.parse(localStorage.getItem('currentuser')) || null;
    if (!currentUser) {
        toast({ title: 'Lỗi', message: 'Bạn cần đăng nhập để hủy đơn hàng!', type: 'error', duration: 2000 });
        return;
    }
    if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
        let userPhone = currentUser.phone;
        let orders = localStorage.getItem('order') ? JSON.parse(localStorage.getItem('order')) : [];
        let order = orders.find(item => item.id == orderId);
        // Cho phép admin hủy bất kỳ đơn nào, khách chỉ được hủy đơn của mình
        if (order && (order.khachhang == userPhone || currentUser.userType == 1)) {
            // Hoàn trả số lượng sách về kho
            let orderDetails = localStorage.getItem('orderDetails') ? JSON.parse(localStorage.getItem('orderDetails')) : [];
            let products = JSON.parse(localStorage.getItem('products'));
            let details = orderDetails.filter(item => item.madon == orderId);
            details.forEach(async detail => {
                let p = products.find(sp => sp.id == detail.id);
                if (p) {
                    p.soluong += parseInt(detail.soluong);
                    // Gọi API cập nhật số lượng về database
                    await fetch('update_product_quantity.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: p.id, soluong: p.soluong })
                    });
                }
            });
            localStorage.setItem('products', JSON.stringify(products));
            // Chuẩn bị dữ liệu để gửi đến server
            const requestData = {
                orderId: orderId,
                userPhone: userPhone
            };
            if (currentUser.userType == 1) {
                requestData.isAdmin = true;
            }
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
                        } else {
                            // Sử dụng giá trị mặc định nếu không nhận được từ API
                            orders[orderIndex].trangthai = 4;
                        }
                        // Lưu vào localStorage
                        localStorage.setItem("order", JSON.stringify(orders));
                    }
                    // Lọc và hiển thị lại danh sách đơn hàng
                    let filteredOrders = orders.filter(order => order.sdtnhan === sdt);
                    showOrdersdt(filteredOrders, sdt);
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
                toast({ 
                    title: "Lỗi", 
                    message: "Đã xảy ra lỗi khi hủy đơn hàng: " + error.message, 
                    type: "error", 
                    duration: 3000 
                });
            });
        } else {
            toast({ title: 'Lỗi', message: 'Bạn không có quyền hủy đơn này!', type: 'error', duration: 2000 });
            return;
        }
    }
}

// Hàm đồng bộ trạng thái đơn hàng với server
function syncOrderStatusWithServer() {
    // Lấy danh sách đơn hàng từ localStorage
    let orders = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];
    if (orders.length === 0) {
        return;
    }
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
            orders.forEach((localOrder, index) => {
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
                const serverOrder = serverOrders.find(o => o.id === localOrder.id);
                if (serverOrder) {
                    const localStatus = parseInt(orders[index].trangthai);
                    const serverStatus = parseInt(serverOrder.trangthai);
                    if (localStatus !== serverStatus) {
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
            if (hasUpdates) {
                localStorage.setItem("order", JSON.stringify(orders));
                if (fixes.length > 0) {
                    toast({ 
                        title: "Đồng bộ dữ liệu", 
                        message: `Đã cập nhật trạng thái cho ${fixes.length} đơn hàng`, 
                        type: "info", 
                        duration: 3000 
                    });
                }
                // KHÔNG render lại bảng ở đây!
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
