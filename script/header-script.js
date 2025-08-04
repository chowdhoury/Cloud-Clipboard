function toggleMenu() {
    const mobileMenu = document.querySelector(".mobile-menu");
    mobileMenu.style.display = 'flex';
    const sizeControl = document.querySelector("header iframe");
    sizeControl.style.height = '100%';
}
function closeMenu() {
    const mobileMenu = document.querySelector(".mobile-menu");
    mobileMenu.style.display = 'none';
}
