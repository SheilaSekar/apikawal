import './ui.js';
import '../vendor/leaflet/leaflet.js';
import '../vendor/chartjs/chart.umd.js';
import { initPages } from './modules/pages.js';
const year=document.querySelector('[data-year]'); if(year) year.textContent=new Date().getFullYear();
initPages();
