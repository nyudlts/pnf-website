/**
 * Scroll to Top Button functionality
 * The mobile menu is now handled by Alpine.js in header.html.twig
 * with proper focus trapping (x-trap) for WCAG 2.1.2 compliance
 */

document.addEventListener('DOMContentLoaded', function() {
    var scrollToTopButton = document.getElementById('scroll-to-top');

    if (scrollToTopButton) {
        var toggleScrollToTopVisibility = function () {
            if (window.scrollY > 200) {
                scrollToTopButton.classList.add('visible');
            } else {
                scrollToTopButton.classList.remove('visible');
            }
        };

        toggleScrollToTopVisibility();

        window.addEventListener('scroll', toggleScrollToTopVisibility, { passive: true });

        scrollToTopButton.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Keyboard support for scroll-to-top button
        scrollToTopButton.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });
    }
});
