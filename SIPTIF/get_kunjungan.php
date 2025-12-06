<?php
// get_kunjungan.php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

include 'db_config.php';

$sql = "SELECT * FROM data_kunjungan ORDER BY tanggal DESC, masuk DESC";
$result = mysqli_query($conn, $sql);

$data = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

echo json_encode($data);
mysqli_close($conn);
?>