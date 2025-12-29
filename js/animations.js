/**
 * Scroll Animations
 * Adds fade-in and slide-up animations as elements come into view
 */

document.addEventListener('DOMContentLoaded', function() {
    // Detect if user is on mobile device
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth < 768;

    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: isMobile ? 0.05 : 0.1, // Trigger earlier on mobile
        rootMargin: isMobile ? '0px' : '0px 0px -50px 0px' // Remove bottom margin on mobile
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-visible');
                // Optionally unobserve after animation
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements with animation classes
    const animatedElements = document.querySelectorAll('.service-card, .feature, .industry-card, .section-header, .manufacturer-image-wrapper, .manufacturer-text-wrapper');

    animatedElements.forEach((el, index) => {
        if (isMobile) {
            // On mobile: instant appearance, no animation
            el.style.opacity = '1';
            el.style.transform = 'none';
        } else {
            // On desktop: smooth animation
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = `opacity 0.3s ease, transform 0.3s ease`;
        }

        observer.observe(el);
    });

    // Add visible class styles dynamically
    const style = document.createElement('style');
    style.textContent = `
        .fade-in-visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
    `;
    document.head.appendChild(style);

    // Smooth scroll for anchor links (instant on mobile, smooth on desktop)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: isMobile ? 'auto' : 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Add parallax effect to hero background elements (desktop only)
    if (!isMobile) {
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    const scrolled = window.pageYOffset;
                    const parallaxElements = document.querySelectorAll('.hero::before, .hero::after');

                    parallaxElements.forEach(function(el) {
                        const speed = 0.5;
                        el.style.transform = `translateY(${scrolled * speed}px)`;
                    });

                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    // Add hover sound effect (optional, subtle)
    const cards = document.querySelectorAll('.service-card, .industry-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        });
    });

    // Animated counter for stats (if they exist) - optimized with requestAnimationFrame
    const stats = document.querySelectorAll('.stat-number');
    if (stats.length > 0) {
        const animateCounter = (element) => {
            const target = parseInt(element.getAttribute('data-target')) || parseInt(element.textContent);
            const duration = 1000;
            const hasPlus = element.textContent.includes('+');
            let startTime = null;

            const updateCounter = (currentTime) => {
                if (!startTime) startTime = currentTime;
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                const current = Math.floor(progress * target);
                element.textContent = current + (hasPlus ? '+' : '');

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target + (hasPlus ? '+' : '');
                }
            };

            requestAnimationFrame(updateCounter);
        };

        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        stats.forEach(stat => statsObserver.observe(stat));
    }
});
