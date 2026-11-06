import { CONFIG } from '../config.js';
import { boundingBox, haversineKm } from './geo.js';
const closed = new Set(['ditolak', 'selesai', 'padam_sendiri', 'duplikat']);
export function findDuplicate(reports, candidate, now = Date.now()) {
  const box = boundingBox(candidate.lat, candidate.lng, CONFIG.duplicateRadiusKm);
  return reports.filter(r => !r.parentId && !closed.has(r.status) && now - r.createdAt <= CONFIG.duplicateWindowHours * 3600000 && now >= r.createdAt && r.lat >= box.minLat && r.lat <= box.maxLat && r.lng >= box.minLng && r.lng <= box.maxLng && haversineKm(candidate, r) <= CONFIG.duplicateRadiusKm).sort((a,b)=>haversineKm(candidate,a)-haversineKm(candidate,b))[0] || null;
}
export function duplicateConflict(reports, root, userId) {
  return reports.some(r => (r.id === root.id || r.parentId === root.id) && r.reporterId === userId) || (root.reporterId === userId);
}
