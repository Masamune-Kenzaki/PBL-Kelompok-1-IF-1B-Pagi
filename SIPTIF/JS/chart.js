// Chart.js Implementation
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('visitorChart');
    
    if (ctx) {
        const visitorChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Pengunjung Per Bulan',
                    data: [65, 59, 80, 81, 56, 55, 40, 50, 70, 85, 90, 100],
                    backgroundColor: 'rgba(25, 118, 210, 0.2)',
                    borderColor: 'rgba(25, 118, 210, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Statistik Pengunjung Tahunan'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Pengunjung'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                }
            }
        });
    }
});