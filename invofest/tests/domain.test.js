import test from 'node:test';
import assert from 'node:assert/strict';
import { haversineKm, boundingBox } from '../assets/js/domain/geo.js';
import { findDuplicate, duplicateConflict } from '../assets/js/domain/duplicate.js';
import { evaluateVerification, canVote, voteWeight } from '../assets/js/domain/verification.js';
import { calculatePriority } from '../assets/js/domain/priority.js';
import { transitionStatus } from '../assets/js/domain/status.js';

const now=Date.parse('2026-11-06T12:00:00Z');
const report=(extra={})=>({id:'AK-1',reporterId:'u1',lat:-3.2,lng:105.4,landType:'gambut',status:'baru',createdAt:now-3600000,photos:[],...extra});

test('geo distance, bounding box, and public precision', async()=>{
  const {roundCoord}=await import('../assets/js/domain/geo.js');
  assert.ok(haversineKm({lat:0,lng:0},{lat:0,lng:.01})>1.1);
  assert.ok(boundingBox(-3,105,.3).maxLat> -3);
  assert.equal(roundCoord(-3.12346),-3.123);
});
test('duplicate requires open parent, 300m, and six-hour window',()=>{
  const root=report();
  assert.equal(findDuplicate([root],{lat:-3.2001,lng:105.4},now).id,root.id);
  assert.equal(findDuplicate([report({createdAt:now-7*3600000})],{lat:-3.2,lng:105.4},now),null);
  assert.equal(findDuplicate([report({status:'selesai'})],{lat:-3.2,lng:105.4},now),null);
  assert.equal(findDuplicate([root],{lat:-3.21,lng:105.4},now),null);
  assert.equal(duplicateConflict([root],root,'u1'),true);
});
test('verification applies role weights and status thresholds',()=>{
  assert.equal(voteWeight('relawan',true),2); assert.equal(voteWeight('relawan',false),1);
  assert.equal(evaluateVerification([{vote:'konfirmasi',weight:1},{vote:'konfirmasi',weight:1}]).status,'terverifikasi');
  assert.equal(evaluateVerification([{vote:'tidak_terlihat',weight:3}]).status,'ditolak');
  assert.equal(evaluateVerification([{vote:'sudah_padam',weight:2}]).status,'padam_sendiri');
  assert.equal(evaluateVerification([{vote:'konfirmasi',weight:3,role:'relawan',approved:true}]).status,'terverifikasi');
  assert.equal(canVote(report(),{id:'u1'},[]),false);
  assert.equal(canVote(report(),{id:'u2'},[{userId:'u2'}]),false);
});
test('priority uses six factors and caps an unverified report at 59',()=>{
  const result=calculatePriority(report(),{votes:[{vote:'konfirmasi',weight:3}],hotspots:[],pois:[],duplicateCount:4},undefined,now);
  assert.equal(Object.keys(result.breakdown).length,6);
  assert.equal(result.score,59); assert.equal(result.level,'sedang'); assert.equal(result.unverified,true);
  assert.equal(calculatePriority(report({status:'terverifikasi'}),{votes:[],hotspots:[],pois:[]},undefined,now).unverified,false);
});
test('status machine requires officer note and admin override reason',()=>{
  assert.equal(transitionStatus(report({status:'terverifikasi'}),'diteruskan','petugas','Unit bergerak').status,'diteruskan');
  assert.throws(()=>transitionStatus(report({status:'terverifikasi'}),'diteruskan','petugas',''),/Catatan/);
  assert.throws(()=>transitionStatus(report(),'selesai','petugas','tidak sah'),/tidak diizinkan/);
  assert.throws(()=>transitionStatus(report(),'selesai','admin',' '),/Alasan/);
  assert.equal(transitionStatus(report(),'selesai','admin','Koreksi admin').status,'selesai');
});
