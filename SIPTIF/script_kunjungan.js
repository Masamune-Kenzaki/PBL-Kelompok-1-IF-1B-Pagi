// script_kunjungan.js - Versi Ringan dan Cepat
(function() {
    'use strict';
    
    // Mode development (false untuk production)
    const DEBUG_MODE = false;
    
    // Cache DOM elements
    let form, popupOverlay, popupBtn;
    
    document.addEventListener('DOMContentLoaded', init);
    
    function init() {
        // Get elements
        form = document.getElementById('kunjunganForm');
        popupOverlay = document.getElementById('popupOverlay');
        popupBtn = document.getElementById('popupBtn');
        
        // Validasi elemen
        if (!form || !popupOverlay) {
            if (DEBUG_MODE) console.error('Elemen penting tidak ditemukan');
            return;
        }
        
        // Setup defaults
        setupDefaults();
        
        // Setup event listeners
        setupEvents();
        
        if (DEBUG_MODE) console.log('✅ Aplikasi siap');
    }
    
    function setupDefaults() {
        // Tanggal default (hari ini)
        const today = new Date();
        const dateStr = today.toISOString().split('T')[0];
        
        // Waktu default (jam sekarang + 1 jam)
        const nextHour = new Date(today.getTime() + 60 * 60 * 1000);
        const timeStr = `${String(nextHour.getHours()).padStart(2, '0')}:${String(nextHour.getMinutes()).padStart(2, '0')}`;
        
        document.getElementById('tanggal').value = dateStr;
        document.getElementById('tanggal').setAttribute('min', dateStr);
        document.getElementById('waktu').value = timeStr;
    }
    
    function setupEvents() {
        // Form submission
        form.addEventListener('submit', handleFormSubmit);
        
        // Popup close
        if (popupBtn) {
            popupBtn.addEventListener('click', hidePopup);
        }
        
        // Close popup on overlay click
        popupOverlay.addEventListener('click', function(e) {
            if (e.target === popupOverlay) hidePopup();
        });
        
        // Close popup on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && popupOverlay.classList.contains('active')) {
                hidePopup();
            }
        });
    }
    
    async function handleFormSubmit(e) {
        e.preventDefault();
        
        // Validasi cepat
        if (!validateQuick()) return;
        
        // Disable submit button
        const submitBtn = form.querySelector('.btn-submit');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Menyimpan...';
        submitBtn.disabled = true;
        
        try {
            // Kirim data
            const response = await fetch('save_kunjungan.php', {
                method: 'POST',
                body: new FormData(form)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showPopup();
                form.reset();
                setupDefaults(); // Reset ke nilai default
            } else {
                alert('⚠️ ' + (result.message || 'Gagal menyimpan data'));
            }
        } catch (error) {
            if (DEBUG_MODE) console.error('Fetch error:', error);
            alert('❌ Gagal terhubung ke server');
        } finally {
            // Reset button
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    }
    
    function validateQuick() {
        // Validasi nama
        const nama = document.getElementById('nama').value.trim();
        if (nama.length < 2) {
            alert('❌ Nama minimal 2 karakter');
            return false;
        }
        
        // Validasi email sederhana
        const email = document.getElementById('email').value.trim();
        if (!email.includes('@') || !email.includes('.')) {
            alert('❌ Email tidak valid');
            return false;
        }
        
        // Validasi keperluan
        const keperluan = document.getElementById('keperluan').value;
        if (!keperluan) {
            alert('❌ Pilih keperluan kunjungan');
            return false;
        }
        
        return true;
    }
    
    function showPopup() {
        popupOverlay.classList.add('active');
        // Auto hide setelah 2.5 detik
        setTimeout(hidePopup, 2500);
    }
    
    function hidePopup() {
        popupOverlay.classList.remove('active');
    }
    
    // Nonaktifkan console.log di production
    if (!DEBUG_MODE) {
        console.log = function() {};
        console.error = function() {};
        console.warn = function() {};
    }
})();