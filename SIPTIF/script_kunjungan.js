document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Script kunjungan berjalan');
    
    const form = document.getElementById('kunjunganForm');
    const popupOverlay = document.getElementById('popupOverlay');
    const popupBtn = document.getElementById('popupBtn');
    
    // Debug elements
    console.log('Form element:', form);
    console.log('Popup overlay:', popupOverlay);
    console.log('Popup button:', popupBtn);
    
    // Validasi elemen penting
    if (!form) {
        console.error('❌ Form dengan ID kunjunganForm tidak ditemukan!');
        alert('Error: Form tidak ditemukan. Periksa konsol untuk detail.');
        return;
    }
    
    if (!popupOverlay) {
        console.error('❌ Popup overlay dengan ID popupOverlay tidak ditemukan!');
        alert('Error: Popup overlay tidak ditemukan.');
        return;
    }

    if (!popupBtn) {
        console.error('❌ Popup button dengan ID popupBtn tidak ditemukan!');
    }

    console.log('✅ Semua elemen utama ditemukan');

    // 1. Tangani submit form
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('📝 Form submitted');
        
        if (validateForm()) {
            console.log('✅ Form valid, memproses data...');
            submitFormData();
        } else {
            console.log('❌ Form tidak valid');
        }
    });
    
    // 2. Handler untuk tombol popup
    if (popupBtn) {
        popupBtn.addEventListener('click', function() {
            console.log('🔘 Popup button clicked');
            closePopup();
        });
    }
    
    // 3. Handler untuk klik di luar popup
    popupOverlay.addEventListener('click', function(e) {
        if (e.target === popupOverlay) {
            console.log('🎯 Klik di luar popup');
            closePopup();
        }
    });
    
    // 4. Handler untuk ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && popupOverlay.classList.contains('active')) {
            console.log('⌨️ ESC key pressed');
            closePopup();
        }
    });
    
    // Fungsi untuk menutup popup
    function closePopup() {
        console.log('🗂️ Menutup popup');
        popupOverlay.classList.remove('active');
        // Reset form setelah popup ditutup
        setTimeout(() => {
            form.reset();
            setDefaultDate(); // Set ulang tanggal default
            console.log('🔄 Form direset');
        }, 300);
    }
    
    // Fungsi validasi form
    function validateForm() {
        const nama = document.getElementById('nama').value.trim();
        const email = document.getElementById('email').value.trim();
        const tanggal = document.getElementById('tanggal').value;
        const waktu = document.getElementById('waktu').value;
        const keperluan = document.getElementById('keperluan').value;
        
        console.log('🔍 Validating form data:', { 
            nama: nama.substring(0, 10) + '...', 
            email, 
            tanggal, 
            waktu, 
            keperluan 
        });
        
        // Validasi field kosong
        if (nama === '' || email === '' || tanggal === '' || waktu === '' || keperluan === '') {
            showAlert('Harap lengkapi semua field yang wajib diisi!', 'error');
            return false;
        }
        
        // Validasi email
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            showAlert('Format email tidak valid! Contoh: email@domain.com', 'error');
            return false;
        }
        
        // Validasi tanggal tidak boleh kurang dari hari ini
        const today = new Date().toISOString().split('T')[0];
        if (tanggal < today) {
            showAlert('Tanggal kunjungan tidak boleh kurang dari hari ini!', 'error');
            return false;
        }
        
        console.log('✅ Semua validasi passed');
        return true;
    }
    
    // Fungsi untuk menampilkan alert
    function showAlert(message, type = 'info') {
        alert(message); // Bisa diganti dengan custom alert yang lebih baik
    }
    
    // Fungsi untuk submit data ke PHP
    function submitFormData() {
        const submitBtn = form.querySelector('.btn-submit');
        const originalText = submitBtn.textContent;
        
        // Update button state
        submitBtn.textContent = 'Menyimpan...';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';
        submitBtn.style.cursor = 'not-allowed';
        
        const formData = new FormData(form);
        
        // Debug: log data yang akan dikirim
        console.log('📤 Data yang dikirim ke server:');
        for (let [key, value] of formData.entries()) {
            console.log(`   ${key}: ${value}`);
        }
        
        // Kirim data ke server
        fetch('save_kunjungan.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('📥 Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📨 Response data dari server:', data);
            
            if (data.success) {
                console.log('🎉 Data berhasil disimpan, menampilkan popup');
                
                // Tampilkan popup sukses
                showSuccessPopup();
                
            } else {
                console.error('❌ Server mengembalikan error:', data.message);
                showAlert('Gagal menyimpan data: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('💥 Fetch error:', error);
            showAlert('Terjadi kesalahan jaringan. Periksa koneksi internet Anda.', 'error');
        })
        .finally(() => {
            // Reset button state
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
            console.log('🔄 Button state direset');
        });
    }
    
    // Fungsi untuk menampilkan popup sukses
    function showSuccessPopup() {
        console.log('🪟 Menampilkan popup sukses');
        
        // Animasi masuk popup
        popupOverlay.classList.add('active');
        
        // Auto close setelah 5 detik (opsional)
        setTimeout(() => {
            if (popupOverlay.classList.contains('active')) {
                console.log('⏰ Auto close popup');
                closePopup();
            }
        }, 5000);
    }
    
    // Fungsi untuk set tanggal default
    function setDefaultDate() {
        const today = new Date().toISOString().split('T')[0];
        const tanggalInput = document.getElementById('tanggal');
        
        if (tanggalInput) {
            tanggalInput.setAttribute('min', today);
            tanggalInput.value = today;
            console.log('📅 Tanggal di-set ke:', today);
        }
    }
    
    // Inisialisasi saat halaman dimuat
    function initializePage() {
        setDefaultDate();
        
        // Set waktu default ke jam sekarang
        const now = new Date();
        const waktuInput = document.getElementById('waktu');
        if (waktuInput) {
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            waktuInput.value = `${hours}:${minutes}`;
            console.log('⏰ Waktu di-set ke:', waktuInput.value);
        }
        
        console.log('🚀 Halaman siap digunakan');
    }
    
    // Jalankan inisialisasi
    initializePage();
    
    // Tambahkan style untuk button loading (opsional)
    const style = document.createElement('style');
    style.textContent = `
        .btn-submit:disabled {
            opacity: 0.7 !important;
            cursor: not-allowed !important;
        }
        
        /* Animasi untuk popup */
        @keyframes popupFadeIn {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .popup-overlay.active .popup {
            animation: popupFadeIn 0.3s ease-out;
        }
    `;
    document.head.appendChild(style);
});