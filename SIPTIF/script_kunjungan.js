document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kunjunganForm');
    const popupOverlay = document.getElementById('popupOverlay');
    const popupBtn = document.getElementById('popupBtn');
    
    // Fungsi untuk menampilkan/menyembunyikan field NIM
    window.toggleNIM = function() {
        const status = document.getElementById('status').value;
        const nimGroup = document.getElementById('nimGroup');
        
        if (status === 'Mahasiswa' || status === 'Dosen') {
            nimGroup.style.display = 'block';
            document.getElementById('nim').required = true;
        } else {
            nimGroup.style.display = 'none';
            document.getElementById('nim').required = false;
            document.getElementById('nim').value = '';
        }
    };
    
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
    });
    
    popupOverlay.addEventListener('click', function(e) {
        if (e.target === popupOverlay) {
            popupOverlay.classList.remove('active');
        }
    });
    
    // Fungsi validasi form
    function validateForm() {
        const nama = document.getElementById('nama').value.trim();
        const email = document.getElementById('email').value.trim();
        const status = document.getElementById('status').value;
        const tanggal = document.getElementById('tanggal').value;
        const waktu = document.getElementById('waktu').value;
        const keperluan = document.getElementById('keperluan').value;
        const nim = document.getElementById('nim').value.trim();
        
        if (nama === '' || email === '' || status === '' || tanggal === '' || waktu === '' || keperluan === '') {
            alert('Harap lengkapi semua field yang wajib diisi!');
            return false;
        }
        
        // Validasi email
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            alert('Format email tidak valid!');
            return false;
        }
        
        // Validasi NIM jika status Mahasiswa atau Dosen
        if ((status === 'Mahasiswa' || status === 'Dosen') && nim === '') {
            alert('NIM/NIDN wajib diisi untuk status ' + status);
            return false;
        }
        
        return true;
    }
    
    // Fungsi untuk submit data ke PHP
    function submitFormData() {
        const formData = new FormData(form);
        const status = document.getElementById('status').value;
        const nim = document.getElementById('nim').value.trim();
        
        // Set NIM berdasarkan status
        if (status === 'Umum') {
            formData.set('nim', '-');
        } else {
            formData.set('nim', nim);
        }
        
        // Tambahkan waktu masuk (menggunakan waktu dari form)
        formData.set('masuk', document.getElementById('waktu').value);
        
        fetch('save_kunjungan.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                popupOverlay.classList.add('active');
                setTimeout(function() {
                    form.reset();
                    document.getElementById('nimGroup').style.display = 'none';
                }, 3000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan data.');
        });
    }
    
    // Set tanggal minimal ke hari ini
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal').setAttribute('min', today);
});