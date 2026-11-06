import { CONFIG } from '../config.js';
import { createSeed } from '../domain/seed.js';
import { findDuplicate, duplicateConflict } from '../domain/duplicate.js';
import { evaluateVerification, voteWeight, canVote } from '../domain/verification.js';
import { calculatePriority } from '../domain/priority.js';
import { transitionStatus } from '../domain/status.js';

const key=CONFIG.storageKey;
const clone=value=>structuredClone(value);
const syncRole=state=>{state.role=currentRole(state);state.currentUserId=`user-${state.role}`;};
export function getState() {
  try { const saved=JSON.parse(localStorage.getItem(key)); if(saved?.version===1&&Array.isArray(saved.reports)) return saved; } catch {}
  return resetState();
}
export function saveState(state) { try { localStorage.setItem(key,JSON.stringify(state)); return true; } catch(error) { if(error?.name==='QuotaExceededError'||error?.name==='NS_ERROR_DOM_QUOTA_REACHED') throw new Error('Penyimpanan browser penuh. Hapus foto atau reset data demo.'); throw error; } }
export function resetState() { const state=createSeed(); saveState(state); return state; }
export function currentRole(state=getState()) { try { const persisted=JSON.parse(localStorage.getItem(key)); if(CONFIG.roles.includes(persisted?.role))return persisted.role; } catch {} return state.role || 'warga'; }
export function setRole(role) { if(!CONFIG.roles.includes(role)) throw new Error('Peran demo tidak dikenal.'); const s=getState(); s.role=role; s.currentUserId=`user-${role}`; saveState(s); return s; }
export function currentUser(state=getState()) { const role=currentRole(state); return {id:`user-${role}`,name:CONFIG.roleLabels[role],role,approved:role==='relawan'}; }
function updateScore(state,report,now=Date.now()) { const children=state.reports.filter(r=>r.parentId===report.id); const nearbyHotspots=state.hotspots.filter(h=>now-h.detectedAt<=24*3600000); report.priority=calculatePriority(report,{votes:report.votes,hotspots:nearbyHotspots,pois:state.pois,duplicateCount:children.length},state.weights,now); report.updatedAt=now; }
export function listReports(state=getState()) { const role=currentRole(state);return state.reports.filter(r=>!r.parentId&&(!r.needsModeration||role==='admin')).map(clone).sort((a,b)=>b.createdAt-a.createdAt); }
export function findReport(code,state=getState()) { return state.reports.find(r=>r.id===code||r.code===code)||null; }
export function createReport(input,state=getState(),now=Date.now()) {
  const user=currentUser(state);
  if(user.role==='public') throw new Error('Masuk ke mode warga untuk membuat laporan.');
  const since=state.rateEvents.filter(e=>e.userId===user.id&&now-e.at<3600000); if(since.length>=5) throw new Error('Batas demo: maksimal 5 laporan per jam.');
  const report={...input,id:`AK-${new Date(now).getFullYear()}-${String(state.reports.length+1).padStart(6,'0')}`,reporterId:user.id,status:'baru',createdAt:now,updatedAt:now,photos:input.photos||[],votes:[],logs:[{from:null,to:'baru',actor:user.role,note:'Laporan dibuat',at:now}],isSimulated:true,parentId:null,needsModeration:false};
  const duplicate=findDuplicate(state.reports,report,now);
  if(duplicate) {
    if(duplicateConflict(state.reports,duplicate,user.id)) throw new Error('Kamu sudah mendukung laporan serupa ini.');
    report.parentId=duplicate.id; report.status='duplikat'; report.logs.push({from:'baru',to:'duplikat',actor:'system',note:'Digabung sebagai laporan serupa',at:now});
    duplicate.votes.push({userId:user.id,vote:'konfirmasi',weight:voteWeight(user.role,user.approved),role:user.role,approved:user.approved,sourceReportId:report.id});
    duplicate.logs.push({from:duplicate.status,to:duplicate.status,actor:'system',note:'Dukungan bertambah dari laporan serupa',at:now}); updateScore(state,duplicate,now);
    const evaluation=evaluateVerification(duplicate.votes,{status:duplicate.status});
    if(evaluation.status!==duplicate.status){const before=duplicate.status;duplicate.status=evaluation.status;duplicate.logs.push({from:before,to:evaluation.status,actor:'system',note:'Status diperbarui setelah laporan serupa menambah dukungan',at:now});if(evaluation.status==='terverifikasi'){state.reputations ||= {};state.reputations[duplicate.reporterId]=(state.reputations[duplicate.reporterId]||0)+1;state.notifications.unshift({id:`N-${now}`,userId:duplicate.reporterId,reportId:duplicate.id,message:'Laporanmu telah terverifikasi melalui dukungan laporan serupa.',at:now,read:false});}}
    updateScore(state,duplicate,now);
  }
  state.reports.push(report); state.rateEvents=[...since,{userId:user.id,at:now}];
  state.reputations ||= {}; if((state.reputations[user.id]??0)<=-3){report.needsModeration=true;state.flags.push({reportId:report.id,reason:'Reputasi demo rendah',resolved:false,at:now});}
  syncRole(state);saveState(state); return {report,duplicate};
}
export function addVote(reportId,vote,state=getState(),now=Date.now()) {
  const report=findReport(reportId,state), user=currentUser(state);
  if(!report) throw new Error('Laporan tidak ditemukan.');
  if(!canVote(report,user,report.votes)) throw new Error('Kamu tidak dapat memverifikasi laporan ini.');
  if(!['konfirmasi','tidak_terlihat','sudah_padam'].includes(vote)) throw new Error('Pilihan verifikasi tidak valid.');
  report.votes.push({userId:user.id,vote,weight:voteWeight(user.role,user.approved),role:user.role,approved:user.approved,at:now});
  const result=evaluateVerification(report.votes,{status:report.status});
  if(result.status!==report.status){const before=report.status;report.status=result.status;report.logs.push({from:before,to:result.status,actor:'system',note:'Status diperbarui berdasarkan hasil verifikasi',at:now});state.reputations ||= {};if(result.status==='terverifikasi'){state.reputations[report.reporterId]=(state.reputations[report.reporterId]||0)+1;state.notifications.unshift({id:`N-${now}`,userId:report.reporterId,reportId:report.id,message:'Laporanmu telah terverifikasi.',at:now,read:false},...['petugas','admin'].map(role=>({id:`N-${now}-${role}`,userId:`user-${role}`,reportId:report.id,message:`Laporan ${report.id} terverifikasi dan siap ditinjau.`,at:now,read:false})));}else if(result.status==='ditolak')state.reputations[report.reporterId]=(state.reputations[report.reporterId]||0)-1;if((state.reputations[report.reporterId]||0)<=-3){report.needsModeration=true;state.flags.push({reportId:report.id,reason:'Reputasi demo rendah',resolved:false,at:now});}}
  updateScore(state,report,now);syncRole(state);saveState(state); return report;
}
export function changeStatus(reportId,to,note,state=getState(),now=Date.now()) {
  const report=findReport(reportId,state); if(!report) throw new Error('Laporan tidak ditemukan.');
  const role=currentRole(state); const before=report.status;
  const updated=transitionStatus(report,to,role,note); Object.assign(report,updated);
  report.logs.push({from:before,to,actor:role,note,at:now});
  state.notifications.unshift({id:`N-${now}`,userId:report.reporterId,reportId:report.id,message:`Status laporan berubah menjadi ${to}.`,at:now,read:false});
  updateScore(state,report,now);syncRole(state);saveState(state);return report;
}
export function setWeights(weights,state=getState()) { const total=Object.values(weights).reduce((s,n)=>s+Number(n),0); if(total!==100) throw new Error(`Total bobot harus 100 (saat ini ${total}).`); state.weights=Object.fromEntries(Object.entries(weights).map(([k,v])=>[k,Number(v)]));state.reports.forEach(r=>updateScore(state,r));syncRole(state);saveState(state);return state; }
export function resolveFlag(reportId,state=getState()){if(currentRole(state)!=='admin')throw new Error('Hanya peran admin demo yang dapat meninjau laporan.');const report=findReport(reportId,state);if(report)report.needsModeration=false;state.flags.forEach(f=>{if(f.reportId===reportId)f.resolved=true;});syncRole(state);saveState(state);return state;}
export function flagReport(reportId,reason,state=getState(),now=Date.now()){const report=findReport(reportId,state),user=currentUser(state);if(!report)throw new Error('Laporan tidak ditemukan.');if(!reason?.trim())throw new Error('Pilih alasan laporan penyalahgunaan.');if(state.flags.some(f=>f.reportId===reportId&&f.flaggedBy===user.id))throw new Error('Kamu sudah melaporkan item ini.');state.flags.push({reportId,flaggedBy:user.id,reason:reason.trim(),resolved:false,at:now});syncRole(state);saveState(state);return state;}
export function getStoreKey(){return key;}
