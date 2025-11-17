// Data contoh pengunjung dengan NIM
const dataPengunjung = [
    { id: 1, nim: "2021001001", nama: "Ahmad Rizki", status: "Mahasiswa", tanggal: "2023-10-15", masuk: "08:30", keluar: "12:15", keperluan: "Mencari referensi skripsi" },
    { id: 2, nim: "198011022001", nama: "Siti Nurhaliza", status: "Dosen", tanggal: "2023-10-15", masuk: "09:45", keluar: "11:30", keperluan: "Konsultasi dengan mahasiswa" },
    { id: 3, nim: "2021001002", nama: "Budi Santoso", status: "Mahasiswa", tanggal: "2023-10-16", masuk: "10:20", keluar: "15:45", keperluan: "Belajar untuk ujian" },
    { id: 4, nim: "-", nama: "Maya Sari", status: "Umum", tanggal: "2023-10-16", masuk: "14:10", keluar: "16:30", keperluan: "Membaca buku koleksi" },
    { id: 5, nim: "2021001003", nama: "Rizki Pratama", status: "Mahasiswa", tanggal: "2023-10-17", masuk: "08:15", keluar: "11:20", keperluan: "Mengerjakan tugas" },
    { id: 6, nim: "199005152001", nama: "Dewi Anggraini", status: "Dosen", tanggal: "2023-10-17", masuk: "13:30", keluar: "16:45", keperluan: "Penelitian" },
    { id: 7, nim: "2021001004", nama: "Fajar Nugroho", status: "Mahasiswa", tanggal: "2023-10-18", masuk: "09:00", keluar: "12:30", keperluan: "Mencari jurnal" },
    { id: 8, nim: "-", nama: "Indah Permata", status: "Umum", tanggal: "2023-10-18", masuk: "11:15", keluar: "14:20", keperluan: "Membaca koran" },
    { id: 9, nim: "2021001005", nama: "Hendra Wijaya", status: "Mahasiswa", tanggal: "2023-10-19", masuk: "10:05", keluar: "13:40", keperluan: "Belajar kelompok" },
    { id: 10, nim: "198512102001", nama: "Lina Marlina", status: "Dosen", tanggal: "2023-10-19", masuk: "08:45", keluar: "10:30", keperluan: "Rapat" },
    { id: 11, nim: "2021001006", nama: "Rina Wijaya", status: "Mahasiswa", tanggal: "2023-10-20", masuk: "13:15", keluar: "16:45", keperluan: "Mengerjakan laporan" },
    { id: 12, nim: "2021001007", nama: "Dodi Pratama", status: "Mahasiswa", tanggal: "2023-10-20", masuk: "10:30", keluar: "14:20", keperluan: "Belajar mandiri" },
    { id: 13, nim: "-", nama: "Bambang Susilo", status: "Umum", tanggal: "2023-10-21", masuk: "09:45", keluar: "12:15", keperluan: "Membaca koran" },
    { id: 14, nim: "2021001008", nama: "Sari Dewi", status: "Mahasiswa", tanggal: "2023-10-21", masuk: "08:20", keluar: "11:30", keperluan: "Pinjam buku" },
    { id: 15, nim: "199208152001", nama: "Rudi Hartono", status: "Dosen", tanggal: "2023-10-22", masuk: "14:00", keluar: "16:45", keperluan: "Konsultasi penelitian" }
];

// Variabel global
let currentPage = 1;
const itemsPerPage = 5;
let filteredData = [...dataPengunjung];

// Fungsi untuk menampilkan data dalam tabel
function renderTable(data, page = 1) {
    const tableBody = document.getElementById('tableBody');
    const startIndex = (page - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageData = data.slice(startIndex, endIndex);
    
    tableBody.innerHTML = '';
    
    if (pageData.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8" style="text-align: center;">Tidak ada data yang ditemukan</td></tr>';
        return;
    }
    
    pageData.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${startIndex + index + 1}</td>
            <td>${item.nim}</td>
            <td>${item.nama}</td>
            <td>${item.status}</td>
            <td>${formatTanggal(item.tanggal)}</td>
            <td>${item.masuk}</td>
            <td>${item.keluar}</td>
            <td>${item.keperluan}</td>
        `;
        tableBody.appendChild(row);
    });
    
    updatePagination(data.length, page);
}

// Fungsi untuk memperbarui informasi halaman
function updatePagination(totalItems, currentPage) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const pageInfo = document.getElementById('pageInfo');
    const prevButton = document.getElementById('prevPage');
    const nextButton = document.getElementById('nextPage');
    
    pageInfo.textContent = `Halaman ${currentPage} dari ${totalPages}`;
    
    prevButton.disabled = currentPage === 1;
    nextButton.disabled = currentPage === totalPages || totalPages === 0;
}

// Fungsi untuk memformat tanggal
function formatTanggal(tanggal) {
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    return new Date(tanggal).toLocaleDateString('id-ID', options);
}

// Fungsi untuk menerapkan filter
function applyFilter() {
    const filterTanggal = document.getElementById('filterTanggal').value;
    const filterStatus = document.getElementById('filterStatus').value;
    
    filteredData = dataPengunjung.filter(item => {
        const matchTanggal = !filterTanggal || item.tanggal === filterTanggal;
        const matchStatus = filterStatus === 'semua' || item.status === filterStatus;
        
        return matchTanggal && matchStatus;
    });
    
    currentPage = 1;
    renderTable(filteredData, currentPage);
}

// Fungsi untuk mereset filter
function resetFilter() {
    document.getElementById('filterTanggal').value = '';
    document.getElementById('filterStatus').value = 'semua';
    
    filteredData = [...dataPengunjung];
    currentPage = 1;
    renderTable(filteredData, currentPage);
}

// Event listeners ketika halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Render tabel dengan data awal
    renderTable(filteredData, currentPage);
    
    // Event listener untuk tombol filter
    document.getElementById('btnFilter').addEventListener('click', applyFilter);
    
    // Event listener untuk tombol reset
    document.getElementById('btnReset').addEventListener('click', resetFilter);
    
    // Event listener untuk pagination
    document.getElementById('prevPage').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            renderTable(filteredData, currentPage);
        }
    });
    
    document.getElementById('nextPage').addEventListener('click', function() {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderTable(filteredData, currentPage);
        }
    });
});