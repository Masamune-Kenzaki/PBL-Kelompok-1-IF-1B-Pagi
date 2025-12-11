// menu.js - Perbaikan Active State
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");
    
    // If elements exist
    if (hamburger && navMenu) {
        // Toggle menu on hamburger click
        hamburger.addEventListener("click", (e) => {
            e.stopPropagation();
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
        });
        
        // Close menu when clicking on links
        document.querySelectorAll(".nav-link").forEach(link => {
            link.addEventListener("click", () => {
                hamburger.classList.remove("active");
                navMenu.classList.remove("active");
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (event) => {
            const isClickInsideMenu = navMenu.contains(event.target);
            const isClickOnHamburger = hamburger.contains(event.target);
            
            if (!isClickInsideMenu && !isClickOnHamburger && navMenu.classList.contains('active')) {
                hamburger.classList.remove("active");
                navMenu.classList.remove("active");
            }
        });
        
        // Close menu on Escape key
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && navMenu.classList.contains('active')) {
                hamburger.classList.remove("active");
                navMenu.classList.remove("active");
            }
        });
        
        // Highlight active menu item - FIXED
        highlightActiveMenu();
    }
    
    // Toast notification system
    window.showToast = function(message, type = 'info') {
        const toast = document.createElement('div');
        toast.id = 'custom-toast';
        toast.className = type;
        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-${getToastIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => toast.style.transform = 'translateX(0)', 10);
        setTimeout(() => {
            toast.style.transform = 'translateX(120%)';
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    };
    
    function getToastIcon(type) {
        switch(type) {
            case 'success': return 'check-circle';
            case 'error': return 'exclamation-circle';
            case 'warning': return 'exclamation-triangle';
            default: return 'info-circle';
        }
    }
    
    function highlightActiveMenu() {
        const currentPage = window.location.pathname.split('/').pop();
        
        // Mapping halaman ke menu item
        const pageToMenu = {
            'admin.php': 'Dashboard',
            'data_kunjungan.php': 'Data Kunjungan',
            'tambah_data_kunjungan.html': 'Tambah Data',
            'tambah_data_kunjungan.php': 'Tambah Data',
            'logout.php': 'Keluar'
        };
        
        const activeMenuName = pageToMenu[currentPage] || 'Dashboard';
        
        // Remove active class from all
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        
        // Add active class to correct menu
        document.querySelectorAll('.nav-link').forEach(link => {
            const linkText = link.textContent.trim();
            if (linkText.includes(activeMenuName)) {
                link.classList.add('active');
            }
        });
    }
});