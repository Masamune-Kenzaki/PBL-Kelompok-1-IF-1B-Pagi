<?php
// get_chart_data.php
header('Content-Type: application/json');
include 'db_config.php';

$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));

// Data untuk chart
$sql_chart = "SELECT tanggal, COUNT(*) as jumlah 
              FROM data_kunjungan 
              WHERE tanggal >= DATE_SUB('$today', INTERVAL 7 DAY)
              GROUP BY tanggal 
              ORDER BY tanggal";
$result_chart = mysqli_query($conn, $sql_chart);

// Inisialisasi array
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $formatted_date = date('d/m', strtotime($date));
    $chart_data[$formatted_date] = 0;
}

// Isi data
while ($row = mysqli_fetch_assoc($result_chart)) {
    $date = date('d/m', strtotime($row['tanggal']));
    if (isset($chart_data[$date])) {
        $chart_data[$date] = $row['jumlah'];
    }
}

// Statistik
$sql_today = "SELECT COUNT(*) as total FROM data_kunjungan WHERE tanggal = '$today'";
$result_today = mysqli_query($conn, $sql_today);
$total_today = mysqli_fetch_assoc($result_today)['total'];

$sql_active = "SELECT COUNT(*) as active FROM data_kunjungan WHERE keluar IS NULL OR keluar = ''";
$result_active = mysqli_query($conn, $sql_active);
$total_active = mysqli_fetch_assoc($result_active)['active'];

$sql_total = "SELECT COUNT(*) as total FROM data_kunjungan";
$result_total = mysqli_query($conn, $sql_total);
$total_all = mysqli_fetch_assoc($result_total)['total'];

echo json_encode([
    'success' => true,
    'labels' => array_keys($chart_data),
    'values' => array_values($chart_data),
    'stats' => [
        'today' => $total_today,
        'active' => $total_active,
        'total' => $total_all
    ]
]);

mysqli_close($conn);
?>