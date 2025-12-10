<?php
// export_excel.php
include 'db_config.php';

// Ambil filter dari parameter
$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$filter_keperluan = isset($_GET['keperluan']) ? $_GET['keperluan'] : '';

// Build query dengan filter
$sql = "SELECT * FROM data_kunjungan WHERE 1=1";

if (!empty($filter_tanggal)) {
    $sql .= " AND tanggal = '$filter_tanggal'";
}

if (!empty($filter_keperluan)) {
    $sql .= " AND keperluan = '" . mysqli_real_escape_string($conn, $filter_keperluan) . "'";
}

$sql .= " ORDER BY tanggal DESC, masuk DESC";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_kunjungan_" . date('Ymd_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$result = mysqli_query($conn, $sql);

echo "<table border='1'>";
echo "<tr>
    <th>No</th>
    <th>Nama</th>
    <th>Email</th>
    <th>Tanggal</th>
    <th>Jam Masuk</th>
    <th>Jam Keluar</th>
    <th>Keperluan</th>
    <th>Status</th>
</tr>";

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $status = $row['keluar'] ? 'Selesai' : 'Aktif';
    
    echo "<tr>
        <td>{$no}</td>
        <td>{$row['nama']}</td>
        <td>{$row['email']}</td>
        <td>{$row['tanggal']}</td>
        <td>{$row['masuk']}</td>
        <td>" . ($row['keluar'] ?: '-') . "</td>
        <td>{$row['keperluan']}</td>
        <td>{$status}</td>
    </tr>";
    
    $no++;
}

echo "</table>";

mysqli_close($conn);
?>