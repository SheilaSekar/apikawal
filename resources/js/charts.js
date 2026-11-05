import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const values = JSON.parse(canvas.dataset.values || '[]');
        const type = canvas.dataset.chart === 'trend' ? 'line' : 'bar';
        const palette = ['#123b46', '#c2410c', '#0f766e', '#b45309', '#b91c1c', '#475569'];
        const isTrend = type === 'line';
        new Chart(canvas, {
            type,
            data: { labels, datasets: [{
                label: 'Jumlah laporan',
                data: values,
                borderColor: isTrend ? '#c2410c' : palette,
                backgroundColor: isTrend ? 'rgb(194 65 12 / 14%)' : palette,
                borderWidth: isTrend ? 3 : 1,
                pointRadius: isTrend ? 3 : 0,
                pointHoverRadius: 5,
                tension: 0.32,
                fill: isTrend,
                borderRadius: isTrend ? 0 : 8,
            }] },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#123b46', titleColor: '#ffffff', bodyColor: '#ffffff', padding: 12, cornerRadius: 10 },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#475569', maxRotation: 0, autoSkip: true } },
                    y: { beginAtZero: true, ticks: { precision: 0, color: '#475569' }, grid: { color: 'rgb(129 146 135 / 22%)' } },
                },
            },
        });
    });
});
