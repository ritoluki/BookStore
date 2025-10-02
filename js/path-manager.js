/**
 * Dynamic Path Manager
 * Quản lý đường dẫn động cho việc deploy lên server
 */

class PathManager {
    constructor() {
        this.basePath = this.detectBasePath();
        this.baseUrl = this.detectBaseUrl();
    }

    /**
     * Detect base path từ current URL
     */
    detectBasePath() {
        const pathname = window.location.pathname;
        
        // Nếu đang ở localhost với subfolder
        if (pathname.includes('/Bookstore_DATN/')) {
            return '/Bookstore_DATN';
        }
        
        // Nếu đang ở production hoặc root
        return '';
    }

    /**
     * Detect base URL
     */
    detectBaseUrl() {
        return window.location.origin;
    }

    /**
     * Tạo URL cho API endpoint
     */
    getApiUrl(endpoint) {
        return `${this.baseUrl}${this.basePath}/src/controllers/${endpoint}`;
    }

    /**
     * Tạo URL cho assets
     */
    getAssetUrl(path) {
        return `${this.baseUrl}${this.basePath}/${path.replace(/^\//, '')}`;
    }

    /**
     * Tạo URL cho VNPay
     */
    getVnpayUrl() {
        return `${this.baseUrl}${this.basePath}/vnpay_php/vnpay_pay.php`;
    }

    /**
     * Redirect về trang chủ
     */
    redirectHome() {
        window.location.href = `${this.baseUrl}${this.basePath}/`;
    }

    /**
     * Redirect về trang admin
     */
    redirectAdmin() {
        window.location.href = `${this.baseUrl}${this.basePath}/admin.php`;
    }
}

// Tạo instance global
window.pathManager = new PathManager();

// Export để sử dụng trong các file khác
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PathManager;
}
