<?php
// data_kunjungan.php
include 'db_config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Filter parameters dari URL
$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$filter_keperluan = isset($_GET['keperluan']) ? $_GET['keperluan'] : '';

// Bangun query berdasarkan filter
$sql = "SELECT * FROM data_kunjungan WHERE 1=1";
$conditions = [];

if (!empty($filter_tanggal)) {
    $conditions[] = "tanggal = '$filter_tanggal'";
}

if (!empty($filter_keperluan)) {
    $conditions[] = "keperluan = '" . mysqli_real_escape_string($conn, $filter_keperluan) . "'";
}

if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY tanggal DESC, masuk DESC";
$result = mysqli_query($conn, $sql);

// Hitung total data (untuk statistik)
$sql_count = "SELECT COUNT(*) as total FROM data_kunjungan WHERE 1=1";
if (!empty($conditions)) {
    $sql_count .= " AND " . implode(" AND ", $conditions);
}
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_data = $row_count['total'];

// Dapatkan nama file saat ini untuk active menu
$current_page = basename($_SERVER['PHP_SELF']);

// Cek parameter success/error dari redirect
$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    $success_message = 'Data berhasil diperbarui!';
}

if (isset($_GET['error'])) {
    $error_message = 'Gagal memperbarui data.';
}

if (isset($_GET['delete_success'])) {
    $success_message = 'Data berhasil dihapus!';
}

if (isset($_GET['delete_error'])) {
    $error_message = 'Gagal menghapus data.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kunjungan - SIPTIF Polibatam</title>
    
    <!-- CSS Load Order: Bootstrap -> style_admin.css -> Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style_admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Alert messages */
        .alert-container {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 9999;
        }
        
        .alert-custom {
            min-width: 300px;
            max-width: 400px;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideInRight 0.3s ease, fadeOut 0.3s ease 4.7s forwards;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }
        
        /* Tambahan style untuk halaman data kunjungan */
        .total-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-action {
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            font-size: 1rem;
        }
        
        .btn-edit {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .btn-edit:hover {
            background-color: #bbdefb;
            transform: scale(1.05);
        }
        
        .btn-delete {
            background-color: #ffebee;
            color: #d32f2f;
        }
        
        .btn-delete:hover {
            background-color: #ffcdd2;
            transform: scale(1.05);
        }
        
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid transparent;
        }
        
        .status-active {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-color: #c8e6c9;
        }
        
        .status-completed {
            background-color: #f5f5f5;
            color: #616161;
            border-color: #e0e0e0;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 70px;
            color: #e0e0e0;
            margin-bottom: 25px;
            opacity: 0.7;
        }
        
        .empty-state h3 {
            color: #666;
            margin-bottom: 15px;
            font-size: 1.4rem;
            font-weight: 600;
        }
        
        .empty-state p {
            color: #888;
            margin-bottom: 25px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        
        /* Filter applied badge */
        .filter-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            border: 1px solid #bbdefb;
        }
        
        .filter-badge .remove-filter {
            background: none;
            border: none;
            color: #1976d2;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 50%;
            transition: all 0.3s;
        }
        
        .filter-badge .remove-filter:hover {
            background: #bbdefb;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-action {
                width: 36px;
                height: 36px;
            }
            
            .alert-container {
                top: 80px;
                right: 10px;
                left: 10px;
            }
            
            .alert-custom {
                min-width: auto;
                max-width: none;
            }
        }
                /* Hamburger default (desktop) */
        .hamburger {
            display: block !important;
            cursor: pointer;
            z-index: 9999;
        }

        /* Garis hamburger */
        .hamburger .bar {
            display: block;
            width: 28px;
            height: 4px;
            margin: 6px auto;
            background-color: #fff;
            transition: all 0.3s ease;
        }

        /* MOBILE MODE */
        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .nav-menu {
                position: fixed;
                left: -100%;
                top: 70px;
                flex-direction: column;
                background-color: #1976D2;
                width: 100%;
                text-align: center;
                transition: 0.3s;
                z-index: 1000;
            }

            .nav-menu.active {
                left: 0;
            }

            .nav-item {
            margin: 16px 0;
            }
        }
        /* Nav */
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
                position: fixed !important;
                top: 60px !important;
                left: -100% !important;
                width: 100% !important;
                height: calc(100vh - 60px) !important;
                background: #1976D2 !important;
                z-index: 99999 !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: flex-start !important;
                padding-top: 30px !important;
            }

            .nav-menu.active {
                left: 0 !important;
            }
        }
    </style>
</head>
<body>
    <!-- Alert Messages -->
    <?php if ($success_message): ?>
    <div class="alert-container">
        <div class="alert-custom alert-success">
            <i class="fas fa-check-circle fa-lg"></i>
            <div>
                <strong>Sukses!</strong>
                <div><?php echo $success_message; ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
    <div class="alert-container">
        <div class="alert-custom alert-error">
            <i class="fas fa-exclamation-circle fa-lg"></i>
            <div>
                <strong>Error!</strong>
                <div><?php echo $error_message; ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
<header>
    <nav class="navbar">
        <a href="admin.php" class="nav-branding">
            <i class="fas fa-book-reader"></i> SIPTIF Polibatam
        </a>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="admin.php" class="nav-link <?php echo $current_page == 'admin.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="data_kunjungan.php" class="nav-link <?php echo $current_page == 'data_kunjungan.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Data Kunjungan
                </a>
            </li>
            <li class="nav-item">
                <a href="tambah_data_kunjungan.php" class="nav-link <?php echo ($current_page == 'tambah_data_kunjungan.php') ? 'active' : ''; ?>">
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
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <h2><i class="fas fa-users"></i> Data Kunjungan Tata Usaha</h2>
        <div class="total-badge">
            <i class="fas fa-database"></i>
            <span><?php echo $total_data; ?> Data</span>
        </div>
    </div>
    
    <!-- Tampilkan filter yang aktif -->
    <?php if (!empty($filter_tanggal) || !empty($filter_keperluan)): ?>
    <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
        <?php if (!empty($filter_tanggal)): ?>
        <div class="filter-badge">
            <i class="fas fa-calendar"></i>
            <span>Tanggal: <?php echo date('d/m/Y', strtotime($filter_tanggal)); ?></span>
            <button class="remove-filter" onclick="removeFilter('tanggal')" title="Hapus filter tanggal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($filter_keperluan)): ?>
        <div class="filter-badge">
            <i class="fas fa-filter"></i>
            <span>Keperluan: <?php echo htmlspecialchars($filter_keperluan); ?></span>
            <button class="remove-filter" onclick="removeFilter('keperluan')" title="Hapus filter keperluan">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>

<section class="data-kunjungan">
    <div class="container">
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-group">
                <label for="filterTanggal">
                    <i class="fas fa-calendar"></i> Filter Tanggal:
                </label>
                <input type="date" id="filterTanggal" class="filter-input" value="<?php echo $filter_tanggal; ?>">
            </div>
            <div class="filter-group">
                <label for="filterKeperluan">
                    <i class="fas fa-filter"></i> Filter Keperluan:
                </label>
                <select id="filterKeperluan" class="filter-input">
                    <option value="">Semua Keperluan</option>
                    <option value="Meminjam Ruangan" <?php echo $filter_keperluan == 'Meminjam Ruangan' ? 'selected' : ''; ?>>Meminjam Ruangan</option>
                    <option value="Mengunjungi Perpustakaan" <?php echo $filter_keperluan == 'Mengunjungi Perpustakaan' ? 'selected' : ''; ?>>Mengunjungi Perpustakaan</option>
                    <option value="Meminjam Alat" <?php echo $filter_keperluan == 'Meminjam Alat' ? 'selected' : ''; ?>>Meminjam Alat</option>
                    <option value="Meminjam Buku" <?php echo $filter_keperluan == 'Meminjam Buku' ? 'selected' : ''; ?>>Meminjam Buku</option>
                    <option value="Meminjam Kunci Ruangan" <?php echo $filter_keperluan == 'Meminjam Kunci Ruangan' ? 'selected' : ''; ?>>Meminjam Kunci Ruangan</option>
                    <option value="Lainnya" <?php echo $filter_keperluan == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; align-items: flex-end;">
                <button id="btnFilter" class="btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <button id="btnReset" class="btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </button>
                <a href="tambah_data_kunjungan.php" class="btn btn-success" style="padding: 10px 20px; text-decoration: none;">
                    <i class="fas fa-plus"></i> Tambah Data
                </a>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['nama']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td><span class="time-badge"><?php echo $row['masuk']; ?></span></td>
                            <td>
                                <?php if ($row['keluar']): ?>
                                    <span class="time-badge success"><?php echo $row['keluar']; ?></span>
                                <?php else: ?>
                                    <span class="time-badge warning">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="keperluan-badge"><?php echo htmlspecialchars($row['keperluan']); ?></span>
                            </td>
                            <td>
                                <?php if ($row['keluar']): ?>
                                    <span class="status-badge status-completed">
                                        <i class="fas fa-check-circle"></i> Selesai
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge status-active">
                                        <i class="fas fa-clock"></i> Aktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" onclick="editData(<?php echo $row['id']; ?>)"
                                            title="Edit data">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action btn-delete" onclick="hapusData(<?php echo $row['id']; ?>)"
                                            title="Hapus data">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <?php if (!empty($filter_tanggal) || !empty($filter_keperluan)): ?>
                                        <i class="fas fa-search"></i>
                                        <h3>Data tidak ditemukan</h3>
                                        <p>Tidak ada data yang sesuai dengan filter yang diterapkan</p>
                                        <button onclick="window.location.href='data_kunjungan.php'" class="btn-primary" 
                                               style="text-decoration: none; display: inline-block; padding: 12px 24px; border: none; cursor: pointer;">
                                            <i class="fas fa-redo"></i> Reset Filter
                                        </button>
                                    <?php else: ?>
                                        <i class="fas fa-inbox"></i>
                                        <h3>Belum ada data kunjungan</h3>
                                        <p>Mulai dengan menambahkan data kunjungan pertama Anda</p>
                                        <a href="tambah_data_kunjungan.php" class="btn-primary" 
                                           style="text-decoration: none; display: inline-block; padding: 12px 24px;">
                                            <i class="fas fa-plus"></i> Tambah Data Pertama
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_data > 0): ?>
        <div class="pagination">
            <button id="prevPage" class="btn-pagination">
                <i class="fas fa-chevron-left"></i> Sebelumnya
            </button>
            <span id="pageInfo">Halaman 1 dari <?php echo ceil($total_data / 10); ?></span>
            <button id="nextPage" class="btn-pagination">
                Selanjutnya <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- Export Buttons -->
        <?php if ($total_data > 0): ?>
        <div class="export-buttons">
            <?php
            // Build export URL dengan parameter filter
            $export_params = '';
            if (!empty($filter_tanggal) || !empty($filter_keperluan)) {
                $params = [];
                if (!empty($filter_tanggal)) $params[] = 'tanggal=' . urlencode($filter_tanggal);
                if (!empty($filter_keperluan)) $params[] = 'keperluan=' . urlencode($filter_keperluan);
                $export_params = '?' . implode('&', $params);
            }
            ?>
            <button class="btn-export" onclick="window.location.href='export_excel.php<?php echo $export_params; ?>'">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
            <button class="btn-export" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak
            </button>
            <button class="btn-export" onclick="refreshData()">
                <i class="fas fa-sync-alt"></i> Refresh Data
            </button>
            <a href="tambah_data_kunjungan.php" class="btn-export" style="text-decoration: none;">
                <i class="fas fa-plus-circle"></i> Tambah Data
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal Edit Data -->
<div class="modal fade" id="editDataModal" tabindex="-1" aria-labelledby="editDataLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDataLabel">
                    <i class="fas fa-edit"></i> Edit Data Kunjungan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
            </div>
            <div class="modal-body">
                <form action="ubah_data_kunjungan.php" method="POST" id="editForm">
                    <input type="hidden" id="edit-id" name="id">
                    
                    <div class="mb-3">
                        <label for="edit-nama" class="form-label">
                            <i class="fas fa-user"></i> Nama Lengkap
                        </label>
                        <input type="text" class="form-control" id="edit-nama" name="nama" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-email" class="form-label">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email" class="form-control" id="edit-email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-tanggal" class="form-label">
                            <i class="fas fa-calendar"></i> Tanggal Kunjungan
                        </label>
                        <input type="date" class="form-control" id="edit-tanggal" name="tanggal" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit-masuk" class="form-label">
                                <i class="fas fa-sign-in-alt"></i> Jam Masuk
                            </label>
                            <input type="time" class="form-control" id="edit-masuk" name="masuk" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-keluar" class="form-label">
                                <i class="fas fa-sign-out-alt"></i> Jam Keluar
                            </label>
                            <input type="time" class="form-control" id="edit-keluar" name="keluar">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-keperluan" class="form-label">
                            <i class="fas fa-tasks"></i> Keperluan
                        </label>
                        <select class="form-control" id="edit-keperluan" name="keperluan" required onchange="toggleEditOtherInput()">
                            <option value="">Pilih keperluan</option>
                            <option value="Meminjam Ruangan">Meminjam Ruangan</option>
                            <option value="Mengunjungi Perpustakaan">Mengunjungi Perpustakaan</option>
                            <option value="Meminjam Alat">Meminjam Alat</option>
                            <option value="Meminjam Buku">Meminjam Buku</option>
                            <option value="Meminjam Kunci Ruangan">Meminjam Kunci Ruangan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        
                        <!-- Input untuk keperluan lainnya -->
                        <div class="other-input-container" id="edit-other-input-container" style="margin-top: 10px; display: none;">
                            <input type="text" 
                                   id="edit-keperluan-lainnya" 
                                   name="keperluan_lainnya" 
                                   class="form-control" 
                                   placeholder="Tuliskan keperluan lainnya..."
                                   maxlength="255">
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Hamburger Menu
const hamburger = document.querySelector(".hamburger");
const navMenu = document.querySelector(".nav-menu");

if (hamburger && navMenu) {
    // Toggle menu
    hamburger.addEventListener("click", (e) => {
        e.stopPropagation();
        hamburger.classList.toggle("active");
        navMenu.classList.toggle("active");
    });
    
    // Close menu when clicking links
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
}

// Toggle input lainnya di modal edit
function toggleEditOtherInput() {
    const keperluanSelect = document.getElementById('edit-keperluan');
    const otherInputContainer = document.getElementById('edit-other-input-container');
    const otherInput = document.getElementById('edit-keperluan-lainnya');
    
    if (keperluanSelect.value === 'Lainnya') {
        otherInputContainer.style.display = 'block';
        otherInput.required = true;
        setTimeout(() => otherInput.focus(), 300);
    } else {
        otherInputContainer.style.display = 'none';
        otherInput.required = false;
        otherInput.value = '';
    }
}

// Fungsi edit data
function editData(id) {
    const row = event.target.closest('tr');
    const cells = row.querySelectorAll('td');
    
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-nama').value = cells[1].querySelector('strong').textContent.trim();
    document.getElementById('edit-email').value = cells[2].textContent.trim();
    
    const tanggalText = cells[3].textContent.trim();
    const [day, month, year] = tanggalText.split('/');
    document.getElementById('edit-tanggal').value = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
    
    document.getElementById('edit-masuk').value = cells[4].querySelector('.time-badge').textContent.trim();
    
    const keluarElement = cells[5].querySelector('.time-badge');
    document.getElementById('edit-keluar').value = keluarElement.textContent.trim() !== '-' ? 
        keluarElement.textContent.trim() : '';
    
    // Ambil keperluan dari badge
    const keperluanText = cells[6].querySelector('.keperluan-badge').textContent.trim();
    const keperluanSelect = document.getElementById('edit-keperluan');
    const otherInputContainer = document.getElementById('edit-other-input-container');
    const otherInput = document.getElementById('edit-keperluan-lainnya');
    
    // Cek apakah keperluan ada di dropdown
    let foundInDropdown = false;
    const options = ['Meminjam Ruangan', 'Mengunjungi Perpustakaan', 'Meminjam Alat', 'Meminjam Buku', 'Meminjam Kunci Ruangan'];
    
    if (options.includes(keperluanText)) {
        keperluanSelect.value = keperluanText;
        foundInDropdown = true;
    }
    
    // Jika tidak ditemukan di dropdown, set ke "Lainnya"
    if (!foundInDropdown && keperluanText) {
        keperluanSelect.value = 'Lainnya';
        otherInputContainer.style.display = 'block';
        otherInput.value = keperluanText;
        otherInput.required = true;
    } else {
        otherInputContainer.style.display = 'none';
        otherInput.value = '';
        otherInput.required = false;
    }
    
    // Tampilkan modal
    const modal = new bootstrap.Modal(document.getElementById('editDataModal'));
    modal.show();
}

// Fungsi hapus data
function hapusData(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        window.location.href = 'hapus_data_kunjungan.php?id=' + id;
    }
}

// Filter data
document.getElementById('btnFilter').addEventListener('click', function() {
    const tanggal = document.getElementById('filterTanggal').value;
    const keperluan = document.getElementById('filterKeperluan').value;
    
    // Build URL parameters
    let params = new URLSearchParams();
    if (tanggal) params.append('tanggal', tanggal);
    if (keperluan) params.append('keperluan', keperluan);
    
    // Reload page dengan filter
    window.location.href = 'data_kunjungan.php?' + params.toString();
});

// Hapus filter individual
function removeFilter(filterName) {
    const params = new URLSearchParams(window.location.search);
    params.delete(filterName);
    
    // Redirect tanpa parameter tersebut
    window.location.href = 'data_kunjungan.php?' + params.toString();
}

// Reset semua filter
document.getElementById('btnReset').addEventListener('click', function() {
    window.location.href = 'data_kunjungan.php';
});

// Setup pagination
function setupPagination() {
    const rowsPerPage = 10;
    const tableRows = document.querySelectorAll('.data-table tbody tr');
    const totalPages = Math.ceil(tableRows.length / rowsPerPage);
    
    if (totalPages <= 1) {
        document.querySelector('.pagination').style.display = 'none';
        return;
    }
    
    let currentPage = 1;
    
    function showPage(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        
        tableRows.forEach((row, index) => {
            if (index >= start && index < end) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        document.getElementById('pageInfo').textContent = `Halaman ${page} dari ${totalPages}`;
        document.getElementById('prevPage').disabled = page === 1;
        document.getElementById('nextPage').disabled = page === totalPages;
    }
    
    document.getElementById('prevPage').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    });
    
    document.getElementById('nextPage').addEventListener('click', () => {
        if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
        }
    });
    
    showPage(1);
}

// Refresh data
function refreshData() {
    const refreshBtn = event.target;
    const icon = refreshBtn.querySelector('i');
    
    icon.classList.add('fa-spin');
    refreshBtn.disabled = true;
    
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Initialize on load
window.addEventListener('DOMContentLoaded', function() {
    // Setup pagination
    setupPagination();
    
    // Remove URL parameters after showing alerts
    const urlParams = new URLSearchParams(window.location.search);
    const params = ['success', 'error', 'delete_success', 'delete_error'];
    let hasParams = false;
    params.forEach(param => {
        if (urlParams.has(param)) {
            hasParams = true;
        }
    });
    
    if (hasParams) {
        // Clean URL setelah alert muncul
        setTimeout(() => {
            const cleanUrl = window.location.pathname + '?' + 
                Array.from(urlParams.entries())
                    .filter(([key]) => !params.includes(key))
                    .map(([key, value]) => `${key}=${value}`)
                    .join('&');
            
            if (cleanUrl.endsWith('?')) {
                window.history.replaceState({}, '', window.location.pathname);
            } else {
                window.history.replaceState({}, '', cleanUrl);
            }
        }, 100);
    }
});

// Form submission
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('editDataModal'));
        modal.hide();
        
        // Redirect dengan parameter success dan filter yang aktif
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('success', '1');
        
        setTimeout(() => {
            window.location.href = 'data_kunjungan.php?' + urlParams.toString();
        }, 500);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengupdate data');
    });
});
</script>
<script src="js/admin.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>