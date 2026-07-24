

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
    stroke: { width: 3, curve: 'smooth' },
    markers: { size: 0, hover: { size: 5 } },
    grid: { show: false, padding: { left: 10, right: 10 } },
    tooltip: { theme: 'light' },
    xaxis: { axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: '#9aa6a4', fontSize: '11px' } } },
};

window.L = L;

window.ApexCharts = ApexCharts;
window.Alpine = Alpine;

Alpine.start();




