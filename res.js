// Toggle mobile menu
document.getElementById("menu-toggle").addEventListener("click", function() {
    document.querySelector("nav ul").classList.toggle("show");
});

// Scroll-to-top button behavior
window.onscroll = function() {
    showScrollButton();
};

function showScrollButton() {
    const btn = document.getElementById("scrollBtn");
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        btn.style.display = "block";
    } else {
        btn.style.display = "none";
    }
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: "smooth" });
}

// Smooth scrolling for internal nav links
document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener("click", function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute("href"));
        if (target) {
            target.scrollIntoView({ behavior: "smooth" });
        }
    });
});