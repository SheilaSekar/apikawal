import test from 'node:test';
import assert from 'node:assert/strict';
import { createReport, addVote, changeStatus, setRole, currentRole } from '../assets/js/modules/store.js';

class MemoryStorage { data=new Map(); getItem(k){return this.data.get(k)??null;} setItem(k,v){this.data.set(k,String(v));} removeItem(k){this.data.delete(k);} }
globalThis.localStorage=new MemoryStorage();
const state=()=>({version:1,role:'warga',reports:[],hotspots:[],pois:[],weights:{verification:30,landtype:20,hotspot:20,poi:15,support:10,photo:5},notifications:[],flags:[],rateEvents:[],preferences:{}});
test('report creation enforces demo hourly limit and records local simulation',()=>{
  localStorage=new MemoryStorage();const s=state();setRole('warga');
  for(let i=0;i<5;i++)createReport({lat:-3.3+i*.01,lng:105.3,landType:'hutan',signs:['asap']},s,100000+i);
  assert.equal(s.reports.length,5);assert.ok(s.reports.every(r=>r.isSimulated));
  assert.throws(()=>createReport({lat:-3.2,lng:105.2,landType:'gambut',signs:['api']},s,110000),/maksimal 5/);
});
test('duplicate report adds a weighted confirmation and stores no second root',()=>{
  localStorage=new MemoryStorage();const s=state();setRole('relawan');
  const root={id:'AK-root',reporterId:'user-lain',lat:-3.3,lng:105.3,landType:'hutan',status:'baru',createdAt:100000,updatedAt:100000,photos:[],votes:[],logs:[],isSimulated:true,parentId:null};s.reports.push(root);
  const result=createReport({lat:-3.3001,lng:105.3,landType:'hutan',signs:['asap']},s,101000);
  assert.equal(result.duplicate.id,root.id);assert.equal(result.report.status,'duplikat');assert.equal(root.votes[0].weight,2);assert.equal(s.reports.length,2);
});
test('vote threshold and status changes append audit logs',()=>{
  localStorage=new MemoryStorage();const s=state();setRole('warga');const r={id:'AK-test',reporterId:'pelapor',lat:-3.3,lng:105.3,landType:'gambut',status:'baru',createdAt:100000,photos:[],votes:[{userId:'u2',vote:'konfirmasi',weight:1,role:'warga'}],logs:[],parentId:null};s.reports.push(r);
  addVote(r.id,'konfirmasi',s,200000);assert.equal(r.status,'terverifikasi');assert.equal(r.logs.at(-1).to,'terverifikasi');
  setRole('petugas');changeStatus(r.id,'diteruskan','Petugas demo bergerak',s,300000);assert.equal(r.logs.at(-1).note,'Petugas demo bergerak');assert.ok(s.notifications.length);
  assert.equal(currentRole(s),'petugas');
});
