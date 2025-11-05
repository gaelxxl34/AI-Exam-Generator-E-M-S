// Hero Carousel Functionality
document.addEventListener("DOMContentLoaded", function () {
    const slides = document.querySelectorAll(".carousel-slide");
    const dots = document.querySelectorAll(".carousel-dot");
    const prevBtn = document.querySelector(".carousel-prev");
    const nextBtn = document.querySelector(".carousel-next");
    const carousel = document.getElementById("hero-carousel");
    let currentSlide = 0;
    let touchStartX = 0;
    let touchEndX = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove("active");
            if (i === index) {
                slide.classList.add("active");
            }
        });

        dots.forEach((dot, i) => {
            const innerDot = dot.querySelector("span:first-child");
            if (i === index) {
                dot.classList.add("active");
                if (innerDot) {
                    innerDot.classList.remove(
                        "bg-gray-400",
                        "hover:bg-gray-600"
                    );
                    innerDot.classList.add("bg-gray-900");
                }
            } else {
                dot.classList.remove("active");
                if (innerDot) {
                    innerDot.classList.remove("bg-gray-900");
                    innerDot.classList.add("bg-gray-400", "hover:bg-gray-600");
                }
            }
        });

        currentSlide = index;
    }

    function nextSlide() {
        const next = (currentSlide + 1) % slides.length;
        showSlide(next);
    }

    function prevSlide() {
        const prev = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(prev);
    }

    // Button controls
    if (nextBtn && prevBtn) {
        nextBtn.addEventListener("click", nextSlide);
        prevBtn.addEventListener("click", prevSlide);
    }

    // Dot controls
    dots.forEach((dot, index) => {
        dot.addEventListener("click", () => showSlide(index));
    });

    // Touch swipe support
    if (carousel) {
        carousel.addEventListener("touchstart", (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });

        carousel.addEventListener("touchend", (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
    }

    function handleSwipe() {
        if (touchEndX < touchStartX - 50) nextSlide();
        if (touchEndX > touchStartX + 50) prevSlide();
    }

    // Keyboard navigation
    document.addEventListener("keydown", (e) => {
        if (e.key === "ArrowLeft") prevSlide();
        if (e.key === "ArrowRight") nextSlide();
    });

    // Initialize
    showSlide(0);
});
