// JavaScript untuk menangani form submission dan pop-up
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kunjunganForm');
    const popupOverlay = document.getElementById('popupOverlay');
    const popupBtn = document.getElementById('popupBtn');
    
    // Tangani submit form
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validasi form
        if (validateForm()) {
            // Tampilkan pop-up
            popupOverlay.classList.add('active');
            
            // Reset form setelah 3 detik
            setTimeout(function() {
                form.reset();
            }, 3000);
        }
    });
    
    // Tutup pop-up ketika tombol OK diklik
    popupBtn.addEventListener('click', function() {
        popupOverlay.classList.remove('active');
    });
    
    // Tutup pop-up ketika klik di luar pop-up
    popupOverlay.addEventListener('click', function(e) {
        if (e.target === popupOverlay) {
            popupOverlay.classList.remove('active');
        }
    });
    
    // Fungsi validasi form
    function validateForm() {
        const nama = document.getElementById('nama').value.trim();
        const nim = document.getElementById('nim').value.trim();
        const prodi = document.getElementById('prodi').value.trim();
        const tanggal = document.getElementById('tanggal').value;
        const waktu = document.getElementById('waktu').value;
        const keperluan = document.getElementById('keperluan').value;
        
        // Validasi sederhana
        if (nama === '' || nim === '' || prodi === '' || tanggal === '' || waktu === '' || keperluan === '') {
            alert('Harap lengkapi semua field yang wajib diisi!');
            return false;
        }
        
        // Validasi NIM (minimal 8 digit angka)
        if (!/^\d{8,}$/.test(nim)) {
            alert('NIM harus terdiri dari minimal 8 digit angka!');
            return false;
        }
        
        return true;
    }
    
    // Set tanggal minimal ke hari ini
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal').setAttribute('min', today);
});