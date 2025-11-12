let stickyHeaderExists = false;
let sections = [];
let stickyNavLinks = [];
let scrollTimeout;
let scrollToTopButton;

// Throttle scroll events for better performance
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

const throttledScrollHandler = throttle(function() {
    if (stickyHeaderExists) {
        scrollFunction();
        updateScrollSpy();
    }
}, 16); // ~60fps

window.addEventListener('scroll', throttledScrollHandler);
window.addEventListener('resize', throttle(function() {
    if (stickyHeaderExists) {
        initScrollSpy();
    }
}, 250));

function scrollFunction() {
    const stickyHeader = document.getElementById("header-sticky");
    const scrollPosition = document.body.scrollTop || document.documentElement.scrollTop;
    
    if (stickyHeader) {
        if (scrollPosition > 300) {
            stickyHeader.style.transform = "translateY(0)";
            // Show scroll to top button when sticky header is visible
            if (scrollToTopButton) {
                scrollToTopButton.classList.add('visible');
            }
        } else {
            stickyHeader.style.transform = "translateY(-100%)";
            // Hide scroll to top button when sticky header is hidden
            if (scrollToTopButton) {
                scrollToTopButton.classList.remove('visible');
            }
        }
    }
}

function updateScrollSpy() {
    const scrollPosition = document.body.scrollTop || document.documentElement.scrollTop;
    const windowHeight = window.innerHeight;
    const viewportMiddle = scrollPosition + (windowHeight / 2);

    let currentSection = null;

    // Find which section the viewport middle is currently in
    for (let i = 0; i < sections.length; i++) {
        const section = sections[i];
        const sectionTop = section.element.offsetTop;
        
        // For the last section, use the document height as the bottom
        let sectionBottom;
        if (i === sections.length - 1) {
            sectionBottom = document.documentElement.scrollHeight;
        } else {
            sectionBottom = sections[i + 1].element.offsetTop;
        }

        if (viewportMiddle >= sectionTop && viewportMiddle < sectionBottom) {
            currentSection = section;
            break;
        }
    }

    // If no section is in the middle of the viewport, check for edge cases
    if (!currentSection && sections.length > 0) {
        // If scrolled to the very top, highlight the first section
        if (scrollPosition < sections[0].element.offsetTop) {
            currentSection = sections[0];
        }
        // If scrolled to the bottom, highlight the last section
        else if (scrollPosition + windowHeight >= document.documentElement.scrollHeight - 1) {
            currentSection = sections[sections.length - 1];
        }
    }

    updateNavHighlight(currentSection);
}

function updateNavHighlight(activeSection) {
    stickyNavLinks.forEach(link => {
        link.classList.remove('active-section');
        link.classList.remove('text-primary-600', 'dark:text-primary-500');
        link.classList.add('text-gray-700', 'dark:text-gray-400');
    });
    
    if (activeSection) {
        const targetId = activeSection.id;
        const activeLink = document.querySelector(`#header-sticky a[href="#${targetId}"]`);
        
        if (activeLink) {
            activeLink.classList.add('active-section');
            activeLink.classList.add('text-primary-600', 'dark:text-primary-500');
            activeLink.classList.remove('text-gray-700', 'dark:text-gray-400');
        }
    }
}

function initScrollSpy() {
    const stickyLinks = document.querySelectorAll('#header-sticky a[href^="#"]');
    stickyNavLinks = Array.from(stickyLinks);
    
    sections = [];
    
    // Find all scroll spy anchor elements
    const scrollSpyAnchors = document.querySelectorAll('.scroll-spy-anchor');
    
    scrollSpyAnchors.forEach(anchor => {
        const targetId = anchor.id;
        
        // Only include anchors that have corresponding links in the sticky menu
        const correspondingLink = document.querySelector(`#header-sticky a[href="#${targetId}"]`);
        
        if (correspondingLink) {
            sections.push({
                id: targetId,
                element: anchor
            });
        }
    });
    
    // Sort by the offsetTop of the anchor elements
    sections.sort((a, b) => a.element.offsetTop - b.element.offsetTop);
    
    updateScrollSpy();
}

document.addEventListener('DOMContentLoaded', function() {
    const stickyHeader = document.getElementById("header-sticky");
    
    if (stickyHeader) {
        stickyHeaderExists = true;
        initScrollSpy();
        
        const stickyLinks = document.querySelectorAll('#header-sticky a[href^="#"]');
        stickyLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    const offsetTop = targetElement.offsetTop - 50;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }
    
    // Initialize scroll to top button
    scrollToTopButton = document.getElementById('scroll-to-top');
    if (scrollToTopButton) {
        scrollToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
