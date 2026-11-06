import { CONFIG } from '../config.js';
export function evaluateVerification(votes, { status = 'baru' } = {}) {
  if (status !== 'baru') return { status, confirmW: 0, denyW: 0, outW: 0 };
  const sums = { confirmW: 0, denyW: 0, outW: 0 };
  for (const v of votes) {
    const key = ({ konfirmasi: 'confirmW', tidak_terlihat: 'denyW', sudah_padam: 'outW' })[v.vote];
    if (key) sums[key] += Number(v.weight) || 0;
  }
  const trusted = votes.some(v => v.vote === 'konfirmasi' && ['relawan','petugas','admin'].includes(v.role) && (v.role !== 'relawan' || v.approved));
  let next = status;
  if (trusted || (sums.confirmW >= 2 && sums.confirmW > sums.denyW)) next = 'terverifikasi';
  else if (sums.denyW >= 3 && sums.denyW > sums.confirmW) next = 'ditolak';
  else if (sums.outW >= 2 && sums.outW >= sums.confirmW) next = 'padam_sendiri';
  return { status: next, ...sums };
}
export function canVote(report, user, existingVotes = []) {
  if (!user || !report || !['baru','terverifikasi'].includes(report.status)) return false;
  if (report.reporterId === user.id) return false;
  if (existingVotes.some(v => v.userId === user.id)) return false;
  return true;
}
export function voteWeight(role, approved = false) { return role === 'relawan' && !approved ? 1 : CONFIG.weights[role] || 1; }
