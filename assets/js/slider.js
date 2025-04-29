// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the banner slider
    initBannerSlider();
});

/**
 * Initialize the banner slider with navigation and auto-slide
 */
function initBannerSlider() {
    const container = document.querySelector('.slide-banner__container');
    const slides = document.querySelectorAll('.slide-banner__item');
    const dots = document.querySelectorAll('.slide-banner__dot');
    const prevBtn = document.querySelector('.slide-banner__nav-button--prev');
    const nextBtn = document.querySelector('.slide-banner__nav-button--next');
    
    if (!container || !slides.length) {
        console.warn('Banner slider elements not found');
        return;
    }
    
    // Preload slide background images
    slides.forEach((slide, index) => {
        if (slide.style.backgroundImage) {
            const bgUrl = slide.style.backgroundImage.match(/url\(['"]?([^'"]+)['"]?\)/);
            if (bgUrl && bgUrl[1]) {
                const img = new Image();
                img.src = bgUrl[1];
            }
        }
    });
    
    let currentSlide = 0;
    let autoSlideInterval;
    let isTransitioning = false;

    function updateSlide(index) {
        if (!container || isTransitioning) return;
        
        isTransitioning = true;
        
        // Remove hover effects during transition
        slides.forEach(slide => {
            slide.classList.remove('is-transitioning');
            slide.classList.add('is-transitioning');
        });
        
        container.style.transform = `translateX(-${100 * index}%)`;
        
        if (dots && dots.length) {
            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.classList.add('slide-banner__dot--active');
                } else {
                    dot.classList.remove('slide-banner__dot--active');
                }
            });
        }
        
        currentSlide = index;
        
        setTimeout(() => {
            isTransitioning = false;
            // Re-enable hover effects after transition
            slides.forEach(slide => {
                slide.classList.remove('is-transitioning');
            });
        }, 500);
    }
    
    // Set initial state
    updateSlide(0);

    // Previous button click event
    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (isTransitioning) return;
            
            const index = (currentSlide - 1 + slides.length) % slides.length;
            updateSlide(index);
            resetAutoSlide();
        });
    }

    // Next button click event
    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (isTransitioning) return;
            
            const index = (currentSlide + 1) % slides.length;
            updateSlide(index);
            resetAutoSlide();
        });
    }

    // Dot navigation click events
    if (dots && dots.length) {
        dots.forEach((dot, i) => {
            dot.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (isTransitioning) return;
                
                updateSlide(i);
                resetAutoSlide();
            });
        });
    }

    function resetAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
        }
        startAutoSlide();
    }

    function startAutoSlide() {
        autoSlideInterval = setInterval(function() {
            if (!isTransitioning && document.visibilityState === 'visible') {
                const index = (currentSlide + 1) % slides.length;
                updateSlide(index);
            }
        }, 5000);
    }

    // Initialize auto-slide
    startAutoSlide();
    
    // Add event listener for transition end
    container.addEventListener('transitionend', function() {
        isTransitioning = false;
    });
    
    // Pause auto-slide when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            clearInterval(autoSlideInterval);
        } else {
            startAutoSlide();
        }
    });
    
    // Touch support
    let touchStartX = 0;
    let touchEndX = 0;
    
    container.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    
    container.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });
    
    function handleSwipe() {
        if (isTransitioning) return;
        
        const swipeThreshold = 50;
        if (touchEndX < touchStartX - swipeThreshold) {
            // Swipe left - next slide
            const index = (currentSlide + 1) % slides.length;
            updateSlide(index);
            resetAutoSlide();
        }
        
        if (touchEndX > touchStartX + swipeThreshold) {
            // Swipe right - previous slide
            const index = (currentSlide - 1 + slides.length) % slides.length;
            updateSlide(index);
            resetAutoSlide();
        }
    }
} 