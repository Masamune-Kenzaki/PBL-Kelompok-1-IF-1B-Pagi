// Data contoh jadwal kunjungan dengan NIM
const dataJadwal = [
    { 
        id: 1, 
        nim: "2021001001",
        nama: "Ahmad Rizki", 
        instansi: "Universitas Indonesia",
        tanggal: "2023-10-20", 
        waktu: "08:30 - 12:00",
        keperluan: "Konsultasi Skripsi",
        status: "berlangsung"
    },
    { 
        id: 2, 
        nim: "2021001002",
        nama: "Siti Nurhaliza", 
        instansi: "Universitas Indonesia",
        tanggal: "2023-10-20", 
        waktu: "09:00 - 11:30",
        keperluan: "Penelitian",
        status: "terjadwal"
    },
    { 
        id: 3, 
        nim: "SMA001",
        nama: "Budi Santoso", 
        instansi: "SMA Negeri 1 Jakarta",
        tanggal: "2023-10-20", 
        waktu: "10:15 - 15:00",
        keperluan: "Studi Tour",
        status: "terjadwal"
    },
    { 
        id: 4, 
        nim: "-",
        nama: "Maya Sari", 
        instansi: "Umum",
        tanggal: "2023-10-20", 
        waktu: "13:00 - 16:30",
        keperluan: "Membaca",
        status: "terjadwal"
    },
    { 
        id: 5, 
        nim: "2021001003",
        nama: "Rizki Pratama", 
        instansi: "Universitas Indonesia",
        tanggal: "2023-10-21", 
        waktu: "08:00 - 10:30",
        keperluan: "Pinjam Buku",
        status: "terjadwal"
    },
    { 
        id: 6, 
        nim: "198011022001",
        nama: "Dewi Anggraini", 
        instansi: "Universitas Indonesia",
        tanggal: "2023-10-21", 
        waktu: "09:30 - 12:00",
        keperluan: "Konsultasi Tesis",
        status: "terjadwal"
    },
    { 
        id: 7, 
        nim: "SMP005",
        nama: "Fajar Nugroho", 
        instansi: "SMP Negeri 5 Jakarta",
        tanggal: "2023-10-21", 
        waktu: "10:00 - 14:00",
        keperluan: "Kunjungan Edukasi",
        status: "terjadwal"
    },
    { 
        id: 8, 
        nim: "-",
        nama: "Indah Permata", 
        instansi: "Umum",
        tanggal: "2023-10-19", 
        waktu: "14:00 - 16:00",
        keperluan: "Membaca Koran",
        status: "selesai"
    },
    { 
        id: 9, 
        nim: "2021001004",
        nama: "Hendra Wijaya", 
        instansi: "Universitas Indonesia",
        tanggal: "2023-10-19", 
        waktu: "08:30 - 11:00",
        keperluan: "Belajar Kelompok",
        status: "selesai"
    },
    { 
        id: 10, 
        nim: "199005152001",
        nama: "Lina Marlina", 
        instansi: "Universitas Indonesia",
        tanggal: "2023-10-18", 
        waktu: "13:30 - 15:30",
        keperluan: "Rapat",
        status: "dibatalkan"
    },
    { 
        id: 11, 
        nim: "2021001005",
        nama: "Rina Wijaya", 
        instansi: "Universitas Indonesia",
        tanggal: "2023-10-22", 
        waktu: "10:00 - 12:30",
        keperluan: "Mengerjakan Tugas",
        status: "terjadwal"
    },
    { 
        id: 12, 
        nim: "2021001006",
        nama: "Dodi Pratama", 
        instansi: "Universitas Indonesia",
        tanggal: "2023-10-22", 
        waktu: "13:15 - 16:45",
        keperluan: "Belajar Mandiri",
        status: "terjadwal"
    },
    { 
        id: 13, 
        nim: "-",
        nama: "Bambang Susilo", 
        instansi: "Umum",
        tanggal: "2023-10-23", 
        waktu: "09:45 - 12:15",
        keperluan: "Membaca Buku",
        status: "terjadwal"
    },
    { 
        id: 14, 
        nim: "2021001007",
        nama: "Sari Dewi", 
        instansi: "Universitas Indonesia",
        tanggal: "2023-10-23", 
        waktu: "08:20 - 11:30",
        keperluan: "Pinjam Buku Referensi",
        status: "terjadwal"
    },
    { 
        id: 15, 
        nim: "199208152001",
        nama: "Rudi Hartono", 
        instansi: "Universitas Indonesia",
        tanggal: "2023-10-24", 
        waktu: "14:00 - 16:45",
        keperluan: "Konsultasi Penelitian",
        status: "terjadwal"
    }
];

// Variabel global
let currentPage = 1;
const itemsPerPage = 8;
let filteredData = [...dataJadwal];

// Fungsi untuk menampilkan data dalam tabel
function renderTable(data, page = 1) {
    const tableBody = document.getElementById('tableBody');
    const startIndex = (page - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageData = data.slice(startIndex, endIndex);
    
    tableBody.innerHTML = '';
    
    if (pageData.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="9" style="text-align: center;">Tidak ada jadwal yang ditemukan</td></tr>';
        return;
    }
    
    pageData.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${startIndex + index + 1}</td>
            <td>${item.nim}</td>
            <td>${item.nama}</td>
            <td>${item.instansi}</td>
            <td>${formatTanggal(item.tanggal)}</td>
            <td>${item.waktu}</td>
            <td>${item.keperluan}</td>
            <td><span class="status-badge status-${item.status}">${getStatusText(item.status)}</span></td>
            <td>
                <button class="btn-action btn-view" data-id="${item.id}">Lihat</button>
                <button class="btn-action btn-edit" data-id="${item.id}">Edit</button>
                <button class="btn-action btn-delete" data-id="${item.id}">Hapus</button>
            </td>
        `;
        tableBody.appendChild(row);
    });
    
    updatePagination(data.length, page);
    
    // Tambahkan event listener untuk tombol aksi
    attachActionListeners();
}

// Fungsi untuk mendapatkan teks status
function getStatusText(status) {
    const statusMap = {
        'terjadwal': 'Terjadwal',
        'berlangsung': 'Berlangsung',
        'selesai': 'Selesai',
        'dibatalkan': 'Dibatalkan'
    };
    return statusMap[status] || status;
}

// Fungsi untuk menambahkan event listener pada tombol aksi
function attachActionListeners() {
    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = parseInt(this.getAttribute('data-id'));
            viewDetail(id);
        });
    });
    
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = parseInt(this.getAttribute('data-id'));
            editJadwal(id);
        });
    });
    
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = parseInt(this.getAttribute('data-id'));
            deleteJadwal(id);
        });
    });
}

// Fungsi untuk melihat detail jadwal
function viewDetail(id) {
    const jadwal = dataJadwal.find(item => item.id === id);
    if (jadwal) {
        alert(`Detail Jadwal Kunjungan:\n\nNIM: ${jadwal.nim}\nNama: ${jadwal.nama}\nInstansi: ${jadwal.instansi}\nTanggal: ${formatTanggal(jadwal.tanggal)}\nWaktu: ${jadwal.waktu}\nKeperluan: ${jadwal.keperluan}\nStatus: ${getStatusText(jadwal.status)}`);
    }
}

// Fungsi untuk mengedit jadwal
function editJadwal(id) {
    const jadwal = dataJadwal.find(item => item.id === id);
    if (jadwal) {
        // Simulasi edit - dalam implementasi nyata akan membuka form edit
        const newStatus = prompt(`Ubah status untuk ${jadwal.nama} (NIM: ${jadwal.nim}):\n1. Terjadwal\n2. Berlangsung\n3. Selesai\n4. Dibatalkan\n\nMasukkan pilihan (1-4):`);
        
        const statusMap = {'1': 'terjadwal', '2': 'berlangsung', '3': 'selesai', '4': 'dibatalkan'};
        if (newStatus && statusMap[newStatus]) {
            jadwal.status = statusMap[newStatus];
            renderTable(filteredData, currentPage);
            alert('Status jadwal berhasil diubah!');
        }
    }
}

// Fungsi untuk menghapus jadwal
function deleteJadwal(id) {
    if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
        const index = dataJadwal.findIndex(item => item.id === id);
        if (index !== -1) {
            dataJadwal.splice(index, 1);
            applyFilter(); // Refresh data yang difilter
            alert('Jadwal berhasil dihapus!');
        }
    }
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
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    filteredData = dataJadwal.filter(item => {
        const matchTanggal = !filterTanggal || item.tanggal === filterTanggal;
        const matchStatus = filterStatus === 'semua' || item.status === filterStatus;
        const matchSearch = !searchTerm || 
            item.nim.toLowerCase().includes(searchTerm) ||
            item.nama.toLowerCase().includes(searchTerm) ||
            item.instansi.toLowerCase().includes(searchTerm) ||
            item.keperluan.toLowerCase().includes(searchTerm);
        
        return matchTanggal && matchStatus && matchSearch;
    });
    
    currentPage = 1;
    renderTable(filteredData, currentPage);
}

// Fungsi untuk mereset filter
function resetFilter() {
    document.getElementById('filterTanggal').value = '';
    document.getElementById('filterStatus').value = 'semua';
    document.getElementById('searchInput').value = '';
    
    filteredData = [...dataJadwal];
    currentPage = 1;
    renderTable(filteredData, currentPage);
}

// Event listeners ketika halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Set tanggal default ke hari ini
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('filterTanggal').value = today;
    
    // Render tabel dengan data awal
    renderTable(filteredData, currentPage);
    
    // Event listener untuk tombol filter
    document.getElementById('btnFilter').addEventListener('click', applyFilter);
    
    // Event listener untuk tombol reset
    document.getElementById('btnReset').addEventListener('click', resetFilter);
    
    // Event listener untuk pencarian real-time
    document.getElementById('searchInput').addEventListener('input', applyFilter);
    
    // Event listener untuk perubahan select
    document.getElementById('filterStatus').addEventListener('change', applyFilter);
    document.getElementById('filterTanggal').addEventListener('change', applyFilter);
    
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