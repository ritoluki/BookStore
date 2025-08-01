<?php
include 'php/main.php';
session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Shop</title>
    <link href='./assets/img/iconlogo.png' rel='icon' type='image/x-icon' />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./assets/css/main.css">
    <link rel="stylesheet" href="./assets/css/admin-responsive.css">
    <link rel="stylesheet" href="./assets/css/toast-message.css">
    <link rel="stylesheet" href="./assets/css/slider.css">
    <link rel="stylesheet" href="./assets/css/responsive.css">
    <link rel="stylesheet" href="./assets/css/book_review.css">
    <link rel="stylesheet" href="./assets/font/font-awesome-pro-v6-6.2.0/css/all.min.css"/>
</head>
<body>
    <header>
        <div class="header-middle">
            <div class="container">
                <div class="header-middle-left">
                    <div class="header-logo">
                        <a href="">
                            <img src="./assets/img/iconlogos.jpg" alt="" class="header-logo-img">
                        </a>
                    </div>
                </div>
                <!-- Search bar will be hidden on mobile via CSS -->
                <div class="header-middle-center">
                    <form action="" class="form-search">
                        <span class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                        <input type="text" class="form-search-input" placeholder="Tìm kiếm sách..."
                            oninput="searchProducts()">
                        <button class="filter-btn"><i class="fa-light fa-filter-list"></i><span>Lọc</span></button>
                    </form>
                </div>
                <div class="header-middle-right">
                    <ul class="header-middle-right-list">
                        <li class="mobile-menu-toggle header-middle-right-item" onclick="toggleMobileMenu()">
                            <div class="cart-icon-menu">
                                <i class="fa-light fa-bars"></i>
                            </div>
                        </li>
                        <li class="header-middle-right-item dnone open" onclick="openSearchMb()">
                            <div class="cart-icon-menu">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                        </li>
                        <li class="header-middle-right-item close" onclick="closeSearchMb()">
                            <div class="cart-icon-menu">
                                <i class="fa-light fa-circle-xmark"></i>
                            </div>
                        </li>
                        <li class="header-middle-right-item dropdown open">
                            <i class="fa-light fa-user"></i>
                            <div class="auth-container">
                                <span class="text-dndk">Đăng nhập / Đăng ký</span>
                                <span class="text-tk">Tài khoản <i class="fa-sharp fa-solid fa-caret-down"></i></span>
                            </div>
                            <ul class="header-middle-right-menu">
                                <li><a id="login" href="javascript:;"><i class="fa-light fa-right-to-bracket"></i> Đăng nhập</a></li>
                                <li><a id="signup" href="javascript:;"><i class="fa-light fa-user-plus"></i> Đăng ký</a></li>
                            </ul>
                        </li>
                        <li class="header-middle-right-item open" onclick="openCart()">
                            <div class="cart-icon-menu">
                                <i class="fa-light fa-basket-shopping"></i>
                                <span class="count-product-cart">0</span>
                            </div>
                            <span>Giỏ hàng</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <div class="mobile-menu-overlay" onclick="toggleMobileMenu()"></div>
    <div class="mobile-search-overlay">
        <form action="" class="form-search">
            <span class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
            <input type="text" class="form-search-input" placeholder="Tìm kiếm sách..."
                oninput="searchProducts()">
            <button type="button" class="mobile-search-close" onclick="closeSearchMb()">
                <i class="fa-light fa-circle-xmark"></i>
            </button>
        </form>
    </div>
    <div class="mobile-menu-panel">
        <div class="mobile-menu-header">
            <button class="mobile-menu-close" onclick="toggleMobileMenu()">
                <i class="fa-light fa-circle-xmark"></i>
            </button>
        </div>
        <ul class="mobile-menu-list">
            <li class="mobile-menu-item"><a href="" class="mobile-menu-link">Trang chủ</a></li>
            <li class="mobile-menu-item"><a href="javascript:;" class="mobile-menu-link" onclick="showCategory('Sách Hay')">Sách Hay</a></li>
            <li class="mobile-menu-item"><a href="javascript:;" class="mobile-menu-link" onclick="showCategory('Khoa Học')">Khoa Học</a></li>
            <li class="mobile-menu-item"><a href="javascript:;" class="mobile-menu-link" onclick="showCategory('Tiểu Thuyết')">Tiểu Thuyết</a></li>
            <li class="mobile-menu-item"><a href="javascript:;" class="mobile-menu-link" onclick="showCategory('Thiếu Nhi')">Thiếu Nhi</a></li>
            <li class="mobile-menu-item"><a href="javascript:;" class="mobile-menu-link" onclick="showGioiThieu()">Giới thiệu</a></li>
            <li class="mobile-menu-item"><a href="javascript:;" class="mobile-menu-link" onclick="showTraCuu()">Tra cứu đơn hàng</a></li>
        </ul>
    </div>
    <nav  class="header-bottom">
        <div align="center" class="container">
            <ul class="menu-list">
                <pre>    </pre>
                <li class="menu-list-item" onclick="showTrangChu()"><a href="javascript:;" class="menu-link">Trang chủ</a></li>
                <li class="menu-list-item" onclick="showCategory('Sách Hay')"><a href="javascript:;" class="menu-link">Sách Hay</a></li>
                <li class="menu-list-item" onclick="showCategory('Khoa Học')"><a href="javascript:;" class="menu-link">Khoa Học</a></li>
                <li class="menu-list-item" onclick="showCategory('Tiểu Thuyết')"><a href="javascript:;" class="menu-link">Tiểu Thuyết</a></li>
                <li class="menu-list-item" onclick="showCategory('Thiếu Nhi')"><a href="javascript:;" class="menu-link">Thiếu Nhi</a></li>
                <li class="menu-list-item" onclick="showGioiThieu()"><a href="javascript:;" class="menu-link">Giới thiệu</a></li>
                <li class="menu-list-item" onclick="showTraCuu()"><a href="javascript:;" class="menu-link">Tra cứu đơn hàng</a></li>
            </ul>
        </div>
    </nav>
    <div class="advanced-search mobile-hide">
        <div class="container">
            <div class="advanced-search-category">
                <span>Phân loại </span>
                <select name="" id="advanced-search-category-select" onchange="searchProducts()">
                    <option>Tất cả</option>
                    <option>Sách Hay</option>
                    <option>Khoa Học</option>
                    <option>Tiểu Thuyết</option>
                    <option>Thiếu Nhi</option>
                </select>
            </div>
            <div class="advanced-search-price">
                <span>Giá từ</span>
                <input type="number" value="0" placeholder="tối thiểu" id="min-price" onchange="searchProducts()">
                <span>đến</span>
                <input type="number" placeholder="tối đa" id="max-price" onchange="searchProducts()">
                <button id="advanced-search-price-btn"><i class="fa-light fa-magnifying-glass-dollar"></i></button>
            </div>
            <div class="advanced-search-control">
                <button id="sort-ascending" onclick="searchProducts(1)"><i class="fa-regular fa-arrow-up-short-wide"></i></button>
                <button id="sort-descending" onclick="searchProducts(2)"><i class="fa-regular fa-arrow-down-wide-short"></i></button>
                <button id="reset-search" onclick="searchProducts(0)"><i class="fa-light fa-arrow-rotate-right"></i></button>
                <button onclick="closeSearchAdvanced()"><i class="fa-light fa-xmark"></i></button>
            </div>
        </div>
    </div>
    <main class="main-content">
        <div class="container" id="trangchu">
            <div class="slide-banner">
                <div class="slide-banner__container">
                    <div class="slide-banner__item bg-top" style="background:  url('./assets/img/banner-1.jpg');">
                        <div class="slide-banner__content">
                            <h1 class="slide-banner__title">Read.</h1>
                            <a href="#" class="slide-banner__button">
                                <span>Read now</span>
                            </a>
                        </div>
                    </div>
                    <div class="slide-banner__item bg-center" style="background:  url('./assets/img/banner.png');">
                        <div class="slide-banner__content">
                            <h1 class="slide-banner__title">Explore. </h1>
                            <a href="#" class="slide-banner__button">
                                <span>Explore now</span>
                            </a>
                        </div>
                    </div>
                    <div class="slide-banner__item bg-center" style="background:  url('./assets/img/banner.png');">
                        <div class="slide-banner__content">
                            <h1 class="slide-banner__title">Discover. </h1>
                            <a href="#" class="slide-banner__button">
                                <span>Discover now</span>
                            </a>
                        </div>
                    </div>
                </div>
                <button class="slide-banner__nav-button slide-banner__nav-button--prev">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 19L8 12L15 5" stroke="#b5292f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button class="slide-banner__nav-button slide-banner__nav-button--next">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 5L16 12L9 19" stroke="#b5292f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="slide-banner__dots-container">
                    <div class="slide-banner__dots">
                        <div class="slide-banner__dot slide-banner__dot--active"></div>
                        <div class="slide-banner__dot"></div>
                        <div class="slide-banner__dot"></div>
                    </div>
                </div>
            </div>
            <div class="home-service" id="home-service">
                <div class="home-service-item">
                    <div class="home-service-item-icon">
                        <i class="fa-light fa-person-carry-box"></i>
                    </div>
                    <div class="home-service-item-content">
                        <h4 class="home-service-item-content-h">GIAO HÀNG NHANH</h4>
                        <p class="home-service-item-content-desc">Cho tất cả đơn hàng</p>
                    </div>
                </div>
                <div class="home-service-item">
                    <div class="home-service-item-icon">
                        <i class="fa-light fa-shield-heart"></i>
                    </div>
                    <div class="home-service-item-content">
                        <h4 class="home-service-item-content-h">SÁCH CHÍNH HÃNG</h4>
                        <p class="home-service-item-content-desc">Cam kết chất lượng</p>
                    </div>
                </div>
                <div class="home-service-item">
                    <div class="home-service-item-icon">
                        <i class="fa-light fa-headset"></i>
                    </div>
                    <div class="home-service-item-content">
                        <h4 class="home-service-item-content-h">HỖ TRỢ 24/7</h4>
                        <p class="home-service-item-content-desc">Tất cả ngày trong tuần</p>
                    </div>
                </div>
                <div class="home-service-item">
                    <div class="home-service-item-icon">
                        <i class="fa-light fa-circle-dollar"></i>
                    </div>
                    <div class="home-service-item-content">
                        <h4 class="home-service-item-content-h">HOÀN LẠI TIỀN</h4>
                        <p class="home-service-item-content-desc">Nếu không hài lòng</p>
                    </div>
                </div>
            </div>
            <div class="home-title-block" id="home-title">
                <h2 class="home-title">Khám phá vũ trụ sách của chúng tôi</h2>
            </div>
            <div class="home-products" id="home-products">
            </div>
            <div class="page-nav" id="page-products">
                <ul class="page-nav-list">
                </ul>
            </div>
        </div>
        <div class="container" id="account-user">
            <div class="main-account">
                <div class="main-account-header">
                    <h3>Thông tin tài khoản của bạn</h3>
                    <p>Quản lý thông tin để bảo mật tài khoản</p>
                </div>
                <div class="main-account-body">
                    <div class="main-account-body-col">
                        <form action="" class="info-user">
                            <div class="form-group">
                                <label for="infoname" class="form-label">Họ và tên</label>
                                <input class="form-control" type="text" name="infoname" id="infoname" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="infophone" class="form-label">Số điện thoại</label>
                                <input class="form-control" type="text" name="infophone" id="infophone" disabled="true"
                                    placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="infoemail" class="form-label">Email</label>
                                <input class="form-control" type="email" name="infoemail" id="infoemail"
                                    placeholder="Thêm địa chỉ email của bạn">
                                <span class="inforemail-error form-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="infoaddress" class="form-label">Địa chỉ</label>
                                <input class="form-control" type="text" name="infoaddress" id="infoaddress"
                                    placeholder="Thêm địa chỉ giao hàng của bạn">
                            </div>
                        </form>
                    </div>
                    <div class="main-account-body-col">
                        <form action="" class="change-password">
                            <div class="form-group">
                                <label class="form-label w60">Mật khẩu hiện tại</label>
                                <input class="form-control" type="password" name="" id="password-cur-info"
                                    placeholder="Nhập mật khẩu hiện tại">
                                <span class="password-cur-info-error form-message"></span>
                            </div>
                            <div class="form-group">
                                <label class="form-label w60">Mật khẩu mới </label>
                                <input class="form-control" type="password" name="" id="password-after-info"
                                    placeholder="Nhập mật khẩu mới">
                                <span class="password-after-info-error form-message"></span>
                            </div>
                            <div class="form-group">
                                <label class="form-label w60">Xác nhận mật khẩu mới</label>
                                <input class="form-control" type="password" name="" id="password-comfirm-info"
                                    placeholder="Nhập lại mật khẩu mới">
                                <span class="password-after-comfirm-error form-message"></span>
                            </div>
                        </form>
                    </div>
                    <div class="main-account-body-row">
                        <div>
                            <button id="save-info-user" onclick="changeInformation()"><i
                                    class="fa-regular fa-floppy-disk"></i> Lưu thay đổi</button>
                        </div>
                        <div>
                            <button id="save-password" onclick="changePassword()"><i class="fa-regular fa-key"></i> Đổi
                                mật khẩu</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container" id="order-history">
            <div class="main-account">
                <div class="main-account-header">
                    <h3>Quản lý đơn hàng của bạn</h3>
                    <p>Xem chi tiết, trạng thái của những đơn hàng đã đặt.</p>
                </div>
                <div class="section">
                    <div class="table">
                        <table width="100%">
                            <thead>
                                <tr>
                                    <td>Mã đơn</td>
                                    <td>Tên người nhận</td>
                                    <td>Ngày đặt</td>
                                    <td>Tổng tiền</td>
                                    <td>Trạng thái</td>
                                    <td>Thao tác</td>
                                </tr>
                            </thead>
                            <tbody id="showOrder">
                            </tbody>
                        </table>
                    </div>
                </div>

                </div>
            </div>
        </div>
        <div class="container" id="gioithieu" style="display: none; overflow: hidden; scroll-behavior: auto;">
            <div class="introduction-container">
               <p>Chào mừng đến với trang web của chúng tôi</p>
            </div>
        </div>
        <div class="container" id="tracuu" style="display: none; overflow: hidden; scroll-behavior: auto;">
            <div class="home-title-block" id="home-title">
                <h1 class="home-title">Tra cứu đơn hàng</h1><br>
            </div>
            <div class="tracuu-instruction">
                <div class="tracuu-icon">
                    <i class="fa-solid fa-magnifying-glass-chart"></i>
                </div>
                <div class="tracuu-description">
                    <p>Nhập số điện thoại bạn đã dùng để đặt hàng vào ô bên dưới để tra cứu thông tin đơn hàng của bạn.</p>
                    <p>Hệ thống sẽ hiển thị tất cả các đơn hàng đã đặt với số điện thoại này, bao gồm thông tin về trạng thái, ngày đặt và tổng tiền.</p>
                </div>
            </div>
            <form class="form-tracuu">
                <input type="number" class="tracuudon" placeholder="Nhập SĐT đặt hàng...">
                <button class="filter-don">
                    <span>Tra cứu</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>
            <div class="tracuu-note">
                <h3>Lưu ý khi tra cứu đơn hàng</h3>
                <ul>
                    <li><i class="fa-solid fa-circle-check"></i> Nhập đúng số điện thoại đã dùng để đặt hàng</li>
                    <li><i class="fa-solid fa-circle-check"></i> Đơn hàng sẽ được hiển thị theo thứ tự thời gian, mới nhất lên đầu</li>
                    <li><i class="fa-solid fa-circle-check"></i> Bạn có thể xem chi tiết đơn hàng bằng cách nhấp vào nút "Chi tiết"</li>
                    <li><i class="fa-solid fa-circle-check"></i> Nếu không tìm thấy đơn hàng, vui lòng liên hệ hotline: 0123 456 789</li>
                </ul>
            </div>
            <div class="container" align="center">
                <div id="showOrdersdt"></div>
            </div>
        </div>
    </main>
    <div class="modal product-detail">
        <button class="modal-close close-popup"><i class="fa-thin fa-xmark"></i></button>
        <div class="modal-container mdl-cnt" id="product-detail-content">
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
            </form>
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
            </form>
        </div>
    </div>
    <div class="modal signup-login">
        <div class="modal-container">
            <button class="form-close" onclick="closeModal()"><i class="fa-regular fa-xmark"></i></button>
            <div class="forms mdl-cnt">
                <div class="form-content sign-up">
                    <h3 class="form-title">
                        Đăng ký tài khoản
                    </h3>
                    <p class="form-description">Đăng ký thành viên để mua hàng và nhận những ưu đãi đặc biệt từ chúng tôi</p>
                    <form action="" class="signup-form">
                        <div class="form-group">
                            <label for="fullname" class="form-label">Tên đầy đủ </label>
                            <input id="fullname" name="fullname" type="text" placeholder="Nhập họ và tên"
                                class="form-control" required>
                            <span class="form-message-name form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email </label>
                            <input id="email" name="email" type="email" placeholder="Nhập địa chỉ email"
                                class="form-control" required>
                            <span class="form-message-email form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label">Số điện thoại </label>
                            <input id="phone" name="phone" type="text" placeholder="Nhập số điện thoại"
                                class="form-control" required>
                            <span class="form-message-phone form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Mật khẩu </label>
                            <div class="input-group" style="position: relative;">
                                <input id="password" name="password" type="password" placeholder="Nhập mật khẩu"
                                    class="form-control" required style="padding-right: 40px;">
                                <button class="btn btn-outline-secondary toggle-password" type="button" 
                                    style="position: absolute; right: 0; top: -10px; height: 100%; width: 40px; border: none; background: none;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span class="form-message-password form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Nhập lại mật khẩu </label>
                            <div class="input-group" style="position: relative;">
                                <input id="password_confirmation" name="password_confirmation"
                                    placeholder="Nhập lại mật khẩu" type="password" class="form-control" required style="padding-right: 40px;">
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    style="position: absolute; right: 0; top: -10px; height: 100%; width: 40px; border: none; background: none;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span class="form-message-password-confi form-message"></span>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkbox-signup" required>
                                <label class="custom-control-label" for="checkbox-signup">
                                    Tôi đồng ý với <a href="#" title="Điều khoản sử dụng" target="_blank">điều khoản sử dụng</a> và 
                                    <a href="#" title="Chính sách bảo mật" target="_blank">chính sách bảo mật</a>
                                </label>
                            </div>
                            <p class="form-message-checkbox form-message"></p>
                        </div>
                        <button class="form-submit" name="submit" id="signup-button">
                            <i class=""></i>Đăng ký
                        </button>
                    </form>
                    <p class="change-login mt-3">Đã có tài khoản? <a href="javascript:;" class="login-link">Đăng nhập ngay</a></p>
                </div>
                <div class="form-content login">
                    <h3 class="form-title">Đăng nhập tài khoản</h3>
                    <p class="form-description">Đăng nhập thành viên để mua hàng và nhận những ưu đãi đặc biệt từ chúng
                        tôi</p>
                    <form action="" class="login-form">
                        <div class="form-group">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input id="phone-login" name="phone" type="text" placeholder="Nhập số điện thoại"
                                class="form-control">
                            <span class="form-message phonelog"></span>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input id="password-login" name="password" type="password" placeholder="Nhập mật khẩu"
                                class="form-control">
                            <span class="form-message-check-login form-message"></span>
                        </div>
                        <button class="form-submit" id="login-button">Đăng nhập</button>
                    </form>
                    <p class="change-login">Bạn chưa có tài khoản ? <a href="javascript:;" class="signup-link">Đăng kí
                            ngay</a><br><a href="quenpass.php" class="">Quên mật khẩu</a></p>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-cart">
        <div class="cart-container">
            <div class="cart-header">
                <h3 class="cart-header-title"><i class="fa-regular fa-basket-shopping-simple"></i> Giỏ hàng</h3>
                <button class="cart-close" onclick="closeCart()"><i class="fa-sharp fa-solid fa-xmark"></i></button>
            </div>
            <div class="cart-body">
                <div class="gio-hang-trong">
                    <i class="fa-thin fa-cart-xmark"></i>
                    <p>Không có sản phẩm nào trong giỏ hàng của bạn</p>
                </div>
                <ul class="cart-list">
                </ul>
            </div>
            <div class="cart-footer">
                <div class="cart-total-price">
                    <p class="text-tt">Tổng tiền:</p>
                    <p class="text-price">0đ</p>
                </div>
                <div class="cart-footer-payment">
                    <button class="them-sach"><i class="fa-regular fa-plus"></i> Thêm sách</button>
                    <button class="thanh-toan disabled">Thanh toán</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal detail-order">
        <div class="modal-container mdl-cnt">
            <h3 class="modal-container-title">Thông tin đơn hàng</h3>
            <button class="form-close" onclick="closeModal()"><i class="fa-regular fa-xmark"></i></button>
            <div class="detail-order-content">
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <div class="footer-top">
                <div class="footer-top-content">
                    <div class="footer-top-img">
                        <img src="./assets/img/iconlogos.jpg" alt="">
                    </div>
                    <div class="footer-top-subbox">
                        <div class="footer-top-subs">
                            <h2 class="footer-top-subs-title">Đăng ký nhận tin</h2>
                            <p class="footer-top-subs-text">Nhận thông tin mới nhất từ chúng tôi</p>
                        </div>
                        <form class="form-ground">
                            <input type="email" class="form-ground-input" placeholder="Nhập email của bạn">
                            <button class="form-ground-btn">
                                <span>ĐĂNG KÝ</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-area">
            <div class="container">
                <div class="widget-row">
                    <div class="widget-row-col-1">
                        <h3 class="widget-title">Về chúng tôi</h3>
                        <div class="widget-row-col-content">
                            <p>Book Shop là thương hiệu được thành lập vào năm 2025 với tiêu chí đặt chất lượng sản phẩm lên hàng đầu.</p>
                        </div>
                        <div class="widget-social">
                            <div class="widget-social-item">
                                <a href="">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            </div>
                            <div class="widget-social-item">
                                <a href="">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            </div>
                            <div class="widget-social-item">
                                <a href="">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                            <div class="widget-social-item">
                                <a href="">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="widget-row-col">
                        <h3 class="widget-title">Liên kết</h3>
                        <ul class="widget-contact">
                            <li class="widget-contact-item">
                                <a href="">
                                    <i class="fa-regular fa-arrow-right"></i>
                                    <span>Về chúng tôi</span>
                                </a>
                            </li>
                            <li class="widget-contact-item">
                                <a href="">
                                    <i class="fa-regular fa-arrow-right"></i>
                                    <span>Điều khoản</span>
                                </a>
                            </li>
                            <li class="widget-contact-item">
                                <a href="">
                                    <i class="fa-regular fa-arrow-right"></i>
                                    <span>Liên hệ</span>
                                </a>
                            </li>
                            <li class="widget-contact-item">
                                <a href="">
                                    <i class="fa-regular fa-arrow-right"></i>
                                    <span>Tin tức</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="widget-row-col">
                        <h3 class="widget-title">Sách Bán Chạy</h3>
                        <ul class="widget-contact">
                            <li class="widget-contact-item">
                                <a href="">
                                    <i class="fa-regular fa-arrow-right"></i>
                                    <span>Khoa Học</span>
                                </a>
                            </li>
                            <li class="widget-contact-item">
                                <a href="">
                                    <i class="fa-regular fa-arrow-right"></i>
                                    <span>Tiểu Thuyết</span>
                                </a>
                            </li>
                            <li class="widget-contact-item">
                                <a href="">
                                    <i class="fa-regular fa-arrow-right"></i>
                                    <span>Thiếu Nhi</span>
                                </a>
                            </li>
                            <li class="widget-contact-item">
                                <a href="">
                                    <i class="fa-regular fa-arrow-right"></i>
                                    <span>Sách khác</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="widget-row-col-1">
                        <h3 class="widget-title">Liên hệ</h3>
                        <div class="contact">
                            <div class="contact-item">
                                <div class="contact-item-icon">
                                    <i class="fa-regular fa-location-dot"></i>
                                </div>
                                <div class="contact-content">
                                    <span>Hoài Đức, Hà Nội</span>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-item-icon">
                                    <i class="fa-regular fa-phone"></i>
                                </div>
                                <div class="contact-content contact-item-phone">
                                    <span>0123 456 789</span>
                                 
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-item-icon">
                                    <i class="fa-regular fa-envelope"></i>
                                </div>
                                <div class="contact-content conatct-item-email">
                                    <span>20212270@eaut.edu.vn</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <div class="copyright-wrap">
        <div class="container">
            <div class="copyright-content">
                <p>Copyright 2025 Book Shop. All Rights Reserved.</p>
            </div>
        </div>
    </div>
    <div class="back-to-top">
        <a href="#"><i class="fa-regular fa-arrow-up"></i></a>
    </div>
    <div class="checkout-page">
        <div class="checkout-header">
            <div class="checkout-return">
                <button onclick="closecheckout()"><i class="fa-regular fa-chevron-left"></i></button>
            </div>
            <h2 class="checkout-title">Thanh toán</h2>
        </div>
        <main class="checkout-section container">
            <div class="checkout-col-left">
                <div class="checkout-row">
                    <div class="checkout-col-title">
                        Thông tin đơn hàng
                    </div>
                    <div class="checkout-col-content">
                        <div class="content-group">
                            <p class="checkout-content-label">Hình thức giao nhận</p>
                            <div class="checkout-type-order">
                                <button class="type-order-btn active" id="giaotannoi">
                                    <i class="fa-duotone fa-moped"
                                        style="--fa-secondary-opacity: 1.0; --fa-primary-color: dodgerblue; --fa-secondary-color: #ffb100;"></i>
                                    Giao tận nơi
                                </button>
                                <button class="type-order-btn" id="tudenlay">
                                    <i class="fa-duotone fa-box-heart"
                                        style="--fa-secondary-opacity: 1.0; --fa-primary-color: pink; --fa-secondary-color: palevioletred;"></i>
                                    Tự đến lấy
                                </button>
                            </div>
                        </div>
                        <div class="content-group">
                            <p class="checkout-content-label">Ngày giao hàng</p>
                            <div class="date-order">
                            </div>
                        </div>
                        <div class="content-group chk-ship" id="giaotannoi-group">
                            <p class="checkout-content-label">Thời gian giao hàng</p>
                            <div class="delivery-time">
                                <input type="radio" name="giaongay" id="giaongay" class="radio">
                                <label for="giaongay">Giao ngay khi xong</label>
                            </div>
                            <div class="delivery-time">
                                <input type="radio" name="giaongay" id="deliverytime" class="radio">
                                <label for="deliverytime">Giao vào giờ</label>
                                <select class="choise-time">
                                    <option data-hours="08" value="08:00" selected="selected">08:00 - 09:00</option>

                                    <option data-hours="09" value="09:00">09:00 - 10:00</option>

                                    <option data-hours="10" value="10:00"> 10:00 - 11:00</option>

                                    <option data-hours="11" value="11:00"> 11:00 - 12:00</option>

                                    <option data-hours="12" value="12:00"> 12:00 - 13:00</option>

                                    <option data-hours="13" value="13:00"> 13:00 - 14:00</option>

                                    <option data-hours="14" value="14:00"> 14:00 - 15:00</option>

                                    <option data-hours="15" value="15:00"> 15:00 - 16:00</option>

                                    <option data-hours="16" value="16:00"> 16:00 - 17:00</option>

                                    <option data-hours="17" value="17:00"> 17:00 - 18:00</option>

                                    <option data-hours="18" value="18:00"> 18:00 - 19:00</option>

                                    <option data-hours="19" value="19:00"> 19:00 - 20:00</option>

                                    <option data-hours="20" value="20:00"> 20:00 - 21:00</option>

                                    <option data-hours="21" value="21:00"> 21:00 - 22:00</option>

                                </select>
                            </div>
                        </div>
                        <div class="content-group" id="tudenlay-group">
                            <p class="checkout-content-label">Lấy hàng tại chi nhánh</p>
                            <div class="delivery-time">
                                <input type="radio" name="chinhanh" id="chinhanh-1" class="radio">
                                <label for="chinhanh-1">Hoài Đức, Hà Nội</label>
                            </div>
                            <div class="delivery-time">
                                <input type="radio" name="chinhanh" id="chinhanh-2" class="radio">
                                <label for="chinhanh-2">Cầu Giấy, Hà Nội</label>
                            </div>
                        </div>
                        <div class="content-group">
                            <p class="checkout-content-label">Ghi chú đơn hàng</p>
                            <textarea type="text" class="note-order" placeholder="Nhập ghi chú"></textarea>
                        </div>
                    </div>
                </div>
                <div class="checkout-row">
                    <div class="checkout-col-title">
                        Thông tin người nhận
                    </div>
                    <div class="checkout-col-content">
                        <div class="content-group">
                            <form action="" class="info-nhan-hang">
                                <div class="form-group">
                                    <input id="tennguoinhan" name="tennguoinhan" type="text"
                                        placeholder="Tên người nhận" class="form-control">
                                    <span class="form-message"></span>
                                </div>
                                <div class="form-group">
                                    <input id="sdtnhan" name="sdtnhan" type="text" placeholder="Số điện thoại nhận hàng"
                                        class="form-control">
                                    <span class="form-message"></span>
                                </div>
                                <div class="form-group">
                                    <input id="diachinhan" name="diachinhan" type="text" placeholder="Địa chỉ nhận hàng"
                                        class="form-control chk-ship">
                                    <span class="form-message"></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="checkout-col-right">
                <p class="checkout-content-label">Đơn hàng</p>
                <div class="bill-total" id="list-order-checkout">
                </div>
                <div class="bill-payment">
                    <div class="total-bill-order">
                    </div>
                    <div class="policy-note">
                        Bằng việc bấm vào nút "Đặt hàng", tôi đồng ý với
                        <a href="#" target="_blank">chính sách hoạt động</a>
                        của chúng tôi.
                    </div>
                </div>
                <div class="total-checkout">
                    <div class="text">Tổng tiền</div>
                    <div class="price-bill">
                        <div class="price-final" id="checkout-cart-price-final">0</div>
                    </div>
                </div>
                <button class="complete-checkout-btn">Đặt hàng</button>
                <button class="vnpay-checkout-btn" id="btnVnpay">
                    <img src="https://sandbox.vnpayment.vn/paymentv2/images/brands/logo-en.svg" alt="VNPAY" height="24">
                    Thanh toán ngay
                </button>
            </div>
        </main>
    </div>
    <div id="toast"></div>
    <!-- Bootstrap JS và các dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script src="./js/initialization.js"></script>
    <script src="./js/main.js"></script>
    <script src="./js/checkout.js"></script>
    <script src="./js/checkorder.js"></script>
    <script src="./assets/js/slider.js"></script>
    <script src="./js/toast-message.js"></script>
    <style>
        .vnpay-checkout-btn {
            margin-top: 10px;
            width: 100%;
            height: 45px;
            border-radius: 5px;
            background-color: #0066b3;
            color: #fff;
            border: none;
            font-weight: 600;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .vnpay-checkout-btn:hover {
            background-color: #004d86;
        }
    </style>
    <script>
        createProduct();
        createAdminAccount();
        createOrders();
        createOrderDetails();
        
        // Lấy thông tin tài khoản đăng nhập
        let currentUser = localStorage.getItem("currentuser") ? JSON.parse(localStorage.getItem("currentuser")) : null;
        
        // Đồng bộ trạng thái đơn hàng với server
        if (typeof syncOrderStatusWithServer === 'function') {
            syncOrderStatusWithServer();
        }
        
        // Hiển thị đơn hàng trong tài khoản người dùng
        if (currentUser) {
            // Lấy danh sách đơn hàng từ localStorage
            let orders = localStorage.getItem("order") ? JSON.parse(localStorage.getItem("order")) : [];
            
            // Lọc danh sách đơn hàng theo tài khoản đó
            let userOrders = orders.filter(order => order.khachhang === currentUser.phone);
            
            // Hiển thị danh sách đơn hàng
            showOrder(userOrders);
            
            // Hiện thị lịch sử đơn hàng
            renderOrderProduct();
        }

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const mobileMenuPanel = document.querySelector('.mobile-menu-panel');
            const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
            
            mobileMenuPanel.classList.toggle('active');
            mobileMenuOverlay.classList.toggle('active');
            
            // Prevent scrolling on body when menu is open
            if (mobileMenuPanel.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
        
        // Mobile Search Functions
        function openSearchMb() {
            const mobileSearchOverlay = document.querySelector('.mobile-search-overlay');
            mobileSearchOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Focus on the search input
            setTimeout(() => {
                mobileSearchOverlay.querySelector('.form-search-input').focus();
            }, 300);
        }
        
        function closeSearchMb() {
            const mobileSearchOverlay = document.querySelector('.mobile-search-overlay');
            mobileSearchOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    </script>
</body>
</html>