<?php
/**
 * URL Helper - Tạo URL đẹp cho website
 */

class URLHelper {
    
    /**
     * Tạo URL cho trang chủ
     */
    public static function home() {
        return self::baseUrl();
    }
    
    /**
     * Tạo URL cho trang sản phẩm
     */
    public static function products() {
        return self::baseUrl() . 'san-pham';
    }
    
    /**
     * Tạo URL cho chi tiết sản phẩm
     */
    public static function product($slug) {
        return self::baseUrl() . 'san-pham/' . $slug;
    }
    
    /**
     * Tạo URL cho danh mục
     */
    public static function category($slug) {
        return self::baseUrl() . 'danh-muc/' . $slug;
    }
    
    /**
     * Tạo URL cho giỏ hàng
     */
    public static function cart() {
        return self::baseUrl() . 'gio-hang';
    }
    
    /**
     * Tạo URL cho trang thanh toán
     */
    public static function checkout() {
        return self::baseUrl() . 'thanh-toan';
    }
    
    /**
     * Tạo URL cho tra cứu đơn hàng
     */
    public static function checkOrder() {
        return self::baseUrl() . 'tra-cuu-don-hang';
    }
    
    /**
     * Tạo URL cho trang đăng nhập
     */
    public static function login() {
        return self::baseUrl() . 'dang-nhap';
    }
    
    /**
     * Tạo URL cho trang đăng ký
     */
    public static function signup() {
        return self::baseUrl() . 'dang-ky';
    }
    
    /**
     * Tạo URL cho trang quên mật khẩu
     */
    public static function forgotPassword() {
        return self::baseUrl() . 'quen-mat-khau';
    }
    
    /**
     * Tạo URL cho trang tài khoản
     */
    public static function account() {
        return self::baseUrl() . 'tai-khoan';
    }
    
    /**
     * Tạo URL cho trang liên hệ
     */
    public static function contact() {
        return self::baseUrl() . 'lien-he';
    }
    
    /**
     * Tạo URL cho trang giới thiệu
     */
    public static function about() {
        return self::baseUrl() . 'gioi-thieu';
    }
    
    /**
     * Tạo URL cho trang tin tức
     */
    public static function news() {
        return self::baseUrl() . 'tin-tuc';
    }
    
    /**
     * Tạo URL cho chi tiết tin tức
     */
    public static function newsDetail($slug) {
        return self::baseUrl() . 'tin-tuc/' . $slug;
    }
    
    /**
     * Tạo URL cho trang tìm kiếm
     */
    public static function search($query = '') {
        $url = self::baseUrl() . 'tim-kiem';
        if ($query) {
            $url .= '?q=' . urlencode($query);
        }
        return $url;
    }
    
    /**
     * Tạo URL cho trang admin
     */
    public static function admin() {
        return self::baseUrl() . 'admin';
    }
    
    /**
     * Tạo URL cho assets (CSS, JS, images)
     */
    public static function asset($path) {
        return self::baseUrl() . 'assets/' . ltrim($path, '/');
    }
    
    /**
     * Tạo URL cho images
     */
    public static function image($path) {
        return self::asset('img/' . ltrim($path, '/'));
    }
    
    /**
     * Tạo URL cho CSS
     */
    public static function css($path) {
        return self::asset('css/' . ltrim($path, '/'));
    }
    
    /**
     * Tạo URL cho JavaScript
     */
    public static function js($path) {
        return self::asset('js/' . ltrim($path, '/'));
    }
    
    /**
     * Lấy base URL của website
     */
    public static function baseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['SCRIPT_NAME']);
        
        // Loại bỏ index.php nếu có
        $path = str_replace('/index.php', '', $path);
        
        // Đảm bảo path kết thúc bằng /
        if (substr($path, -1) !== '/') {
            $path .= '/';
        }
        
        return $protocol . $host . $path;
    }
    
    /**
     * Tạo URL tương đối cho navigation
     */
    public static function nav($path) {
        return $path;
    }
    
    /**
     * Tạo URL cho API endpoints
     */
    public static function api($endpoint) {
        return self::baseUrl() . 'api/' . ltrim($endpoint, '/');
    }
    
    /**
     * Tạo URL cho controllers
     */
    public static function controller($controller, $action = '', $params = []) {
        $url = self::baseUrl() . 'src/controllers/' . $controller;
        if ($action) {
            $url .= '/' . $action;
        }
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }
    
    /**
     * Tạo URL cho current page với parameters
     */
    public static function current($params = []) {
        $currentUrl = $_SERVER['REQUEST_URI'];
        if (!empty($params)) {
            $separator = strpos($currentUrl, '?') !== false ? '&' : '?';
            $currentUrl .= $separator . http_build_query($params);
        }
        return $currentUrl;
    }
    
    /**
     * Tạo URL cho previous page
     */
    public static function previous() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : self::home();
    }
    
    /**
     * Tạo URL cho next page (pagination)
     */
    public static function nextPage($currentPage, $totalPages) {
        if ($currentPage < $totalPages) {
            return self::current(['page' => $currentPage + 1]);
        }
        return '#';
    }
    
    /**
     * Tạo URL cho previous page (pagination)
     */
    public static function prevPage($currentPage) {
        if ($currentPage > 1) {
            return self::current(['page' => $currentPage - 1]);
        }
        return '#';
    }
    
    /**
     * Tạo URL cho trang cụ thể (pagination)
     */
    public static function page($pageNumber) {
        return self::current(['page' => $pageNumber]);
    }
    
    /**
     * Tạo URL cho sản phẩm với slug
     */
    public static function productBySlug($title, $id) {
        // Tạo slug từ title
        $slug = self::createSlug($title);
        return self::product($slug);
    }
    
    /**
     * Tạo slug từ title
     */
    public static function createSlug($title) {
        // Chuyển về chữ thường
        $slug = strtolower($title);
        
        // Thay thế các ký tự đặc biệt
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        
        // Thay thế khoảng trắng bằng dấu gạch ngang
        $slug = preg_replace('/[\s]+/', '-', $slug);
        
        // Loại bỏ dấu gạch ngang thừa
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Tạo URL cho danh mục với slug
     */
    public static function categoryBySlug($name) {
        $slug = self::createSlug($name);
        return self::category($slug);
    }
    
    /**
     * Tạo URL cho tin tức với slug
     */
    public static function newsBySlug($title) {
        $slug = self::createSlug($title);
        return self::newsDetail($slug);
    }
}

// Tạo function helper để sử dụng dễ dàng hơn
if (!function_exists('url')) {
    function url($path = '') {
        if (empty($path)) {
            return URLHelper::home();
        }
        return URLHelper::baseUrl() . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset($path) {
        return URLHelper::asset($path);
    }
}

if (!function_exists('image')) {
    function image($path) {
        return URLHelper::image($path);
    }
}

if (!function_exists('css')) {
    function css($path) {
        return URLHelper::css($path);
    }
}

if (!function_exists('js')) {
    function js($path) {
        return URLHelper::js($path);
    }
}
