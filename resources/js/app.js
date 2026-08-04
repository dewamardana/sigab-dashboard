

import Alpine from 'alpinejs';
import './bootstrap';
import 'flowbite';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import ApexCharts from 'apexcharts';

// Fix ikon marker default Leaflet yang rusak saat di-bundle Vite/webpack
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

// Preset config ApexCharts bergaya lembut — grid nyaris tak terlihat,
// axis minimalis, dipakai bersama di index/show/device
window.softChartDefaults = {
    chart: { fontFamily: 'Inter, sans-serif', toolbar: { show: false }, foreColor: '#9aa6a4' },
    dataLabels: { enabled: false },
    stroke: { width: 3, curve: 'smooth' },
    markers: { size: 0, hover: { size: 5 } },
    grid: {
        show: true,
        borderColor: '#f1f3f2',
        strokeDashArray: 0,
        xaxis: { lines: { show: true } },
        yaxis: { lines: { show: true } },
        padding: { left: 10, right: 10 },
    },
    tooltip: { theme: 'light', marker: { show: true }, fixed: { enabled: false } },
    xaxis: {
        axisBorder: { show: true, color: '#e2e6e5' },
        axisTicks: { show: true, color: '#e2e6e5' },
        crosshairs: { show: true, stroke: { color: '#c7cecd', width: 1, dashArray: 3 } },
        labels: { style: { colors: '#9aa6a4', fontSize: '11px' } },
    },
};

window.L = L;

window.ApexCharts = ApexCharts;
window.Alpine = Alpine;

// ============================================================================
// Status badge — satu sumber kebenaran untuk warna/label AMAN/SIAGA/BAHAYA,
// dipakai baik saat render awal (lewat <x-status-badge>) maupun saat update
// real-time (Echo). Menyimpan style di sini mencegah dua tempat (Blade & JS)
// jadi tidak sinkron kalau suatu saat warna status diubah.
// ============================================================================
window.STATUS_STYLES = {
    BAHAYA: { bg: 'bg-status-bahaya/10', text: 'text-status-bahaya', dot: 'bg-status-bahaya', hex: '#e02424', label: 'Bahaya' },
    SIAGA: { bg: 'bg-status-siaga/10', text: 'text-status-siaga', dot: 'bg-status-siaga', hex: '#e3a008', label: 'Siaga' },
    AMAN: { bg: 'bg-status-aman/10', text: 'text-status-aman', dot: 'bg-status-aman', hex: '#2ba84a', label: 'Aman' },
};
window.statusStyle = function (status) {
    return window.STATUS_STYLES[status] || { bg: 'bg-neutral-100', text: 'text-neutral-500', dot: 'bg-neutral-300', hex: '#9aa6a4', label: 'Belum ada data' };
};
// el = elemen <span> hasil <x-status-badge>, sudah punya data-base-class
// dari server supaya JS tidak perlu tahu daftar kelas padding/ukurannya.
window.applyStatusBadge = function (el, status) {
    if (!el) return;
    const s = window.statusStyle(status);
    el.className = `${el.dataset.baseClass} ${s.bg} ${s.text}`;
    const dot = el.querySelector('[data-badge-dot]');
    const label = el.querySelector('[data-badge-label]');
    if (dot) dot.className = `w-1.5 h-1.5 rounded-full shrink-0 ${s.dot}`;
    if (label) label.textContent = s.label;
};

// ============================================================================
// Gauge TMA — setengah lingkaran dengan pita AMAN/SIAGA/BAHAYA yang
// proporsinya dihitung dari threshold ASLI milik device (bukan angka tetap),
// karena tiap device di SIGAB bisa punya threshold berbeda.
// ============================================================================
window.createTmaGauge = function (containerEl, { value, siaga, bahaya, max, variant = 'normal' }) {
    const cx = 150, cy = 150, r = 120;
    const toXY = (v) => {
        const clamped = Math.max(0, Math.min(v, max));
        const angle = (180 - (clamped / max) * 180) * (Math.PI / 180);
        return { x: cx + r * Math.cos(angle), y: cy - r * Math.sin(angle) };
    };
    const arcPath = (v0, v1) => {
        const p0 = toXY(v0), p1 = toXY(v1);
        const large = (v1 - v0) / max > 0.5 ? 1 : 0;
        return `M ${p0.x.toFixed(2)} ${p0.y.toFixed(2)} A ${r} ${r} 0 ${large} 1 ${p1.x.toFixed(2)} ${p1.y.toFixed(2)}`;
    };

    // Varian "hero" dipakai di kartu gradasi hijau (angka besar) — pita
    // memakai putih tembus pandang bertingkat supaya tetap kontras di atas
    // gradasi, sementara varian "normal" tetap pakai warna status asli.
    const bandColors = variant === 'hero'
        ? ['rgba(255,255,255,0.9)', 'rgba(255,233,173,0.95)', 'rgba(255,196,196,0.95)']
        : [window.STATUS_STYLES.AMAN.hex, window.STATUS_STYLES.SIAGA.hex, window.STATUS_STYLES.BAHAYA.hex];
    const needleColor = variant === 'hero' ? '#ffffff' : '#040f0f';
    const labelColor = variant === 'hero' ? 'rgba(255,255,255,0.7)' : '#6b7876';
    const showLabels = variant !== 'hero';

    containerEl.innerHTML = `
    <svg viewBox="0 0 300 160" class="w-full max-w-xs mx-auto">
      <path d="${arcPath(0, siaga)}" stroke="${bandColors[0]}" stroke-width="15" fill="none"/>
      <path d="${arcPath(siaga, bahaya)}" stroke="${bandColors[1]}" stroke-width="15" fill="none"/>
      <path d="${arcPath(bahaya, max)}" stroke="${bandColors[2]}" stroke-width="15" fill="none"/>
      ${showLabels ? `<g font-family="JetBrains Mono, monospace" font-size="9" fill="${labelColor}">
        <text x="30" y="146" text-anchor="middle">0</text>
        <text x="150" y="20" text-anchor="middle">${Math.round(max / 2)}</text>
        <text x="270" y="146" text-anchor="middle">${max}</text>
      </g>` : ''}
      <g data-needle>
        <line x1="150" y1="150" x2="60" y2="150" stroke="${needleColor}" stroke-width="3" stroke-linecap="round"/>
      </g>
      <circle cx="150" cy="150" r="5" fill="${needleColor}"/>
    </svg>`;

    const needle = containerEl.querySelector('[data-needle]');
    const update = (v) => {
        const angle = Math.max(0, Math.min(180, (v / max) * 180));
        needle.setAttribute('transform', `rotate(${angle} 150 150)`);
    };
    update(value);
    return { update };
};

// ============================================================================
// Bar gauge — dipakai untuk Curah Hujan, yang secara fisik lebih pas
// digambarkan sebagai penampung (silinder/bar) daripada busur seperti TMA.
// Threshold-nya juga per-device, sama seperti gauge TMA.
// ============================================================================
window.createBarGauge = function (containerEl, { value, siaga, bahaya, max, size = 'md' }) {
    const pct = (v) => Math.max(0, Math.min(100, (v / max) * 100));
    const siagaPct = pct(siaga), bahayaPct = pct(bahaya), valPct = pct(value);
    const trackH = size === 'sm' ? '6px' : '10px';
    const markerD = size === 'sm' ? 10 : 14;
    // Pakai style inline (bukan kelas Tailwind seperti bg-status-aman/70) supaya
    // warnanya tidak bergantung pada Tailwind content-scan menemukan kelas ini —
    // kelas yang cuma dirakit di app.js gampang ke-purge dari build produksi.
    const aman = window.STATUS_STYLES.AMAN.hex, siagaHex = window.STATUS_STYLES.SIAGA.hex, bahayaHex = window.STATUS_STYLES.BAHAYA.hex;

    containerEl.innerHTML = `
    <div style="position:relative">
      <div style="height:${trackH};border-radius:999px;overflow:hidden;background:#f1f3f2;display:flex">
        <div style="width:${siagaPct}%;background:${aman};opacity:0.7"></div>
        <div style="width:${bahayaPct - siagaPct}%;background:${siagaHex};opacity:0.7"></div>
        <div style="width:${100 - bahayaPct}%;background:${bahayaHex};opacity:0.7"></div>
      </div>
      <div style="position:absolute;top:50%;transform:translateY(-50%);border-radius:999px;background:#040f0f;
        box-shadow:0 0 0 2px #fcfffc, 0 1px 3px rgba(0,0,0,0.2);
        width:${markerD}px;height:${markerD}px;left:calc(${valPct}% - ${markerD / 2}px)"></div>
    </div>`;
};

// Klasifikasi nilai ke zona AMAN/SIAGA/BAHAYA berdasarkan threshold device —
// dipakai di kartu-kartu ringkas (bukan gauge penuh) untuk pewarnaan titik.
window.classifyValue = function (value, siaga, bahaya) {
    if (value === null || value === undefined) return null;
    if (value < siaga) return 'AMAN';
    if (value < bahaya) return 'SIAGA';
    return 'BAHAYA';
};

// ============================================================================
// Panel perbandingan — dipakai di halaman lokasi (semua device) dan halaman
// device (device ini + saudara-saudaranya di lokasi yang sama). Sengaja
// menampilkan SATU device + SATU parameter sekaligus (lewat tab), bukan
// menumpuk banyak garis warna dalam satu grafik — supaya tidak perlu
// legenda warna-per-device yang gampang bingung dibaca.
// ============================================================================
window.renderComparisonPanel = function (root, { charts, devices, defaultDeviceId }) {
    if (!root || !charts || !charts.length || !devices || !devices.length) return null;

    const chartsByCode = {};
    charts.forEach((c) => {
        const seriesByDevice = {};
        c.series.forEach((s) => { seriesByDevice[s.device_id] = s.data.map((p) => [p.x, p.y]); });
        chartsByCode[c.code] = { ...c, seriesByDevice };
    });

    const deviceTabsEl = root.querySelector('[data-device-tabs]');
    const metricTabsEl = root.querySelector('[data-metric-tabs]');
    const chartEl = root.querySelector('[data-chart-el]');
    const emptyEl = root.querySelector('[data-empty-state]');
    const rangeToggle = root.querySelector('[data-range-toggle]');
    const rangeMenu = root.querySelector('[data-range-menu]');
    const rangeLabel = root.querySelector('[data-range-label]');

    let activeDevice = defaultDeviceId && devices.some((d) => d.device_id === defaultDeviceId)
        ? defaultDeviceId : devices[0].device_id;
    let activeCode = charts[0].code;
    let rangeDays = 7;
    let chart = null;

    function filterPoints(points, days) {
        if (days === 0) return points;
        const cutoff = Date.now() - days * 24 * 60 * 60 * 1000;
        return points.filter((p) => p[0] >= cutoff);
    }

    function renderDeviceTabs() {
        deviceTabsEl.innerHTML = '';
        devices.forEach((d) => {
            const selected = d.device_id === activeDevice;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `text-[12px] font-medium px-[10px] py-[5px] rounded-full border transition inline-flex items-center gap-[6px] ${selected ? 'bg-primary-600 text-white border-primary-600' : 'bg-neutral-50 text-neutral-600 border-neutral-200'}`;
            const style = window.statusStyle(d.status);
            btn.innerHTML = `<span class="w-[6px] h-[6px] rounded-full shrink-0" style="background:${style.hex}"></span>${d.name}`;
            btn.addEventListener('click', () => { activeDevice = d.device_id; renderDeviceTabs(); renderChart(); });
            deviceTabsEl.appendChild(btn);
        });
    }

    function renderMetricTabs() {
        metricTabsEl.innerHTML = '';
        charts.forEach((c) => {
            const selected = c.code === activeCode;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `text-[12px] font-medium px-[10px] py-[5px] rounded-md transition ${selected ? 'bg-neutral-950 text-white' : 'bg-neutral-100 text-neutral-600'}`;
            btn.textContent = c.name;
            btn.addEventListener('click', () => { activeCode = c.code; renderMetricTabs(); renderChart(); });
            metricTabsEl.appendChild(btn);
        });
    }

    function renderChart() {
        const entry = chartsByCode[activeCode];
        const points = (entry && entry.seriesByDevice[activeDevice]) || [];
        const filtered = filterPoints(points, rangeDays);

        if (!filtered.length) {
            if (chartEl) chartEl.style.display = 'none';
            if (emptyEl) emptyEl.style.display = '';
            return;
        }
        chartEl.style.display = '';
        emptyEl.style.display = 'none';

        const seriesData = [{ name: entry.name, data: filtered }];
        if (!chart) {
            chart = new ApexCharts(chartEl, {
                ...window.softChartDefaults,
                chart: { ...window.softChartDefaults.chart, height: 240, type: 'area' },
                series: seriesData,
                colors: ['#248232'],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.22, opacityTo: 0, stops: [0, 90, 100] } },
                tooltip: {
                    ...window.softChartDefaults.tooltip,
                    x: { format: 'dd MMM HH:mm' },
                    y: {
                        formatter: (val) => (val === null || val === undefined ? '-' : `${val} ${entry.unit || ''}`.trim()),
                    },
                },
                xaxis: { ...window.softChartDefaults.xaxis, type: 'datetime' },
            });
            chart.render();
        } else {
            chart.updateSeries(seriesData);
        }
    }

    if (rangeToggle && rangeMenu) {
        rangeToggle.addEventListener('click', () => rangeMenu.classList.toggle('hidden'));
        document.addEventListener('click', (e) => {
            if (!rangeMenu.classList.contains('hidden') && !root.contains(e.target)) rangeMenu.classList.add('hidden');
        });
        root.querySelectorAll('[data-range-option]').forEach((btn) => {
            btn.addEventListener('click', () => {
                rangeDays = Number(btn.dataset.rangeOption);
                if (rangeLabel) rangeLabel.textContent = btn.textContent.trim();
                rangeMenu.classList.add('hidden');
                renderChart();
            });
        });
    }

    renderDeviceTabs();
    renderMetricTabs();
    renderChart();

    return {
        pushPoint(deviceId, code, x, y) {
            const entry = chartsByCode[code];
            if (!entry) return;
            if (!entry.seriesByDevice[deviceId]) entry.seriesByDevice[deviceId] = [];
            entry.seriesByDevice[deviceId].push([x, y]);
            if (deviceId === activeDevice && code === activeCode) renderChart();
        },
    };
};

// ============================================================================
// Grafik per-sensor — satu chart mandiri per jenis sensor milik SATU device,
// semuanya tampil sekaligus dalam kartu terpisah (bukan lewat tab). Dipakai
// di halaman device untuk riwayat sensor device itu sendiri.
// ============================================================================
window.renderSensorCharts = function (root, { sensorTypes, history, height = 180 }) {
    const charts = {};
    const rawPoints = {};

    sensorTypes.forEach((type) => {
        const points = history
            .map((h) => {
                // REVISI FUZZY ON-DEVICE: tma_cm/hujan_mm sudah bukan kolom
                // khusus lagi di sensor_data - semua sensor (termasuk
                // keduanya) sekarang lewat h.readings, sama seperti sensor
                // lain. Case khusus lama dihapus karena h.tma_cm/h.hujan_mm
                // tidak pernah ada di payload JSON SensorData lagi (selalu
                // undefined), yang bikin grafik TMA & Hujan kosong.
                const value = h.readings ? h.readings[type.code] : null;
                return value === null || value === undefined ? null : [new Date(h.recorded_at).getTime(), value];
            })
            .filter((p) => p !== null);
        rawPoints[type.code] = points;

        const el = root.querySelector(`[data-sensor-chart="${type.code}"]`);
        if (!el) return;

        const chart = new ApexCharts(el, {
            ...window.softChartDefaults,
            chart: { ...window.softChartDefaults.chart, height, type: 'area' },
            series: [{ name: type.name, data: points }],
            colors: [type.is_core ? '#248232' : '#2ba84a'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.2, opacityTo: 0, stops: [0, 90, 100] } },
            tooltip: {
                custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                    const value = series[seriesIndex][dataPointIndex];
                    const xValue = w.globals.seriesX[seriesIndex][dataPointIndex];
                    const dateStr = new Date(xValue).toLocaleString('id-ID', {
                        day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit',
                    });
                    const color = w.globals.colors[seriesIndex] || '#248232';
                    const displayValue = (value === null || value === undefined) ? '-' : value;
                    return `
                        <div style="padding:8px 12px;font-family:Inter,sans-serif;min-width:120px;">
                            <div style="font-size:11px;color:#9aa6a4;margin-bottom:4px;">${dateStr}</div>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <span style="width:8px;height:8px;border-radius:999px;background:${color};display:inline-block;flex-shrink:0;"></span>
                                <span style="font-size:13px;font-weight:600;color:#111818;">${displayValue} ${type.unit || ''}</span>
                            </div>
                        </div>`;
                },
            },
            xaxis: { ...window.softChartDefaults.xaxis, type: 'datetime' },
        });
        chart.render();
        charts[type.code] = chart;
    });

    return {
        pushPoint(code, x, y) {
            if (y === null || y === undefined || !rawPoints[code]) return;
            rawPoints[code].push([x, y]);
            if (charts[code]) charts[code].appendData([{ data: [[x, y]] }]);
        },
    };
};

// Format "5 menit lalu" dari timestamp ISO — dipakai untuk menampilkan
// kesegaran data terakhir tanpa perlu refresh halaman.
window.timeAgo = function (isoString) {
    if (!isoString) return '—';
    const diffSec = Math.max(0, Math.floor((Date.now() - new Date(isoString).getTime()) / 1000));
    if (diffSec < 60) return 'baru saja';
    const diffMin = Math.floor(diffSec / 60);
    if (diffMin < 60) return `${diffMin} menit lalu`;
    const diffHour = Math.floor(diffMin / 60);
    if (diffHour < 24) return `${diffHour} jam lalu`;
    const diffDay = Math.floor(diffHour / 24);
    return `${diffDay} hari lalu`;
};

Alpine.start();