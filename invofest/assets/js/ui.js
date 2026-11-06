import { CONFIG } from './config.js';
import { currentRole, setRole } from './modules/store.js';

const page = location.pathname.split('/').pop() || 'index.html';
const nav = [
  ['index.html', 'Beranda', 'all'], ['ekosistem.html', 'Ekosistem', 'all'], ['peta.html', 'Peta', 'all'],
  ['lapor.html', 'Buat laporan', 'warga'], ['dashboard.html', 'Dashboard', 'warga'],
  ['petugas.html', 'Antrean', 'petugas'], ['admin.html', 'Admin', 'admin'],
  ['analitik.html', 'Analitik', 'all'], ['tentang.html', 'Tentang', 'all'],
];
const logo = '<svg viewBox="0 0 40 40" aria-hidden="true"><path fill="currentColor" d="M21 3C17 12 9 16 9 25a11 11 0 0 0 22 0c0-7-5-12-10-22Zm-2 29a6 6 0 0 1-5-9c1-2 3-4 5-6-1 5 4 7 4 11a4 4 0 0 1-4 4Z"/></svg>';
const mount = document.querySelector('[data-site-chrome]');
if (mount) {
  mount.innerHTML = `<div class="emergency-bar" role="note"><strong>DARURAT</strong><span>${CONFIG.emergency}</span></div><header class="site-header"><nav class="site-nav" aria-label="Navigasi utama"><a class="brand" href="index.html"><span class="brand-mark">${logo}</span><span class="brand-wordmark">APIKAWAL</span></a><button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-links" aria-label="Buka navigasi"><span aria-hidden="true">☰</span></button><div class="nav-links" id="primary-links">${nav.map(([href,label,role])=>`<a class="nav-link" href="${href}" data-role="${role}" ${page===href?'aria-current="page"':''}>${label}</a>`).join('')}</div><div class="role-control"><label for="demo-role">MODE DEMO · PERAN</label><select id="demo-role" aria-label="Pilih peran simulasi">${CONFIG.roles.map(r=>`<option value="${r}">${CONFIG.roleLabels[r]}</option>`).join('')}</select></div></nav><div class="demo-strip"><span class="demo-dot"></span> MODE SIMULASI <span>·</span> Data dan tindakan tersimpan di perangkat ini</div></header>`;
  const savedRole = currentRole();
  const select = mount.querySelector('#demo-role'); select.value = CONFIG.roles.includes(savedRole) ? savedRole : 'warga';
  const applyRole = role => mount.querySelectorAll('.nav-link[data-role]').forEach(a => a.hidden = a.dataset.role !== 'all' && a.dataset.role !== role);
  applyRole(select.value);
  select.addEventListener('change', () => { setRole(select.value); applyRole(select.value); document.dispatchEvent(new CustomEvent('apikawal:rolechange',{detail:{role:select.value}})); });
  const button=mount.querySelector('.menu-toggle'), links=mount.querySelector('.nav-links');
  button.addEventListener('click',()=>{const open=button.getAttribute('aria-expanded')!=='true';button.setAttribute('aria-expanded',String(open));links.classList.toggle('is-open',open);button.setAttribute('aria-label',open?'Tutup navigasi':'Buka navigasi');});
}
const footer=document.querySelector('[data-site-footer]');
if(footer) footer.innerHTML=`<footer class="site-footer"><div class="footer-inner"><a class="brand footer-brand" href="index.html"><span class="brand-mark">${logo}</span><span class="brand-wordmark">APIKAWAL</span></a><p>Kolaborasi warga untuk laporan risiko kebakaran lahan yang dapat ditindaklanjuti.</p><p class="footer-limit">Bukan pendeteksi api otomatis dan bukan pengganti layanan darurat. Peran petugas dan seluruh data demo disimulasikan.</p><small>INVOFEST 2026 · Prototipe interaktif · <a href="tentang.html">Batasan dan metode</a></small></div></footer>`;
