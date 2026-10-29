import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const values = JSON.parse(canvas.dataset.values || '[]');
        const type = canvas.dataset.chart === 'trend' ? 'line' : 'bar';
        new Chart(canvas, {
            type,
            data: { labels, datasets: [{ label: 'Jumlah laporan', data: values, borderColor: '#c2410c', backgroundColor: '#fdba74', tension: 0.25 }] },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
        });
    });
});
