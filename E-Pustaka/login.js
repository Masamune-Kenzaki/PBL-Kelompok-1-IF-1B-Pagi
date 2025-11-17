// login.js - Handle form submission dan redirect ke admin.html

document.addEventListener('DOMContentLoaded', function() {
    initializeLoginSystem();
});

function initializeLoginSystem() {
    // Handle form submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleLogin();
        });
    }
}

function handleLogin() {
    const username = document.querySelector('input[name="username"]').value;
    const password = document.querySelector('input[name="password"]').value;
    
    // Validasi input
    if (!validateInput(username, password)) {
        return;
    }
    
    // Proses login dan redirect
    processLogin(username, password);
}

function validateInput(username, password) {
    if (!username.trim()) {
        showError('Silakan isi username!');
        return false;
    }
    
    if (!password.trim()) {
        showError('Silakan isi password!');
        return false;
    }
    
    return true;
}

function processLogin(username, password) {
    // Simpan informasi user di sessionStorage
    sessionStorage.setItem('currentUser', username);
    sessionStorage.setItem('isLoggedIn', 'true');
    sessionStorage.setItem('loginTime', new Date().toISOString());
    
    // Redirect ke halaman admin
    window.location.href = 'admin.html';
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
    if (formTitle) {
        formTitle.parentNode.insertBefore(errorDiv, formTitle.nextSibling);
    }
    
    // Auto remove error setelah 5 detik
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.remove();
        }
    }, 5000);
}