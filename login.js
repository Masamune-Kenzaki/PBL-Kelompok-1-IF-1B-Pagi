// login.js - Handle form submission dan redirect berdasarkan role

document.addEventListener('DOMContentLoaded', function() {
    // Jika di halaman login, handle form submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const role = document.getElementById('role').value;
            const username = document.querySelector('input[name="username"]').value;
            const password = document.querySelector('input[name="password"]').value;
            
            // Validasi input
            if (!validateInput(role, username, password)) {
                return;
            }
            
            // Proses login dan redirect
            processLogin(role, username, password);
        });
    }
    
    // Cek status login di halaman yang membutuhkan autentikasi
    if (window.location.pathname.includes('admin.html') || 
        window.location.pathname.includes('index.html')) {
        checkAuthentication();
    }
    
    function validateInput(role, username, password) {
        if (!role) {
            showError('Silakan pilih peran Anda!');
            return false;
        }
        
        if (!username.trim()) {
            showError('Silakan isi username!');
            return false;
        }
        
        if (!password.trim()) {
            showError('Silakan isi password!');
            return false;
        }
        
        if (password.length < 3) {
            showError('Password harus minimal 3 karakter!');
            return false;
        }
        
        return true;
    }
    
    function processLogin(role, username, password) {
        // Simpan informasi user di sessionStorage
        sessionStorage.setItem('currentUser', username);
        sessionStorage.setItem('userRole', role);
        sessionStorage.setItem('isLoggedIn', 'true');
        sessionStorage.setItem('loginTime', new Date().toISOString());
        
        // Redirect berdasarkan role
        if (role === 'admin') {
            window.location.href = 'admin.html';
        } else if (role === 'user') {
            window.location.href = 'index.html';
        }
    }
    
    function showError(message) {
        // Hapus error sebelumnya jika ada
        const existingError = document.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Buat element error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.cssText = `
            color: #ff6b6b;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ff6b6b;
        `;
        errorDiv.textContent = message;
        
        // Sisipkan error message setelah judul
        const formTitle = document.querySelector('.login-form h2');
        formTitle.parentNode.insertBefore(errorDiv, formTitle.nextSibling);
        
        // Auto remove error setelah 5 detik
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 5000);
    }
    
    function checkAuthentication() {
        const isLoggedIn = sessionStorage.getItem('isLoggedIn');
        const userRole = sessionStorage.getItem('userRole');
        
        if (isLoggedIn !== 'true') {
            // Jika belum login, redirect ke halaman login
            window.location.href = 'masuk.html';
            return;
        }
        
        // Cek role untuk halaman admin
        if (window.location.pathname.includes('admin.html') && userRole !== 'admin') {
            alert('Anda tidak memiliki akses ke halaman admin!');
            logout();
            return;
        }
    }
});

// Fungsi logout - HAPUS SEMUA DATA dan redirect ke login
function logout() {
    // Hapus semua data session
    sessionStorage.removeItem('currentUser');
    sessionStorage.removeItem('userRole');
    sessionStorage.removeItem('isLoggedIn');
    sessionStorage.removeItem('loginTime');
    
    // Redirect ke halaman login
    window.location.href = 'masuk.html';
}

// Fungsi untuk mendapatkan info user saat ini
function getCurrentUser() {
    return sessionStorage.getItem('currentUser');
}

// Fungsi untuk mendapatkan role user saat ini
function getCurrentUserRole() {
    return sessionStorage.getItem('userRole');
}

// Fungsi untuk cek apakah user sudah login
function isUserLoggedIn() {
    return sessionStorage.getItem('isLoggedIn') === 'true';
}