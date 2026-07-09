<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Absensi — <?php echo e($event?->nama ?? 'Konvensi Improvement Dharma'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <style>
        body {
            background: #f7f8fa;
            <?php if($event?->wallpaper_url): ?>
            background: linear-gradient(rgba(247,248,250,0.88), rgba(247,248,250,0.88)),
                        url('<?php echo e($event->wallpaper_url); ?>') center/cover fixed no-repeat;
            <?php endif; ?>
        }
        @keyframes fadeInRow {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .new-row { animation: fadeInRow 0.4s ease forwards; }
    </style>
</head>
<body class="min-h-screen font-sans text-gray-800" x-data="liveAbsensi()">


<header class="px-6 py-4 flex items-center justify-between border-b bg-white"
        style="border-color:rgba(36,76,107,0.12); position:sticky; top:0; z-index:10">
    <div class="flex items-center gap-4">
        <?php if($event?->logo): ?>
            <img src="<?php echo e(asset('storage/'.$event->logo)); ?>" class="h-10 w-auto object-contain" alt="">
        <?php endif; ?>
        <img src="<?php echo e(asset('images/dharma-group.png')); ?>" class="h-8 w-auto object-contain" alt="">
        <div>
            <h1 class="font-bold leading-tight" style="color:#244C6B">Live Absensi</h1>
            <p class="text-gray-500 text-xs"><?php echo e($event?->nama ?? 'Konvensi Improvement Dharma'); ?></p>
        </div>
    </div>
    <div class="flex items-center gap-2 text-xs text-gray-400">
        <div class="w-2 h-2 rounded-full bg-green-500 animate-ping"></div>
        Auto-refresh 5 detik
    </div>
</header>

<div class="max-w-5xl mx-auto px-4 py-6">

    
    <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="rounded-2xl p-5 text-center flex flex-col items-center justify-center bg-white"
             style="border:1px solid rgba(36,76,107,0.12)">
            <p class="text-5xl font-black" style="color:#22c55e" x-text="data.totalAttended ?? 0"></p>
            <p class="text-gray-500 text-sm mt-1">Hadir Fisik</p>
        </div>
        <div class="flex flex-col gap-4">
            <div class="rounded-2xl p-4 text-center bg-white" style="border:1px solid rgba(36,76,107,0.12)">
                <p class="text-3xl font-black" style="color:#244C6B" x-text="data.totalInvited ?? 0"></p>
                <p class="text-gray-500 text-sm mt-1">Total Undangan</p>
            </div>
            <div class="rounded-2xl p-4 text-center bg-white" style="border:1px solid rgba(36,76,107,0.12)">
                <p class="text-3xl font-black" :style="pctColor"
                   x-text="data.totalInvited > 0 ? Math.round((data.totalAttended/data.totalInvited)*100) + '%' : '0%'"></p>
                <p class="text-gray-500 text-sm mt-1">Persentase</p>
            </div>
        </div>
    </div>

    
    <div class="text-center mb-4">
        <img src="<?php echo e(asset('images/dharma-group.png')); ?>" class="h-10 w-auto object-contain mx-auto mb-2" alt="">
        <h5 class="font-bold text-lg" style="color:#244C6B">Grafik Kehadiran Peserta Konvensi Improvement Dharma 31</h5>
    </div>

    
    <div class="rounded-2xl p-4 mb-4 bg-white" style="border:1px solid rgba(36,76,107,0.12)">
        <div class="relative" :style="'height:' + Math.max(180, (data.bySubco?.length || 0) * 38) + 'px'">
            <canvas id="subco-chart"></canvas>
        </div>
        <template x-if="!data.bySubco?.length">
            <div class="text-center text-gray-400 py-8">
                <p>Belum ada data</p>
            </div>
        </template>
    </div>
</div>

<script>
function liveAbsensi() {
    return {
        data: {},
        chart: null,
        get pctColor() {
            const pct = this.data.totalInvited > 0
                ? Math.round((this.data.totalAttended / this.data.totalInvited) * 100)
                : 0;
            if (pct >= 80) return 'color: #16a34a';
            if (pct >= 50) return 'color: #d97706';
            return 'color: #dc2626';
        },
        renderChart() {
            const subco = this.data.bySubco || [];
            const ctx = document.getElementById('subco-chart');
            if (!ctx || !window.Chart) return;

            const labels = subco.map(s => s.subco || '(Lainnya)');
            const hadir  = subco.map(s => s.hadir);
            const total  = subco.map(s => s.total);
            const pct    = subco.map(s => s.pct);

            if (this.chart) {
                this.chart.data.labels = labels;
                this.chart.data.datasets[0].data = hadir;
                this.chart.data.datasets[1].data = total;
                this.chart.options.plugins.datalabels._pct = pct;
                this.chart.update();
                return;
            }

            this.chart = new Chart(ctx, {
                type: 'bar',
                plugins: [ChartDataLabels],
                data: {
                    labels,
                    datasets: [
                        { label: 'Hadir', data: hadir, backgroundColor: '#22c55e', borderRadius: 6, maxBarThickness: 18 },
                        { label: 'Total Undangan', data: total, backgroundColor: 'rgba(36,76,107,0.18)', borderRadius: 6, maxBarThickness: 18, datalabels: { display: false } },
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { right: 36 } },
                    plugins: {
                        legend: { labels: { color: '#244C6B' } },
                        datalabels: {
                            _pct: pct,
                            anchor: 'end',
                            align: 'end',
                            color: '#16a34a',
                            font: { weight: 'bold', size: 11 },
                            formatter: (value, ctx) => (ctx.chart.options.plugins.datalabels._pct?.[ctx.dataIndex] ?? 0) + '%',
                        },
                    },
                    scales: {
                        x: { beginAtZero: true, ticks: { color: '#7B91A1' }, grid: { color: 'rgba(36,76,107,0.06)' } },
                        y: { ticks: { color: '#244C6B' }, grid: { display: false } },
                    },
                }
            });
        },
        async fetch() {
            try {
                const res = await window.fetch('/liveabsensi/data');
                this.data = await res.json();
                this.$nextTick(() => this.renderChart());
            } catch(e) {}
        },
        init() {
            this.fetch();
            setInterval(() => this.fetch(), 5000);
        }
    }
}
</script>
</body>
</html>
<?php /**PATH /var/www/AbsenKID/resources/views/public/liveabsensi.blade.php ENDPATH**/ ?>