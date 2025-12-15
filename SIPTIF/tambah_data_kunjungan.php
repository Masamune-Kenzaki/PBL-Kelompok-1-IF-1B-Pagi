<?php
// tambah_data_kunjungan.php
include 'db_config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $masuk = mysqli_real_escape_string($conn, $_POST['masuk']);
    $keluar = mysqli_real_escape_string($conn, $_POST['keluar']);
    $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan']);
    
    // Jika keperluan adalah "Lainnya", ambil dari input lainnya
    if ($keperluan == 'Lainnya' && isset($_POST['keperluan_lainnya'])) {
        $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan_lainnya']);
    }
    
    $sql = "INSERT INTO data_kunjungan (nama, email, tanggal, masuk, keluar, keperluan) 
            VALUES ('$nama', '$email', '$tanggal', '$masuk', '$keluar', '$keperluan')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>
            alert('Data berhasil ditambahkan!');
            window.location.href = 'data_kunjungan.php';
        </script>";
        exit();
    } else {
        echo "<script>
            alert('Gagal menambahkan data: " . addslashes(mysqli_error($conn)) . "');
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Kunjungan - SIPTIF Polibatam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/style_admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #1976D2;
            outline: none;
        }
        
        .other-input-container {
            margin-top: 10px;
            display: none;
        }
        
        .other-input-container.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #1976D2 0%, #0D47A1 100%);
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-submit:hover {
            background: linear-gradient(135deg, #1565C0 0%, #0a3d91 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
        }
        
        .btn-back {
            background: #f8f9fa;
            color: #2c3e50;
            padding: 14px 30px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-back:hover {
            background: #e9ecef;
            border-color: #1976D2;
            color: #1976D2;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar">
        <a href="admin.php" class="nav-branding">
            <i class="fas fa-book-reader"></i> SIPTIF Polibatam
        </a>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="admin.php" class="nav-link <?php echo $current_page == 'admin.php' ? '' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="data_kunjungan.php" class="nav-link <?php echo $current_page == 'data_kunjungan.php' ? '' : ''; ?>">
                    <i class="fas fa-users"></i> Data Kunjungan
                </a>
            </li>
            <li class="nav-item">
                <a href="tambah_data_kunjungan.php" class="nav-link active">
                    <i class="fas fa-plus-circle"></i> Tambah Data
                </a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link logout">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </li>
        </ul>
        
        <div class="hamburger">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </nav>
</header>

<section class="title">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2><i class="fas fa-plus-circle"></i> Tambah Data Kunjungan</h2>
        <a href="data_kunjungan.php" style="color: white; text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Kembali ke Data
        </a>
    </div>
</section>

<section style="padding: 30px 0;">
    <div class="container">
        <div class="form-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="nama">
                        <i class="fas fa-user"></i> Nama Lengkap
                    </label>
                    <input type="text" id="nama" name="nama" class="form-control" 
                           placeholder="Masukkan nama lengkap" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="contoh@email.com" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="tanggal">
                        <i class="fas fa-calendar"></i> Tanggal Kunjungan
                    </label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" 
                           value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="masuk" class="form-label">
                            <i class="fas fa-sign-in-alt"></i> Jam Masuk
                        </label>
                        <input type="time" id="masuk" name="masuk" class="form-control" 
                               value="<?php echo date('H:i'); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="keluar" class="form-label">
                            <i class="fas fa-sign-out-alt"></i> Jam Keluar (Opsional)
                        </label>
                        <input type="time" id="keluar" name="keluar" class="form-control">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="keperluan">
                        <i class="fas fa-tasks"></i> Keperluan
                    </label>
                    <select id="keperluan" name="keperluan" class="form-control" required onchange="toggleOtherInput()">
                        <option value="">Pilih keperluan</option>
                        <option value="Meminjam Ruangan">Meminjam Ruangan</option>
                        <option value="Menemui Wali Dosen">Menemui Wali Dosen</option>
                        <option value="Meminjam Alat">Meminjam Alat</option>
                        <option value="Menemui Staff TU">Menemui Staff TU</option>
                        <option value="Meminjam Kunci Ruangan">Meminjam Kunci Ruangan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    
                    <!-- Input untuk keperluan lainnya -->
                    <div class="other-input-container" id="otherInputContainer">
                        <input type="text" 
                               id="keperluan_lainnya" 
                               name="keperluan_lainnya" 
                               class="form-control" 
                               placeholder="Tuliskan keperluan lainnya..."
                               maxlength="255"
                               style="margin-top: 10px;">
                    </div>
                </div>
                
                <div class="button-group">
                    <a href="data_kunjungan.php" class="btn-back">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hamburger Menu
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");

    if (hamburger && navMenu) {
        hamburger.addEventListener("click", (e) => {
            e.stopPropagation();
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
        });
        
        document.querySelectorAll(".nav-link").forEach(link => {
            link.addEventListener("click", () => {
                hamburger.classList.remove("active");
                navMenu.classList.remove("active");
            });
        });
    }

    // Toggle input lainnya
    const keperluanSelect = document.getElementById('keperluan');
    const otherInputContainer = document.getElementById('otherInputContainer');
    const otherInput = document.getElementById('keperluan_lainnya');
    
    function toggleOtherInput() {
        if (keperluanSelect.value === 'Lainnya') {
            otherInputContainer.classList.add('show');
            otherInput.required = true;
        } else {
            otherInputContainer.classList.remove('show');
            otherInput.required = false;
            otherInput.value = '';
        }
    }
    
    keperluanSelect.addEventListener('change', toggleOtherInput);
    
    // Set waktu default untuk jam keluar (3 jam setelah masuk)
    document.getElementById('masuk').addEventListener('change', function() {
        const masukTime = this.value;
        if (masukTime) {
            const [hours, minutes] = masukTime.split(':');
            const masukDate = new Date();
            masukDate.setHours(parseInt(hours) + 3, parseInt(minutes));
            
            const keluarHours = masukDate.getHours().toString().padStart(2, '0');
            const keluarMinutes = masukDate.getMinutes().toString().padStart(2, '0');
            
            document.getElementById('keluar').value = `${keluarHours}:${keluarMinutes}`;
        }
    });
    
    // Initialize
    toggleOtherInput();
});
</script>
</body>
</html>
<?php mysqli_close($conn); ?>