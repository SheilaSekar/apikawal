import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const values = JSON.parse(canvas.dataset.values || '[]');
        const type = canvas.dataset.chart === 'trend' ? 'line' : 'bar';
        const cssToken = (name, fallback) => getComputedStyle(document.documentElement).getPropertyValue(name).trim() || fallback;
        const primary = cssToken('--primary', '#123b46');
        const accent = cssToken('--accent', '#c2410c');
        const palette = [primary, accent, cssToken('--priority-low', '#0f766e'), cssToken('--priority-medium', '#b45309'), cssToken('--priority-critical', '#b91c1c'), cssToken('--muted', '#475569')];
        const isTrend = type === 'line';
        new Chart(canvas, {
            type,
            data: { labels, datasets: [{
                label: 'Jumlah laporan',
                data: values,
                borderColor: isTrend ? accent : palette,
                backgroundColor: isTrend ? `${accent}24` : palette,
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
                    tooltip: { backgroundColor: primary, titleColor: '#ffffff', bodyColor: '#ffffff', padding: 12, cornerRadius: 10 },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#475569', maxRotation: 0, autoSkip: true } },
                    y: { beginAtZero: true, ticks: { precision: 0, color: '#475569' }, grid: { color: 'rgb(129 146 135 / 22%)' } },
                },
            },
        });
    });
});
