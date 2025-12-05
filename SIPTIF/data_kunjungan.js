// data_kunjungan.js - Versi Disesuaikan dengan CSS
let currentPage = 1;
const itemsPerPage = 5;
let allData = [];
let filteredData = [];
let currentFilter = {
    tanggal: '',
    nama: '',
    keperluan: ''
};

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 Data Kunjungan - Script Loaded');
    
    // Load data pertama kali
    loadData();
    
    // Setup event listeners
    setupEventListeners();
    
    // Setup filter UI (jika belum ada)
    setupFilterUI();
});

// Setup semua event listeners
function setupEventListeners() {
    // Filter button
    const btnFilter = document.getElementById('btnFilter');
    if (btnFilter) {
        btnFilter.addEventListener('click', applyFilter);
    }
    
    // Reset filter button
    const btnReset = document.getElementById('btnReset');
    if (btnReset) {
        btnReset.addEventListener('click', resetFilter);
    }
    
    // Pagination buttons
    const prevPage = document.getElementById('prevPage');
    const nextPage = document.getElementById('nextPage');
    if (prevPage) prevPage.addEventListener('click', goToPrevPage);
    if (nextPage) nextPage.addEventListener('click', goToNextPage);
    
    // Real-time filter untuk nama
    const filterNama = document.getElementById('filterNama');
    if (filterNama) {
        filterNama.addEventListener('input', function() {
            currentFilter.nama = this.value;
            debounceApplyFilter();
        });
    }
    
    // Real-time filter untuk keperluan
    const filterKeperluan = document.getElementById('filterKeperluan');
    if (filterKeperluan) {
        filterKeperluan.addEventListener('change', function() {
            currentFilter.keperluan = this.value;
            applyFilter();
        });
    }
    
    // Real-time filter untuk tanggal
    const filterTanggal = document.getElementById('filterTanggal');
    if (filterTanggal) {
        filterTanggal.addEventListener('change', function() {
            currentFilter.tanggal = this.value;
            applyFilter();
        });
    }
}

// Setup UI filter jika belum ada
function setupFilterUI() {
    // Cek jika filter container sudah ada
    const existingFilterContainer = document.querySelector('.filter-container');
    if (existingFilterContainer) return; // Sudah ada, skip
    
    const filterSection = document.querySelector('.filter-section');
    if (!filterSection) return;
    
    // Ganti filter section dengan container yang lebih lengkap
    const newFilterHTML = `
        <div class="filter-container">
            <h5 style="margin-bottom: 20px; color: #555;">
                <i class="fas fa-filter"></i> Filter Data
            </h5>
            
            <div class="filter-row">
                <div class="filter-col">
                    <label class="filter-label" for="filterNama">
                        <i class="fas fa-user"></i> Cari Nama
                    </label>
                    <input type="text" class="filter-input" id="filterNama" placeholder="Masukkan nama...">
                </div>
                
                <div class="filter-col">
                    <label class="filter-label" for="filterTanggal">
                        <i class="fas fa-calendar"></i> Tanggal Kunjungan
                    </label>
                    <input type="date" class="filter-input" id="filterTanggal">
                </div>
                
                <div class="filter-col">
                    <label class="filter-label" for="filterKeperluan">
                        <i class="fas fa-tasks"></i> Keperluan
                    </label>
                    <select class="filter-input" id="filterKeperluan">
                        <option value="">Semua Keperluan</option>
                        <option value="Meminjam Ruangan">Meminjam Ruangan</option>
                        <option value="Mengunjungi Perpustakaan">Mengunjungi Perpustakaan</option>
                        <option value="Meminjam Alat">Meminjam Alat</option>
                        <option value="Meminjam Buku">Meminjam Buku</option>
                        <option value="Meminjam Kunci Ruangan">Meminjam Kunci Ruangan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
            
            <div class="data-summary" id="dataSummary">
                <i class="fas fa-info-circle"></i> Memuat data...
            </div>
            
            <div class="filter-actions">
                <button id="btnFilter" class="btn-primary">
                    <i class="fas fa-search"></i> Terapkan Filter
                </button>
                <button id="btnReset" class="btn-secondary">
                    <i class="fas fa-redo"></i> Reset Filter
                </button>
            </div>
            
            <div id="activeFilters" class="active-filters"></div>
            <div id="filterStats" class="filter-stats"></div>
        </div>
    `;
    
    filterSection.insertAdjacentHTML('afterend', newFilterHTML);
    filterSection.remove(); // Hapus filter section lama
    
    // Re-attach event listeners
    setTimeout(() => {
        setupEventListeners();
    }, 100);
}

// Debounce untuk real-time filtering
let debounceTimer;
function debounceApplyFilter() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        applyFilter();
    }, 300);
}

// Ambil data dari database
async function fetchDataKunjungan() {
    try {
        const response = await fetch('get_kunjungan.php');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log(`✅ Data berhasil diambil: ${data.length} records`);
        return data;
    } catch (error) {
        console.error('❌ Error mengambil data:', error);
        showToast('Gagal memuat data. Periksa koneksi internet.', 'error');
        return [];
    }
}

// Load data dan render tabel
async function loadData() {
    const tableBody = document.getElementById('tableBody');
    if (!tableBody) return;
    
    const loadingHTML = `
        <tr>
            <td colspan="8" style="text-align: center; padding: 40px;">
                <div style="display: inline-block; padding: 10px 20px; background: #f8f9fa; border-radius: 5px;">
                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                </div>
            </td>
        </tr>
    `;
    
    tableBody.innerHTML = loadingHTML;
    
    allData = await fetchDataKunjungan();
    filteredData = [...allData];
    
    // Reset ke halaman 1
    currentPage = 1;
    
    // Render tabel
    renderTable();
    updateActiveFilters();
    updateStats();
}

// Terapkan filter
function applyFilter() {
    console.log('🔍 Menerapkan filter:', currentFilter);
    
    // Reset ke halaman 1
    currentPage = 1;
    
    // Filter data
    filteredData = allData.filter(item => {
        let match = true;
        
        // Filter by tanggal
        if (currentFilter.tanggal) {
            match = match && (item.tanggal === currentFilter.tanggal);
        }
        
        // Filter by nama (case-insensitive partial match)
        if (currentFilter.nama) {
            match = match && item.nama.toLowerCase().includes(currentFilter.nama.toLowerCase());
        }
        
        // Filter by keperluan
        if (currentFilter.keperluan) {
            match = match && (item.keperluan === currentFilter.keperluan);
        }
        
        return match;
    });
    
    console.log(`📊 Hasil filter: ${filteredData.length} dari ${allData.length} records`);
    
    // Render tabel dengan data yang sudah difilter
    renderTable();
    updateActiveFilters();
    updateStats();
}

// Reset semua filter
function resetFilter() {
    console.log('🔄 Mereset filter');
    
    // Reset filter values
    const filterNama = document.getElementById('filterNama');
    const filterTanggal = document.getElementById('filterTanggal');
    const filterKeperluan = document.getElementById('filterKeperluan');
    
    if (filterNama) filterNama.value = '';
    if (filterTanggal) filterTanggal.value = '';
    if (filterKeperluan) filterKeperluan.value = '';
    
    // Reset filter object
    currentFilter = {
        tanggal: '',
        nama: '',
        keperluan: ''
    };
    
    // Reset data
    filteredData = [...allData];
    currentPage = 1;
    
    // Render ulang
    renderTable();
    updateActiveFilters();
    updateStats();
    
    showToast('Filter telah direset', 'success');
}

// Render tabel dengan data
function renderTable() {
    const tableBody = document.getElementById('tableBody');
    if (!tableBody) return;
    
    if (filteredData.length === 0) {
        const noDataHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px;">
                    <div style="color: #6c757d;">
                        <i class="fas fa-search" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                        <p style="margin: 0; font-weight: 500;">Tidak ada data yang ditemukan</p>
                        ${Object.values(currentFilter).some(val => val) ? 
                          '<p style="margin: 5px 0 0 0; font-size: 14px;">Coba ubah kriteria filter</p>' : 
                          ''}
                    </div>
                </td>
            </tr>
        `;
        tableBody.innerHTML = noDataHTML;
        updatePagination();
        return;
    }
    
    // Hitung data untuk halaman saat ini
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, filteredData.length);
    const pageData = filteredData.slice(startIndex, endIndex);
    
    let tableHTML = '';
    
    pageData.forEach((item, index) => {
        const actualIndex = startIndex + index + 1;
        
        tableHTML += `
            <tr data-id="${item.id}">
                <td>${actualIndex}</td>
                <td><strong>${escapeHTML(item.nama)}</strong></td>
                <td>${escapeHTML(item.email)}</td>
                <td>${formatTanggal(item.tanggal)}</td>
                <td><span class="time-badge">${item.masuk}</span></td>
                <td>${item.keluar ? `<span class="time-badge success">${item.keluar}</span>` : '<span class="time-badge warning">Belum keluar</span>'}</td>
                <td>
                    <span class="keperluan-badge">${escapeHTML(item.keperluan)}</span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-edit" onclick="editData(${item.id})" title="Ubah data">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-delete" onclick="hapusData(${item.id})" title="Hapus data">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tableBody.innerHTML = tableHTML;
    updatePagination();
}

// Update pagination info
function updatePagination() {
    const pageInfo = document.getElementById('pageInfo');
    const prevButton = document.getElementById('prevPage');
    const nextButton = document.getElementById('nextPage');
    
    if (!pageInfo || !prevButton || !nextButton) return;
    
    const totalPages = Math.max(1, Math.ceil(filteredData.length / itemsPerPage));
    
    pageInfo.textContent = `Halaman ${currentPage} dari ${totalPages}`;
    
    // Enable/disable buttons
    prevButton.disabled = currentPage === 1;
    nextButton.disabled = currentPage === totalPages || totalPages === 0;
    
    // Update button styles
    prevButton.style.opacity = prevButton.disabled ? '0.5' : '1';
    nextButton.style.opacity = nextButton.disabled ? '0.5' : '1';
}

// Navigasi halaman
function goToPrevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderTable();
    }
}

function goToNextPage() {
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        renderTable();
    }
}

// Update filter stats
function updateStats() {
    let statsContainer = document.getElementById('filterStats');
    
    if (!statsContainer) {
        statsContainer = document.createElement('div');
        statsContainer.id = 'filterStats';
        statsContainer.className = 'filter-stats';
        const filterContainer = document.querySelector('.filter-container');
        if (filterContainer) {
            filterContainer.appendChild(statsContainer);
        }
        return;
    }
    
    const total = allData.length;
    const filtered = filteredData.length;
    
    if (filtered === total) {
        statsContainer.innerHTML = `<i class="fas fa-info-circle"></i> Menampilkan semua ${total} data`;
    } else {
        statsContainer.innerHTML = `<i class="fas fa-filter"></i> Menampilkan ${filtered} dari ${total} data (tersaring ${total - filtered})`;
    }
}

// Update active filters display
function updateActiveFilters() {
    let activeFiltersContainer = document.getElementById('activeFilters');
    
    if (!activeFiltersContainer) {
        activeFiltersContainer = document.createElement('div');
        activeFiltersContainer.id = 'activeFilters';
        activeFiltersContainer.className = 'active-filters';
        const filterContainer = document.querySelector('.filter-container');
        if (filterContainer) {
            const filterActions = filterContainer.querySelector('.filter-actions');
            if (filterActions) {
                filterActions.after(activeFiltersContainer);
            }
        }
        return;
    }
    
    let activeFiltersHTML = '';
    
    if (currentFilter.tanggal) {
        activeFiltersHTML += `
            <span class="filter-tag">
                Tanggal: ${formatTanggal(currentFilter.tanggal)}
                <span class="remove-filter" onclick="removeFilter('tanggal')">&times;</span>
            </span>
        `;
    }
    
    if (currentFilter.nama) {
        activeFiltersHTML += `
            <span class="filter-tag">
                Nama: "${currentFilter.nama}"
                <span class="remove-filter" onclick="removeFilter('nama')">&times;</span>
            </span>
        `;
    }
    
    if (currentFilter.keperluan) {
        activeFiltersHTML += `
            <span class="filter-tag">
                Keperluan: ${currentFilter.keperluan}
                <span class="remove-filter" onclick="removeFilter('keperluan')">&times;</span>
            </span>
        `;
    }
    
    activeFiltersContainer.innerHTML = activeFiltersHTML || 
        '<span style="color: #6c757d; font-size: 14px;">Tidak ada filter aktif</span>';
}

// Hapus filter tertentu
function removeFilter(filterType) {
    console.log(`🗑️ Menghapus filter: ${filterType}`);
    
    // Reset filter value
    currentFilter[filterType] = '';
    
    // Reset input UI
    if (filterType === 'tanggal') {
        const filterTanggal = document.getElementById('filterTanggal');
        if (filterTanggal) filterTanggal.value = '';
    } else if (filterType === 'nama') {
        const filterNama = document.getElementById('filterNama');
        if (filterNama) filterNama.value = '';
    } else if (filterType === 'keperluan') {
        const filterKeperluan = document.getElementById('filterKeperluan');
        if (filterKeperluan) filterKeperluan.value = '';
    }
    
    // Apply filter ulang
    applyFilter();
}

// Helper functions
function formatTanggal(tanggal) {
    if (!tanggal) return '-';
    const date = new Date(tanggal);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function escapeHTML(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Toast notification
function showToast(message, type = 'info') {
    let toast = document.getElementById('custom-toast');
    
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'custom-toast';
        document.body.appendChild(toast);
    }
    
    // Set warna berdasarkan type
    if (type === 'success') {
        toast.style.background = '#28a745';
    } else if (type === 'error') {
        toast.style.background = '#dc3545';
    } else if (type === 'warning') {
        toast.style.background = '#ffc107';
        toast.style.color = '#212529';
    } else {
        toast.style.background = '#4a90e2';
    }
    
    toast.textContent = message;
    
    // Tampilkan toast
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 10);
    
    // Sembunyikan setelah 3 detik
    setTimeout(() => {
        toast.style.transform = 'translateX(120%)';
    }, 3000);
}

// Fungsi edit data (sesuaikan dengan modal yang ada)
function editData(id) {
    const data = filteredData.find(item => item.id == id);
    if (!data) {
        showToast('Data tidak ditemukan', 'error');
        return;
    }

    // Isi form modal
    document.getElementById('edit-id').value = data.id;
    document.getElementById('edit-nama').value = data.nama;
    document.getElementById('edit-email').value = data.email;
    document.getElementById('edit-tanggal').value = data.tanggal;
    document.getElementById('edit-masuk').value = data.masuk;
    document.getElementById('edit-keluar').value = data.keluar || '';
    document.getElementById('edit-keperluan').value = data.keperluan;

    // Tampilkan modal
    const modal = new bootstrap.Modal(document.getElementById('editDataModal'));
    modal.show();
}

// Fungsi hapus data
function hapusData(id) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
        window.location.href = "hapus_data_kunjungan.php?id=" + id;
    }
}

// Export functions
function exportToExcel() {
    const data = filteredData.length > 0 ? filteredData : allData;
    const csvContent = generateCSV(data);
    downloadCSV(csvContent, 'data_kunjungan.csv');
    showToast('Data berhasil diexport ke CSV', 'success');
}

function generateCSV(data) {
    const headers = ['No', 'Nama', 'Email', 'Tanggal', 'Masuk', 'Keluar', 'Keperluan', 'Created At'];
    const rows = data.map((item, index) => [
        index + 1,
        `"${item.nama}"`,
        `"${item.email}"`,
        item.tanggal,
        item.masuk,
        item.keluar || '-',
        `"${item.keperluan}"`,
        item.created_at
    ]);
    
    return [headers, ...rows].map(row => row.join(',')).join('\n');
}

function downloadCSV(content, filename) {
    const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function printTable() {
    const printWindow = window.open('', '_blank');
    const tableHTML = document.querySelector('.data-table').outerHTML;
    
    printWindow.document.write(`
        <html>
        <head>
            <title>Data Kunjungan - SIPTIF Polibatam</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #2c3e50; text-align: center; }
                .info { background: #f8f9fa; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background: #34495e; color: white; padding: 10px; text-align: left; }
                td { padding: 8px; border-bottom: 1px solid #ddd; }
                tr:nth-child(even) { background: #f2f2f2; }
                .footer { margin-top: 30px; text-align: center; color: #7f8c8d; font-size: 12px; }
            </style>
        </head>
        <body>
            <h1>Data Kunjungan Perpustakaan</h1>
            <div class="info">
                <strong>SIPTIF Polibatam</strong><br>
                Dicetak pada: ${new Date().toLocaleString('id-ID')}
            </div>
            ${tableHTML}
            <div class="footer">
                © ${new Date().getFullYear()} SIPTIF Polibatam - Sistem Informasi Perpustakaan
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

function reloadData() {
    showToast('Memuat ulang data...', 'info');
    loadData();
}

// Update data summary secara berkala
setInterval(() => {
    const total = allData.length || 0;
    const filtered = filteredData.length || 0;
    const summary = document.getElementById('dataSummary');
    
    if (summary) {
        if (filtered === total) {
            summary.innerHTML = `<i class="fas fa-database"></i> Total ${total} data kunjungan`;
        } else {
            summary.innerHTML = `<i class="fas fa-filter"></i> Menampilkan ${filtered} dari ${total} data`;
        }
    }
}, 1000);