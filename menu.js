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

// Chart.js Implementation
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('visitorChart');
    
    if (ctx) {
        const visitorChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Pengunjung Per Bulan',
                    data: [65, 59, 80, 81, 56, 55, 40, 50, 70, 85, 90, 100],
                    backgroundColor: 'rgba(25, 118, 210, 0.2)',
                    borderColor: 'rgba(25, 118, 210, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Statistik Pengunjung Tahunan'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Pengunjung'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                }
            }
        });
    }
});