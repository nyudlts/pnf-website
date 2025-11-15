var toggleOpen = document.getElementById('toggleOpen');
var toggleClose = document.getElementById('toggleClose');
var collapseMenu = document.getElementById('collapseMenu');

function handleClick() {
  if (collapseMenu.style.display === 'block') {
    collapseMenu.style.display = 'none';
  } else {
    collapseMenu.style.display = 'block';
  }
}

toggleOpen.addEventListener('click', handleClick);
toggleClose.addEventListener('click', handleClick);

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
    }
});
