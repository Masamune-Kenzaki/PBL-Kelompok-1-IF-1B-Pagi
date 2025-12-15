<?php
// kunjungan.php
include 'db_config.php';

$success = false;
$error = false;

// Jika ada form submission, proses data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $masuk = mysqli_real_escape_string($conn, $_POST['waktu']);
    $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan']);
    
    // Jika keperluan adalah "Lainnya", ambil dari input lainnya
    if ($keperluan == 'Lainnya' && isset($_POST['keperluan_lainnya'])) {
        $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan_lainnya']);
    }
    
    // Simpan ke database
    $sql = "INSERT INTO data_kunjungan (nama, email, tanggal, masuk, keperluan, created_at) 
            VALUES ('$nama', '$email', '$tanggal', '$masuk', '$keperluan', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        $success = true;
    } else {
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Kunjungan - SIPTIF Polibatam</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style_kunjungan.css">
    <style>
        /* Inline CSS untuk performa */
        body { 
            margin: 0; 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background: #f5f7fa;
        }
        .navbar { 
            background: white; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            padding: 15px 0;
        }
        .form-wrapper { 
            background: white; 
            border-radius: 10px; 
            padding: 30px; 
            margin: 30px auto; 
            max-width: 600px;
            position: relative;
        }
        
        /* Styling untuk input lainnya */
        .other-input-container {
            margin-top: 10px;
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .other-input-container.show {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .other-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .other-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        /* Pop-up Styles (diperbaiki) */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .popup-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .popup {
            background-color: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            transform: scale(0.9) translateY(20px);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .popup-overlay.active .popup {
            transform: scale(1) translateY(0);
        }
        
        .popup-icon {
            font-size: 70px;
            color: #2ecc71;
            margin-bottom: 20px;
            animation: bounceIn 0.6s ease;
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .popup h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .popup p {
            color: #7f8c8d;
            margin-bottom: 25px;
            font-size: 1.1rem;
            line-height: 1.5;
        }
        
        .popup-btn {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .popup-btn:hover {
            background: linear-gradient(135deg, #2980b9 0%, #1c6ea4 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }
        
        /* Loading spinner */
        .btn-loading {
            position: relative;
            pointer-events: none;
        }
        
        .btn-loading .btn-text {
            opacity: 0;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
        /* Tombol */
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn-submit,
        .btn-reset {
            flex: 1;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        /* Tombol Simpan */
        .btn-submit {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: #fff;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.35);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #2980b9, #1f6fb2);
            transform: translateY(-2px);
        }

        /* Tombol Reset */
        .btn-reset {
            background: #e3f2fd;
            color: #1565c0;
            border: 2px solid #3498db;
        }

        .btn-reset:hover {
            background: #3498db;
            color: #fff;
            transform: translateY(-2px);
        }

        /* Disabled / loading */
        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.html" class="logo">
                    <img src="../png/siptif.png" alt="logo SIPTIF Polibatam" width="40" height="40">
                </a>
                <h1 class="judul">SIPTIF Polibatam</h1>
            </div>
            <ul class="nav-links">
                <li><a href="index.html">Beranda</a></li>
                <li><a href="kunjungan.php" class="active">Kunjungan</a></li>
            </ul>
        </div>
    </nav>

    <section class="kunjungan-section">
        <div class="container">
            <div class="form-wrapper">
                <h2>Form Kunjungan SIPTIF</h2>
                <p class="form-description">Isi data diri Anda untuk mengunjungi Tata Usaha</p>
                
                <form class="simple-form" id="kunjunganForm">
                    <div class="input-group">
                        <label for="nama">Nama Lengkap *</label>
                        <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" placeholder="contoh@email.com" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="tanggal">Tanggal Kunjungan *</label>
                        <input type="date" id="tanggal" name="tanggal" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="waktu">Waktu Kunjungan *</label>
                        <input type="time" id="waktu" name="waktu" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="keperluan">Keperluan *</label>
                        <select id="keperluan" name="keperluan" required>
                            <option value="">Pilih keperluan</option>
                            <option value="Meminjam Ruangan">Meminjam Ruangan</option>
                            <option value="Mengunjungi Perpustakaan">Mengunjungi Perpustakaan</option>
                            <option value="Meminjam Kunci Ruangan">Meminjam Kunci Ruangan</option>
                            <option value="Meminjam Alat">Meminjam Alat</option>
                            <option value="Meminjam Buku">Meminjam Buku</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        
                        <!-- Input untuk keperluan lainnya -->
                        <div class="other-input-container" id="otherInputContainer">
                            <input type="text" 
                                   id="keperluan_lainnya" 
                                   name="keperluan_lainnya" 
                                   class="other-input" 
                                   placeholder="Tuliskan keperluan lainnya..."
                                   maxlength="255">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <span class="btn-text">Simpan Data</span>
                        </button>
                        <button type="reset" class="btn-reset">Reset Form</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Popup Konfirmasi (diperbaiki) -->
    <div class="popup-overlay" id="popupOverlay">
        <div class="popup">
            <div class="popup-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 id="popupTitle">Data Tersimpan!</h3>
            <p id="popupMessage">Data kunjungan Anda telah berhasil disimpan ke sistem.</p>
            <button class="popup-btn" id="popupBtn">OK</button>
        </div>
    </div>

    <!-- Popup Error -->
    <div class="popup-overlay" id="errorPopup">
        <div class="popup">
            <div class="popup-icon" style="color: #e74c3c;">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3>Terjadi Kesalahan</h3>
            <p id="errorMessage">Gagal menyimpan data. Silakan coba lagi.</p>
            <button class="popup-btn" id="errorBtn">Coba Lagi</button>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>SIPTIF Polibatam</h3>
                    <p>Perpustakaan digital Politeknik Negeri Batam</p>
                </div>
                <div class="footer-section">
                    <h3>Kontak Kami</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Jl. Ahmad Yani, Batam Center, Batam</p>
                    <p><i class="fas fa-phone"></i> (0778) 469858</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 SIPTIF Polibatam. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const form = document.getElementById('kunjunganForm');
        const keperluanSelect = document.getElementById('keperluan');
        const otherInputContainer = document.getElementById('otherInputContainer');
        const otherInput = document.getElementById('keperluan_lainnya');
        const popupOverlay = document.getElementById('popupOverlay');
        const errorPopup = document.getElementById('errorPopup');
        const popupBtn = document.getElementById('popupBtn');
        const errorBtn = document.getElementById('errorBtn');
        const submitBtn = form.querySelector('.btn-submit');
        
        // Check PHP success/error state
        <?php if ($success): ?>
        setTimeout(function() {
            showPopup('Data Tersimpan!', 'Data kunjungan Anda telah berhasil disimpan ke sistem.');
        }, 300);
        <?php elseif ($error): ?>
        setTimeout(function() {
            showError('Gagal menyimpan data. Silakan coba lagi.');
        }, 300);
        <?php endif; ?>
        
        // Set defaults
        function setupDefaults() {
            const today = new Date();
            const dateStr = today.toISOString().split('T')[0];
            const nextHour = new Date(today.getTime() + 60 * 60 * 1000);
            const timeStr = `${String(nextHour.getHours()).padStart(2, '0')}:${String(nextHour.getMinutes()).padStart(2, '0')}`;
            
            document.getElementById('tanggal').value = dateStr;
            document.getElementById('tanggal').setAttribute('min', dateStr);
            document.getElementById('waktu').value = timeStr;
        }
        
        // Toggle input lainnya
        function toggleOtherInput() {
            if (keperluanSelect.value === 'Lainnya') {
                otherInputContainer.classList.add('show');
                otherInput.required = true;
                setTimeout(() => otherInput.focus(), 300);
            } else {
                otherInputContainer.classList.remove('show');
                otherInput.required = false;
                otherInput.value = '';
            }
        }
        
        // Form validation
        function validateForm() {
            // Validasi nama
            const nama = document.getElementById('nama').value.trim();
            if (nama.length < 2) {
                alert('❌ Nama minimal 2 karakter');
                return false;
            }
            
            // Validasi email
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('❌ Email tidak valid');
                return false;
            }
            
            // Validasi keperluan
            const keperluan = keperluanSelect.value;
            if (!keperluan) {
                alert('❌ Pilih keperluan kunjungan');
                return false;
            }
            
            // Jika pilih Lainnya, validasi input lainnya
            if (keperluan === 'Lainnya') {
                const lainnyaValue = otherInput.value.trim();
                if (!lainnyaValue) {
                    alert('❌ Silakan isi keperluan lainnya');
                    otherInput.focus();
                    return false;
                }
                if (lainnyaValue.length < 3) {
                    alert('❌ Keperluan lainnya minimal 3 karakter');
                    otherInput.focus();
                    return false;
                }
            }
            
            return true;
        }
        
        // Show popup
        function showPopup(title, message) {
            document.getElementById('popupTitle').textContent = title;
            document.getElementById('popupMessage').textContent = message;
            popupOverlay.classList.add('active');
            
            // Auto hide setelah 3 detik
            setTimeout(() => {
                hidePopup();
                // Redirect ke halaman sukses setelah popup hilang
                setTimeout(() => {
                    window.location.href = 'kunjungan.php?success=true';
                }, 500);
            }, 3000);
        }
        
        // Show error popup
        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            errorPopup.classList.add('active');
        }
        
        // Hide popup
        function hidePopup() {
            popupOverlay.classList.remove('active');
        }
        
        // Hide error popup
        function hideError() {
            errorPopup.classList.remove('active');
        }
        
        // Form submission handler
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!validateForm()) return;
            
            // Set loading state
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
            
            // Prepare form data
            const formData = new FormData(form);
            
            try {
                // Kirim form menggunakan AJAX
                const response = await fetch('kunjungan.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.text();
                
                // Check if success
                if (response.ok) {
                    // Reset form
                    form.reset();
                    setupDefaults();
                    toggleOtherInput();
                    
                    // Show success popup
                    showPopup('Data Tersimpan!', 'Data kunjungan Anda telah berhasil disimpan ke sistem.');
                    
                    // Reset form defaults setelah popup
                    setTimeout(() => {
                        setupDefaults();
                    }, 3500);
                } else {
                    throw new Error('Server error');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Gagal menyimpan data. Silakan coba lagi.');
            } finally {
                // Reset button state
                submitBtn.classList.remove('btn-loading');
                submitBtn.disabled = false;
            }
        });
        
        // Event listeners
        keperluanSelect.addEventListener('change', toggleOtherInput);
        
        // Popup button events
        popupBtn.addEventListener('click', function() {
            hidePopup();
            // Refresh halaman untuk reset form
            setTimeout(() => {
                window.location.href = 'kunjungan.php';
            }, 300);
        });
        
        errorBtn.addEventListener('click', function() {
            hideError();
        });
        
        // Close popups on overlay click
        popupOverlay.addEventListener('click', function(e) {
            if (e.target === popupOverlay) {
                hidePopup();
                setTimeout(() => {
                    window.location.href = 'kunjungan.php';
                }, 300);
            }
        });
        
        errorPopup.addEventListener('click', function(e) {
            if (e.target === errorPopup) hideError();
        });
        
        // Close popup on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hidePopup();
                hideError();
            }
        });
        
        // Initialize
        setupDefaults();
        toggleOtherInput();
        
        // Check URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            showPopup('Data Tersimpan!', 'Data kunjungan Anda telah berhasil disimpan ke sistem.');
            // Clean URL
            setTimeout(() => {
                window.history.replaceState({}, document.title, 'kunjungan.php');
            }, 1000);
        }
    });
    </script>
    <script src="js/admin.js"></script>
</body>
</html>
<?php
if (isset($conn)) {
    mysqli_close($conn);
}
?>