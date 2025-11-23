// Variabel global
let currentPage = 1;
const itemsPerPage = 5;
let filteredData = [];

// Fungsi untuk mengambil data dari database
async function fetchDataKunjungan() {
    try {
        const response = await fetch('get_kunjungan.php');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching data:', error);
        return [];
    }
}

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
            <td>${item.keluar || '-'}</td>
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
    
    filteredData = filteredData.filter(item => {
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
    
    currentPage = 1;
    loadData();
}

// Fungsi untuk memuat data
async function loadData() {
    const data = await fetchDataKunjungan();
    filteredData = data;
    renderTable(filteredData, currentPage);
}

// Event listeners ketika halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Load data dari database
    loadData();
    
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