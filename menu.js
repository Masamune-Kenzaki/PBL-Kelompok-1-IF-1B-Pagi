const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');
const body = document.body;

// Fungsi untuk toggle menu
hamburger.addEventListener('click', (e) => {
    e.stopPropagation();
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('active');
    
    // Prevent body scroll ketika menu terbuka
    if (navMenu.classList.contains('active')) {
        body.style.overflow = 'hidden';
    } else {
        body.style.overflow = '';
    }
});

// Tutup menu ketika link diklik
document.querySelectorAll('.nav-link').forEach(n => n.addEventListener('click', () => {
    hamburger.classList.remove('active');
    navMenu.classList.remove('active');
    body.style.overflow = ''; // Kembalikan scroll
}));

// Tutup menu ketika klik di luar menu
document.addEventListener('click', (e) => {
    const isClickInsideMenu = e.target.closest('.nav-menu');
    const isClickOnHamburger = e.target.closest('.hamburger');
    
    if (!isClickInsideMenu && !isClickOnHamburger && navMenu.classList.contains('active')) {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
        body.style.overflow = ''; // Kembalikan scroll
    }
});

// Tutup menu dengan ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && navMenu.classList.contains('active')) {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
        body.style.overflow = ''; // Kembalikan scroll
    }
});