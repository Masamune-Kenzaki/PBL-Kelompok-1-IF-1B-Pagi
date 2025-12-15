<?php
// admin.php
include 'db_config.php';
session_start();

// Cek apakah user sudah login (basic auth)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Hitung statistik
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));

// Total hari ini
$sql_today = "SELECT COUNT(*) as total FROM data_kunjungan WHERE tanggal = '$today'";
$result_today = mysqli_query($conn, $sql_today);
$row_today = mysqli_fetch_assoc($result_today);
$total_today = $row_today ? $row_today['total'] : 0;

// Total kemarin
$sql_yesterday = "SELECT COUNT(*) as total FROM data_kunjungan WHERE tanggal = '$yesterday'";
$result_yesterday = mysqli_query($conn, $sql_yesterday);
$row_yesterday = mysqli_fetch_assoc($result_yesterday);
$total_yesterday = $row_yesterday ? $row_yesterday['total'] : 0;

// Total keseluruhan
$sql_total = "SELECT COUNT(*) as total FROM data_kunjungan";
$result_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_all = $row_total ? $row_total['total'] : 0;

// Data untuk chart (7 hari terakhir)
$sql_chart = "SELECT tanggal, COUNT(*) as jumlah 
              FROM data_kunjungan 
              WHERE tanggal >= DATE_SUB('$today', INTERVAL 7 DAY)
              GROUP BY tanggal 
              ORDER BY tanggal";
$result_chart = mysqli_query($conn, $sql_chart);

// Inisialisasi array untuk chart
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $formatted_date = date('d/m', strtotime($date));
    $chart_data[$formatted_date] = 0;
}

// Isi data chart
while ($row = mysqli_fetch_assoc($result_chart)) {
    $date = date('d/m', strtotime($row['tanggal']));
    if (isset($chart_data[$date])) {
        $chart_data[$date] = $row['jumlah'];
    }
}

// Konversi ke array untuk JavaScript
$chart_labels = array_keys($chart_data);
$chart_values = array_values($chart_data);

// Hitung pengunjung aktif (belum keluar)
$sql_active = "SELECT COUNT(*) as active FROM data_kunjungan WHERE keluar IS NULL OR keluar = ''";
$result_active = mysqli_query($conn, $sql_active);
$row_active = mysqli_fetch_assoc($result_active);
$total_active = $row_active ? $row_active['active'] : 0;

// Statistik keperluan
$sql_keperluan = "SELECT keperluan, COUNT(*) as jumlah 
                  FROM data_kunjungan 
                  GROUP BY keperluan 
                  ORDER BY jumlah DESC LIMIT 5";
$result_keperluan = mysqli_query($conn, $sql_keperluan);
$keperluan_stats = [];
while ($row = mysqli_fetch_assoc($result_keperluan)) {
    $keperluan_stats[] = $row;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SIPTIF Polibatam</title>
    <link rel="stylesheet" href="css/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/style_admin.css">
    <style>
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .stat-card i {
            font-size: 40px;
            color: #1976D2;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #1976D2;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .stat-change {
            font-size: 14px;
            color: #666;
        }
        
        .stat-change.positive {
            color: #4CAF50;
        }
        
        .stat-change.negative {
            color: #f44336;
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: 400px;
        }
        
        .quick-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .quick-stat {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .quick-stat h4 {
            margin: 0 0 10px 0;
            color: #555;
            font-size: 14px;
        }
        
        .quick-stat p {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #1976D2;
        }
        
        .last-updated {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 12px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        @media (max-width: 768px) {
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
            
            .stat-card {
                padding: 20px;
            }
            
            .chart-container {
                height: 300px;
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
    <header>
        <nav class="navbar">
            <a href="admin.php" class="nav-branding">SIPTIF Polibatam</a>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="admin.php" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="data_kunjungan.php" class="nav-link">
                        <i class="fas fa-users"></i> Data Kunjungan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="tambah_data_kunjungan.php" class="nav-link">
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
        <h2><i class="fas fa-tachometer-alt"></i> Dashboard Admin</h2>
        <p style="margin-top: 5px; font-size: 14px; opacity: 0.9;">Sistem Informasi Tata Usaha Polibatam</p>
    </section>

    <section class="dashboard">
        <div class="container">
            <h2 style="margin-bottom: 20px;">Statistik Kunjungan</h2>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <i class="fas fa-calendar-day"></i>
                    <div class="stat-label">Hari Ini</div>
                    <div class="stat-number"><?php echo $total_today; ?></div>
                    <div class="stat-change">
                        <?php 
                        $change = $total_today - $total_yesterday;
                        if ($change > 0) {
                            echo '<span class="positive"><i class="fas fa-arrow-up"></i> +' . $change . '</span>';
                        } elseif ($change < 0) {
                            echo '<span class="negative"><i class="fas fa-arrow-down"></i> ' . $change . '</span>';
                        } else {
                            echo '<span>0</span>';
                        }
                        ?> vs kemarin
                    </div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-user-clock"></i>
                    <div class="stat-label">Sedang Berkunjung</div>
                    <div class="stat-number"><?php echo $total_active; ?></div>
                    <div class="stat-change">Belum keluar dari sistem</div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-chart-line"></i>
                    <div class="stat-label">Total Kunjungan</div>
                    <div class="stat-number"><?php echo $total_all; ?></div>
                    <div class="stat-change">Data sejak awal</div>
                </div>
            </div>
        </div>
    </section>

    <section class="charts">
        <div class="container">
            <h2>Grafik Kunjungan 7 Hari Terakhir</h2>
            <div class="chart-container">
                <canvas id="visitorChart"></canvas>
            </div>
        </div>
    </section>

    <section class="quick-stats">
        <div class="container">
            <h2>Statistik Berdasarkan Keperluan</h2>
            <div class="quick-stats-grid">
                <?php foreach ($keperluan_stats as $stat): ?>
                <div class="quick-stat">
                    <h4><?php echo htmlspecialchars($stat['keperluan']); ?></h4>
                    <p><?php echo $stat['jumlah']; ?></p>
                    <small><?php echo round(($stat['jumlah'] / $total_all) * 100, 1); ?>%</small>
                </div>
                <?php endforeach; ?>
                <?php if (empty($keperluan_stats)): ?>
                <div class="quick-stat">
                    <h4>Belum ada data</h4>
                    <p>0</p>
                    <small>0%</small>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="last-updated">
        <i class="fas fa-sync-alt"></i> Data diperbarui: <?php echo date('d F Y H:i:s'); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data untuk chart
        const chartLabels = <?php echo json_encode($chart_labels); ?>;
        const chartValues = <?php echo json_encode($chart_values); ?>;
        
        // Warna untuk chart
        const ctx = document.getElementById('visitorChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(25, 118, 210, 0.8)');
        gradient.addColorStop(1, 'rgba(25, 118, 210, 0.1)');
        
        // Inisialisasi chart
        const visitorChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Jumlah Kunjungan',
                    data: chartValues,
                    backgroundColor: gradient,
                    borderColor: 'rgba(25, 118, 210, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(25, 118, 210, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                if (Number.isInteger(value)) {
                                    return value;
                                }
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'nearest'
                }
            }
        });

        // Auto-refresh chart setiap 60 detik
        setInterval(() => {
            fetch('get_chart_data.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        visitorChart.data.labels = data.labels;
                        visitorChart.data.datasets[0].data = data.values;
                        visitorChart.update();
                        
                        // Update statistik
                        if (data.stats) {
                            document.querySelector('.stat-card:nth-child(1) .stat-number').textContent = data.stats.today;
                            document.querySelector('.stat-card:nth-child(2) .stat-number').textContent = data.stats.active;
                            document.querySelector('.stat-card:nth-child(3) .stat-number').textContent = data.stats.total;
                            
                            // Update last updated time
                            document.querySelector('.last-updated').innerHTML = 
                                `<i class="fas fa-sync-alt"></i> Data diperbarui: ${new Date().toLocaleString('id-ID')}`;
                        }
                    }
                })
                .catch(error => console.error('Error refreshing data:', error));
        }, 60000);

        // Hamburger menu functionality
        const hamburger = document.querySelector(".hamburger");
        const navMenu = document.querySelector(".nav-menu");

        hamburger.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
        });

        document.querySelectorAll(".nav-link").forEach(n => n.addEventListener("click", () => {
            hamburger.classList.remove("active");
            navMenu.classList.remove("active");
        }));
    </script>
    <script src="js/menu.js"></script>
    <script src="js/script.js"></script>
</body>
</html>