document.addEventListener('DOMContentLoaded', function() {
    // Banner Slider
    const bannerWrapper = document.querySelector('.banner-wrapper');
    const bannerSlides = document.querySelectorAll('.banner-slide');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const dots = document.querySelectorAll('.dot');
    let currentSlide = 0;
    let slideInterval;

    // Banner otomatik kaydırma fonksiyonu
    function startSlideInterval() {
        slideInterval = setInterval(() => {
            moveToNextSlide();
        }, 5000); // 5 saniyede bir otomatik kayma
    }

    // Banner kaydırma fonksiyonu
    function moveToSlide(slideIndex) {
        if (slideIndex < 0) {
            slideIndex = bannerSlides.length - 1;
        } else if (slideIndex >= bannerSlides.length) {
            slideIndex = 0;
        }

        bannerWrapper.style.transform = `translateX(-${slideIndex * 33.333}%)`;
        currentSlide = slideIndex;

        // Aktif dot güncelleme
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });

        // Otomatik kaydırmayı sıfırla
        clearInterval(slideInterval);
        startSlideInterval();
    }

    function moveToPrevSlide() {
        moveToSlide(currentSlide - 1);
    }

    function moveToNextSlide() {
        moveToSlide(currentSlide + 1);
    }

    // Banner kontrol butonları
    prevBtn.addEventListener('click', moveToPrevSlide);
    nextBtn.addEventListener('click', moveToNextSlide);

    // Banner dot tıklama
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            moveToSlide(index);
        });
    });

    // Otomatik kaydırmayı başlat
    startSlideInterval();

    // Dokunmatik ekran için kaydırma desteği
    let touchStartX = 0;
    let touchEndX = 0;

    const bannerContainer = document.querySelector('.banner-container');

    bannerContainer.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });

    bannerContainer.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        const swipeThreshold = 50; // Kaydırma eşiği
        if (touchEndX < touchStartX - swipeThreshold) {
            // Sola kaydırma
            moveToNextSlide();
        } else if (touchEndX > touchStartX + swipeThreshold) {
            // Sağa kaydırma
            moveToPrevSlide();
        }
    }

    // Mobil Menü
    const mobileMenuBtn = document.querySelector('.mobile-menu');
    const menu = document.querySelector('.menu');

    mobileMenuBtn.addEventListener('click', function() {
        menu.classList.toggle('active');
        this.classList.toggle('active');
    });

    // Mobil menü için CSS ekleme
    const style = document.createElement('style');
    style.textContent = `
        @media (max-width: 768px) {
            .menu.active {
                display: block;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: var(--white-color);
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
                padding: 20px;
                z-index: 1000;
            }
            
            .menu.active ul {
                flex-direction: column;
            }
            
            .menu.active ul li {
                margin: 10px 0;
                margin-left: 0;
            }
            
            .mobile-menu.active i:before {
                content: "\\f00d";
            }
        }
    `;
    document.head.appendChild(style);

    // Markalar slider kontrolü
    const brandsSlider = document.querySelector('.brands-slider');
    const brandPrevBtn = document.querySelector('.brand-prev-btn');
    const brandNextBtn = document.querySelector('.brand-next-btn');
    
    if (brandsSlider && brandPrevBtn && brandNextBtn) {
        // Kaydırma miktarı
        const scrollAmount = 200;
        let brandSliderInterval;

        function startBrandSlider() {
            brandSliderInterval = setInterval(() => {
                const maxScroll = brandsSlider.scrollWidth - brandsSlider.clientWidth;
                const currentScroll = brandsSlider.scrollLeft;

                if (currentScroll >= maxScroll) {
                    brandsSlider.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    brandsSlider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                }
            }, 3000); // 3 saniyede bir otomatik kayma
        }

        function stopBrandSlider() {
            clearInterval(brandSliderInterval);
        }
        
        // Sağa kaydırma
        brandNextBtn.addEventListener('click', () => {
            stopBrandSlider();
            brandsSlider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            startBrandSlider();
        });
        
        // Sola kaydırma
        brandPrevBtn.addEventListener('click', () => {
            stopBrandSlider();
            brandsSlider.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            startBrandSlider();
        });
        
        // Dokunmatik ekran için kaydırma desteği
        let touchStartX = 0;
        let touchEndX = 0;
        
        brandsSlider.addEventListener('touchstart', (e) => {
            stopBrandSlider();
            touchStartX = e.changedTouches[0].screenX;
        });
        
        brandsSlider.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleBrandSwipe();
            startBrandSlider();
        });
        
        function handleBrandSwipe() {
            const swipeThreshold = 50; // Kaydırma eşiği
            if (touchEndX < touchStartX - swipeThreshold) {
                // Sola kaydırma
                brandsSlider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            } else if (touchEndX > touchStartX + swipeThreshold) {
                // Sağa kaydırma
                brandsSlider.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            }
        }

        // Mouse hover durumunda otomatik kaydırmayı durdur
        brandsSlider.addEventListener('mouseenter', stopBrandSlider);
        brandsSlider.addEventListener('mouseleave', startBrandSlider);

        // Otomatik kaydırmayı başlat
        startBrandSlider();
    }
});