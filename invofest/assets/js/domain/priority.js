import { CONFIG } from '../config.js';
import { haversineKm } from './geo.js';
const landFactor = { gambut: 1, hutan: .8, semak: .7, kebun: .6, lainnya: .3 };
const proximity = km => km == null ? 0 : km <= 1 ? 1 : Math.max(0, 1 - (km - 1) / 4);
const clamp = n => Math.min(1, Math.max(0, n));
export function calculatePriority(report, context = {}, weights = CONFIG.weightsDefault, now = Date.now()) {
  const votes = context.votes || [];
  const yes = votes.filter(v=>v.vote==='konfirmasi').reduce((n,v)=>n+(Number(v.weight)||0),0);
  const no = votes.filter(v=>v.vote==='tidak_terlihat').reduce((n,v)=>n+(Number(v.weight)||0),0);
  const hours = Math.max(0,(now-report.createdAt)/3600000);
  const recency = hours < 6 ? 1 : Math.max(0, 1 - (hours - 6) / 42);
  const duplicates = context.duplicateCount || 0;
  const hotspots = context.hotspots || [], pois = context.pois || [];
  const distance = places => places.length ? Math.min(...places.map(p=>haversineKm(report,p))) : null;
  const factors = {
    verification: clamp((yes-no)/3), landtype: landFactor[report.landType] ?? .3,
    hotspot: proximity(distance(hotspots)), poi: proximity(distance(pois)),
    support: .6*Math.min(1,duplicates/4)+.4*recency, photo: (report.photos?.length || 0)>0 ? 1 : 0,
  };
  const breakdown = Object.fromEntries(Object.entries(factors).map(([k,factor])=>[k,{factor,weight:Number(weights[k] ?? CONFIG.weightsDefault[k]),score:factor*Number(weights[k] ?? CONFIG.weightsDefault[k])}]));
  const raw = Math.round(Object.values(breakdown).reduce((sum,v)=>sum+v.score,0));
  const score = report.status === 'baru' ? Math.min(CONFIG.unverifiedScoreCap,raw) : raw;
  const level = score >= 80 ? 'kritis' : score >= 60 ? 'tinggi' : score >= 40 ? 'sedang' : 'rendah';
  return { score, level, breakdown, unverified: report.status === 'baru' };
}
