<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='./assets/img/iconlogos.jpg' rel='icon' type='image/x-icon' />
    <link rel="stylesheet" href="assets/css/admin.css?v=20250819">
    <link rel="stylesheet" href="./assets/css/toast-message.css">
    <link href="./assets/font/font-awesome-pro-v6-6.2.0/css/all.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="./assets/css/admin-responsive.css?v=20250819">
    <title>Quản lý cửa hàng</title>
</head>

<body>
    <header class="header">
        <button class="menu-icon-btn">
            <div class="menu-icon">
                <i class="fa-regular fa-bars"></i>
            </div>
        </button>
    </header>
    <div class="container">
        <aside class="sidebar open">
            <div class="top-sidebar">
                <!-- <a href="#" class="channel-logo"><img src="./assets/img/iconlogo.png" alt="Channel Logo"></a> -->
                <!-- <div class="hidden-sidebar your-channel"><img src="assets/img/admin/bookshop.jpg"
                        style="height: 50px;" alt="">
                </div> -->
            </div>
            <div class="middle-sidebar">
                <ul class="sidebar-list">
                    <li class="sidebar-list-item tab-content active">
                        <a href="#" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-house"></i></div>
                            <div class="hidden-sidebar">Trang tổng quan</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item tab-content">
                        <a href="#" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-pot-food"></i></div>
                            <div class="hidden-sidebar">Sản phẩm</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item tab-content">
                        <a href="#" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-users"></i></div>
                            <div class="hidden-sidebar">Khách hàng</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item tab-content">
                        <a href="#" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-basket-shopping"></i></div>
                            <div class="hidden-sidebar">Đơn hàng</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item tab-content">
                        <a href="#" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-chart-simple"></i></div>
                            <div class="hidden-sidebar">Thống kê</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item tab-content">
                        <a href="#" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-tags"></i></div>
                            <div class="hidden-sidebar">Giảm giá</div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="bottom-sidebar">
                <ul class="sidebar-list">
                    <li class="sidebar-list-item user-logout">
                        <a href="./index.php" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-thin fa-circle-chevron-left"></i></div>
                            <div class="hidden-sidebar">Trang chủ</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item user-logout">
                        <a href="#" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-circle-user"></i></div>
                            <div class="hidden-sidebar" id="name-acc"></div>
                        </a>
                    </li>
                    <li class="sidebar-list-item user-logout">
                        <a href="./index.php" class="sidebar-link" id="logout-acc">
                            <div class="sidebar-icon"><i class="fa-light fa-arrow-right-from-bracket"></i></div>
                            <div class="hidden-sidebar">Đăng xuất</div>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <main class="content">
            <div class="section active">
                <h1 class="page-title">Trang tổng quát của cửa hàng Book Shop</h1>
                <div class="cards">
                    <div class="card-single">
                        <div class="box">
                            <h2 id="amount-user">0</h2>
                            <div class="on-box">
                                <img src="assets/img/admin/s1.png" alt="" style=" width: 200px;">
                                <h3>Khách hàng</h3>
                                <p>Sản phẩm là bất cứ cái gì có thể đưa vào thị trường để tạo sự chú ý, mua sắm, sử dụng
                                    hay tiêu dùng nhằm thỏa mãn một nhu cầu hay ước muốn. Nó có thể là những vật thể,
                                    dịch vụ, con người, địa điểm, tổ chức hoặc một ý tưởng.</p>
                            </div>

                        </div>
                    </div>
                    <div class="card-single">
                        <div class="box">
                            <div class="on-box">
                                <img src="assets/img/admin/s2.png" alt="" style=" width: 200px;">
                                <h2 id="amount-product">0</h2>
                                <h3>Sản phẩm</h3>
                                <p>Khách hàng mục tiêu là một nhóm đối tượng khách hàng trong phân khúc thị trường mục
                                    tiêu mà doanh nghiệp bạn đang hướng tới. </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-single">
                        <div class="box">
                            <h2 id="doanh-thu"></h2>
                            <div class="on-box">
                                <img src="assets/img/admin/s3.png" alt="" style=" width: 200px;">
                                <h3>Doanh thu</h3>
                                <p>Doanh thu của doanh nghiệp là toàn bộ số tiền sẽ thu được do tiêu thụ sản phẩm, cung
                                    cấp dịch vụ với sản lượng.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Product  -->
            <div class="section product-all">
                <div class="admin-control">
                    <div class="admin-control-left">
                        <select name="the-loai" id="the-loai" onchange="showProduct()">
                            <option>Tất cả</option>
                            <option>Sách Hay</option>
                            <option>Khoa Học</option>
                            <option>Tiểu Thuyết</option>
                            <option>Thiếu Nhi</option>
                            <option>Sách khác</option>
                            <option>Đã xóa</option>
                        </select>
                    </div>
                    <div class="admin-control-center">
                        <form action="" class="form-search">
                            <span class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                            <input id="form-search-product" type="text" class="form-search-input" placeholder="Tìm kiếm sách..." oninput="showProduct()" autocomplete="off">
                        </form>
                    </div>
                    <div class="admin-control-right">
                        <button class="btn-control-large" id="btn-cancel-product" onclick="cancelSearchProduct()"><i class="fa-light fa-rotate-right"></i> Làm mới</button>
                        <button class="btn-control-large" id="btn-add-product"><i class="fa-light fa-plus"></i> Thêm sách mới</button>                  
                    </div>
                </div>
                <div id="show-product"></div>
                <div class="page-nav">
                    <ul class="page-nav-list">
                    </ul>
                </div>
            </div>
            <!-- Account  -->
            <div class="section">
                <div class="admin-control">
                    <div class="admin-control-left">
                        <select name="tinh-trang-user" id="tinh-trang-user" onchange="showUser()">
                            <option value="2">Tất cả</option>
                            <option value="1">Hoạt động</option>
                            <option value="0">Bị khóa</option>
                        </select>
                    </div>
                    <div class="admin-control-center">
                        <form action="" class="form-search">
                            <span class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                            <input id="form-search-user" type="text" class="form-search-input" placeholder="Tìm kiếm khách hàng..." oninput="showUser()" autocomplete="off">
                        </form>
                    </div>
                    <div class="admin-control-right">
                        <form action="" class="fillter-date">
                            <div>
                                <label for="time-start">Từ</label>
                                <input type="date" class="form-control-date" id="time-start-user" onchange="showUser()">
                            </div>
                            <div>
                                <label for="time-end">Đến</label>
                                <input type="date" class="form-control-date" id="time-end-user" onchange="showUser()">
                            </div>
                        </form>      
                        <button class="btn-reset-order" onclick="cancelSearchUser()"><i class="fa-light fa-arrow-rotate-right"></i></button>     
                        <button id="btn-add-user" class="btn-control-large" onclick="openCreateAccount()"><i class="fa-light fa-plus"></i> <span>Thêm khách hàng</span></button>          
                    </div>
                </div>
                <div class="table">
                    <table width="100%">
                        <thead>
                            <tr>
                                <td>STT</td>
                                <td>Họ và tên</td>
                                <td>Liên hệ</td>
                                <td>Ngày tham gia</td>
                                <td>Tình trạng</td>
                                <td>Thao tác</td>
                            </tr>
                        </thead>
                        <tbody id="show-user">
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Order  -->
            <div class="section">
                <div class="admin-control">
                    <div class="admin-control-left">
                        <select name="tinh-trang" id="tinh-trang" onchange="findOrder()">
                            <option value="2">Tất cả</option>
                            <option value="0">Chưa xử lý</option>
                            <option value="1">Đã xác nhận</option>
                            <option value="2">Đang giao hàng</option>
                            <option value="3">Hoàn thành</option>
                            <option value="4">Đã hủy</option>
                        </select>
                        <select name="thanh-toan" id="thanh-toan" onchange="findOrder()">
                            <option value="2">Tất cả</option>
                            <option value="1">Đã thanh toán</option>
                            <option value="0">Chưa thanh toán</option>
                        </select>
                    </div>
                    <div class="admin-control-center">
                        <form action="" class="form-search">
                            <span class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                            <input id="form-search-order" type="text" class="form-search-input" placeholder="Tìm kiếm mã đơn, khách hàng..." oninput="findOrder()" autocomplete="off">
                        </form>
                    </div>
                    <div class="admin-control-right">
                        <form action="" class="fillter-date">
                            <div>
                                <label for="time-start">Từ</label>
                                <input type="date" class="form-control-date" id="time-start" onchange="findOrder()">
                            </div>
                            <div>
                                <label for="time-end">Đến</label>
                                <input type="date" class="form-control-date" id="time-end" onchange="findOrder()">
                            </div>
                        </form>      
                        <button class="btn-reset-order" onclick="cancelSearchOrder()"><i class="fa-light fa-arrow-rotate-right"></i></button>               
                    </div>
                </div>
                <div class="table">
                    <table width="100%">
                        <thead>
                            <tr>
                                <td>Mã đơn</td>
                                <td>Khách hàng</td>
                                <td>Ngày đặt</td>
                                <td>Tổng tiền</td>
                                <td>Trạng thái</td>
                                <td>Thanh toán</td>
                                <td>Phương thức</td>
                                <td>Thao tác</td>
                            </tr>
                        </thead>
                        <tbody id="showOrder">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="section">
                <div class="admin-control">
                    <div class="admin-control-left">
                        <select name="the-loai-tk" id="the-loai-tk" onchange="thongKe()">
                            <option>Tất cả</option>
                            <option>Sách Hay</option>
                            <option>Khoa Học</option>
                            <option>Tiểu Thuyết</option>
                            <option>Thiếu Nhi</option>
                        </select>
                    </div>
                    <div class="admin-control-center">
                        <form action="" class="form-search">
                            <span class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                            <input id="form-search-tk" type="text" class="form-search-input" placeholder="Tìm kiếm sách..." oninput="thongKe()" autocomplete="off">
                        </form>
                    </div>
                    <div class="admin-control-right">
                        <form action="" class="fillter-date">
                            <div>
                                <label for="time-start">Từ</label>
                                <input type="date" class="form-control-date" id="time-start-tk" onchange="thongKe()">
                            </div>
                            <div>
                                <label for="time-end">Đến</label>
                                <input type="date" class="form-control-date" id="time-end-tk" onchange="thongKe()">
                            </div>
                        </form> 
                        <button class="btn-reset-order" onclick="thongKe(1)"><i class="fa-regular fa-arrow-up-short-wide"></i></i></button>
                        <button class="btn-reset-order" onclick="thongKe(2)"><i class="fa-regular fa-arrow-down-wide-short"></i></button>
                        <button class="btn-reset-order" onclick="thongKe(0)"><i class="fa-light fa-arrow-rotate-right"></i></button>                    
                    </div>
                </div>
                <div class="order-statistical" id="order-statistical">
                    <div class="order-statistical-item">
                        <div class="order-statistical-item-content">
                            <p class="order-statistical-item-content-desc">Sản phẩm được bán ra</p>
                            <h4 class="order-statistical-item-content-h" id="quantity-product"></h4>
                            <small class="order-statistical-note">(Chỉ tính đơn chưa bị hủy)</small>
                        </div>
                        <div class="order-statistical-item-icon">
                            <i class="fa-light fa-salad"></i>
                        </div>
                    </div>
                    <div class="order-statistical-item">
                        <div class="order-statistical-item-content">
                            <p class="order-statistical-item-content-desc">Số lượng bán ra</p>
                            <h4 class="order-statistical-item-content-h" id="quantity-order"></h4>
                            <small class="order-statistical-note">(Chỉ tính đơn chưa bị hủy)</small>
                        </div>
                        <div class="order-statistical-item-icon">
                            <i class="fa-light fa-file-lines"></i>
                        </div>
                    </div>
                    <div class="order-statistical-item">
                        <div class="order-statistical-item-content">
                            <p class="order-statistical-item-content-desc">Doanh thu</p>
                            <h4 class="order-statistical-item-content-h" id="quantity-sale"></h4>
                            <small class="order-statistical-note">(Chỉ tính đơn chưa bị hủy)</small>
                        </div>
                        <div class="order-statistical-item-icon">
                            <i class="fa-light fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                <div class="table">
                    <table width="100%">
                        <thead>
                            <tr>
                                <td>STT</td>
                                <td>Tên sách</td>
                                <td>Số lượng bán</td>
                                <td>Doanh thu</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody id="showTk">
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Discount  -->
            <div class="section">
                <div class="admin-control">
                    <div class="admin-control-left">
                        <button class="btn-add-discount" onclick="showAddDiscountModal()">
                            <i class="fa-light fa-plus"></i>
                            <span>Tạo chương trình giảm giá</span>
                        </button>
                    </div>
                    <div class="admin-control-center">
                        <form action="" class="form-search">
                            <span class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                            <input id="form-search-discount" type="text" class="form-search-input" placeholder="Tìm kiếm chương trình giảm giá..." oninput="searchDiscounts()" autocomplete="off">
                        </form>
                    </div>
                    <div class="admin-control-right">
                        <select name="discount-status-filter" id="discount-status-filter" onchange="filterDiscounts()">
                            <option value="all">Tất cả</option>
                            <option value="active">Đang hoạt động</option>
                            <option value="pending">Chưa kích hoạt</option>
                            <option value="expired">Đã hết hạn</option>
                            <option value="inactive">Không hoạt động</option>
                        </select>
                    </div>
                </div>
                <div class="table">
                    <table width="100%">
                        <thead>
                            <tr>
                                <td>Tên chương trình</td>
                                <td>Loại giảm giá</td>
                                <td>Giá trị</td>
                                <td>Thời gian</td>
                                <td>Trạng thái</td>
                                <td>Số sản phẩm</td>
                                <td>Thao tác</td>
                            </tr>
                        </thead>
                        <tbody id="showDiscounts">
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <div class="modal add-product">
        <div class="modal-container">
            <h3 class="modal-container-title add-product-e">THÊM MỚI SẢN PHẨM</h3>
            <h3 class="modal-container-title edit-product-e">CHỈNH SỬA SẢN PHẨM</h3>
            <button class="modal-close product-form"><i class="fa-regular fa-xmark"></i></button>
            <div class="modal-content">
                <form action="" class="add-product-form" enctype="multipart/form-data">
                    <div class="modal-content-left">
                        <img src="./assets/img/blank-image.png" alt="" class="upload-image-preview">
                        <div class="form-group file">
                            <label for="up-hinh-anh" class="form-label-file"><i class="fa-regular fa-cloud-arrow-up"></i>Chọn hình ảnh</label>
                            <input accept="image/jpeg, image/png, image/jpg, image/webp" id="up-hinh-anh" name="up-hinh-anh" type="file" class="form-control" onchange="uploadImage(this)">
                        </div>
                    </div>
                    <div class="modal-content-right">
                        <div class="form-group">
                            <label for="ten-sach" class="form-label">Tên sách</label>
                            <input id="ten-sach" name="ten-sach" type="text" placeholder="Nhập tên sách" autocomplete="off"
                                class="form-control" autocomplete="off">
                            <span class="form-message"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Chọn sách</label>
                            <select name="category" id="chon-sach">
                                <option>Sách Hay</option>
                                <option>Khoa Học</option>
                                <option>Tiểu Thuyết</option>
                                <option>Thiếu Nhi</option>
                                <option>Không Phân Loại</option>
                            </select>
                            <span class="form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="gia-moi" class="form-label">Giá bán</label>
                            <input id="gia-moi" name="gia-moi" type="number" placeholder="Nhập giá bán"
                                class="form-control">
                            <span class="form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="so-luong" class="form-label">Số lượng</label>
                            <input id="so-luong" name="so-luong" type="number" min="0" placeholder="Nhập số lượng" class="form-control">
                            <span class="form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="mo-ta" class="form-label">Mô tả</label>
                            <textarea class="product-desc" id="mo-ta" placeholder="Nhập mô tả sách..."></textarea>
                            <span class="form-message"></span>
                        </div>
                        <button class="form-submit btn-add-product-form add-product-e" id="add-product-button">
                            <i class="fa-regular fa-plus"></i>
                            <span>THÊM SÁCH</span>
                        </button>
                        <button class="form-submit btn-update-product-form edit-product-e" id="update-product-button">
                            <i class="fa-light fa-pencil"></i>
                            <span>LƯU THAY ĐỔI</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal detail-order">
        <div class="modal-container">
            <h3 class="modal-container-title">CHI TIẾT ĐƠN HÀNG</h3>
            <button class="modal-close"><i class="fa-regular fa-xmark"></i></button>
            <div class="modal-detail-order">
            </div>
            <div class="modal-detail-bottom">               
            </div>
        </div>
    </div>
    <div class="modal detail-order-product">
        <div class="modal-container">
            <button class="modal-close"><i class="fa-regular fa-xmark"></i></button>
            <div class="table">
                <table width="100%">
                    <thead>
                        <tr>
                            <td>Mã đơn</td>
                            <td>Số lượng</td>
                            <td>Đơn giá</td>
                            <td>Ngày đặt</td>
                        </tr>
                    </thead>
                    <tbody id="show-product-order-detail">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal signup">
        <div class="modal-container">
            <h3 class="modal-container-title add-account-e">THÊM KHÁCH HÀNG MỚI</h3>
            <button class="modal-close"><i class="fa-regular fa-xmark"></i></button>
            <div class="form-content sign-up">
                <form action="" class="signup-form">
                    <div class="form-group">
                        <label for="fullname" class="form-label">Tên đầy đủ</label>
                        <input id="fullname" name="fullname" type="text" placeholder="VD: Phan Nhật Tân" class="form-control" autocomplete="name">
                        <span class="form-message-name form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input id="phone" name="phone" type="text" placeholder="Nhập số điện thoại" class="form-control" autocomplete="tel">
                        <span class="form-message-phone form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input id="password" name="password" type="password" placeholder="Nhập mật khẩu" class="form-control" autocomplete="new-password">
                        <span class="form-message-password form-message"></span>
                    </div>   
                    <div class="form-group">
                        <label class="form-label">Trạng thái</label>
                        <input type="checkbox" id="user-status" class="switch-input">
                        <label for="user-status" class="switch"></label>
                    </div>
                    <button class="form-submit add-account-e" id="signup-button">Đăng ký</button>
                    <button class="form-submit edit-account-e" id="btn-update-account"><i class="fa-regular fa-floppy-disk"></i> Lưu thông tin</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal hủy đơn hàng -->
    <div class="modal cancel-order-modal">
        <div class="modal-container">
            <h3 class="modal-container-title">HỦY ĐƠN HÀNG</h3>
            <button class="modal-close"><i class="fa-regular fa-xmark"></i></button>
            <div class="cancel-order-content">
                <p>Bạn có chắc chắn muốn hủy đơn hàng <strong id="cancel-order-id"></strong>?</p>
                <div class="form-group">
                    <label for="cancel-reason-select" class="form-label">Lý do hủy đơn</label>
                    <select id="cancel-reason-select" class="form-control" onchange="toggleCustomReason()">
                        <option value="">Chọn lý do hủy đơn</option>
                        <option value="Khách hàng yêu cầu hủy">Khách hàng yêu cầu hủy</option>
                        <option value="Sản phẩm hết hàng">Sản phẩm hết hàng</option>
                        <option value="Thông tin giao hàng không chính xác">Thông tin giao hàng không chính xác</option>
                        <option value="Đơn hàng bị trùng lặp">Đơn hàng bị trùng lặp</option>
                        <option value="Vấn đề về thanh toán">Vấn đề về thanh toán</option>
                        <option value="Khác">Lý do khác</option>
                    </select>
                </div>
                <div class="form-group" id="custom-reason-group" style="display: none;">
                    <label for="cancel-reason-custom" class="form-label">Nhập lý do cụ thể</label>
                    <textarea id="cancel-reason-custom" class="form-control" placeholder="Nhập lý do hủy đơn..." rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn-cancel-confirm" onclick="confirmCancelOrder()">
                        <i class="fa-regular fa-ban"></i> Xác nhận hủy
                    </button>
                    <button class="btn-cancel-close" onclick="closeCancelOrderModal()">
                        <i class="fa-regular fa-times"></i> Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div id="toast"></div>
    
    <!-- Modal tạo/chỉnh sửa chương trình giảm giá -->
    <div class="modal add-discount">
        <div class="modal-container">
            <h3 class="modal-container-title add-discount-e">TẠO CHƯƠNG TRÌNH GIẢM GIÁ</h3>
            <h3 class="modal-container-title edit-discount-e">CHỈNH SỬA CHƯƠNG TRÌNH GIẢM GIÁ</h3>
            <button class="modal-close discount-form"><i class="fa-regular fa-xmark"></i></button>
            <div class="modal-content">
                <form action="" class="add-discount-form">
                    <div class="modal-content-left">
                        <div class="form-group">
                            <label for="discount-name" class="form-label">Tên chương trình</label>
                            <input id="discount-name" name="discount-name" type="text" placeholder="VD: Giảm giá mùa hè" class="form-control" autocomplete="off">
                            <span class="form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="discount-description" class="form-label">Mô tả</label>
                            <textarea id="discount-description" name="discount-description" placeholder="Mô tả chương trình giảm giá..." class="form-control"></textarea>
                            <span class="form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="discount-type" class="form-label">Loại giảm giá</label>
                            <select name="discount-type" id="discount-type" class="form-control" onchange="toggleDiscountValue()">
                                <option value="percentage">Giảm theo phần trăm (%)</option>
                                <option value="fixed_amount">Giảm theo số tiền cố định</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="discount-value" class="form-label">Giá trị giảm</label>
                            <input id="discount-value" name="discount-value" type="number" min="0" placeholder="VD: 20 hoặc 50000" class="form-control">
                            <small id="discount-value-hint">Nhập số phần trăm (VD: 20 = 20%)</small>
                            <span class="form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="min-order-amount" class="form-label">Giá sản phẩm tối thiểu</label>
                            <input id="min-order-amount" name="min-order-amount" type="number" min="0" placeholder="VD: 100000 (để trống = không giới hạn)" class="form-control">
                            <small class="form-hint">Sản phẩm phải có giá tối thiểu này để được áp dụng giảm giá</small>
                            <span class="form-message"></span>
                        </div>
                    </div>
                    <div class="modal-content-right">
                        <div class="form-group">
                            <label for="discount-start-date" class="form-label">Ngày bắt đầu</label>
                            <input id="discount-start-date" name="discount-start-date" type="datetime-local" class="form-control">
                            <span class="form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="discount-end-date" class="form-label">Ngày kết thúc</label>
                            <input id="discount-end-date" name="discount-end-date" type="datetime-local" class="form-control">
                            <span class="form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="discount-max-uses" class="form-label">Số lượng tối đa</label>
                            <input id="discount-max-uses" name="discount-max-uses" type="number" min="0" placeholder="0 = không giới hạn" class="form-control">
                            <span class="form-message"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Trạng thái</label>
                            <input type="checkbox" id="discount-status" class="switch-input">
                            <label for="discount-status" class="switch"></label>
                        </div>
                        <div class="form-group">
                            <label for="discount-apply-type" class="form-label">Áp dụng cho</label>
                            <select name="discount-apply-type" id="discount-apply-type" class="form-control" onchange="toggleProductSelection()">
                                <option value="specific_products">Sản phẩm cụ thể</option>
                                <option value="category">Toàn bộ danh mục</option>
                            </select>
                        </div>
                        <div class="form-group" id="category-selection" style="display: none;">
                            <label for="discount-category" class="form-label">Chọn danh mục</label>
                            <select name="discount-category" id="discount-category" class="form-control">
                                <option value="">Chọn danh mục</option>
                                <option value="Sách hay">Sách hay</option>
                                <option value="Khoa học">Khoa học</option>
                                <option value="Tiểu thuyết">Tiểu thuyết</option>
                                <option value="Thiếu nhi">Thiếu nhi</option>
                                <option value="Không phân loại">Không phân loại</option>
                            </select>
                        </div>
                        <div class="form-group" id="product-selection">
                            <label for="discount-products" class="form-label">Chọn sản phẩm</label>
                            <div class="product-search-container">
                                <input id="discount-product-search" type="text" placeholder="Tìm kiếm sản phẩm..." class="form-control" oninput="searchProductsForDiscount()" autocomplete="off">
                                <div id="discount-product-results" class="product-search-results"></div>
                            </div>
                            <div id="selected-discount-products" class="selected-products"></div>
                        </div>
                        <button class="form-submit add-discount-e" id="add-discount-button">
                            <i class="fa-regular fa-plus"></i>
                            <span>TẠO CHƯƠNG TRÌNH</span>
                        </button>
                        <button class="form-submit edit-discount-e" id="update-discount-button">
                            <i class="fa-light fa-pencil"></i>
                            <span>LƯU THAY ĐỔI</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="./js/admin.js"></script>
    <script>
      // Đảm bảo gọi showUser khi trang load (nếu có tab khách hàng)
      if (typeof showUser === 'function') showUser();
      else window.addEventListener('DOMContentLoaded', function() { if (typeof showUser === 'function') showUser(); });
    </script>
    <script src="./js/toast-message.js"></script>
</body>
</html>