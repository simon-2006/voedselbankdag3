import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

const observeElements = document.querySelectorAll('.reveal');

if ('IntersectionObserver' in window && observeElements.length > 0) {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    observer.unobserve(entry.target);
                }
            });
        },
        {
            threshold: 0.15,
        },
    );

    observeElements.forEach((element) => {
        element.style.animationPlayState = 'paused';
        observer.observe(element);
    });
}
