document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kunjunganForm');
    const popupOverlay = document.getElementById('popupOverlay');
    const popupBtn = document.getElementById('popupBtn');
    
    // Tangani submit form
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            submitFormData();
        }
    });
    
    // Tutup pop-up
    popupBtn.addEventListener('click', function() {
        popupOverlay.classList.remove('active');
        form.reset(); // Reset form setelah popup ditutup
    });
    
    popupOverlay.addEventListener('click', function(e) {
        if (e.target === popupOverlay) {
            popupOverlay.classList.remove('active');
            form.reset(); // Reset form setelah popup ditutup
        }
    });
    
    // Fungsi validasi form
    function validateForm() {
        const nama = document.getElementById('nama').value.trim();
        const email = document.getElementById('email').value.trim();
        const tanggal = document.getElementById('tanggal').value;
        const waktu = document.getElementById('waktu').value;
        const keperluan = document.getElementById('keperluan').value;
        
        if (nama === '' || email === '' || tanggal === '' || waktu === '' || keperluan === '') {
            alert('Harap lengkapi semua field yang wajib diisi!');
            return false;
        }
        
        // Validasi email
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            alert('Format email tidak valid!');
            return false;
        }
        
        return true;
    }
    
    // Fungsi untuk submit data ke PHP
    function submitFormData() {
        // Tampilkan loading state
        const submitBtn = form.querySelector('.btn-submit');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Menyimpan...';
        submitBtn.disabled = true;
        
        const formData = new FormData(form);
        
        // Debug: log data yang akan dikirim
        console.log('Data yang dikirim:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        fetch('save_kunjungan.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                popupOverlay.classList.add('active');
                // Reset form setelah 2 detik
                setTimeout(function() {
                    form.reset();
                }, 2000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan data. Periksa konsol untuk detail.');
        })
        .finally(() => {
            // Reset button state
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    }
    
    // Set tanggal minimal ke hari ini
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal').setAttribute('min', today);
    
    // Set nilai default tanggal ke hari ini
    document.getElementById('tanggal').value = today;
});