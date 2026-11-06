import { CONFIG } from '../config.js';
import { calculatePriority } from './priority.js';

function random(seed) { let x=seed>>>0; return () => { x=(x*1664525+1013904223)>>>0; return x/4294967296; }; }
const pick=(a,r)=>a[Math.floor(r()*a.length)];
const statuses=CONFIG.statuses;
const lands=['gambut','hutan','semak','kebun','lainnya'];
const roles=['warga','relawan','petugas','admin'];
const demoVotes=[[],[{vote:'konfirmasi',weight:1,role:'warga'}],[{vote:'konfirmasi',weight:2,role:'relawan',approved:true}],[{vote:'tidak_terlihat',weight:1,role:'warga'}],[{vote:'sudah_padam',weight:2,role:'relawan',approved:true}]];
export function createSeed(now=Date.now()) {
  const r=random(20261106), rand=(min,max)=>min+r()*(max-min), reports=[];
  for(let i=0;i<30;i++){
    const status=statuses[i%statuses.length], createdAt=now-Math.round(rand(.25,330)*3600000);
    const rep={id:`AK-${now?new Date(now).getFullYear():2026}-${String(i+1).padStart(6,'0')}`,reporterId:`user-${1+Math.floor(r()*10)}`,lat:rand(-3.57,-3.03),lng:rand(105.03,105.77),landType:pick(lands,r),signs:[pick(['asap','api','bara'],r)],description:['Terlihat asap tipis dari sisi kebun.','Warga melaporkan bau asap di sekitar lokasi.','Ada indikasi bara dekat area semak.','Pengamatan visual dari jalan umum.'][i%4],status,createdAt,updatedAt:createdAt,photos:[],isSimulated:true,parentId:null,votes:structuredClone(demoVotes[i%demoVotes.length]),logs:[{from:null,to:'baru',actor:'system',note:'Laporan simulasi dibuat',at:createdAt}],needsModeration:false};
    if(status!=='baru') rep.logs.push({from:'baru',to:status,actor:status==='diteruskan'||status==='dalam_penanganan'||status==='terkendali'||status==='selesai'?'petugas':'system',note:'Perubahan status data simulasi',at:createdAt+3600000});
    rep.priority=calculatePriority(rep,{votes:rep.votes,hotspots:[],pois:[],duplicateCount:0},CONFIG.weightsDefault,now);
    reports.push(rep);
  }
  for(let i=0;i<4;i++){reports[26+i].parentId=reports[i].id; reports[26+i].status='duplikat';reports[26+i].lat=reports[i].lat+rand(-.001,.001);reports[26+i].lng=reports[i].lng+rand(-.001,.001);reports[26+i].createdAt=Math.min(reports[i].createdAt+3600000,now);}
  const hotspots=Array.from({length:300},(_,i)=>({id:`HS-${String(i+1).padStart(4,'0')}`,lat:rand(-3.58,-3.02),lng:rand(105.02,105.78),confidence:Math.round(rand(30,100)),detectedAt:now-Math.round(rand(1,i<15?24:365*24)*3600000),source:'SIMULASI',isSimulated:true}));
  const poiKinds=['permukiman','sekolah','faskes'];
  const pois=Array.from({length:30},(_,i)=>({id:`POI-${i+1}`,name:`${pick(['Desa','Dusun','Sekolah','Puskesmas'],r)} Demo ${i+1}`,kind:pick(poiKinds,r),lat:rand(-3.57,-3.03),lng:rand(105.03,105.77),isSimulated:true}));
  for(const rep of reports) rep.priority=calculatePriority(rep,{votes:rep.votes,hotspots:hotspots.filter(h=>now-h.detectedAt<86400000),pois,duplicateCount:reports.filter(x=>x.parentId===rep.id).length},CONFIG.weightsDefault,now);
  const cells=[];
  for(let i=0;i<12;i++)for(let j=0;j<16;j++){const minLat=CONFIG.bounds.minLat+i*.05,minLng=CONFIG.bounds.minLng+j*.05;const inside=p=>p.lat>=minLat&&p.lat<minLat+.05&&p.lng>=minLng&&p.lng<minLng+.05;cells.push({key:`${(-72+i)}_${2100+j}`,minLat,minLng,maxLat:minLat+.05,maxLng:minLng+.05,hotspotCount:hotspots.filter(inside).length,reportCount:reports.filter(x=>!x.parentId&&x.status==='terverifikasi'&&inside(x)).length});}
  const maxH=Math.max(1,...cells.map(c=>c.hotspotCount)),maxR=Math.max(1,...cells.map(c=>c.reportCount));cells.forEach(c=>c.score=Math.round((.7*c.hotspotCount/maxH+.3*c.reportCount/maxR)*100));cells.sort((a,b)=>b.score-a.score);cells.forEach((c,i)=>{c.rank=i+1;c.level=i<cells.length*.2?'tinggi':i<cells.length*.5?'sedang':'rendah';});
  return {version:1,createdAt:now,role:'warga',currentUserId:'user-warga',weights:{...CONFIG.weightsDefault},reports,hotspots,pois,notifications:[],flags:[],grid:cells,rateEvents:[],reputations:{},preferences:{}};
}
