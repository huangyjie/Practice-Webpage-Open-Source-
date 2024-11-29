document.addEventListener('DOMContentLoaded', function() {
    const fadeElements = document.querySelectorAll('.fade-in');
    fadeElements.forEach(element => {
        element.style.opacity = '1';
    });

    window.addEventListener('scroll', checkSlide);
});

function checkSlide() {
    const sliders = document.querySelectorAll('.slide-in');
    sliders.forEach(slider => {
        const slideInAt = (window.scrollY + window.innerHeight) - slider.offsetHeight / 2;
        const elementBottom = slider.offsetTop + slider.offsetHeight;
        const isHalfShown = slideInAt > slider.offsetTop;
        const isNotScrolledPast = window.scrollY < elementBottom;

        if (isHalfShown && isNotScrolledPast) {
            slider.classList.add('active');
        } else {
            slider.classList.remove('active');
        }
    });
} 